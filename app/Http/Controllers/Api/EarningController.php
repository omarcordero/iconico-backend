<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ApiRatingCreateRequest;
use App\Models\Earning;
use App\Models\Order;
use App\Models\Rating;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EarningController extends Controller
{
    public function index()
    {
        $lastEarningDate = null;
        $totalEarnings = Transaction::where("status", "credit")->where('user_id', Auth::user()->id)->where('is_paid', 0)->sum('amount');
        $earnings = Transaction::where("status", "credit")->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page'));
        if (Transaction::where('user_id', Auth::user()->id)->where("status", "credit")->exists()) {
            $lastEarningDate = Transaction::where('user_id', Auth::user()->id)->where("status", "credit")->orderBy('created_at', 'desc')->first()->created_at->toDateString();
        }

        return response()->json(
            [
                'total_earnings' => $totalEarnings,
                'last_earning_date' => $lastEarningDate,
                'earnings' => $earnings
            ]
        );
    }

    /**
     * Display the store of current logged in user
     *
     * @param Earning $earning
     * @return \Illuminate\Http\Response
     */
    public function show(Earning $earning)
    {
        return response()->json($earning);
    }

    public function chart(Request $request)
    {
        $totalEarnings = Transaction::where("status", "credit")->where('user_id', Auth::user()->id)->sum('amount');
        $earningsChartData = Transaction::select(DB::raw('DATE(created_at) as created_at'), DB::raw('SUM(amount) as total'))
            ->whereDate('created_at', '>=', Carbon::now()->subDays(6))
            ->whereDate('created_at', '<=', Carbon::now())
            ->where('status', 'credit')
            ->where('user_id', Auth::user()->id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        $deliveryFeeChartData = Order::select(DB::raw('DATE(created_at) as created_at'), DB::raw('SUM(delivery_fee) as total'))
            ->whereDate('created_at', '>=', Carbon::now()->subDays(6))
            ->whereDate('created_at', '<=', Carbon::now())
            ->where('store_id', Auth::user()->store->id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        return response()->json([
            "total_earnings" => $totalEarnings,
            "earnings_chart_data" => $earningsChartData,
            "delivery_fee_chart_data" => $deliveryFeeChartData
        ]);
    }
}
