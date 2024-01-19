<?php

namespace App\Http\Controllers\Api\ICONICO;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    
    public function getAll(Request $request){
        $settings = DB::table('settings')->select('value')->get();

        $arraySettings = array(
            'text_coupon' => $settings[15],
            'url_coupon' => $settings[16],
            'file_coupon' => $settings[17],
            'text_big' => $settings[18]
        );

        return array(
            'status' => true,
            'message' => 'Query successfully',
            'data' => $arraySettings
        );
    }

    public function getAllImagenNotNull() {
        $settings = DB::table('settings_images')->where('upload', '!=', null)->get();

        return array(
                'status' => true,
                'message' => 'Query successfully',
                'data' => $settings
            );
    }
    
}
