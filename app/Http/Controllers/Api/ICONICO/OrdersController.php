<?php

namespace App\Http\Controllers\Api\ICONICO;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Mail\TemplateMail;
use Mail;

class OrdersController extends Controller
{
    
    public function newOrder(Request $request){
        $aRequest           = $request->all();
        $action             = "CREATE-ORDER";
        $userId             = (isset($aRequest['userId'])) ? $aRequest['userId'] : "";
        //$coordinates        = (isset($aRequest['coordinates'])) ? $aRequest['coordinates'] : "";
        $coordinates        = $aRequest['coordinates'];
        $methodPaymentId    = (isset($aRequest['methodPaymentId'])) ? $aRequest['methodPaymentId'] : "";
        $checkout           = (isset($aRequest['checkout'])) ? $aRequest['checkout'] : "";
        $priceTotal         = (isset($aRequest['priceTotal'])) ? $aRequest['priceTotal'] : "";
        $sucursalId         = (isset($aRequest['sucursalId'])) ? $aRequest['sucursalId'] : "";
        $typeOrderId        = (isset($aRequest['typeOrderId'])) ? $aRequest['typeOrderId'] : "";
        $subtotal           = (isset($aRequest['subtotal'])) ? $aRequest['subtotal'] : "";
        $igv                = (isset($aRequest['igv'])) ? $aRequest['igv'] : "";
        $discount           = (isset($aRequest['discount'])) ? $aRequest['discount'] : "";
        $description        = (isset($aRequest['description'])) ? $aRequest['description'] : NULL;
        $pago_con           = (isset($aRequest['pago_con'])) ? $aRequest['pago_con'] : 0;
        $waiter             = (isset($aRequest['waiter'])) ? $aRequest['waiter'] : false;
        $board              = (isset($aRequest['board'])) ? $aRequest['board'] : "";
        //$deliveryPrice    = (isset($aRequest['delivery'])) ? $aRequest['delivery'] : "";
        $covered            = (isset($aRequest['covered'])) ? $aRequest['covered'] : "";
        $coupon             = (isset($aRequest['coupon'])) ? $aRequest['coupon'] : "";
        $coupon_code        = (isset($aRequest['coupon_code'])) ? $aRequest['coupon_code'] : "N/A";

        if (
            $userId === "" || $methodPaymentId === "" || $igv === "" || $checkout === "" 
            || $sucursalId === "" || $typeOrderId === "" || $subtotal === "" || $discount === ""
        ) {
            $aResponse      = array(
                                'status' => false,
                                'message' => 'Fields required'
                            );
        } else {

            $userResult = DB::select("SELECT * FROM users WHERE id = ?", [$userId]);
            $aResponse = array();
            if ($userResult[0]) {
                if ($userResult[0]->mobile_number === NULL || $userResult[0]->mobile_number === "") {
                    $aResponse = array(
                        'status' => false,
                        'message' => 'Phone not found'
                    );
                    return $aResponse;
                }
            }

            $storeResult = DB::select("SELECT * FROM stores WHERE id = ?", [$sucursalId]);
            if ($storeResult[0]) {
                $minimum_order = $storeResult[0]->minimum_order;
                if (intval($priceTotal) <= ($minimum_order - 1)) {
                    $aResponse = array(
                        'status' => false,
                        'message' => 'Price not valid'
                    );
                    return $aResponse;
                }
            }
            $addressExact = "";
            $reference = "";
            if(count($coordinates) === 0) {
                $addressId = NULL;
            } else {
                $title              = $coordinates['title'];
                $latitude           = $coordinates['latitude'];
                $longitude          = $coordinates['longitude'];
                $address            = ($coordinates['address'] == "") ? $aRequest['addressExact'] : $coordinates['address'];
                $addressExact       = (isset($aRequest['addressExact'])) ? $aRequest['addressExact'] : "";
                $reference          = (isset($aRequest['reference'])) ? $aRequest['reference'] : "";
                $desValidateAddress = DB::select("CALL SP_ADDRESS('VALIDATE-ADDRESS', NULL, NULL, ?, ?, ?)", [$latitude, $longitude, $userId]);
                $validateAddress    = $desValidateAddress[0]->VAR_VALIDATE_ADDRESS;
                IF ( $validateAddress === '0' ) {
                    $desAddress     = DB::select("CALL SP_ADDRESS('CREATE-ADDRESS', ?, ?, ?, ?, ?)", [$title, $address, $latitude, $longitude, $userId]);
                    $addressId      = $desAddress[0]->VAR_ID_ADDRESS;
                } ELSE {
                    $addressId      = $desValidateAddress[0]->VAR_ID_ADDRESS;
                }
            }

            if ($typeOrderId === '1' || $typeOrderId === 1) {
                $storesDB = DB::select("SELECT delivery_fee FROM stores WHERE id = ?", [$sucursalId]);
                $deliveryPrice = intval($storesDB[0]->delivery_fee);
                $priceTotal = intval($storesDB[0]->delivery_fee) + intval($priceTotal);
            } else {
                $deliveryPrice = 0;
            }

            if ($coupon != "") {
                $couponGet  = DB::select('SELECT * FROM coupons WHERE code = ?', [$coupon]);
                $reward     = $couponGet[0]->reward;
                $type       = $couponGet[0]->type;

                if ($type === "percent") {
                    // Porcentaje
                    $discount = $priceTotal * ($reward / 100);
                    $priceTotal = $priceTotal - $discount;
                }

                if ($type === "fixed") {
                    // Resta
                    $discount = $reward;
                    $priceTotal = $priceTotal - $discount;
                }
            }

            $desOrder           = DB::select("CALL SP_ORDERS(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$action, $userId, $addressId, $methodPaymentId, $priceTotal, $sucursalId, $typeOrderId, $subtotal, $igv, $discount, $description, $pago_con, $deliveryPrice, $addressExact, $reference, $covered, $coupon_code]);

            $orderId            = $desOrder[0]->VAR_ORDER_ID;
            
            for ($i = 0; $i < count($checkout); $i++) {
                $groupComplement = $checkout[$i]['complement'];
                $menuId = $checkout[$i]['menu']['id'];
                $quantity = $checkout[$i]['quantity'];
                
                #Created table order_menu
                $orderMenuId = DB::table('order_menu')->insertGetId([
                    'orderId' => $orderId,
                    'menuId' => $menuId,
                    'quantity' => $quantity
                ]);

                //echo count($groupComplement;
                if (count($groupComplement) > 0) {
                    for ($j = 0; $j < count($groupComplement); $j++) {
                        $groupId    = $groupComplement[$j]['groupId'];
                        $nameGroup  = $groupComplement[$j]['name'];
                        $complement = $groupComplement[$j]['complement'];
                        for ($k = 0; $k < count($complement); $k++) {
                            $complementId = $complement[$k]['complementId'];
                            $nameComplement = $complement[$k]['name'];
                            $price = $complement[$k]['price'];
                            DB::table('order_menu_complement')->insert([
                                'orderMenuId' => $orderMenuId,
                                'orderId' => $orderId,
                                'menuId' => $menuId,
                                'groupId' => $groupId,
                                'nameGroup' => $nameGroup,
                                'complementId' => $complementId,
                                'nameComplement' => $nameComplement,
                                'price' => $price
                            ]);
                        }
                    }
                }
            }

            $stores         = DB::select('SELECT * FROM stores WHERE id = ?', [$sucursalId]);
            $nameStores     = $stores[0]->name;
            $storeUserId    = $stores[0]->owner_id;
            $users          = DB::select('SELECT * FROM users WHERE id = ?', [$userId]);
            $nameUser       = $users[0]->name;
            $usersStore     = DB::select('SELECT * FROM users WHERE id = ?', [$storeUserId]);
            $emailStore     = $usersStore[0]->email;

            //$this->sendMail($nameStores, $nameUser, $orderId, $emailStore);

            if ($waiter) {
                DB::table('orders')->where('id', '=', $orderId)->update(['boardId' => $board]);
            }

            $aResponse = array(
                            'status' => true,
                            'message' => 'Order created successfully',
                            'data' => array('orderId' => $orderId)
                        );
        }
        
        return $aResponse;
    }

    public function getOrderMenu(Request $request) {
        $aRequest   = $request->all();
        $orderId    = $aRequest['orderId'];
        $order_menu = DB::select('SELECT * FROM order_menu WHERE orderId = ?', [$orderId]);
        $dataOrder  = [];
        if (isset($order_menu)) {
            for ($i = 0; $i < count($order_menu); $i++) {
                $menuId         = $order_menu[$i]->menuId;
                $quantity       = $order_menu[$i]->quantity;
                $orderMenuId    = $order_menu[$i]->orderMenuId;
                $menu           = DB::select('SELECT * FROM menu_items WHERE id = ?', [$menuId]);
                $complement     = DB::select('SELECT * FROM order_menu_complement WHERE orderMenuId = ? AND orderId = ? AND menuId = ?', [$orderMenuId, $orderId, $menuId]);
                $arrayC     = [];
                for ($j = 0; $j < count($complement); $j++) {
                    $nameGroup      = $complement[$j]->nameGroup;
                    $nameComplement = $complement[$j]->nameComplement;
                    $price          = $complement[$j]->price;
                    $array          = array(
                            'nameGroup' => $nameGroup,
                            'nameComplement' => $nameComplement,
                            'price' => $price
                        );
                    array_push($arrayC, $array);
                }
                if (isset($menu[0])) {
                    $menu[0]->quantity      = $quantity;
                    $menu[0]->complement    = $arrayC;
                    array_push($dataOrder, $menu[0]);
                }
            }
        }

        $aResponse = array(
            'status' => true,
            'message' => 'Query successfully',
            'data' => $dataOrder
        );

        return $aResponse;
    }

    public function addFile(Request $request) {
        $aRequest   = $request->all();
        $orderId    = $aRequest['orderId'];
        if ($request->hasFile('image')){
            $requestName = $request->file('image')->store('uploads', 'public');
            DB::table('orders')->where('id', '=', $orderId)->update(['comprobante_file' => $requestName]);
            $aResponse = array(
                    'status' => true,
                    'message' => $requestName
                );
        } else {
            $aResponse = array(
                    'status' => false,
                    'message' => 'Image not found'
                );
        }

        return $aResponse;
    }

    public function getOrders(Request $request) {
        $aRequest   = $request->all();
        $userId     = $aRequest['userId'];
        $orders     = DB::table('orders')->where('user_id', '=', $userId)->orderBy('id', 'DESC')->get();

        for($k = 0; $k < count($orders); $k++) {
            $orderId    = $orders[$k]->id;
            $storeId    = $orders[$k]->store_id;
            $paymentId  = $orders[$k]->payment_method_id;
            $addressId  = $orders[$k]->address_id;
            $stores     = DB::select('SELECT * FROM stores WHERE id = ?', [$storeId]);
            $order_menu = DB::select('SELECT * FROM order_menu WHERE orderId = ?', [$orderId]);
            $payment    = DB::select('SELECT * FROM payment_methods WHERE id = ?', [$paymentId]);
            $address    = DB::select('SELECT * FROM addresses WHERE id = ?', [$addressId]);
            $users      = DB::select('SELECT * FROM users WHERE id = ?', [$userId]);
            $dataOrder  = [];
            if (isset($order_menu)) {
                for ($i = 0; $i < count($order_menu); $i++) {
                    $orderMenuId    = $order_menu[$i]->orderMenuId;
                    $menuId         = $order_menu[$i]->menuId;
                    $quantity       = $order_menu[$i]->quantity;
                    $menu           = DB::select("SELECT *, 0 AS quantity, '' AS complement FROM menu_items WHERE id = ?", [$menuId]);
                    $complement     = DB::select('SELECT * FROM order_menu_complement WHERE orderMenuId = ? AND orderId = ? AND menuId = ?', [$orderMenuId, $orderId, $menuId]);
                    $sumComplement  = DB::select('SELECT SUM(price) AS priceTotal FROM order_menu_complement WHERE orderMenuId = ? AND orderId = ? AND menuId = ?', [$orderMenuId, $orderId, $menuId]);
                    $priceTotalComplement = $sumComplement[0]->priceTotal;
                    $arrayC     = [];
                    for ($j = 0; $j < count($complement); $j++) {
                        $nameGroup      = $complement[$j]->nameGroup;
                        $nameComplement = $complement[$j]->nameComplement;
                        $price          = $complement[$j]->price;
                        $array          = array(
                                'nameGroup' => $nameGroup,
                                'nameComplement' => $nameComplement,
                                'price' => $price,
                                'totalComplement' => floatval($priceTotalComplement)
                            );
                        array_push($arrayC, $array);
                    }
                    if (isset($menu[0])) {
                        $menu[0]->quantity      = $quantity;
                        $menu[0]->complement    = $arrayC;
                        array_push($dataOrder, $menu[0]);
                    }
                }
            }
            $orders[$k]->store              = $stores;
            $orders[$k]->detailComplement   = $dataOrder;
            $orders[$k]->payment            = $payment;
            $orders[$k]->addresses          = $address;
            $orders[$k]->users              = $users;
        }

        $aResponse = array(
            'status' => true,
            'message' => 'Query successfully',
            'data' => $orders
        );

        return $aResponse;
    }

    public function emailTesting(){
        $dataNow    = date("Y-m-d H:i:s");
        $store      = 51;
        $user       = 'Omar Cordero';
        $numOrden   = '16';
        $data       = ['store' => 51, 'user' => $user, 'numberOrden' => $numOrden, 'dataNow' => $dataNow];
        Mail::to('oacf.devs@gmail.com')->send(new TemplateMail($data));
    }

    public function sendMail($nameStores, $nameUser, $orderId, $emailStore){
        $dataNow    = date("Y-m-d H:i:s");
        $store      = $nameStores;
        $user       = $nameUser;
        $numOrden   = $orderId;
        $data       = ['store' => $store, 'user' => $user, 'numberOrden' => $numOrden, 'dataNow' => $dataNow];
        Mail::to($emailStore)->send(new TemplateMail($data));
        //Mail::to("marko.pachas@gmail.com")->send(new TemplateMail($data));
        Mail::to("javier.izne@gmail.com")->send(new TemplateMail($data));
    }
    
    public function getOrdersDelivery(Request $request){
        $aRequest   = $request->all();
        $profileId  = $aRequest['profileId'];
        $state  = $aRequest['state'];
        if ($profileId === "") {
            $aResponse = array(
                    'status' => false,
                    'message' => 'Fields required'
                );
        } else {
            if ($state === "0") {
                $orders = DB::table('orders')->where('delivery_profile_id', '=', $profileId)->where('status_order_two', '=', 'Orden en Camino')->orderBy('id','DESC')->get();
                for($i = 0; $i < count($orders); $i++) {
                    $payment_method_id = $orders[$i]->payment_method_id;
                    $payment = DB::table('payment_methods')->where('id', '=', $payment_method_id)->get();
                    $user_id = $orders[$i]->user_id;
                    $users = DB::table('users')->where('id', '=', $user_id)->get();
                    $address_id = $orders[$i]->address_id;
                    $addresses = DB::table('addresses')->where('id', '=', $address_id)->get();
                    $orders[$i]->payment = $payment;
                    $orders[$i]->users = $users;
                    $orders[$i]->addresses = $addresses;
                }
            } elseif ($state === "0") {
                $orders = DB::table('orders')->where([
                    ['delivery_profile_id', '=', $profileId], ['state_dispatched', '=', '1']
                ])->get();
            }

            $aResponse = array(
                    'status' => true,
                    'message' => 'Query successfully',
                    'data' => $orders
                );
        }

        return $aResponse;
    }

}
