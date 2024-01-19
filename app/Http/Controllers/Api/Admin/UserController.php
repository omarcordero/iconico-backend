<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\Auth\Registered;
use App\Http\Controllers\Controller;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\BankDetail;
use App\Models\DeliveryProfile;
use App\Models\Earning;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $users = User::whereRaw("1=1")->with('roles');

        $currentStore = admin_get_store();
        if ($currentStore) {
            // restrict the list of users based on current user's role
            $deliveryUsers = DeliveryProfile::where('created_by', $currentStore->user->id)->get()->pluck(['user_id'])->all();
            $users = $users->whereIn('id', array_merge($deliveryUsers, [$currentStore->owner_id]));
        }

        if ($request->email_like) {
            $users = $users->where('email', 'like', "%" . $request->email_like . "%");
        }

        if ($request->name_like) {
            $users = $users->where('name', 'like', "%" . $request->name_like . "%");
        }

        if ($request->roles_like) {
            $role = $request->roles_like;
            $users = $users->whereHas('roles', function ($query) use ($role) {
                $query->where('name', 'like', '%' . $role . '%');
            });
        }

        return response()->json($users->with('bankDetail')->with('wallet')->orderBy('created_at', 'asc')->paginate(config('constants.paginate_per_page')));
    }

    public function show($id)
    {
        $user = User::with('roles')->find($id);

        return response()->json($user->load('bankDetail')->load('wallet'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'mobile_number' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,id'
        ]);

        $user = User::create($request->all());
        $user->password = bcrypt($request->password);
        $user->save();

        // if current user is store, associated the new created user with the store
        $currentStore = admin_get_store();

        
        if ($currentStore) {
            $user->created_by = $currentStore->user->id;
            $user->save();
        }

        if($request->role == 4){// 4 es el valor del id del perfil de delivery, en la tabla maestra roles
            $dataDelivery['is_online'] = 0;
            $dataDelivery['longitude'] = null;
            $dataDelivery['latitude'] = null;
            $dataDelivery['assigned'] = 0;
            $dataDelivery['user_id'] = $user->id;
            $dataDelivery['created_by'] = $user->created_by;

            $profile = DeliveryProfile::create($dataDelivery);
            
            //FALTARÍA METODO ADICIONAL PARA METER EL USUARIO Y ROL EN LA TABLA USER_ROLES
            
            $user->roles()->attach($request->role);

        }else{
            // attach role
        $user->roles()->attach($request->role);

        event(new Registered($user, Role::find($request->role)->name));
        }

        // attach role
        //$user->roles()->attach($request->role);

        //event(new Registered($user, Role::find($request->role)->name));

        //return response()->json($user->load('bankDetail'));
    }

    public function update(Request $request, User $user)
    {
        if(env('ADMIN_USER_EDIT_DISABLED', true)) {
            return response()->json([], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|max:255',
            'pay_earnings' => 'in:0,1',
            'pay_earning_proof_image' => 'image|nullable',
            'account_name' => 'required_with:bank_name,account_number,ifsc|string|nullable',
            'bank_name' => 'required_with:account_name,account_number,ifsc|string|nullable',
            'account_number' => 'required_with:account_name,bank_name,ifsc|string|nullable',
            'ifsc' => 'required_with:account_name,bank_name,account_number|string|nullable',
        ]);

        $validator->sometimes('email', 'unique:users', function ($input) use ($user) {
            return strtolower($input->email) != strtolower($user->email);
        });

        $validator->sometimes('mobile_number', 'unique:users', function ($input) use ($user) {
            return strtolower($input->mobile_number) != strtolower($user->mobile_number);
        });

        $validator->sometimes('password', 'min:6', function ($input) {
            return $input->password;
        });

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->mobile_number = $request->get('mobile_number');

        if ($request->has('password')) {
            $user->password = bcrypt($request->get('password'));
        }

        $user->save();

        //roles
        if ($request->has('roles')) {
            $user->roles()->detach();

            if ($request->get('roles')) {
                $user->roles()->attach($request->get('roles'));
            }
        }

        // bank details
        if ($request->account_name) {
            if ($user->bankDetail) {
                $bankDetail = $user->bankDetail;
            } else {
                $bankDetail = new BankDetail();
            }
            $bankDetailsData = array_merge(["user_id" => $user->id, "name" => $request->account_name], $request->only(['bank_name', 'account_number', 'ifsc']));
            $bankDetail->fill($bankDetailsData);
            $bankDetail->save();
        } else if (!$request->account_name && $user->bankDetail) {
            $bankDetail = $user->bankDetail;
            $bankDetailsData = array_merge(["user_id" => $user->id, "name" => $request->account_name], $request->only(['bank_name', 'account_number', 'ifsc']));
            $bankDetail->fill($bankDetailsData);
            $bankDetail->save();
        }

        // pay requested withdrawl amount to user
        if($request->pay_earnings) {

            // attach if any proof was provided
            $proof_url = null;
            if ($request->pay_earning_proof_image) {
                $path = $request->file('image')->store('uploads');
                $proof_url = Storage::url($path);
            }

            Transaction::where('user_id', $user->id)    ->where('is_paid', 0)
                ->update(['is_paid' => 1, 'image_url' => $proof_url]);
            $user->withdraw($user->balance);
        }

        return response()->json($user->load('bankDetail')->load('wallet'));
    }

    public function destroy(User $user)
    {
        // do not allow deletion of administrator user
        if (!$user->hasRole('administrator')) {
            $user->forceDelete();
        }

        return response()->json([], 204);
    }

    public function roles()
    {
        $currentStore = admin_get_store();
        if($currentStore) {
            // store owners can only create delivery users
            $roles = Role::where('name', 'delivery')->get();
        } else {
            $roles = Role::all();
        }

        return response()->json($roles);
    }

    /**
     * Delete notifications
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearNotification(Request $request)
    {
        Auth::user()->unreadNotifications->where('type', $request->type)->markAsRead();
        return response()->json(["success" => 1]);
    }

    public function getValidateUserProfile(Request $request) {
        $aRequest = $request->all();
        $userId = $aRequest['userId'];
        $validate = intval($userId);
        $users = DB::select('select * from users_roles where user_id = ?', [$validate]);
        return $users;
    }

}
