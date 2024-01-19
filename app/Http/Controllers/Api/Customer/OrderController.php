<?php

namespace App\Http\Controllers\Api\Customer;

use App\Events\Ordered;
use App\Exceptions\CouponException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiPaymentRequest;
use App\Http\Requests\ApiStripePaymentRequest;
use App\Http\Requests\Customer\ApiOrderCreateRequest;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\MenuItem;
use App\Models\MenuItemChoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemChoice;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\Store;
use Carbon\Carbon;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yabacon\Paystack;

class OrderController extends Controller
{

    /**
     * Get orders by user_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', Auth::user()->id);
        if ($request->status) {
            $orders = $orders->where('status', $request->status);
        }
        return response()->json($orders->orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page')));
    }

    /**
     * @param ApiOrderCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function store(ApiOrderCreateRequest $request)
    {
        $order = new Order();
        $store = Store::find($request->store_id);
        $address = Address::find($request->address_id);
        $orderItems = [];
        $orderItemChoices = [];
        $settings = settings_as_dictionary();
        $subtotal = 0;
        $coupon = null;
        // check coupon validity once again
        if ($request->coupon) {
            try {
                $coupon = Coupon::where('code', $request->coupon)->firstOrFail();
                Coupon::checkValidity($coupon, Auth::user());
            } catch (CouponException $e) {
                throw ValidationException::withMessages([
                    'coupon' => $e->getMessage()
                ]);
            } catch (Exception $exception) {
                throw ValidationException::withMessages([
                    'coupon' => 'Invalid coupon'
                ]);
            }
        }
        $order->payment_method_id = $request->payment_method_id;
        $order->store_id = $request->store_id;
        $order->address_id = $request->address_id;
        $order->user_id = Auth::user()->id;
        $order->save();
        // calculate total price for each item and subtotal of complete order
        $items = $request['items'];

        foreach ($items as $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            $itemTotal = $item['quantity'] * $menuItem->price;

            $orderItem = new OrderItem();
            $orderItem->menu_item_id = $item['menu_item_id'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->total = $itemTotal;
            $orderItem->order_id = $order->id;
            $orderItem->save();

            // handle menu item choices
            if (array_key_exists('choices', $item)) {
                foreach ($item['choices'] as $choice) {
                    $menuItemChoice = MenuItemChoice::find($choice['menu_item_choice_id']);
                    $itemTotal += $menuItemChoice->price; // add the price of add-on(item choice) in item's total price

                    $orderItemChoice = new OrderItemChoice();
                    $orderItemChoice->menu_item_id = $item['menu_item_id'];
                    $orderItemChoice->menu_item_choice_id = $menuItemChoice->id;
                    $orderItemChoice->total = $menuItemChoice->price;
                    $orderItemChoice->order_id = $order->id;
                    $orderItemChoice->order_item_id = $orderItem->id;
                    $orderItemChoice->save();
                }
            }

            $subtotal += $itemTotal;
        }
        
        //SE MODIFICA EL SUB TOTAL, SE LE RESTA EL IGV DEL TOTAL
        $subtotaligv = $subtotal;
        $subtotal = ($subtotal / (($settings['tax_in_percent'] / 100)+1));
        
        // set total, taxes, delivery fee for an order
        $requestFields = $request->all();
        $requestFields['subtotal'] = $subtotal;

        // delivery fee
        $deliveryFee = $settings['delivery_fee'] ? $settings['delivery_fee'] : 0;
        if(isset($request->addres_id) || $request->addres_id != null){
            switch ($settings['delivery_fee_set_by']) {
                case 'store':
                    $deliveryFee = $store->delivery_fee ? $store->delivery_fee : 0;
                    break;
                case 'distance':
                    // calculate distance between store and user's location to calculate delivery charges
                    $storeLat = $store->latitude;
                    $storeLng = $store->longitude;
                    $userLat = $address->latitude;
                    $userLng = $address->longitude;
    
                    $distances = DB::select(
                        'SELECT ST_Distance_Sphere(Point(:store_lng,:store_lat),Point(:user_lng,:user_lat)) as distance',
                        ['store_lng' => $storeLng, 'store_lat' => $storeLat, 'user_lng' => $userLng, 'user_lat' => $userLat,]
                    );
                    $distanceInKms = ceil($distances[0]->distance / 1000);
                    $deliveryChargePerKm = $settings['delivery_fee_per_km_charge'] ? $settings['delivery_fee_per_km_charge'] : 0;
                    $deliveryFee = $distanceInKms * $deliveryChargePerKm;
                    break;
            }
        }
       /*  switch ($settings['delivery_fee_set_by']) {
            case 'store':
                $deliveryFee = $store->delivery_fee ? $store->delivery_fee : 0;
                break;
            case 'distance':
                // calculate distance between store and user's location to calculate delivery charges
                $storeLat = $store->latitude;
                $storeLng = $store->longitude;
                $userLat = $address->latitude;
                $userLng = $address->longitude;

                $distances = DB::select(
                    'SELECT ST_Distance_Sphere(Point(:store_lng,:store_lat),Point(:user_lng,:user_lat)) as distance',
                    ['store_lng' => $storeLng, 'store_lat' => $storeLat, 'user_lng' => $userLng, 'user_lat' => $userLat,]
                );
                $distanceInKms = ceil($distances[0]->distance / 1000);
                $deliveryChargePerKm = $settings['delivery_fee_per_km_charge'] ? $settings['delivery_fee_per_km_charge'] : 0;
                $deliveryFee = $distanceInKms * $deliveryChargePerKm;
                break;
        } */
        $requestFields['delivery_fee'] = $deliveryFee;

        // taxes
        //$tax = ($subtotal * $settings['tax_in_percent']) / 100;
        $tax = $subtotaligv -$subtotal;
        //$tax = ($subtotal * $settings['tax_in_percent']) / 100;
        $requestFields['taxes'] = $tax;

        // apply coupon
        $requestFields['discount'] = 0; // reset discount to 0, since client may already have set this field
        if ($coupon !== null) {
            $discount = 0;
            if ($coupon->type == 'fixed') {
                $discount = $coupon->reward;
            }

            if ($coupon->type == 'percent') {
                $discount = ($subtotal * $coupon->reward) / 100;
            }

            $requestFields['discount'] = $discount;

            $coupon->users()->attach(Auth::user()->id, [
                'used_at' => Carbon::now(),
            ]);
        }

        $requestFields['total'] = ($subtotal + $tax + $requestFields['delivery_fee']) - $requestFields['discount'];

        //new fields
        //$requestFields[''] 

        $orderlast = Order::find($order->id);
        $orderlast->fill($requestFields);
        $orderlast->save();

        $paymentMethod = PaymentMethod::find($request->payment_method_id);
        if ($paymentMethod->slug == 'cod' ||$paymentMethod->slug == 'tarjeta' || $paymentMethod->slug == 'efectivo_local' || $paymentMethod->slug == 'tarjeta_local' ) {
            event(new Ordered(Order::find($orderlast->id)));
        }

        //return response()->json(Order::find($orderlast->id));
        return response()->json($orderlast);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid',
        ]);

        $order->fill($request->only('payment_status'));
        $order->save();

        return response($order);
    }

    public function makeStripePayment(Order $order, ApiStripePaymentRequest $request)
    {
        $amount = number_format((float)$order->total, 2, '.', '');
        $currency = Setting::where('key', 'currency')->first()->value;

        try {
            $token = $request->token;
            $charge = Stripe::charges()->create([
                'amount' => $amount,
                'currency' => strtolower($currency),
                'description' => 'Payment for Order #' . $order->id,
                'source' => $token,
            ]);
            $order->payment_status = 'paid';
            $order->save();

            event(new Ordered(Order::find($order->id)));

            return response()->json(["status" => true, 'charge' => $charge]);
        } catch (\Exception $ex) {
            abort(400);
        }
    }

    public function makePayUPayment(Order $order, Request $request)
    {
        $key                =   $request['key'];
        $salt                =   $request['salt'];
        $txnid                 =     $request['txnid'];
        $amount              =     $request['amount'];
        $productInfo          =     $request['productinfo'];
        $firstname            =     $request['firstname'];
        $email                =    $request['email'];
        $udf5                =   $request['udf5'];
        $mihpayid            =    $request['mihpayid'];
        $status                =     $request['status'];
        $resphash                =     $request['hash'];
        //Calculate response hash to verify
        $keyString               =      $key . '|' . $txnid . '|' . $amount . '|' . $productInfo . '|' . $firstname . '|' . $email . '|||||' . $udf5 . '|||||';
        $keyArray               =     explode("|", $keyString);
        $reverseKeyArray     =     array_reverse($keyArray);
        $reverseKeyString    =    implode("|", $reverseKeyArray);
        $CalcHashString     =     strtolower(hash('sha512', $salt . '|' . $status . '|' . $reverseKeyString));


        if ($status == 'success') {
            $order->payment_status = 'paid';
            $order->save();
            return response()->json(["message" => "Transaction Successful"]);
        } else {
            return response()->json(["message" => "Transaction Failed"], 400);
        }
    }

    public function makePaystackPayment(Order $order, Request $request)
    {
        $paystack = new Paystack(env('PAYSTACK_SECURE_KEY'));
        try {
            $tranx = $paystack->transaction->initialize([
                'amount' => $order->total,
                'email' => $order->user->email,         // unique to customers
                'reference' => (string)time() . '-' . $order->id, // unique to transactions,
                'callback_url' => url('/api/customer/order/' . $order->id . '/payment/paystack/callback')
            ]);
        } catch (\Yabacon\Paystack\Exception\ApiException $e) {
            print_r($e->getResponseObject());
            die($e->getMessage());
        }

        // store transaction reference so we can query in case user never comes back
        // perhaps due to network issue
        // save_last_transaction_reference($tranx->data->reference);

        // redirect to page so User can pay
        header('Location: ' . $tranx->data->authorization_url);
    }


    public function paystackCallback(Order $order, Request $request)
    {
        $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
        if (!$reference) {
            header('Location: ' . url('/api/customer/order/' . $order->id . '/payment/paystack/status?result=error'));
        }

        // initiate the Library's Paystack Object
        $paystack = new Paystack(env('PAYSTACK_SECURE_KEY'));
        try {
            // verify using the library
            $tranx = $paystack->transaction->verify([
                'reference' => $reference, // unique to transactions
            ]);
        } catch (\Yabacon\Paystack\Exception\ApiException $e) {
            print_r($e->getResponseObject());
            header('Location: ' . url('/api/customer/order/' . $order->id . '/payment/paystack/status?result=error'));
        }

        if ('success' === $tranx->data->status) {
            $order->payment_status = 'paid';
            $order->save();
            header('Location: ' . url('/api/customer/order/' . $order->id . '/payment/paystack/status?result=success'));
        }
    }

    public function paystackStatus(Request $request)
    {
        echo $request->result;
    }

    public function calculateDeliveryFee(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id'
        ]);

        $settings = settings_as_dictionary();
        $store = Store::find($request->store_id);

        // delivery fee
        $deliveryFee = $settings['delivery_fee'] ? $settings['delivery_fee'] : 0;
        switch ($settings['delivery_fee_set_by']) {
            case 'store':
                $deliveryFee = $store->delivery_fee ? $store->delivery_fee : 0;
                break;
            case 'distance':
                $request->validate([
                    'address_id' => 'required|exists:addresses,id',
                ]);

                $address = Address::find($request->address_id);

                // calculate distance between store and user's location to calculate delivery charges
                $storeLat = $store->latitude;
                $storeLng = $store->longitude;
                $userLat = $address->latitude;
                $userLng = $address->longitude;

                $distances = DB::select(
                    'SELECT ST_Distance_Sphere(Point(:store_lng,:store_lat),Point(:user_lng,:user_lat)) as distance',
                    ['store_lng' => $storeLng, 'store_lat' => $storeLat, 'user_lng' => $userLng, 'user_lat' => $userLat,]
                );
                $distanceInKms = ceil($distances[0]->distance / 1000);
                $deliveryChargePerKm = $settings['delivery_fee_per_km_charge'] ? $settings['delivery_fee_per_km_charge'] : 0;
                $deliveryFee = $distanceInKms * $deliveryChargePerKm;
                break;
        }

        return response()->json(["delivery_fee" => $deliveryFee]);
    }
}
