<?php

namespace App\Http\Controllers\Admin\Json;

use App\Events\Auth\Registered;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Validator;

class DashboardController extends Controller
{
    /**
     * Orders chart data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ordersChartData(Request $request)
    {
        $ordersChartData = Order::select(DB::raw('DATE(created_at) as created_at'), DB::raw('count(*) as total'))
            ->whereDate('created_at', '>', $request->from)
            ->whereDate('created_at', '<', $request->to)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        return response()->json(["ordersChartData" => $ordersChartData]);
    }

    /**
     * Orders chart data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function usersChartData(Request $request)
    {
        $usersChartData = User::select(DB::raw('DATE(created_at) as created_at'), DB::raw('count(*) as total'))
            ->whereDate('created_at', '>', $request->from)
            ->whereDate('created_at', '<', $request->to)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
        return response()->json(["usersChartData" => $usersChartData]);
    }
}
