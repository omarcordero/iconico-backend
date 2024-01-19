<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiBankDetailCreateRequest;
use App\Http\Requests\ApiStoreUpdateRequest;
use App\Http\Requests\ApiSupportRequest;
use App\Models\BankDetail;
use App\Models\Store;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Create the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function store(ApiSupportRequest $request)
    {
        $support = Support::create($request->all());
        $support->save();
        return response()->json($support);
    }
}
