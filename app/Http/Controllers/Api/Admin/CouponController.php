<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Validator;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $coupon = Coupon::whereRaw("1=1");
        return response()->json($coupon->paginate(config('constants.paginate_per_page')));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons',
            'reward' => 'integer|required',
            'type' => 'required|in:fixed,percent',
            'expires_at' => 'date|nullable|after:today'
        ]);

        $coupon = new Coupon();

        $coupon->fill($request->all());

        $coupon->save();

        return response()->json($coupon, 201);
    }

    public function show(Coupon $coupon)
    {
        return response()->json($coupon);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required',
            'reward' => 'integer|required',
            'type' => 'required|in:fixed,percent',
            'expires_at' => 'date|nullable'
        ]);

        $coupon->fill($request->all());
        $coupon->save();

        return response()->json($coupon, 200);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->users()->detach();
        $coupon->delete();

        return response()->json([], 204);
    }
}
