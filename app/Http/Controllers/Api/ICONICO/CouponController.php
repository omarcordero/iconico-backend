<?php

namespace App\Http\Controllers\Api\ICONICO;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    
    public function newCupon(Request $request) {
        $aRequest       = $request->all();
        $action         = "CREATE-COUPON";
        $typeCoupon     = (isset($aRequest['typeCoupon'])) ? $aRequest['typeCoupon'] : "";
        $value          = (isset($aRequest['value'])) ? $aRequest['value'] : "";
        $description    = (isset($aRequest['description'])) ? $aRequest['description'] : "";
        $dateExpired    = (isset($aRequest['dateExpired'])) ? $aRequest['dateExpired'] : "";
        if ($typeCoupon === "" || $value === "" || $description === "" || $dateExpired === "") {
            $aResponse  = array(
                            'status' => false,
                            'message' => 'Fields required'
                        );
        } else {
            DB::statement('CALL SP_COUPON(?,?,?,?,?)', [$action, $typeCoupon, $value, $description, $dateExpired]);
            $aResponse  = array(
                            'status' => true,
                            'message' => 'Created successfully'
                        );
        }
        
        return $aResponse;
    }
    
    public function getCupon(Request $request) {
        $aRequest   = $request->all();
        $coupon     = $aRequest['coupon'];
        $coupons    = DB::table('coupons')->where('code', '=', $coupon)->get();
        $aResponse  = array(
            'status' => true,
            'message' => 'Query successfully',
            'data' => $coupons
        );

        return $aResponse;
    }
    
}
