<?php

namespace App\Providers;

use App\Events\Auth\PostRegistration;
use App\Events\Auth\Registered;
use App\Events\Auth\SocialLogin;
use App\Events\Ordered;
use App\Events\PostOrder;
use App\Listeners\Auth\LoginListener;
use App\Listeners\Auth\LogoutListener;
use App\Listeners\Auth\PostRegistrationListener;
use App\Listeners\Auth\PostRegistrationListenerQueueable;
use App\Listeners\Auth\RegisteredListener;
use App\Listeners\Auth\SocialLoginListener;
use App\Listeners\OrderListener;
use App\Listeners\PostOrderListener;
use App\Listeners\PostOrderListenerQueueable;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Login::class => [LoginListener::class],
        Logout::class => [LogoutListener::class],
        Registered::class => [RegisteredListener::class],
        SocialLogin::class => [SocialLoginListener::class],
        Ordered::class => [OrderListener::class],
    ];

    /**
     * EventServiceProvider constructor.
     * @param array $listen
     */
    public function __construct()
    {
        if(config('queue.use_queue_for_events')) {
            $this->listen[PostRegistration::class] = [PostRegistrationListenerQueueable::class];
            $this->listen[PostOrder::class] = [PostOrderListenerQueueable::class];
        } else {
            $this->listen[PostRegistration::class] = [PostRegistrationListener::class];
            $this->listen[PostOrder::class] = [PostOrderListener::class];
        }
    }


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
