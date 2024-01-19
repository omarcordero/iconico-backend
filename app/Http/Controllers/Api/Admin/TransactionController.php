<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\User\User;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if($request->user) {
            $user = User::find($request->user);
        }

        $transactions = Transaction::whereRaw("1=1");

        if(!$user->hasRole('administrator')) {
            $transactions = $transactions->where('user_id', $user->id);
        }

        if($request->title_like) {
            $transactions = $transactions->where('title', 'like', '%' . $request->title_like . '%');
        }

        if($request->amount_like) {
            $transactions = $transactions->where('amount', 'like', '%' . $request->amount_like . '%');
        }

        if($request->order_id_like) {
            $transactions = $transactions->where('order_id', 'like', '%' . $request->order_id_like . '%');
        }

        if ($request->user_like) {
            $email = $request->user_like;
            $transactions = $transactions->whereHas('user', function ($query) use ($email) {
                $query->where('email', 'like', '%' . $email . '%');
            });
        }

        if($request->status_like) {
            $transactions = $transactions->where('status', 'like', '%' . $request->status_like . '%');
        }

        if ($request->is_paid_like) {
            if ($request->is_paid_like == 'Yes') {
                $transactions = $transactions->where('is_paid', 1);
            }
            if ($request->is_paid_like == 'No') {
                $transactions = $transactions->where('is_paid', 0);
            }
        }

        return response()->json($transactions->orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page')));
    }

    public function earningAnalytics(Request $request)
    {
        $user = Auth::user();

        if($request->user) {
            $user = User::find($request->user);
        }

        $transactionsChartData = Transaction::select(DB::raw('DATE(created_at) as created_at'), DB::raw('SUM(amount) as total'))
            ->whereDate('created_at', '>=', Carbon::now()->subDays(365))
            ->whereDate('created_at', '<=', Carbon::now())
            ->where('status', 'credit')
            ->where('user_id', $user->id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        $summary = [
            ["title" => "Total", "value" => round(Transaction::whereRaw("1=1")->where('user_id', $user->id)->sum('amount'), 2)],
            ["title" => "Last Month", "value" => round(Transaction::whereDate('created_at', '>', Carbon::now()->subDays(30))->where('user_id', $user->id)->sum('amount'), 2)],
            ["title" => "Last Week", "value" => round(Transaction::whereDate('created_at', '>', Carbon::now()->subDays(7))->where('user_id', $user->id)->sum('amount'), 2)],
            ["title" => "Today", "value" => round(Transaction::whereDate('created_at', '>=', Carbon::now())->where('user_id', $user->id)->sum('amount'), 2)]
        ];

        $chartLabel = array_map([$this, "mapDayName"], $transactionsChartData->pluck('created_at')->toArray());

        return response()->json([
            "chart" => [
                "chartLabel" => $chartLabel,
                "linesData" => [$transactionsChartData->pluck("total")]
            ],
            "summary" => $summary
        ]);
    }

    private function mapDayName($date)
    {
        return $date->format("d");
    }

}
