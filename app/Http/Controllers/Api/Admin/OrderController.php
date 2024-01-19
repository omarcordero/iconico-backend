<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\Ordered;
use App\Http\Requests\Admin\OrderUpdateRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::whereRaw('1=1');

        $currentStore = admin_get_store();
        if($currentStore) {
            $orders = $orders->where('store_id', $currentStore->id);
        }

        if ($request->id_like) {
            $orders = $orders->where('id', '=',  $request->id_like );
        }

        //Buscar id por coincidencia
       /*  if ($request->id_like) {
            $orders = $orders->where('id', 'like', '%' . $request->id_like . '%');
        } */
        if ($request->store_like) {
            $name = $request->store_like;
            $orders = $orders->whereHas('store', function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            });
        }

        if ($request->user_like) {
            $email = $request->user_like;
            $orders = $orders->whereHas('user', function ($query) use ($email) {
                $query->where('email', 'like', '%' . $email . '%');
            });
        }

        if($request->total_like) {
            $orders = $orders->where('total', 'like', '%' . $request->total_like . '%');
        }

        if($request->status_like) {
            $orders = $orders->where('status', 'like', '%' . $request->status_like . '%');
        }

        if($request->delivery_status_like) {
            $orders = $orders->where('delivery_status', 'like', '%' . $request->delivery_status_like . '%');
        }

        if($request->payment_status_like) {
            $orders = $orders->where('payment_status', 'like', '%' . $request->payment_status_like . '%');
        }

        if($request->created_at_like) {
            $orders = $orders->where('created_at', 'like', '%' . $request->created_at_like . '%');
        }

        $orderJson = $orders->orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page'));
        for ($i = 0; $i < count($orderJson); $i++) {
            if ($orderJson[$i]->before_order_status == "1") {
                $orderJson[$i]->status_order = $orderJson[$i]->status_order_two;
            }
        }
        return response()->json($orderJson);
    }


    public function show(Order $order)
    {
        admin_authorize_store($order->store_id);

        return response()->json($order);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected,preparing,dispatched,complete',
            'reject_reason' => 'string|nullable'
        ]);

        // if order status does changed
        if($order->status !== $request->status) {
            if ($request->status == 'dispatched' && !$order->delivery_profile_id && $request->status != 'collect') {
                // if store is trying to update the status of order to `dispatched`, first check if delivery person is assigned
                // to the order, if yes, simply update the order status, if no, try to allot delivery person to the order
                // if success, return 422 status code implying we have allotted the delivery person but order is not yet dispatched,
                // if we don't find any delivery person yet return status code 404
                
                if ($order->allotDeliveryPerson($order)) {
                    return response()->json($order->refresh(), 422);
                };
                return response()->json($order, 404);
            }

            if($request->status == 'complete') {
                $order->delivery_status = 'complete';
            }

            $order->fill($request->all());
            $order->save();

            event(new Ordered($order, true));
        }

        return response()->json($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json([], 204);
    }

    public function updateObservation(Request $request) {
        $aRequest       = $request->all();
        $orderId        = $aRequest['orderId'];
        $observation    = $aRequest['observation'];

        DB::select('UPDATE orders SET observaciones = ? WHERE id = ?', [$observation, $orderId]);

        $aResponse  = array('status' => true, 'message' => 'Update successfully');

        return $aResponse;
    }

    public function updateState(Request $request){
        $aRequest   = $request->all();
        $action     = $aRequest['action'];
        $orderId    = $aRequest['orderId'];
        $obvs       = (isset($aRequest['obvs'])) ? $aRequest['obvs'] : NULL;

        $orders = DB::table('orders')->where('id', $orderId)->get();
        if (isset($orders[0])) {
            $before_order = $orders[0]->before_order_status;
            if ($before_order == '1') {
                //dd([$action, $orderId, $obvs]);
                if ($action == 'ACCEPT') {
                    DB::select('CALL SP_UPDATE_ORDER_NEW(?,?,?)', [$action, $orderId, $obvs]);
                    DB::select('CALL SP_UPDATE_ORDER_NEW(?,?,?)', ['PREPARATION', $orderId, $obvs]);
                    $aResponse  = array('status' => true, 'message' => 'Update successfully');
                } else {
                    DB::select('CALL SP_UPDATE_ORDER_NEW(?,?,?)', [$action, $orderId, $obvs]);
                    $aResponse  = array('status' => true, 'message' => 'Update successfully');
                }

                return $aResponse;
            } else {
                DB::select('CALL SP_UPDATE_ORDER(?,?,?)', [$action, $orderId, $obvs]);

                $aResponse  = array('status' => true, 'message' => 'Update successfully');

                return $aResponse;
            }
        } else {
            $aResponse  = array('status' => false, 'message' => 'Order not found');

            return $aResponse;
        }
    }

    public function getCountOrderByUser (Request $request) {
        $aRequest   = $request->all();
        $userId     = $aRequest['userId'];

        $sql = "SELECT count(*) as countOrder FROM orders WHERE user_id = ?";
        $count = DB::select($sql, [$userId]);

        $aResponse  = array('status' => true, 'message' => 'Count order', 'data' => $count[0]);

        return $aResponse;
    }

}
