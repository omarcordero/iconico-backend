<?php

namespace App\Http\Controllers\Admin;

use App\Events\Auth\Registered;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::whereRaw('1=1');

        if($request->search) {
            $users = $users->where('name', 'like', '%'. $request->search . '%')
                ->orWhere('mobile_number', 'like', '%'. $request->search . '%')
                ->orWhere('email', 'like', '%'. $request->search . '%');
        }

        if($request->role) {
            $role = $request->role;
            $users = $users->whereHas('roles', function ($query) use ($role){
                $query->where('name', $role);
            });
        }

        return view('admin.users.index', ['users' => $users->with('roles')->sortable(['created_at' => 'desc'])->paginate()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create', ['roles' => Role::get()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store(UserRequest $request)
    {
        $user = new User();
        $user->name = $request->get('name');
        $user->mobile_number = $request->get('mobile_number');
        $user->email = $request->get('email');

        if ($request->has('password')) {
            $user->password = bcrypt($request->get('password'));
        }

        $user->mobile_verified = $request->get('mobile_verified', 0);

        $user->save();

        $user->roles()->attach($request->get('role'));

        event(new Registered($user, Role::find($request->get('role'))->name));

        return redirect()->intended(route('admin.users'));
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('admin.users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user, 'roles' => Role::get()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return mixed
     */
    public function update(Request $request, User $user)
    {
        if(env('ADMIN_USER_EDIT_DISABLED', true)) {
            return view('admin.unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|max:255',
            'mobile_verified' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
            'confirmed' => 'sometimes|boolean',
        ]);

        $validator->sometimes('email', 'unique:users', function ($input) use ($user) {
            return strtolower($input->email) != strtolower($user->email);
        });

        $validator->sometimes('mobile_number', 'unique:users', function ($input) use ($user) {
            return strtolower($input->mobile_number) != strtolower($user->mobile_number);
        });

        $validator->sometimes('password', 'min:4', function ($input) {
            return $input->password;
        });

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->mobile_number = $request->get('mobile_number');

        if ($request->has('password')) {
            $user->password = bcrypt($request->get('password'));
        }

//        $user->mobile_verified = $request->get('mobile_verified', 0);
//        $user->active = $request->get('active', 0);
//        $user->confirmed = $request->get('confirmed', 0);

        $user->save();

        //roles
        if ($request->has('roles')) {
            $user->roles()->detach();

            if ($request->get('roles')) {
                $user->roles()->attach($request->get('roles'));
            }
        }

        return redirect()->intended(route('admin.users'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return void
     */
    public function destroy(User $user)
    {
        if(Auth::user()->hasRole('administrator') && !$user->hasRole('administrator')) {
            $user->forceDelete();
        }
        return redirect()->intended(route('admin.users'));
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
}
