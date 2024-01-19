<?php

namespace App\Http\Controllers\Api\ICONICO;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PlateController extends Controller
{
    
    public function newPlate(Request $request){
        $aRequest       = $request->all();
        $action         = "CREATE-PLATE";
        $categoryId     = $aRequest['categoryId'];
        $name           = $aRequest['name'];
        $description    = $aRequest['description'];
        $price          = $aRequest['price'];
        $available      = $aRequest['available'];
        $complements    = (isset($aRequest['complements'])) ? $aRequest['complements'] : "";
        
        $aResponseSP    = DB::select("CALL SP_PLATES(?,0,?,?,?,?,?,0)",
                                     [
                                        $action,
                                        $categoryId,
                                        $name,
                                        $description,
                                        $price,
                                        $available
                                    ]);
        
        $plateId        = $aResponseSP[0]->plateId;
        
        DB::table('plate_complement')->where('platetId', $plateId)->delete();
        
        if ($complements != "") {
            for($i = 0; $i < count($complements); $i++) {
                $complementId = $complements[$i]['complementId'];
                DB::statement("CALL SP_PLATES_COMPLEMENTS(?,?)", [$plateId, $complementId]);
            }
        }
        
        $aResponse = array(
                        'status' => true,
                        'message' => 'Plate add successfully'
                    );
        
        return $aResponse;
    }
    
    public function getAll(Request $request){
        $aRequest   = $request->all();
        $categoryId = (isset($aRequest['categoryId'])) ? $aRequest['categoryId'] : "";
        $sucursalId = (isset($aRequest['sucursalId'])) ? $aRequest['sucursalId'] : "";
        $action     = "ALL-TABLE";
        $sql        = "CALL SP_PLATES(?,0,?,NULL,NULL,0,NULL,?)";
        
        if ($categoryId === "" || $sucursalId === "") {
            $aResponse  = array(
                            'status' => false,
                            'message' => 'Fields required'
                        );
        } else {
            $menu       = DB::select($sql, [$action, $categoryId, $sucursalId]);
        
            $aResponse  = array(
                            'status' => true,
                            'message' => 'Query successfully',
                            'data' => $menu
                        );
        }
        
        return $aResponse;
    }
    
    public function get(Request $request) {
        $aRequest   = $request->all();
        $menuId     = (isset($aRequest['menuId'])) ? $aRequest['menuId'] : "";
        $action     = "GET-TABLE";
        $sql        = "CALL SP_PLATES(?,?,0,NULL,NULL,0,NULL,0)";
        
        if ($menuId === "") {
            $aResponse  = array(
                            'status' => false,
                            'message' => 'Fields required'
                        );
        } else {
            $menu   = DB::select($sql, [$action, $menuId]);
            
            $aResponse  = array(
                            'status' => true,
                            'message' => 'Query successfully',
                            'data' => $menu
                        );
        }
        
        return $aResponse;
    }

    public function addBanner(Request $request) {
        $aRequest   = $request->all();
        $menuId     = (isset($aRequest['menuId'])) ? $aRequest['menuId'] : "";
        $banner     = (isset($aRequest['banner'])) ? $aRequest['banner'] : "";

        if ($menuId === "" || $banner === "") {
            $aResponse  = array(
                            'status' => false,
                            'message' => 'Fields required'
                        );
        } else {
            DB::table('menu_items')->where('id', $menuId)->update(['banner' => $banner]);

            $aResponse  = array(
                            'status' => true,
                            'message' => 'Update banner'
                        );
        }

        return $aResponse;
    }

    public function getBanner(Request $request) {
        $aRequest   = $request->all();
        $paginate   = $aRequest['paginate'];
        $menu_items = DB::select('CALL SP_PAGINATION_BANNER(?)', [$paginate]);

        $aResponse  = array(
                            'status' => true,
                            'message' => 'Query banner successfully',
                            'data' => $menu_items
                        );

        return $aResponse;
    }

    public function addMordeOrder(Request $request) {
        $aRequest   = $request->all();
        $menuId     = (isset($aRequest['menuId'])) ? $aRequest['menuId'] : "";
        $moreOrder  = (isset($aRequest['moreOrder'])) ? $aRequest['moreOrder'] : "";

        if ($menuId === "" || $moreOrder === "") {
            $aResponse  = array(
                            'status' => false,
                            'message' => 'Fields required'
                        );
        } else {
            DB::table('menu_items')->where('id', $menuId)->update(['moreOrder' => $moreOrder]);

            $aResponse  = array(
                            'status' => true,
                            'message' => 'Update MoreOrder'
                        );
        }

        return $aResponse;
    }

    public function getMoreOrder(Request $request) {
        $aRequest   = $request->all();
        $paginate   = $aRequest['paginate'];
        $menu_items = DB::select('CALL SP_PAGINATION_MORE_ORDER(?)', [$paginate]);

        $aResponse  = array(
                            'status' => true,
                            'message' => 'Query banner successfully',
                            'data' => $menu_items
                        );

        return $aResponse;
    }

    public function getOneBanner(Request $request) {
        $aRequest   = $request->all();
        $paginate   = $aRequest['paginate'];
        $menu_items = DB::select('CALL SP_PAGINATION_ONE_BANNER(?)', [$paginate]);

        $aResponse  = array(
                            'status' => true,
                            'message' => 'Query banner successfully',
                            'data' => $menu_items
                        );

        return $aResponse;
    }
    
}
