<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Earning;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    public function index(Request $request)
    {
        $earnings = Earning::whereRaw('1=1');

        $currentStore = admin_get_store();
        if($currentStore) {
            $earnings = $earnings->where('user_id', $currentStore->owner_id);
        }


        // keyword search
//        if ($request->search) {
//            $earnings = $earnings->whereHas('user', function ($subquery) use ($request) {
//                $subquery->where('name', 'like', '%' . $request->search . '%')
//                    ->orWhere('email', 'like', '%' . $request->search . '%')
//                    ->orWhere('mobile_number', 'like', '%' . $request->search . '%');
//            });
//        }
//
//        if($request->input('user_id'))  {
//            $earnings = $earnings->where('user_id', $request->input('user_id'));
//        }
//
//        if($request->input('order_id'))  {
//            $earnings = $earnings->where('order_id', $request->input('order_id'));
//        }
//
//        if($request->input('from'))  {
//            $earnings = $earnings->where('created_at', '>=', $request->input('from'));
//        }
//
//        if($request->input('to'))  {
//            $earnings = $earnings->where('created_at', '<=' ,$request->input('to'));
//        }
//
//        if($request->input('paid'))  {
//            $earnings = $earnings->where('paid', $request->input('paid'));
//        }

        $counts = [
            'total_earnings' => (clone $earnings)->sum('amount'),
            'paid_earnings' => (clone $earnings)->where('paid', 1)->sum('amount'),
            'unpaid_earnings' => (clone $earnings)->where('paid', 0)->sum('amount')

        ];

        return response()->json([
            "earnings" => $earnings->orderBy('title', 'desc')->paginate(config('constants.paginate_per_page')),
            "count" => $counts
        ]);
    }

    public function show(Earning $earning)
    {
        return response()->json($earning);
    }
}
