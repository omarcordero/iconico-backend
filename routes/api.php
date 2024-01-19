<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->name('api.')->group(function () {

    // Backend Api
    Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
        // auth related
        Route::namespace('Auth')->group(function () {
            Route::post('/login', 'LoginController@authenticate');
            Route::post('/validateEmail', 'LoginController@validateEmail');
        });
        
        
        // auth api
        Route::middleware('auth:api')->group(function () {

            // category
            Route::get('/categories/all', 'CategoryController@allCategories');
            Route::apiResource('categories', 'CategoryController');

            Route::apiResource('coupons', 'CouponController');

            // user
            Route::get('/users/profile/getValidate', 'UserController@getValidateUserProfile');
            Route::get('/users/roles', 'UserController@roles');
            Route::apiResource('users', 'UserController');

            // store
            Route::apiResource('stores', 'StoreController');

            Route::post('/stores/coordenates', 'StoreController@saveCoordenates');
            Route::get('/stores/coordenates/get', 'StoreController@getCoordenates');
            Route::delete('/stores/coordenates/del', 'StoreController@deleteCoordenates');
            Route::post('/stores/update/methodPayment', 'StoreController@updateMethodPayment');

            // menuitems
            Route::apiResource('menuitems', 'MenuItemController');

            // deliveryprofiles
            Route::get('/deliveryprofiles/get', 'DeliveryProfileController@getDelivery');
            Route::post('/deliveryprofiles/assigned', 'DeliveryProfileController@assignedDelivery');
            Route::get('/deliveryprofiles/getUser', 'DeliveryProfileController@deliveryGet');
            Route::get('/deliveryprofiles/{deliveryprofile}/favourite', 'FavouriteDriverController@store');
            Route::apiResource('deliveryprofiles', 'DeliveryProfileController')->except('create');

            // order
            Route::get('/orders/byUser', 'OrderController@getCountOrderByUser');
            Route::apiResource('orders', 'OrderController')->except('create');
            Route::post('/orders/update-observation', 'OrderController@updateObservation');
            Route::post('/orders/update-state', 'OrderController@updateState');

            // support
            Route::get('/supports', 'SupportController@index');

            // transactions
            Route::get('/transactions', 'TransactionController@index');
            Route::get('/transactions/earning-analytics', 'TransactionController@earningAnalytics');

            // plan
            Route::apiResource('plans', 'PlanController')->except('create')->except('delete');

            // settings
            Route::get('/settings', 'SettingController@index');
            Route::post('/settings', 'SettingController@update');
            Route::post('/settings/image', 'SettingController@updateImage');
            Route::post('/settings/image/section/{id}', 'SettingController@updateImageSection');
            Route::delete('/settings/image/section/{id}', 'SettingController@deleteImageSection');
            Route::get('/settings/image/section', 'SettingController@getAllSettingsImages');
            Route::get('/settings/image/getAllNotNull', 'SettingController@getAllImagenNotNull');
            Route::get('/settings/image/getAll', 'SettingController@getAllImagen');
            Route::get('/settings/env', 'SettingController@envList');
            Route::post('/settings/env', 'SettingController@updateEnv');

            // dashboard
            Route::get('/dashboard/order-analytics', 'DashboardController@orderAnalytics');
            Route::get('/dashboard/user-analytics', 'DashboardController@userAnalytics');
            Route::get('/dashboard/user-statitics', 'DashboardController@userStatitics');
            Route::get('/dashboard/active-orders', 'DashboardController@activeOrders');
            Route::get('/dashboard/active-delivery', 'DashboardController@activeDelivery');
            Route::get('/dashboard/daily-active-analytics', 'DashboardController@dailyUserAnalytics');
        });
        
    });
    
    // Api Icónico do not authorization
    // ====================================================================================
    Route::namespace('ICONICO')->group(function () {

        //Plates
        Route::get('/plate/getAll','PlateController@getAll')->name('plate.getAll');
        Route::get('/plate/get','PlateController@get')->name('plate.get');
        Route::get('/admin/plate/getBanner','PlateController@getBanner')->name('plate.getBanner');
        Route::get('/admin/plate/getOneBanner','PlateController@getOneBanner')->name('plate.getOneBanner');
        Route::get('/admin/plate/getMoreOrder','PlateController@getMoreOrder')->name('plate.getMoreOrder');
        Route::get('/settings/image/getAllNotNull', 'SettingsController@getAllImagenNotNull');
        
        //Categories
        Route::get('/category/getAll','CategoryController@getAll')->name('category.getAll');
        Route::get('/category/getCount','CategoryController@getCategoryCountHome')->name('category.getCategoryCountHome');

        //Complements
        Route::post('/complements/group/create', 'ComplementsController@newGroup')->name('group.create');
        Route::post('/complements/group/update', 'ComplementsController@updateGroup')->name('group.update');
        Route::get('/complements/group/getAll', 'ComplementsController@getGroup')->name('group.get');
        Route::get('/complements/group/get', 'ComplementsController@getByGroup')->name('group.by');
        Route::get('/complements/group/getComplement', 'ComplementsController@getGroupComplement')->name('group.complement');
        Route::delete('/complements/group/delete', 'ComplementsController@deleteGroup')->name('group.delete');

        Route::post('/complements/create', 'ComplementsController@newComplements')->name('complement.create');
        Route::post('/complements/update', 'ComplementsController@updateComplement')->name('complement.update');
        Route::get('/complements/getAll', 'ComplementsController@getComplements')->name('complement.getAll');
        Route::get('/complements/get', 'ComplementsController@getByComplements')->name('complement.get');
        Route::delete('/complements/delete', 'ComplementsController@deleteComplement')->name('complement.delete');

        //Sucursal
        Route::get('/sucursal/getAll','SucursalController@getAll')->name('sucursal.getAll');
        Route::get('/sucursal/coordenates/get', 'SucursalController@getCoordenates');
        Route::get('/sucursal/validate/openning', 'SucursalController@validateSucursalOpenning');
        Route::post('/sucursal/hour/open', 'SucursalController@validateHourStore');
		Route::post('/sucursal/validatePriceStore', 'SucursalController@validatePriceStore')->name('sucursal.validatePriceStore');

        //menuComplements
        Route::post('/complements/addMenu', 'ComplementsController@addMenuComplement')->name('complement.addMenu');
        Route::post('/complements/menu-complement/getAll', 'ComplementsController@getMenuComplement')->name('complement.getMenu');

        //Coupons
        Route::post('/coupon/getCode','CouponController@getCupon')->name('coupon.getAll');

        //Settings
        Route::get('/settings/getAll', 'SettingsController@getAll');

        Route::post('/reset-password', 'UserController@resetPassword');
        Route::post('/change-reset-password', 'UserController@changeUpdatePasswordEmail');

        Route::post('/update-phone', 'UserController@updatePhone');
    });
    // ====================================================================================

    Route::namespace('Auth')->group(function () {
        Route::post('/login', 'LoginController@authenticate')->name('login');
        Route::post('/loginSocial', 'LoginController@authenticateSocial')->name('loginSocial');
        Route::post('/registerUser', 'RegisterController@registerUser')->name('registerUser');
        Route::post('/register', 'RegisterController@register')->name('register');
        //Route::post('/register', function(){ echo 'Hola'; })->name('register');
        Route::post('/verify-mobile', 'RegisterController@verifyMobile')->name('verifyMobile');
        Route::post('/forgot-password', 'RegisterController@sendResetLinkEmail')->name('forgotPassword');
        Route::post('social/login', 'SocialLoginController@authenticate')->name('social.authenticate');
    });

    Route::post('/support', 'SupportController@store')->name('support.store');

    // system wide settings
    Route::get('/settings', 'SettingController@index')->name('setting.index');


    Route::namespace('Customer')->prefix('customer')->name('customer.')->group(function () {

        Route::get('/category', 'CategoryController@index')->name('category.index');

        // list of store
        Route::get('/store', 'StoreController@index')->name('store.index');
        Route::get('/menu-items', 'StoreController@menuItemSearch')->name('store.menuItemSearch');

        // show store by id
        Route::get('/store/{store}', 'StoreController@show')->name('store.show');

        // get a list of ratings
        Route::get('/rating/{store}', 'RatingController@index')->name('rating.index');

        // Payment gateway - Paystack
        Route::get('/order/{order}/payment/paystack', 'OrderController@makePaystackPayment')->name('order.makePaystackPayment');
        Route::get('/order/{order}/payment/paystack/callback', 'OrderController@paystackCallback')->name('order.paystackCallback');
        Route::get('/order/{order}/payment/paystack/status', 'OrderController@paystackStatus')->name('order.paystackStatus');
    });

    Route::middleware('auth:api')->group(function () {
        
        //ICÓNICO
        Route::namespace('ICONICO')->group(function () {
            //Plates
            Route::post('/plate/new','PlateController@newPlate')->name('plate.new');
            Route::post('/admin/plate/addBanner','PlateController@addBanner')->name('plate.addBanner');
            Route::post('/admin/plate/addMordeOrder','PlateController@addMordeOrder')->name('plate.addMordeOrder');
            
            //Complements
            Route::post('/complement/new','ComplementsController@newComplements')->name('complements.new');
            
            //Coupon
            Route::post('/coupon/new','CouponController@newCupon')->name('coupon.new');
            
            //Orders
            Route::post('/orders/create', 'OrdersController@newOrder')->name('order.new');
            Route::post('/orders/emailTest', 'OrdersController@emailTesting')->name('order.emailTesting');
            Route::post('/orders/uploads', 'OrdersController@addFile')->name('order.addFile');
            Route::post('/orders/get/all', 'OrdersController@getOrders')->name('order.getOrders');
            Route::post('/admin/orders/getMenu', 'OrdersController@getOrderMenu')->name('order.getMenu');

            Route::get('/admin/delivery/orders', 'OrdersController@getOrdersDelivery')->name('order.getOrdersDelivery');

            //Users
            Route::post('/profile/update/info', 'UserController@updateProfile');
            Route::post('/profile/update/password', 'UserController@changePassword');
            Route::get('/profile/get', 'UserController@getProfile');

            //Board
            Route::get('/board/get','BoardController@getBoard');
        });
    
        Route::get('/user', 'UserController@show')->name('user.show');
        Route::put('/user', 'UserController@update')->name('user.update');

        // user earnings
        Route::get('/earnings', 'EarningController@index')->name('earning.index');
        Route::get('/earnings/{earning}', 'EarningController@show')->name('earning.show');

        /* Store related APIs */
        // get store of current logged in user
        Route::get('/store', 'StoreController@show')->name('store.show');
        // update store
        Route::put('/store/update', 'StoreController@update')->name('store.update');

        Route::get('/menuitem', 'MenuItemController@index')->name('menuitem.index');
        Route::post('/menuitem', 'MenuItemController@store')->name('menuitem.store');
        Route::get('/menuitem/{menuItem}', 'MenuItemController@show')->name('menuitem.show');
        Route::post('/menuitem/{menuItem}', 'MenuItemController@update')->name('menuitem.update');
        Route::post('/menuitem/{menuItem}/update-status', 'MenuItemController@updateStatus')->name('menuitem.updateStatus');
        Route::delete('/menuitem/{menuItem}', 'MenuItemController@destroy')->name('menuitem.destroy');

        Route::get('/bank-detail', 'BankDetailController@show')->name('bankdetail.show');
        Route::post('/bank-detail', 'BankDetailController@store')->name('bankdetail.store');

        // wallet and payment
        Route::post('/wallet/recharge/stripe', 'WalletController@rechargeWithStripe')->name('wallet.rechargeWithStripe');
        Route::post('/wallet/withdraw', 'WalletController@withdraw')->name('wallet.withdraw');
        Route::get('/wallet/check-balance', 'WalletController@checkBalance')->name('wallet.checkBalance');
        Route::get('/wallet/transactions', 'WalletController@transactions')->name('wallet.transactions');

        Route::get('/category', 'CategoryController@index')->name('category.index');

        /* order related */
        // get a list of orders of a logged in user's store
        Route::get('/order', 'OrderController@index')->name('order.index');
        Route::get('/order/{order}', 'OrderController@show')->name('order.show');
        Route::put('/order/{order}', 'OrderController@update')->name('order.update');

        // get a list of reviews of a logged in user's store
        Route::get('/rating', 'RatingController@index')->name('rating.index');

        // store plan details
        Route::get('/plans', 'PlanController@plans')->name('plans.index');
        Route::post('/plans/{plan}/payment/stripe', 'PlanController@makeStripePayment')->name('plans.makeStripePayment');
        Route::post('/plans/{plan}/payment/inapp', 'PlanController@inAppPayment')->name('plans.inAppPayment');
        Route::get('/plan-details', 'PlanController@planDetails')->name('plans.planDetails');

        /* Customer related APIs */
        Route::namespace('Customer')->prefix('customer')->name('customer.')->group(function () {
            // activity log
            Route::post('/activity-log', 'ActiveLogController@store')->name('activitylog.store');

            // Get a list of favourite
            Route::get('/favourite', 'FavouriteController@index')->name('favourite.index');

            // mark store as favourite
            Route::post('/favourite/{store}', 'FavouriteController@store')->name('favourite.store');

            // get a rating of a stores rated by current user
            Route::get('/ratings', 'RatingController@show')->name('rating.show');

            // rate a store
            Route::post('/rating/{store}', 'RatingController@store')->name('rating.store')->where('store', '[0-9]+');

            // check coupon validity
            Route::get('/coupons', 'CouponController@index')->name('coupon.index');
            Route::get('/coupon-validity', 'CouponController@couponValidity')->name('coupon.validity');

            /* address related */
            Route::get('/address', 'AddressController@index')->name('address.index');
            Route::post('/address', 'AddressController@store')->name('address.store');
            Route::get('/address/{address}', 'AddressController@show')->name('address.show');
            Route::put('/address/{address}/update', 'AddressController@update')->name('address.update');

            /* orders related */
            // get a list of orders of a current user
            Route::get('/order', 'OrderController@index')->name('order.index');
            Route::post('/order', 'OrderController@store')->name('order.store');
            Route::post('/order/calculate-delivery-fee', 'OrderController@calculateDeliveryFee')->name('order.calculateDeliveryFee');
            Route::post('/order/{order}', 'OrderController@update')->name('order.update');
            Route::post('/order/{order}/payment/stripe', 'OrderController@makeStripePayment')->name('order.makeStripePayment');
            Route::post('/order/{order}/payment/payu', 'OrderController@makePayUPayment')->name('order.makePayUPayment');
            Route::get('/payment-methods', 'PaymentMethodController@index')->name('paymentmethod.index');
        });

        /* Customer related APIs */
        Route::namespace('Delivery')->prefix('delivery')->name('delivery.')->group(function () {
            Route::get('/profile', 'DeliveryProfileController@show')->name('profile.show');
            // update delivery profile
            Route::put('/profile/update', 'DeliveryProfileController@update')->name('profile.update');

            Route::get('/order', 'OrderController@showAvailableOrder')->name('order.showAvailableOrder');
            Route::put('/update-delivery-status/{order}', 'OrderController@updateDeliveryStatus')->name('order.updateDeliveryStatus');
        });
    });
    
});
