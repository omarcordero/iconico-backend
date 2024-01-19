<?php

namespace App\Listeners;

use App\Events\Ordered;
use App\Events\PostOrder;
use App\Helpers\PushNotificationHelper;
use App\Mail\OrderPlaced;
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

class PostOrderListenerQueueable extends PostOrderListener implements ShouldQueue
{
}
