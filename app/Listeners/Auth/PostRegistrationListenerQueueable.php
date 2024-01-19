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

class PostRegistrationListenerQueueable extends PostRegistrationListener implements ShouldQueue
{
    //
}
