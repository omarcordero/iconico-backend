<?php

namespace App\Providers;

use App\Models\Auth\User\User;
use App\Policies\Backend\BackendPolicy;
use App\Policies\Models\User\UserPolicy;
use App\Policies\StoreBackend\StoreBackendPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        /**
         * Models Policies
         */
        User::class => UserPolicy::class,
        /**
         * Without models policies
         */
        'backend' => BackendPolicy::class,
        /**
         * Backend access for store owners
         */
        'storeBackend' => StoreBackendPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensCan([
            'manage-as-admin' => 'Manage Backend as Admin'
        ]);
    }
}
