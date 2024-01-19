<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiLoginRequest;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users via REST API
    |
    */

    public function authenticate(ApiLoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if(!Auth::user()->hasRole($request->role)) {
                return response()->json(["error" => "Permission denied. No suitable role found"], 400);
            }
            $user = Auth::user();

            // fallback mechanism for older users to get referral code
            while(1) {
                $code = generate_numeric_otp(6);
                if(!User::where('refer_code', $code)->exists()) {
                    break;
                }
            }
            $user->refer_code = $code;
            $user->save();

            $token = $user->createToken('Default')->accessToken;
            return response()->json(["token" => $token, "user" => $user->refresh()]);
        }
        return response()->json(["error" => "Invalid Login"], 400);
    }

    public function authenticateSocial(Request $request)
    {
        $aRequest   = $request->all();
        $email      = $aRequest['email'];

        $usersGet   = DB::table('users')->where('email', '=', $email)->get();

        if (isset($usersGet[0])) {
            $users      = User::where('email', '=', $email)->firstOrFail();

            $token      = $users->createToken('Token Name')->accessToken;

            return response()->json(["token" => $token, 'user' => $users]);
        } else {
            return response()->json(["error" => "Email not found"], 400);
        }

    }
}
