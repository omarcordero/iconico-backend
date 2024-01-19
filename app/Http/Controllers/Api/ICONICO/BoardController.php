<?php

namespace App\Http\Controllers\Api\ICONICO;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BoardController extends Controller
{
    
    public function getBoard(Request $request) {
        $getBoard   = DB::table('board')->get();
        
        $aResponse  = array(
                        'status' => true,
                        'message' => 'Query successfully',
                        'data' => $getBoard
                    );
        
        return $aResponse;
    }
    
}
