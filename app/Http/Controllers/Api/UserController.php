<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReferRequest;
use App\Http\Requests\ApiStoreUpdateRequest;
use App\Models\Auth\User\User;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display the store of current logged in user
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()->json(Auth::user());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $user->fill($request->only(['fcm_registration_id']));
        $user->save();

        return response()->json($user);
    }

    public function refer(ReferRequest $request)
    {
        // check if user has already used the referral code
        if(DB::table('refer')->where('user_id', Auth::user()->id)->exists()) {
            return response()->json(["message" => "Already referred"], 403);
        }

        // check if user is not referring himself
        if(Auth::user()->refer_code == $request->code) {
            return response()->json(["message" => "You cannot refer yourself"], 403);
        }

        // get the referrer user
        $referrer = User::where('refer_code', $request->code)->firstOrFail();

        // get the referral amount
        $referAmountSetting = Setting::where('key', 'refer_amount')->firstOrFail();
        $referAmount = $referAmountSetting->value ? $referAmountSetting->value : 0;

        DB::table('refer')->insert(["referrer" => $referrer->id, "user_id" => Auth::user()->id]);

        // credit the amount to referrer
        $referrer->deposit($referAmount);

        // create a transaction for refrrer
        Transaction::create([
            'title' => 'Referral Reward',
            'description' => 'Amount added for referral',
            'status' => 'credit',
            'amount' => $referAmount,
            'user_id' => $referrer->id,
            'source' => 'refer'
        ]);

        // credit the amount to user
        Auth::user()->deposit($referAmount);

        // create a transaction for user
        Transaction::create([
            'title' => 'Referral Reward',
            'description' => 'Amount added for referral',
            'status' => 'credit',
            'amount' => $referAmount,
            'user_id' => Auth::user()->id,
            'source' => 'refer'
        ]);

        return response()->json(["message" => "Referal Successfull"], 200);
    }
    
}
