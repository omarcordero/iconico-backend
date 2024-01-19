<?php

namespace App\Http\Controllers\Admin\Json;

use App\Events\Auth\Registered;
use App\Events\Ordered;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Requests\ApiOrderUpdateRequest;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Validator;

class OrderController extends Controller
{
    /**
     * Update order status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ApiOrderUpdateRequest $request, Order $order)
    {
        if($request->status == 'dispatched' && !$order->delivery_profile_id) {
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
