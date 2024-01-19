<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiOrderUpdateRequest;
use App\Http\Requests\Customer\ApiOrderCreateRequest;
use App\Http\Requests\Delivery\ApiDeliveryProfileUpdateRequest;
use App\Models\DeliveryProfile;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryProfileController extends Controller
{

    /**
     * Display the store of current logged in user
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()->json(Auth::user()->deliveryProfile);
    }

    public function update(ApiDeliveryProfileUpdateRequest $request) {
        $delivery_profile = Auth::user()->deliveryProfile;
        $delivery_profile->fill($request->all());
        $delivery_profile->save();

        return response()->json(DeliveryProfile::find($delivery_profile->id));
    }
}
