<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Setting;
use Brotzka\DotenvEditor\Exceptions\DotEnvException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brotzka\DotenvEditor\DotenvEditor as Env;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Setting::all());
    }

    public function update(Request $request)
    {
        $inputs = $request->all();

        /*if (isset($inputs['file_coupon'])) {
            if($request->hasFile('file_coupon')){
                $imageURL = $request->file('file_coupon')->store('uploads', 'public');
                $inputs['file_coupon'] = $imageURL;
            }
        }*/

        foreach ($inputs as $key => $value) {
            try {
                $setting = Setting::where('key', $key)->firstOrFail();
                $setting->value = $value;
                $setting->save();
            } catch (ModelNotFoundException $ex) {
                //
            }
        }

        return response()->json([]);
    }

    public function updateImage(Request $request) {
        if($request->hasFile('file_coupon')){
            $imageURL = $request->file('file_coupon')->store('uploads', 'public');
            $settings = DB::table('settings')->where('id', '=', 18)->update(['value' => $imageURL]);
        }

    }

    public function updateImageSection(Request $request, $id) {
        if($request->hasFile('file_section')){
            $imageURL = $request->file('file_section')->store('uploads', 'public');
            $settings = DB::table('settings_images')->where('id', '=', $id)->update(['upload' => $imageURL]);

            return array(
                'status' => true,
                'message' => 'Upload successfully',
                'data' => []
            );
        }
    }

    public function getAllSettingsImages() {
        $settings = DB::table('settings_images')->where('upload', '=', null)->get();

        return array(
                'status' => true,
                'message' => 'Query successfully',
                'data' => $settings
            );
    }

    public function getAllImagenNotNull() {
        $settings = DB::table('settings_images')->where('upload', '!=', null)->get();

        return array(
                'status' => true,
                'message' => 'Query successfully',
                'data' => $settings
            );
    }

    public function getAllImagen() {
        $settings = DB::table('settings_images')->get();

        return array(
                'status' => true,
                'message' => 'Query successfully',
                'data' => $settings
            );
    }

    public function deleteImageSection(Request $request, $id) {
        DB::table('settings_images')->where('id', '=', $id)->update(['upload' => null]);

        return array(
                'status' => true,
                'message' => 'Deleted images successfully',
                'data' => []
            );
    }

    public function envList(Request $request)
    {
        $env = new Env();
        return response()->json($env->getContent());
    }

    /**
     * Update env variables.
     *
     * @param Request $request
     * @return mixed
     */
    public function updateEnv(Request $request)
    {
        $env = new Env();
        try {
            $env->changeEnv([
                'MAIL_DRIVER'   => $request->MAIL_DRIVER,
                'MAIL_HOST'   => $request->MAIL_HOST,
                'MAIL_PORT'   => $request->MAIL_PORT,
                'MAIL_USERNAME'   => $request->MAIL_USERNAME,
                'MAIL_PASSWORD'   => $request->MAIL_PASSWORD,
                'MAIL_FROM_ADDRESS'   => $request->MAIL_FROM_ADDRESS,
                'MAIL_FROM_NAME'   => $request->MAIL_FROM_NAME,
                'MAILGUN_DOMAIN'   => $request->MAILGUN_DOMAIN,
                'MAILGUN_SECRET'   => $request->MAILGUN_SECRET,
                'FCM_SERVER_KEY'   => $request->FCM_SERVER_KEY,
                'FCM_SENDER_ID'   => $request->FCM_SENDER_ID,
                'ONESIGNAL_APP_ID'   => $request->ONESIGNAL_APP_ID,
                'ONESIGNAL_REST_API'   => $request->ONESIGNAL_REST_API,
                'APP_TIMEZONE'   => $request->APP_TIMEZONE,
            ]);
        } catch (DotEnvException $e) {
        }

        return response()->json([]);
    }

}
