<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SettingUpdateRequest;
use App\Models\Earning;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Validator;

class EarningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $earnings = Earning::whereRaw('1=1');

        $currentStore = admin_get_store();
        if($currentStore) {
            $earnings = $earnings->where('user_id', $currentStore->owner_id);
        }


        // keyword search
        if ($request->search) {
            $earnings = $earnings->whereHas('user', function ($subquery) use ($request) {
                $subquery->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $request->search . '%');
            });
        }

        if($request->input('user_id'))  {
            $earnings = $earnings->where('user_id', $request->input('user_id'));
        }

        if($request->input('order_id'))  {
            $earnings = $earnings->where('order_id', $request->input('order_id'));
        }

        if($request->input('from'))  {
            $earnings = $earnings->where('created_at', '>=', $request->input('from'));
        }

        if($request->input('to'))  {
            $earnings = $earnings->where('created_at', '<=' ,$request->input('to'));
        }

        $counts = [
            'total_earnings' => (clone $earnings)->sum('amount'),
            'paid_earnings' => (clone $earnings)->where('paid', 1)->sum('amount'),
            'unpaid_earnings' => (clone $earnings)->where('paid', 0)->sum('amount')

        ];

        if($request->input('paid'))  {
            $earnings = $earnings->where('paid', $request->input('paid'));
        }

        return view('admin.earnings.index', ['earnings' => $earnings->paginate(), 'counts' => $counts]);
    }

    /**
     * Show the specified resource.
     *
     * @param Earning $earning
     * @return \Illuminate\Http\Response
     */
    public function edit(Earning $earning)
    {
        return view('admin.earnings.show', ['earning' => $earning]);
    }
}
