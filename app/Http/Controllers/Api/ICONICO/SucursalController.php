<?php

namespace App\Http\Controllers\Api\ICONICO;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SucursalController extends Controller
{
    
    public function getAll(Request $request){
        $action     = "GET-ALL";
        $sql        = "CALL SP_SUCURSAL(?, 0)";
        $categories = DB::select($sql, [$action]);
        
        $aResponse  = array(
                            'status' => true,
                            'message' => 'Query successfully',
                            'data' => $categories
                        );
        
        return $aResponse;
    }
	
	public function validatePriceStore(Request $request) {
        $aRequest   = $request->all();
        $storeId    = $aRequest['storeId'];
        $price      = $aRequest['price'];

        $storeResult = DB::select("SELECT * FROM stores WHERE id = ?", [$storeId]);
        
        if ($storeResult[0]) {
            $minimum_order = $storeResult[0]->minimum_order;
            if (intval($price) <= ($minimum_order - 1)) {
                $aResponse = array(
                    'status' => false,
                    'message' => 'Price not valid',
                    'result' => $storeResult[0]
                );
            } else {
                $aResponse = array(
                    'status' => true,
                    'message' => 'Price valid'
                );
            }
        }
        return $aResponse;
    }

    public function getCoordenates(Request $request) {
        $aResponse      = $request->all();
        $storeId        = (isset($aResponse['storeId'])) ? $aResponse['storeId'] : "";
        $coordenates    = DB::select('SELECT * FROM stores_coordenates WHERE storeId = ?', [$storeId]);
        
        $aResponse      = array(
                'status' => true,
                'message' => 'Query successfully',
                'data' => $coordenates
            );

        return $aResponse;
    }
    
    public function validateSucursalOpenning(Request $request) {
        $aResponse  = $request->all();
        $storeId    = (isset($aResponse['storeId'])) ? $aResponse['storeId'] : "";
        $sql        = DB::select('CALL SP_VALIDATE_SUCURSAL(?)', [$storeId]);

        $aResponse  = array(
                'status' => true,
                'message' => 'Query successfully',
                'data' => $sql
            );

        return $aResponse;
    }

    public function validateHourStore(Request $request): array
    {
        $hour = date("H:i:s");
        $aRequest = $request->all();
        $storeId = ($aRequest['storeId'] === null || $aRequest['storeId'] === NULL || $aRequest['storeId'] === "") ? "" : $aRequest['storeId'];

        if ($storeId === "") {
            return array(
                'status' => false,
                'message' => 'Fields required',
                'data' => array()
            );
        } else {
            $stores = DB::table('stores')->where('id', $storeId)->get();

            if (isset($stores[0])) {
                $open = $stores[0]->opens_at;
                $close = $stores[0]->closes_at;

                if ($open < $hour && $hour < $close) {
                    return array(
                        'status' => true,
                        'message' => 'OPEN',
                        'data' => array(
                            'open' => $open,
                            'close' => $close,
                            'hour' => $hour
                        )
                    );
                } else {
                    return array(
                        'status' => true,
                        'message' => 'CLOSE',
                        'data' => array(
                            'open' => $open,
                            'close' => $close,
                            'hour' => $hour
                        )
                    );
                }
            } else {
                return array(
                    'status' => false,
                    'message' => 'Stores not found',
                    'data' => array()
                );
            }


        }


    }

}
