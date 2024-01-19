<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * Login admin user
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // only admin or store can login
            if(!Auth::user()->hasRole('administrator') && !Auth::user()->hasRole('owner') && !Auth::user()->hasRole('delivery')) {
                return response()->json(["message" => "Permission denied. No suitable role found"], 400);
            }
            $user = Auth::user();
            $scope = $user->hasRole('administrator') ? ['manage-as-admin'] : [];
            $storeId = $user->hasRole('owner') ? $user->store->id: null;
            $token = $user->createToken('Default', $scope)->accessToken;
            return response()->json([
                "token" => $token, "user" => $user,
                "store_id" => $storeId]);
        }
        return response()->json(["message" => "Invalid Login"], 400);
    }

    public function validateEmail(Request $request) {
        $aRequest = $request->all();
        $email = $aRequest['email'];
        $users = DB::table('users')->where('email', '=', $email)->get();
        if ($users[0]) {
            $userId = $users[0]->id;
            $users_roles = DB::table('users_roles')->where('user_id', '=', $userId)->get();
            if ($users_roles[0]) {
                $roleId = $users_roles[0]->role_id;
                if ($roleId === 1 || $roleId === 3 || $roleId === 4) {
                    $aResponse = array(
                        'status' => true,
                        'message' => 'Login successfully',
                        'data' => array(
                                'roleId' => $roleId
                            ),
                        'code' => '200'
                    );
                } else {
                    $aResponse = array(
                        'status' => true,
                        'message' => 'Permission denied',
                        'code' => '300'
                    );
                }
            }
        } else {
            $aResponse = array(
                    'status' => true,
                    'message' => 'Email not found',
                    'code' => '400'
                );
        }

        return $aResponse;
    }
}
