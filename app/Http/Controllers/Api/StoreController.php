<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiStoreUpdateRequest;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    /**
     * Display the store of current logged in user
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()->json(Auth::user()->store);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param ApiStoreUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(ApiStoreUpdateRequest $request)
    {
        $store = Auth::user()->store;
        $store->fill($request->all());
        $store->save();

        return response()->json(Store::find($store->id));
    }
}
