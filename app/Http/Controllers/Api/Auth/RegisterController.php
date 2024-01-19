<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\Auth\Registered;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiRegisterRequest;
use App\Models\Auth\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class RegisterController extends  Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles user registration via REST API
    |
    */

    /**
     * Handle a registration request for the application.
     *
     * @param  ApiRegisterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $data = $request->all();
        $firstname = (isset($data['firstname'])) ? $data['firstname'] : "";
        $lastname = (isset($data['lastname'])) ? $data['lastname'] : "";
        $type = (isset($data['type_doc'])) ? $data['type_doc'] : "";
        $document = (isset($data['document'])) ? $data['document'] : "";
        $email = (isset($data['email'])) ? $data['email'] : "";
        $password = (isset($data['password'])) ? $data['password'] : "";
        $role = (isset($data['role'])) ? $data['role'] : "";
        if( $firstname === "" || $lastname === "" || $type === "" || $document === "" || $email === "" || $password === "" || $role === "" ){
            return response()->json([
                'status' => false,
                'message' => 'Fields required'
            ]);
        }else{
            $bcrypt = bcrypt($password);
            $name = $firstname . ' ' . $lastname;
            $user = new User();
            $users = DB::select("CALL sp_register(?,?,?,?,?,?)", [$name, $type, $document, $email, $bcrypt, $role]);
            $token = $user->createToken('Default')->accessToken;
            return response()->json(["token" => $token, "user" => User::find($users[0]->id)]);
        }
    }

    /**
     * Verifies user's mobile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyMobile(Request $request)
    {
        Validator::make($request->all(), [
            'mobile_number' => 'required|string|exists:users,mobile_number'
        ])->validate();

        $user = User::where('mobile_number', $request->mobile_number)->first();
        $user->mobile_verified = 1;
        $user->save();

        $token = $user->createToken('Default')->accessToken;

        return response()->json(["token" => $token, "user" => $user]);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        Validator::make($request->all(), [
            'email' => 'required|email'
        ])->validate();

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? response()->json(["message" => "Email Sent"])
            : response()->json(["message" => "Email Not Sent"], 400);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Auth\User\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'mobile_number' => $data['mobile_number'],
        ]);
    }

    private function generateOtpCode($user) {
        while(1) {
            $otpCode = rand(1001,9999);
            if(!User::where('otp_code', $otpCode)->exists()) {
                break;
            }
        }
        return $otpCode;
    }

    public function registerUser(Request $request) {
        $aRequest   = $request->all();
        $name       = $aRequest['name'];
        $phone      = isset($aRequest['mobile_number']) ? $aRequest['mobile_number'] : NULL;
        $email      = $aRequest['email'];
        $password   = isset($aRequest['password']) ? bcrypt($aRequest['password']) : NULL;
        $type       = $aRequest['type'];

        if ($type === "GOOGLE" || $type === "FACEBOOK") {
            $emailUser  = DB::select('SELECT * FROM users WHERE email = ?', [$email]);

            if (!isset($emailUser[0])) {
                
                $users      = DB::select('CALL SP_REGISTER(?,NULL,?,NULL,?)', [$name, $email, $type]);

                $userId     = $users[0]->userId;

                DB::table('users_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => '5'
                ]);

                return array('status' => true, 'message' => 'Usuario creado de manera correcta', 'type' => $type, 'code' => '200');
            } else {
                return array('status' => true, 'message' => 'Esta cuenta ya está en uso', 'code' => '400');
            }
        } else {
            $emailUser  = DB::select('SELECT * FROM users WHERE email = ?', [$email]);

            if (!isset($emailUser[0])) {
                $phoneUser  = DB::select('SELECT * FROM users WHERE mobile_number = ?', [$phone]);
                if (!isset($phoneUser[0])) {
                    $users      = DB::select('CALL SP_REGISTER(?,?,?,?,NULL)', [$name, $phone, $email, $password]);

                    $userId     = $users[0]->userId;

                    DB::table('users_roles')->insert([
                        'user_id' => $userId,
                        'role_id' => '5'
                    ]);

                    return array('status' => true, 'message' => 'Usuario creado de manera correcta', 'code' => '200');
                } else {
                    return array('status' => true, 'message' => 'El teléfono se encuentra en uso', 'code' => '400');
                }
            } else {
                return array('status' => true, 'message' => 'Email existe', 'code' => '400');
            }
        }
    }
}
