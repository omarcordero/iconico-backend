<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\ActiveLog;
use App\Models\Auth\User\User;
use App\Models\DeliveryProfile;
use App\Models\Earning;
use App\Models\Order;
use App\Models\Store;
use Arcanedev\LogViewer\Entities\Log;
use Arcanedev\LogViewer\Entities\LogEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function orderAnalytics(Request $request)
    {
        $currentStore = admin_get_store();
        if ($currentStore) {
            $ordersChartData = Order::select(DB::raw('DATE(created_at) as created_at'), DB::raw('count(*) as total'))
                ->whereDate('created_at', '>', Carbon::now()->subDays(30))
                ->whereDate('created_at', '<', Carbon::now())
                ->where('store_id', $currentStore->id)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get();

            $summary = [
                ["title" => "Total", "value" => Order::whereRaw("1=1")->where('store_id', $currentStore->id)->count()],
                ["title" => "Último mes", "value" => Order::whereDate('created_at', '>', Carbon::now()->subDays(30))->where('store_id', $currentStore->id)->count()],
                ["title" => "Última semana", "value" => Order::whereDate('created_at', '>', Carbon::now()->subDays(7))->where('store_id', $currentStore->id)->count()],
                ["title" => "Hoy", "value" => Order::whereDate('created_at', '>', Carbon::now())->where('store_id', $currentStore->id)->count()]
            ];
        } else {
            $ordersChartData = Order::select(DB::raw('DATE(created_at) as created_at'), DB::raw('count(*) as total'))
                ->whereDate('created_at', '>', Carbon::now()->subDays(30))
                ->whereDate('created_at', '<', Carbon::now())
                ->groupBy(DB::raw('DATE(created_at)'))
                ->get();

            $summary = [
                ["title" => "Total", "value" => Order::whereRaw("1=1")->count()],
                ["title" => "Last Month", "value" => Order::whereDate('created_at', '>', Carbon::now()->subDays(30))->count()],
                ["title" => "Last Week", "value" => Order::whereDate('created_at', '>', Carbon::now()->subDays(7))->count()],
                ["title" => "Today", "value" => Order::whereDate('created_at', '>', Carbon::now())->count()]
            ];
        }


        $chartLabel = array_map([$this, "mapDayName"], $ordersChartData->pluck('created_at')->toArray());

        return response()->json([
            "chart" => [
                "chartLabel" => $chartLabel,
                "linesData" => [$ordersChartData->pluck("total")]
            ],
            "summary" => $summary
        ]);
    }

    public function userAnalytics(Request $request)
    {
        $usersChartData = User::select(DB::raw('DATE(created_at) as created_at'), DB::raw('count(*) as total'))
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->whereDate('created_at', '<', Carbon::now())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        $chartLabel = array_map([$this, "mapDayName"], $usersChartData->pluck('created_at')->toArray());

        return response()->json([
            "chart" => [
                "chartLabel" => $chartLabel,
                "linesData" => [$usersChartData->pluck("total")]
            ],
        ]);

    }

    public function userStatitics(Request $request)
    {
        $totalUsers = User::count();

        $customers = User::whereHas('roles', function ($query) {
            $query->where('name', 'customer');
        })->count();

        $stores = User::whereHas('roles', function ($query) {
            $query->where('name', 'owner');
        })->count();

        return response()->json(["total" => $totalUsers, "customers" => $customers, "stores" => $stores]);
    }

    public function activeOrders(Request $request)
    {
        $orders = Order::whereIn('status', ['new', 'pending', 'accepted', 'preparing', 'dispatched', 'intransit', 'complete']);

        $currentStore = admin_get_store();
        if ($currentStore) {
            $orders = $orders->where('store_id', $currentStore->id);
        }
        return response()->json($orders->get());
    }

    public function activeDelivery(Request $request)
    {
        $deliveries = DeliveryProfile::where('is_online', 1);

        return response()->json($deliveries->get());
    }

    public function dailyUserAnalytics(Request $request)
    {
        $dailyActiveUsersData = ActiveLog::select(DB::raw('DATE(created_at) as created_at'), DB::raw('count(*) as total'))
            ->whereDate('created_at', '>', Carbon::now()->subDays(7))
            ->whereDate('created_at', '<', Carbon::now())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        return response()->json([
            "weeks" => array_map([$this, "mapDayLongName"], $dailyActiveUsersData->pluck('created_at')->toArray()),
            "count" => $dailyActiveUsersData->pluck('total')
        ]);
    }

    private function mapDayName($date)
    {
        return $date->format("d");
    }

    private function mapDayLongName($date)
    {
        return $date->format("D");
    }
}
