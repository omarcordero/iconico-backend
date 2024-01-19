<?php

namespace App\Listeners;

use App\Events\Ordered;
use App\Events\PostOrder;
use App\Helpers\PushNotificationHelper;
use App\Mail\OrderPlaced;
use App\Mail\StoreOrderPlaced;
use App\Models\Auth\User\User;
use App\Models\DeliveryProfile;
use App\Models\Earning;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Setting;
use App\Notifications\Admin\NewOrder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PostOrderListener
{
    private $order;
    private $event;

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
     * @param  Ordered $event
     * @return void
     */
    public function handle(PostOrder $event)
    {

        $this->event = $event;
        $this->order = $event->order;

        try {
            $this->_sendEmail();
        } catch (\Exception $ex) {
            Log::error('Unable to send email', [$ex->getMessage(), $ex->getTraceAsString()]);
        }

        // send push notications
        foreach ($event->pushNotifications as $notification) {
            try {
                $notification->send();
            } catch (\Exception $ex) {
                Log::error('Unable to send push notification', [$ex->getMessage(), $ex->getTraceAsString()]);
            }
        }

        try {
            $this->_notifyAdmin();
        } catch (\Exception $ex) {
            Log::error('Unable to notify admin', [$ex->getMessage(), $ex->getTraceAsString()]);
        }
    }

    private function _sendEmail()
    {
        if ($this->order->status == 'new') {
            if ($this->order->user->email) {
                Mail::to($this->order->user)->send(new OrderPlaced($this->order));

                // send to store as well
                Mail::to($this->order->store->user)->send(new StoreOrderPlaced($this->order));
            }
        }

        if ($this->order->status == 'complete') {
            if ($this->order->user->email) {
                Mail::to($this->order->user)->send(new OrderPlaced($this->order));
            }
        }
    }

    private function _notifyAdmin()
    {
        Log::error('Exception occurred', ['Notifying admin']);
        if ($this->order->status == 'new') {
            $user = User::whereHas('roles', function ($query) {
                $query->where('name', 'administrator');
            })->firstOrFail();
            $user->notify(new NewOrder($this->order));
        }
    }
}
