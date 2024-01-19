<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/**
 * Auth routes
 */
Route::group(['namespace' => 'Auth'], function () {

    // Authentication Routes...
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout')->name('logout');

    // Registration Routes...
    if (config('auth.users.registration')) {
        Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('register', 'RegisterController@register');
    }

    // Password Reset Routes...
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset');

    // Confirmation Routes...
    if (config('auth.users.confirm_email')) {
        Route::get('confirm/{user_by_code}', 'ConfirmController@confirm')->name('confirm');
        Route::get('confirm/resend/{user_by_email}', 'ConfirmController@sendEmail')->name('confirm.send');
    }

    // Social Authentication Routes...
    Route::get('social/redirect/{provider}', 'SocialLoginController@redirect')->name('social.redirect');
    Route::get('social/login/{provider}', 'SocialLoginController@login')->name('social.login');
});

/**
 * Backend routes
 */
Route::group(['prefix' => 'backend', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'auth'], function () {

    // Dashboard
    Route::get('/', 'DashboardController@index')->name('dashboard');

    Route::group(['middleware' => 'admin'], function () {
        //Users
        Route::get('users', 'UserController@index')->name('users');
        Route::get('users/create', 'UserController@create')->name('users.create');
        Route::post('users', 'UserController@store')->name('users.store');
        Route::get('users/{user}', 'UserController@show')->name('users.show');
        Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
        Route::put('users/{user}', 'UserController@update')->name('users.update');
        Route::get('users/{user}/delete', 'UserController@destroy')->name('users.destroy');

        Route::get('permissions', 'PermissionController@index')->name('permissions');
        Route::get('permissions/{user}/repeat', 'PermissionController@repeat')->name('permissions.repeat');

        Route::get('dashboard/log-chart', 'DashboardController@getLogChartData')->name('dashboard.log.chart');
        Route::get('dashboard/registration-chart', 'DashboardController@getRegistrationChartData')->name('dashboard.registration.chart');

        Route::get('deliveryProfiles', 'DeliveryProfileController@index')->name('delivery_profiles');
        Route::get('deliveryProfiles/{deliveryProfile}', 'DeliveryProfileController@show')->name('delivery_profiles.show');
        Route::get('deliveryProfiles/{deliveryProfile}/edit', 'DeliveryProfileController@edit')->name('delivery_profiles.edit');
        Route::put('deliveryProfiles/{deliveryProfile}', 'DeliveryProfileController@update')->name('delivery_profiles.update');

        Route::get('supports', 'SupportController@index')->name('supports');
        Route::get('supports/{support}', 'SupportController@show')->name('supports.show');

        Route::get('categories', 'CategoryController@index')->name('categories');
        Route::get('categories/create', 'CategoryController@create')->name('categories.create');
        Route::post('categories', 'CategoryController@store')->name('categories.store');
        Route::get('categories/{category}/edit', 'CategoryController@edit')->name('categories.edit');
        Route::put('categories/{category}', 'CategoryController@update')->name('categories.update');

        Route::get('coupons', 'CouponController@index')->name('coupons');
        Route::get('coupons/create', 'CouponController@create')->name('coupons.create');
        Route::post('coupons', 'CouponController@store')->name('coupons.store');
        Route::get('coupons/{coupon}/edit', 'CouponController@edit')->name('coupons.edit');
        Route::put('coupons/{coupon}', 'CouponController@update')->name('coupons.update');
        Route::delete('coupons/{coupon}', 'CouponController@destroy')->name('coupons.destroy');

        Route::get('menuitems', 'MenuItemController@index')->name('menuitems');
        Route::get('menuitems/create', 'MenuItemController@create')->name('menuitems.create');
        Route::post('menuitems', 'MenuItemController@store')->name('menuitems.store');
        Route::get('menuitems/{menuitem}', 'MenuItemController@show')->name('menuitems.show');
        Route::get('menuitems/{menuitem}/edit', 'MenuItemController@edit')->name('menuitems.edit');
        Route::put('menuitems/{menuitem}', 'MenuItemController@update')->name('menuitems.update');
        Route::put('menuitems/{menuitem}/quick-approve', 'MenuItemController@quickApprove')->name('menuitems.quickApprove');

        #Route::get('settings', 'SettingController@index')->name('settings');
        Route::get('settings/{setting}/edit', 'SettingController@edit')->name('settings.edit');
        Route::get('settings', 'SettingController@settings')->name('settings');
        Route::put('settings/update-env', 'SettingController@updateEnv')->name('settings.updateEnv');
        Route::put('settings/update-setting', 'SettingController@updateSetting')->name('settings.updateSetting');

        Route::post('json/notifications/read', 'Json\NotificationController@readNotifications')->name('json.notifications.read');
        Route::post('json/notifications/delete', 'Json\NotificationController@deleteNotifications')->name('json.notifications.delete');
        Route::post('json/dashboard/orders-chart-data', 'Json\DashboardController@ordersChartData')->name('json.dashboard.ordersChartData');
        Route::post('json/dashboard/users-chart-data', 'Json\DashboardController@usersChartData')->name('json.dashboard.usersChartData');
        Route::put('json/orders/{order}', 'Json\OrderController@update')->name('json.orders.update');
    });

    Route::group(['middleware' => 'store'], function () {
        Route::get('stores', 'StoreController@index')->name('stores');
        Route::get('stores/{store}', 'StoreController@show')->name('stores.show');
        Route::get('stores/{store}/edit', 'StoreController@edit')->name('stores.edit');
        Route::put('stores/{store}', 'StoreController@update')->name('stores.update');

        Route::get('menuitems', 'MenuItemController@index')->name('menuitems');
        Route::get('menuitems/create', 'MenuItemController@create')->name('menuitems.create');
        Route::post('menuitems', 'MenuItemController@store')->name('menuitems.store');
        Route::get('menuitems/{menuitem}', 'MenuItemController@show')->name('menuitems.show');
        Route::get('menuitems/{menuitem}/edit', 'MenuItemController@edit')->name('menuitems.edit');
        Route::put('menuitems/{menuitem}', 'MenuItemController@update')->name('menuitems.update');

        Route::get('ratings', 'RatingController@index')->name('ratings');
        Route::get('ratings/{id}', 'RatingController@destroy')->name('ratings.destroy');

        Route::get('orders', 'OrderController@index')->name('orders');
        Route::get('orders/{order}', 'OrderController@show')->name('orders.show');
        Route::get('orders/{order}/edit', 'OrderController@edit')->name('orders.edit');
        Route::put('orders/{order}', 'OrderController@update')->name('orders.update');

        Route::get('earnings', 'EarningController@index')->name('earnings');
        Route::get('earnings/{earning}', 'EarningController@show')->name('earnings.show');

        Route::get('ratings', 'RatingController@index')->name('ratings');
        Route::get('ratings/{id}', 'RatingController@destroy')->name('ratings.destroy');

        Route::get('bankdetails', 'BankDetailController@index')->name('bankdetails');
        Route::get('bankdetails/{bankdetail}', 'BankDetailController@show')->name('bankdetails.show');
        Route::get('bankdetails/{bankdetail}/edit', 'BankDetailController@edit')->name('bankdetails.edit');
        Route::put('bankdetails/{bankdetail}', 'BankDetailController@update')->name('bankdetails.update');
    });
});
Route::get('/home', function (){
    echo 'Hello world';
});
//Route::get('/', 'HomeController@index');