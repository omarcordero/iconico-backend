<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\OrderUpdateRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = Order::whereRaw('1=1');

        $currentStore = admin_get_store();
        if($currentStore) {
            $orders = $orders->where('store_id', $currentStore->id);
        }

        if ($request->user) {
            $orders = $orders->where('user_id', $request->user);
        }

        if ($request->store) {
            $orders = $orders->where('store_id', $request->store);
        }

        if ($request->delivery_profile_id) {
            $orders = $orders->where('delivery_profile_id', $request->delivery_profile_id);
        }

        if ($request->status) {
            $orders = $orders->where('status', $request->status);
        }

        if ($request->delivery_status) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
        }

        if ($request->payment_status) {
            $orders = $orders->where('payment_status', $request->payment_status);
        }

        if ($request->input('from')) {
            $orders = $orders->where('created_at', '>=', $request->input('from'));
        }

        if ($request->input('to')) {
            $orders = $orders->where('created_at', '<=', $request->input('to'));
        }

        // keyword search
        if ($request->search) {
            $orders = $orders->where(function ($query) use ($request) {
                $query->whereHas('user', function ($subquery) use ($request) {
                    $subquery->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('mobile_number', 'like', '%' . $request->search . '%');
                })
                    ->orWhereHas('store', function ($subquery) use ($request) {
                        $subquery->where('name', 'like', '%' . $request->search . '%')
                            ->orWhere('tagline', 'like', '%' . $request->search . '%')
                            ->orWhere('address', 'like', '%' . $request->search . '%')
                            ->orWhere('area', 'like', '%' . $request->search . '%')
                            ->orWhere('details', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('address', function ($subquery) use ($request) {
                        $subquery->where('address', 'like', '%' . $request->search . '%');
                    })
                    ->orWhere('total', $request->search);
            });
        }

        return view('admin.orders.index', ['orders' => $orders->orderBy('created_at', 'desc')->paginate()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Ordered a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Order $order)
    {
        admin_authorize_store($order->store_id);

        return view('admin.orders.show', ['order' => $order]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Order $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        return view('admin.orders.edit', ['order' => $order]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OrderUpdateRequest $request
     * @param Order $order
     * @return mixed
     */
    public function update(OrderUpdateRequest $request, Order $order)
    {
        $order->fill($request->all());
        $order->save();

        return redirect()->intended(route('admin.orders'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
