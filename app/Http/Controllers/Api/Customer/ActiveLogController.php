<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\ActiveLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActiveLogController extends Controller
{
    public function store(Request $request)
    {
        if(Auth::user()) {
            if (!ActiveLog::whereDate('created_at', Carbon::today())->where('user_id', Auth::user()->id)->exists()) {
                ActiveLog::create([
                    'user_id' => Auth::user()->id
                ]);
            }
        }
        return response()->json([], 200);
    }
}