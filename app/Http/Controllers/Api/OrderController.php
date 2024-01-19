<?php

namespace App\Http\Controllers\Api;

use App\Events\Ordered;
use App\Helpers\PushNotificationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiOrderUpdateRequest;
use App\Http\Requests\Customer\ApiOrderCreateRequest;
use App\Models\DeliveryProfile;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Get orders by store_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $orders = Order::where('store_id', Auth::user()->store->id);
        if($request->status) {
            $orders = $orders->where('status', $request->status);
        }

        // filter for past orders
        if($request->past_orders) {
            $orders = $orders->whereIn('status', ['cancelled', 'rejected', 'complete']);
        }

        // filter for active orders
        if($request->active_orders) {
            $orders = $orders->whereIn('status', ['new', 'accepted', 'preparing', 'dispatched','collect']);
        }

        // for delivery tab in store app
        if($request->deliveries) {
            $orders = $orders->whereIn('status', ['accepted', 'preparing', 'dispatched', 'intransit'])
                ->where('delivery_profile_id', '<>', null)
                ->whereIn('delivery_status', ['allotted', 'started', 'complete']);
        }

        return response()->json($orders->orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page')));
    }

    /**
     * Display the order
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ApiOrderUpdateRequest $request
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function update(ApiOrderUpdateRequest $request, Order $order)
    {
        if($request->status == 'dispatched' && !$order->delivery_profile_id && $request->status != 'collect') {
            // if store is trying to update the status of order to `dispatched`, first check if delivery person is assigned
            // to the order, if yes, simply update the order status, if no, try to allot delivery person to the order
            // if success, return 422 status code implying we have allotted the delivery person but order is not yet dispatched,
            // if we don't find any delivery person yet return status code 404
            if($order->allotDeliveryPerson($order)) {
                return response()->json($order->refresh(), 422);
            };
            return response()->json($order, 404);
        }
        $order->fill($request->all());
        $order->save();

        event(new Ordered($order, true));

        return response()->json($order);
    }
}
