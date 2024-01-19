<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PostRegistration;
use App\Events\Auth\Registered;
use App\Mail\WelcomeUser;
use App\Models\Auth\User\User;
use App\Models\DeliveryProfile;
use App\Models\Store;
use App\Notifications\Admin\NewUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PostRegistrationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered $event
     * @return void
     */
    public function handle(PostRegistration $event)
    {
        try {
            if(config('admin-notification.enabled')) {
                try {
                    $user = User::whereHas('roles', function ($query) {
                        $query->where('name', 'administrator');
                    })->firstOrFail();
                    $user->notify(new NewUser($event->user, $event->role));
                } catch (ModelNotFoundException $ex) {
                    //
                }
            }

            Mail::to($event->user)->send(new WelcomeUser($event->user));
        } catch (\Exception $ex) {
            Log::error('Exception occurred', [$ex->getMessage(), $ex->getTraceAsString()]);
        }
    }
}
