<?php

/**
 * Global helpers file with misc functions.
 */

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

if (!function_exists('gravatar')) {
    /**
     * Access the gravatar helper.
     *
     * @return \Creativeorange\Gravatar\Gravatar|\Illuminate\Foundation\Application|mixed
     */
    function gravatar()
    {
        return app('gravatar');
    }
}

if (!function_exists('to_js')) {
    /**
     * Access the javascript helper.
     */
    function to_js($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('tojs');
        }

        if (is_array($key)) {
            return app('tojs')->put($key);
        }

        return app('tojs')->get($key, $default);
    }
}

if (!function_exists('meta')) {
    /**
     * Access the meta helper.
     */
    function meta()
    {
        return app('meta');
    }
}

if (!function_exists('meta_tag')) {
    /**
     * Access the meta tags helper.
     */
    function meta_tag($name = null, $content = null, $attributes = [])
    {
        return app('meta')->tag($name, $content, $attributes);
    }
}

if (!function_exists('meta_property')) {
    /**
     * Access the meta tags helper.
     */
    function meta_property($name = null, $content = null, $attributes = [])
    {
        return app('meta')->property($name, $content, $attributes);
    }
}

if (!function_exists('protection_context')) {
    /**
     * @return \NetLicensing\Context
     */
    function protection_context()
    {
        return app('netlicensing')->context();
    }
}

if (!function_exists('protection_context_basic_auth')) {
    /**
     * @return \NetLicensing\Context
     */
    function protection_context_basic_auth()
    {
        return app('netlicensing')->context(\NetLicensing\Context::BASIC_AUTHENTICATION);
    }
}

if (!function_exists('protection_context_api_key')) {
    /**
     * @return \NetLicensing\Context
     */
    function protection_context_api_key()
    {
        return app('netlicensing')->context(\NetLicensing\Context::APIKEY_IDENTIFICATION);
    }
}

if (!function_exists('protection_shop_token')) {

    /**
     * @param \App\Models\Auth\User\User $user
     * @param null $successUrl
     * @param null $cancelUrl
     * @param null $successUrlTitle
     * @param null $cancelUrlTitle
     * @return \App\Models\Protection\ProtectionShopToken
     */
    function protection_shop_token(\App\Models\Auth\User\User $user, $successUrl = null, $cancelUrl = null, $successUrlTitle = null, $cancelUrlTitle = null)
    {
        return app('netlicensing')->createShopToken($user, $successUrl, $cancelUrl, $successUrlTitle, $cancelUrlTitle);
    }
}

if (!function_exists('protection_validate')) {

    /**
     * @param \App\Models\Auth\User\User $user
     * @return \App\Models\Protection\ProtectionValidation
     */
    function protection_validate(\App\Models\Auth\User\User $user)
    {
        return app('netlicensing')->validate($user);
    }
}

if (!function_exists('url_without_query_param')) {

    /**
     * @param $param
     * @return string
     */
    function url_without_query_param($param)
    {
        return url()->current().'?'.http_build_query(app('request')->except($param));
    }
}

if (!function_exists('settings_as_dictionary')) {

    /**
     * @return array
     */
    function settings_as_dictionary()
    {
        $settings = [];
        foreach (Setting::all() as $setting) {
            $settings[$setting->key] = $setting->value;
        }
        return $settings;
    }
}

if (!function_exists('admin_get_store')) {

    /**
     * @return mixed
     */
    function admin_get_store()
    {
        if(Auth::user()->hasRole('owner')) {
            return Auth::user()->store;
        }
        return false;
    }
}

if (!function_exists('admin_authorize_store')) {

    /**
     * @return array
     */
    function admin_authorize_store($storeId)
    {
        $currentStore = admin_get_store();
        if($currentStore && $currentStore->id != $storeId) {
            throw new \Symfony\Component\Finder\Exception\AccessDeniedException('Not Authorized');
        }
    }
}

if (!function_exists('attach_categories_to_store')) {

    /**
     * @return array
     */
    function sync_categories_to_store($storeId)
    {
        $store = \App\Models\Store::find($storeId);
        $categories = array();
        foreach (\App\Models\MenuItem::where('store_id', $storeId)->get() as $item) {
            $categories = array_merge($categories, $item->categories()->pluck('id')->all());
        }
        $categories = array_unique($categories);
        $store->categories()->detach();
        $store->categories()->sync($categories);
    }
}

if(!function_exists('generate_numeric_otp')) {

    function generate_numeric_otp($n) {

        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i=0; $i < $n; $i++) {
            $token .= $codeAlphabet[random_int(0, $max-1)];
        }

        return $token;
    }
}