<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Http\Requests\Admin\CouponRequest;
use App\Http\Requests\Admin\CouponUpdateRequest;
use App\Models\Category;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Validator;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.coupons.index', ['coupons' => Coupon::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Category a newly created resource in storage.
     *
     * @param CouponRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CouponRequest $request)
    {
        $coupon = new Coupon();

        $coupon->fill($request->all());

        $coupon->save();

        return redirect()->intended(route('admin.coupons'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Coupon $coupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', ['coupon' => $coupon]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CouponUpdateRequest $request
     * @param Coupon $coupon
     * @return mixed
     */
    public function update(CouponUpdateRequest $request, Coupon $coupon)
    {
        $coupon->fill($request->all());
        $coupon->save();

        return redirect()->intended(route('admin.coupons'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Coupon $coupon
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->users()->detach();
        $coupon->delete();

        return redirect()->intended(route('admin.coupons'));
    }
}
