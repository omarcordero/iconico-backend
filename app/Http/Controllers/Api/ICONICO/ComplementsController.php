<?php

namespace App\Http\Controllers\Api\ICONICO;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ComplementsController extends Controller
{
    
    public function newGroup(Request $request) {
        $aRequest   = $request->all();
        $action     = "CREATE-GROUP";
        $nameGroup  = $aRequest['nameGroup'];
        $cantMin    = $aRequest['cantMin'];
        $cantMax    = $aRequest['cantMax'];
        DB::statement("CALL SP_GROUP_COMPLEMENTS(?,0,?,?,?)", [$action, $nameGroup, $cantMin, $cantMax]);
        
        $aResponse      = array(
                            'status' => true,
                            'message' => 'Created successfully'
                            );
        
        return $aResponse;
    }

    public function getGroup(Request $request) {
        $action = "GET-ALL-GROUP";
        $group  = DB::select("CALL SP_GROUP_COMPLEMENTS(?,0,NULL,NULL,NULL)", [$action]);

        $aResponse      = array(
                            'status' => true,
                            'message' => 'Query successfully',
                            'data' => $group
                            );
        
        return $aResponse;
    }

    public function getByGroup(Request $request) {
        $aRequest   = $request->all();
        $action     = "GET-BY-GROUP";
        $groupId    = $aRequest['groupId'];
        $group      = DB::select("CALL SP_GROUP_COMPLEMENTS(?,?,NULL,NULL,NULL)", [$action, $groupId]);

        $aResponse  = array(
                        'status' => true,
                        'message' => 'Query successfully',
                        'data' => $group
                    );
        
        return $aResponse;
    }

    public function updateGroup(Request $request) {
        $aRequest   = $request->all();
        $action     = "UPDATE-GROUP";
        $groupId    = $aRequest['groupId'];
        $nameGroup  = $aRequest['nameGroup'];
        $cantMin    = $aRequest['cantMin'];
        $cantMax    = $aRequest['cantMax'];
        DB::statement("CALL SP_GROUP_COMPLEMENTS(?,?,?,?,?)", [$action, $groupId, $nameGroup, $cantMin, $cantMax]);
        
        $aResponse      = array(
                            'status' => true,
                            'message' => 'Update successfully'
                            );
        
        return $aResponse;
    }

    public function deleteGroup(Request $request) {
        $aRequest   = $request->all();
        $action     = "DELETE-GROUP";
        $groupId    = $aRequest['groupId'];
        $intGroupId = intval($groupId);

        $ordersCom  = DB::select('SELECT * FROM menu_complement MC INNER JOIN menu_items MI ON MI.id = MC.menuId WHERE MC.complementId = ?', [$intGroupId]);

        $order_menu = DB::select('SELECT * FROM order_menu_complement WHERE groupId = ?', [$intGroupId]);

        $arrayOrdersComplement = count($ordersCom);
        $arrayOrder = count($order_menu);
        
        if ($arrayOrdersComplement > 0) {

            $arrayC = [];
            for($i = 0; $i < $arrayOrdersComplement; $i++) {
                array_push($arrayC, $ordersCom[$i]->title);
            }

            $stringMenu = implode(",", $arrayC);

            $aResponse  = array(
                        'status' => false,
                        'message' => 'No puede elminarse, ya que se encuentra vinculados a en ('.$stringMenu . ')'
                    );
        } else {
            if ($arrayOrder > 0) {
            $aResponse  = array(
                        'status' => false,
                        'message' => 'Tiene ordenes asignadas a este grupo de complementos, no se puede eliminar'
                    );
            } else {
                DB::select("CALL SP_GROUP_COMPLEMENTS(?,?,NULL,NULL,NULL)", [$action, $groupId]);

                $aResponse  = array(
                                'status' => true,
                                'message' => 'Delete successfully'
                            );
            }
        }
        
        return $aResponse;
    }
    
    public function newComplements(Request $request){
        $aRequest       = $request->all();
        $action         = "CREATE-COMPLEMENT";
        $name           = $aRequest['name'];
        $price          = $aRequest['price'];
        $groupId        = $aRequest['groupId'];
        
        DB::statement("CALL SP_COMPLEMENTS(?,0,?,?,?)", [$action, $name, $price, $groupId]);
        
        $aResponse      = array(
                            'status' => true,
                            'message' => 'Created successfully'
                            );
        
        return $aResponse;
    }

    public function getComplements(Request $request) {
        $action         = "GET-COMPLEMENT";
        $complements    = DB::select("CALL SP_COMPLEMENTS(?,0,NULL,NULL,NULL)", [$action]);

        $aResponse      = array(
                            'status' => true,
                            'message' => 'Query successfully',
                            'data' => $complements
                            );
        
        return $aResponse;
    }

    public function getByComplements(Request $request) {
        $aRequest       = $request->all();
        $action         = "GET-BY-COMPLEMENT";
        $groupId        = $aRequest['groupId'];
        $complements    = DB::select("CALL SP_COMPLEMENTS(?,0,NULL,NULL,?)", [$action, $groupId]);

        $aResponse      = array(
                            'status' => true,
                            'message' => 'Query successfully',
                            'data' => $complements
                            );
        
        return $aResponse;
    }

    public function updateComplement(Request $request) {
        $aRequest       = $request->all();
        $action         = "UPDATE-COMPLEMENT";
        $complementId   = $aRequest['complementId'];
        $name           = $aRequest['name'];
        $price          = $aRequest['price'];
        
        DB::statement("CALL SP_COMPLEMENTS(?,?,?,?,0)", [$action, $complementId, $name, $price]);
        
        $aResponse      = array(
                            'status' => true,
                            'message' => 'Update successfully'
                            );
        
        return $aResponse;
    }

    public function deleteComplement(Request $request) {
        $aRequest       = $request->all();
        $action         = "DELETE-COMPLEMENT";
        $complementId   = $aRequest['complementId'];
        $intComplement  = intval($complementId);

        $orderMenuArray = DB::select('SELECT * FROM order_menu_complement WHERE complementId = ?', [$intComplement]);

        if (count($orderMenuArray) > 0) {
            $aResponse  = array(
                        'status' => false,
                        'message' => 'Tiene ordenes asignadas a este complementos, no se puede eliminar'
                    );
        } else {
            DB::statement("CALL SP_COMPLEMENTS(?,?,NULL,NULL,0)", [$action, $complementId]);
        
            $aResponse      = array(
                                'status' => true,
                                'message' => 'Delete successfully'
                                );
        }
        
        return $aResponse;
    }

    public function addMenuComplement(Request $request){
        $aRequest   = $request->all();
        $groups     = $aRequest['groups'];
        $menuId     = $aRequest["menuId"];

        //DB::statement("CALL SP_MENUCOMPLEMENT(?,?,0)", ["DELETE-MENU", $menuId]);
        if (!empty($groups)){
            DB::statement("CALL SP_MENUCOMPLEMENT(?,?,0)", ["DELETE-MENU", $menuId]);
            for($i=0; $i < count($groups); $i++){
                $groupId = $groups[$i]["groupId"];    
                DB::statement("CALL SP_MENUCOMPLEMENT(?,?,?)", ["CREATE-MENU", $menuId,$groupId]);
                $message = "Add menu complement successfully";
            }
        } else {
            DB::statement("CALL SP_MENUCOMPLEMENT(?,?,0)", ["DELETE-MENU", $menuId]);
            $message = "Delete menu complement successfully";
        }
        
        $aResponse      = array(
            'status' => true,
            'message' => $message
            );

        return $aResponse;
    }

    public function getMenuComplement(Request $request){
        $aRequest   = $request->all();
        $menuId     = $aRequest['menuId'];

        $menuComplement     = DB::select("CALL SP_MENUCOMPLEMENT(?,?,0)", ["GET-MENU", $menuId]);

        $aResponse      = array(
            'status' => true,
            'message' => 'Query successfully',
            'data' => $menuComplement
            );

        return $aResponse;
    }

    public function getGroupComplement(Request $request) {
        $action     = "GET-MENU-COMPLEMENT";
        $aRequest   = $request->all();
        $menuId     = $aRequest['menuId'];
        $group      = DB::select("CALL SP_GROUP_COMPLEMENTS(?,?,NULL,NULL,NULL)", [$action, $menuId]);
        $groupComplement = array();

        for ($i = 0; $i < count($group); $i++) {
            $actionCompl    = "GET-BY-COMPLEMENT";
            $groupId        = $group[$i]->groupId;
            $complements    = DB::select("CALL SP_COMPLEMENTS(?,0,NULL,NULL,?)", [$actionCompl, $groupId]);
            $count          = count($complements);
            if ( $count > 0 ) {
                $group[$i]->complement = $complements;
                array_push($groupComplement, $group[$i]);
            }
        }

        $aResponse      = array(
            'status' => true,
            'message' => 'Query successfully',
            'data' => $groupComplement
            );

        return $aResponse;
    }
    
}
