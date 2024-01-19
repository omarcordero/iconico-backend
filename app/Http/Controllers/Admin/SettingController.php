<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SettingUpdateRequest;
use App\Models\Setting;
use Brotzka\DotenvEditor\Exceptions\DotEnvException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;
use Validator;
use Brotzka\DotenvEditor\DotenvEditor as Env;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.settings.index', ['settings' => Setting::paginate()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Setting $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        return view('admin.settings.edit', ['setting' => $setting]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        $settings = Setting::all();
        $env = new Env();
        return view('admin.settings.settings', ['settings' => $settings, 'env' => $env->getContent()]);
    }

    /**
     * Update env variables.
     *
     * @param Request $request
     * @return mixed
     */
    public function updateSetting(Request $request)
    {
        $inputs = $request->all();

        foreach ($inputs as $key => $value) {
            try {
                $setting = Setting::where('key', $key)->firstOrFail();
                $setting->value = $value;
                $setting->save();
            } catch (ModelNotFoundException $ex) {
                //
            }
        }

        return redirect()->intended(route('admin.settings'));
    }

    /**
     * Update env variables.
     *
     * @param Request $request
     * @return mixed
     */
    public function updateEnv(Request $request)
    {
        $inputs = $request->except(['_method', '_token']);

        if($request->APP_LOGO) {
            $path = $request->file('APP_LOGO')->store('uploads');

//            $small_path = $path . "?s=small";
//            Storage::copy($path, $small_path);
//            $small_logo = Image::make(storage_path('app/public/' . $small_path))->resize(null, 50, function ($constraint) {
//                $constraint->aspectRatio();
//            })->encode();
//            Storage::put($small_path, $small_logo);

            $logo_url = Storage::url($path);
            $inputs['APP_LOGO'] = $logo_url;
        }

        $env = new Env();
        try {
            $env->addData($inputs);
        } catch (DotEnvException $e) {
        }

        return redirect()->intended(route('admin.settings'));
    }
}
