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
use App\Models\PushNotification;
use App\Models\Setting;
use App\Models\Transaction;
use App\Notifications\Admin\NewOrder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderListener
{
    private $order;
    private $event;
    private $pushNotifications;

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
    public function handle(Ordered $event)
    {
        try {
            $this->event = $event;
            $this->order = $event->order;
            $this->pushNotifications = [];

            $this->_statusUpdate();

            $this->_deliveryStatusUpdate();

            event(new PostOrder($event->order, $this->pushNotifications));
        } catch (\Exception $ex) {
            Log::error('Exception occurred', [$ex->getMessage(), $ex->getTraceAsString()]);
        }
    }

    private function _statusUpdate()
    {
        if($this->event->statusUpdate) {
            OrderStatusLog::create(['order_id' => $this->order->id, 'status' => $this->order->status]);
        }

        if ($this->order->status == 'new' && ($this->order->paymentMethod->slug == 'cod' || $this->order->paymentMethod->slug == 'tarjeta' || $this->order->paymentMethod->slug == 'efectivo_local' || $this->order->paymentMethod->slug == 'tarjeta_local' )) {
            // send a notification to store
            $this->pushNotifications[] = new PushNotification($this->order->store->user->fcm_registration_id,
            'Nueva Orden', 'Has recibido una nueva orden', ["order_id" => $this->order->id]);    
            //'New Order', 'You have received new order', ["order_id" => $this->order->id]);
        }

        // when store accepts the order, send notification to user and try to allocate delivery boy for the order
        if ($this->order->status == 'accepted') {

            // try to allot a delivery person
            if ($this->order->delivery_status == 'pending') {
                $this->_allotDeliveryPerson();
            }

            // send notification to user that his order accepted by user
            $this->pushNotifications[] = new PushNotification($this->order->user->fcm_registration_id,
                //'Order Accepted', 'Your order has been accepted by store', ["order_id" => $this->order->id]);
                'Orden aceptada', 'Tu orden ha sido aceptada por el restaurante', ["order_id" => $this->order->id]);
        }


        if ($this->order->status == 'preparing') {

            // try to allot a delivery person, if not yet done
            if ($this->order->delivery_status == 'pending') {
                $this->_allotDeliveryPerson();
            }

            $this->pushNotifications[] = new PushNotification($this->order->user->fcm_registration_id,
                //'Order Preparing', 'Store has started preparing your order', ["order_id" => $this->order->id]);
                'Preparando orden', 'El restaurante empezó a preparar tu orden', ["order_id" => $this->order->id]);
        }

        if ($this->order->status == 'dispatched') {

            $this->pushNotifications[] = new PushNotification($this->order->user->fcm_registration_id,
                //'Order Dispatched', 'Restaurant has dispatched your order', ["order_id" => $this->order->id]);
                'Orden despachada', 'El restaurante ha despachado su orden', ["order_id" => $this->order->id]);
        }

        if ($this->order->status == 'collect') {

            $this->pushNotifications[] = new PushNotification($this->order->user->fcm_registration_id,
                //'Order Dispatched', 'Restaurant has dispatched your order', ["order_id" => $this->order->id]);
                'Orden lista ', 'El restaurante ya preparo su orden', ["order_id" => $this->order->id]);
        }

        if ($this->order->status == 'rejected') {
            $this->pushNotifications[] = new PushNotification($this->order->user->fcm_registration_id,
                //'Order Rejected', 'Sorry! Your order has been rejected by store', ["order_id" => $this->order->id]);
                'Orden rechazada', '¡Lo sentimos!! Su orden ha sido rechazada por el restaurante', ["order_id" => $this->order->id]);
        }
    }

    private function _deliveryStatusUpdate()
    {
        if ($this->order->delivery_status == 'complete') {
            $this->_onComplete();
        }
    }

    private function _allotDeliveryPerson()
    {
        $deliveryProfiles = DeliveryProfile::search($this->order->store_id);
        // $deliveryProfiles = DeliveryProfile::where('assigned', 0)->get();
        if (count($deliveryProfiles) > 0) {
            $deliveryProfile = $deliveryProfiles[0];
            $this->order->delivery_profile_id = $deliveryProfile->id;
            $this->order->delivery_status = 'allotted';
            $this->order->save();

            // set the assigned field of delivery boy to true implying delivery boy is not available for pickup
            $deliveryProfile->assigned = true;
            $deliveryProfile->save();

            // send notification to delivery person
            $this->pushNotifications[] = new PushNotification($deliveryProfile->user->fcm_registration_id,
                //'New Delivery', 'You have received new delivery order', ["order_id" => $this->order->id]);
                'Nueva entrega', 'Has recibido una nueva orden de entrega', ["order_id" => $this->order->id]);
        }
    }

    private function _onComplete()
    {
        // when delivery is complete, update the order's status to complete
        $this->order->status = 'complete';
        if($this->order->paymentMethod->slug == 'cod') {
            $this->order->payment_status = 'paid';
        }
        $this->order->save();
        OrderStatusLog::create(['order_id' => $this->order->id, 'status' => $this->order->status]);

        // when order is complete, set assigned field of delivery boy to false implying delivery boy is now available for pickup
        $deliveryProfile = $this->order->deliveryProfile;
        $deliveryProfile->assigned = false;
        $deliveryProfile->save();

        // transfer earnings to store and delivery person

        $adminEarningType = floatval(Setting::where('key', 'admin_fee_subscription_or_per_order')->first()->value);
        $adminTotalEarning = 0;
        $storeEarnings = $this->order->subtotal;

        # calculate store earnings if admin charge it's earning per order
        if($adminEarningType == 'per_order') {
            $adminShareInOrder = floatval(Setting::where('key', 'admin_fee_for_order_in_percent')->first()->value);
            $storeEarnings = $this->order->subtotal - ($this->order->subtotal * $adminShareInOrder) / 100;
            $adminTotalEarning = ($this->order->subtotal * $adminShareInOrder) / 100; // admin gets it's share of earning
        }

        // deposit store earnings in store wallet
        $this->order->store->user->deposit($storeEarnings);

        // create a transaction for store earning
        Transaction::create([
            'title' => 'Order Payment',
            'description' => 'Amount added for Order #' . $this->order->id,
            'status' => 'credit',
            'amount' => $storeEarnings,
            'user_id' => $this->order->store->user->id,
            'order_id' => $this->order->id,
            'source' => 'order'
        ]);

        $this->pushNotifications[] = new PushNotification($this->order->store->user->fcm_registration_id,
            //'Order Complete', 'Order is complete. Earnings credited', ["order_id" => $this->order->id]);
            'Orden completa', 'Orden completa. Ganancias abonadas', ["order_id" => $this->order->id]);

        # delivery person earnings
        $deliveryFee = $this->order->delivery_fee;

        if($this->order->paymentMethod->slug == 'cod') {
            // if delivery person collected the cash
            Transaction::create([
                'title' => 'Monto a cobrar',
                'description' => 'Monto a ser cobrado por el repartidor',
                'status' => 'debit',
                'amount' => $this->order->total - $deliveryFee,
                'user_id' => $this->order->deliveryProfile->user->id,
                'order_id' => $this->order->id,
                'source' => 'cod'
            ]);
            $this->order->deliveryProfile->user->withdraw($this->order->total - $deliveryFee
                , 'withdraw'
                , []
                , false); // last parameter is send to false so that amount can be withdrawn even if user wallet does not have enough money

            // give delivery fee to delivery person
            Transaction::create([
                'title' => 'Order Delivery Payment',
                'description' => 'Amount added for Order #' . $this->order->id . ' delivery',
                'status' => 'credit',
                'amount' => $deliveryFee,
                'user_id' => $this->order->deliveryProfile->user->id,
                'order_id' => $this->order->id,
                'source' => 'order',
                'is_paid' => 1
            ]);
        } else {
            // create a transaction for delivery person's earning
            Transaction::create([
                'title' => 'Order Delivery Payment',
                'description' => 'Amount added for Order #' . $this->order->id . ' delivery',
                'status' => 'credit',
                'amount' => $deliveryFee,
                'user_id' => $this->order->deliveryProfile->user->id,
                'order_id' => $this->order->id,
                'source' => 'order'
            ]);
            $this->order->deliveryProfile->user->deposit($deliveryFee);
        }

        // if admin has earned something from this order, create a transaction for admin's earning
        if($adminTotalEarning !== 0) {
            Transaction::create([
                'title' => "Admin's Commision",
                'description' => 'Commission added for Order #' . $this->order->id,
                'status' => 'credit',
                'amount' => $adminTotalEarning,
                'user_id' => User::find(1)->id,
                'order_id' => $this->order->id,
                'source' => 'order'
            ]);
        }

        $this->pushNotifications[] = new PushNotification($this->order->deliveryProfile->user->fcm_registration_id,
            //'Order Complete', 'Order is complete. Earnings credited', ["order_id" => $this->order->id]);            
            'Orden completa', 'Orden completa. Ganancias abonadas', ["order_id" => $this->order->id]);
    }
}
