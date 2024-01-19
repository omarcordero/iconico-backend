<?php

namespace App\Http\Controllers\Api\ICONICO;

use App\Events\Auth\Registered;
use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
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
use Illuminate\Support\Facades\Hash;
use Mail;


class UserController extends Controller
{

    public function updateProfile(Request $request){
        $aRequest   = $request->all();
        $profileId  = $aRequest['profileId'];
        $name       = $aRequest['name'];
        $phone      = $aRequest['mobile_number'];
        $users      = DB::table('users')
                            ->where('id', '=', $profileId)
                            ->update(['name' => $name, 'mobile_number' => $phone]);
        return array('status' => true, 'message' => 'Usuario cambiado');
    }

    public function changePassword(Request $request){
        $aRequest       = $request->all();
        $profileId      = $aRequest['profileId'];
        $password       = $aRequest['password'];
        $newPassword    = bcrypt($aRequest['newPassword']);
        $users          = DB::table('users')->where('id', '=', $profileId)->get();

        if (isset($users[0])) {
            $hashPassword   = $users[0]->password;
            $compare        = Hash::check($password, $hashPassword);
            if ($compare) {
                DB::table('users')->where('id', '=', $profileId)
                            ->update(['password' => $newPassword]);

                return array('status' => true, 'message' => 'Contraseña actualizada');
            } else {

                return array('status' => true, 'message' => 'Contraseña incorrecta');
            }
        } else {

            return array('status' => true, 'message' => 'No existe el usuario');
        }
    }

    public function getProfile(Request $request){
        $aRequest   = $request->all();
        $profileId  = $aRequest['profileId'];
        $users      = DB::table('users')
                        ->join('users_roles', 'users_roles.user_id', '=', 'users.id')
                        ->join('roles', 'roles.id', '=', 'users_roles.role_id')
                        ->select('users.*', 'roles.name as name_role', 'roles.id as role_id')
                        ->where('users.id', '=', $profileId)->get();

        return array('status' => true, 'message' => 'Query successfully', 'data' => $users);
    }

    public function resetPassword(Request $request) {
        $aRequest   = $request->all();
        $email  = $aRequest['email'];
        $token = $aRequest['subValidate'];

        $users = DB::table('users')->where('email', $email)->get();

        if ( isset($users[0]) ) {
            $name = $users[0]->name;

            $data = [
                'email' => $email,
                'name' => $name,
                'token' => $token
            ];

            Mail::to($email)->send(new ResetPassword($data));

            return array('status' => true, 'message' => 'Mail send successfully');
        } else {
            return array('status' => false, 'message' => 'Email not found');
        }

    }

    public function changeUpdatePasswordEmail (Request $request): array
    {
        $aRequest   = $request->all();
        $email  = $aRequest['email'];
        $password = $aRequest['password'];

        $newPassword = bcrypt($password);
        $users = DB::table('users')->where('email', '=', $email)->get();

        if ( isset($users[0]) ) {
            DB::table('users')->where('email', '=', $email)
                ->update(['password' => $newPassword]);

            return array('status' => true, 'message' => 'Update successfully');
        } else {
            return array('status' => false, 'message' => 'Error user');
        }

    }

    public function updatePhone (Request $request)
    {
        $aRequest = $request->all();
        $phone = $aRequest['phone'];
        $userId = $aRequest['userId'];

        $affected = DB::table('users')->where('id', $userId)->update(['mobile_number' => $phone]);

        return array('status' => true, 'message' => 'Update successfully');
    }

}
