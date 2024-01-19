<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActiveLog;
use App\Models\Auth\User\User;
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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $counts = [
            'customers' => User::whereHas('roles', function($query) {
                $query->where('name', 'customer');
            })->count(),
            'stores' => User::whereHas('roles', function($query) {
                $query->where('name', 'owner');
            })->count(),
            'deliveryProfiles' => User::whereHas('roles', function($query) {
                $query->where('name', 'delivery');
            })->count(),
        ];

        $orderCounts = [
            'total' => Order::count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'pending' => Order::where('status', 'pending')->count(),
        ];

        $earningsCount= [
          'total' => Earning::sum('amount')
        ];

        $activeOrders = Order::whereIn('status', ['new', 'pending', 'accepted', 'preparing', 'dispatched', 'intransit'])->get();

        // orders chart
        $ordersChartData = Order::select(DB::raw('DATE(created_at) as created_at'), DB::raw('count(*) as total'))
            ->whereDate('created_at', '>', Carbon::now()->subDays(29))
            ->whereDate('created_at', '<', Carbon::now())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // registration chart
        $usersChartData = User::select(DB::raw('DATE(created_at) as created_at'), DB::raw('count(*) as total'))
            ->whereDate('created_at', '>', Carbon::now()->subDays(29))
            ->whereDate('created_at', '<', Carbon::now())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // trending stores
        $trendingStores = Order::select('*', DB::raw('count(*) as total'))
            ->groupBy('store_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'counts' => $counts,
            'orderCounts' => $orderCounts,
            'earningsCount' => $earningsCount,
            'activeOrders' => $activeOrders,
            'ordersChartData' => $ordersChartData,
            'usersChartData' => $usersChartData,
            'trendingStores' => $trendingStores,
        ]);
    }

    public function dailyUserAnalytics(Request $request)
    {
        $dailyActiveUsersData = ActiveLog::select(DB::raw('DATE(created_at) as created_at'), DB::raw('count(*) as total'))
            ->whereDate('created_at', '>', Carbon::now()->subDays(7))
            ->whereDate('created_at', '<', Carbon::now())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        return response()->json([
            "weeks" => array_map([$this, "mapDayName"], $dailyActiveUsersData->pluck('created_at')->toArray()),
            "count" => $dailyActiveUsersData->pluck('total')
        ]);
    }


    public function getLogChartData(Request $request)
    {
        \Validator::make($request->all(), [
            'start' => 'required|date|before_or_equal:now',
            'end' => 'required|date|after_or_equal:start',
        ])->validate();

        $start = new Carbon($request->get('start'));
        $end = new Carbon($request->get('end'));

        $dates = collect(\LogViewer::dates())->filter(function ($value, $key) use ($start, $end) {
            $value = new Carbon($value);
            return $value->timestamp >= $start->timestamp && $value->timestamp <= $end->timestamp;
        });


        $levels = \LogViewer::levels();

        $data = [];

        while ($start->diffInDays($end, false) >= 0) {

            foreach ($levels as $level) {
                $data[$level][$start->format('Y-m-d')] = 0;
            }

            if ($dates->contains($start->format('Y-m-d'))) {
                /** @var  $log Log */
                $logs = \LogViewer::get($start->format('Y-m-d'));

                /** @var  $log LogEntry */
                foreach ($logs->entries() as $log) {
                    $data[$log->level][$log->datetime->format($start->format('Y-m-d'))] += 1;
                }
            }

            $start->addDay();
        }

        return response($data);
    }

    public function getRegistrationChartData()
    {

        $data = [
            'registration_form' => User::whereDoesntHave('providers')->count(),
            'google' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'google');
            })->count(),
            'facebook' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'facebook');
            })->count(),
            'twitter' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'twitter');
            })->count(),
        ];

        return response($data);
    }
}
