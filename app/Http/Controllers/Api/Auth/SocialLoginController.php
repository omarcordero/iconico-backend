<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\Auth\SocialLogin;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\SocialAccount;
use App\Models\Auth\User\User;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Auth\RedirectsUsers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SocialLoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $publicKeyURL = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';
        $kids = json_decode(file_get_contents($publicKeyURL), true);

        if($request->token) {
            try {
                $decoded = JWT::decode($request->token, $kids, array('RS256'));

                if($decoded->iss !== env('FIREBASE_ISS')) {
                    throw new \Exception;
                }

                $user = User::where('email', $decoded->email)->first();

                if(!$user) {
                    return response()->json(["message" => 'User does not exist'], 404);
                }

                $token = $user->createToken('Default')->accessToken;
                return response()->json(["token" => $token, "user" => $user]);
            } catch(\Exception $ex) {
                throw new BadRequestHttpException($ex->getMessage());
            }
        }
        throw new BadRequestHttpException('token_not_provided');
    }
}
