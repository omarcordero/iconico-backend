<?php

namespace App\Http\Controllers\Api\ICONICO;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    
    public function getAll(Request $request){
        $action     = "ALL-CATEGORIES";
        $sql        = "CALL SP_CATEGORY(?, 0)";
        $categories = DB::select($sql, [$action]);
        
        $aResponse  = array(
                            'status' => true,
                            'message' => 'Query successfully',
                            'data' => $categories
                        );
        
        return $aResponse;
    }

    public function getCategoryCountHome(Request $request){
        $varCategories  = getenv('CATEGORIES');
        $explode        = explode(',', $varCategories);

        $categories     = DB::select("CALL SP_COUNT_CATEGORIES(?,?,?)", [$explode[0], $explode[1], $explode[2]]);

        $countOrders    = DB::select("SELECT FORMAT(COUNT(*) + 36267, 0) AS count FROM orders");

        $newArray       = array(
                            'id' => 0,
                            'title' => 'Clientes satisfechos',
                            'image' => 'assets/categories/clientesatisfecho.png',
                            'count' => $countOrders[0]->count
                        );

        array_push($categories, $newArray);

        $aResponse      = array(
                            'status' => true,
                            'message' => 'Query successfully',
                            'data' => $categories
                        );
        
        return $aResponse;
    }
    
}
