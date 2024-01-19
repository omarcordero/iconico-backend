<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StripePaymentRequest;
use App\Models\Setting;
use App\Models\Transaction;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function rechargeWithStripe(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'amount' => 'required|numeric'
        ]);

        $amount = number_format((float)$request->amount, 2, '.', '');
        $currency = Setting::where('key', 'currency')->first()->value;

        if(env('DEMO_SKIP_PAYMENT')) {
            $this->onPaymentSuccess($amount);
            return response()->json(["status" => true, "balance" => Auth::user()->balance]);
        }

        try {
            $token = $request->token;
            $charge = Stripe::charges()->create([
                'amount' => $amount,
                'currency' => strtolower($currency),
                'description' => 'Wallet top up',
                'source' => $token,
            ]);

            $this->onPaymentSuccess($amount);

            return response()->json(["status" => true, 'charge' => $charge, "balance" => Auth::user()->balance]);
        } catch(\Exception $ex) {
            abort(400);
        }
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|max:' . Auth::user()->balance
        ]);

        Auth::user()->withdraw($request->amount);

        Transaction::create([
            'title' => 'Withdraw from wallet',
            'description' => 'Amount withdrawn from wallet',
            'status' => 'debit',
            'amount' => $request->amount,
            'user_id' => Auth::user()->id,
            'source' => 'wallet_withdraw'
        ]);

        return response()->json(["balance" => Auth::user()->balance]);
    }

    public function checkBalance(Request $request)
    {
        return response()->json(["balance" => Auth::user()->balance]);
    }

    public function transactions(Request $request)
    {
        return response()->json(Transaction::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page')));
    }

    private function onPaymentSuccess($amount)
    {
        // update user wallet
        Auth::user()->deposit($amount);

        Transaction::create([
            'title' => 'Added to wallet',
            'description' => 'Amount added to wallet',
            'status' => 'credit',
            'amount' => $amount,
            'user_id' => Auth::user()->id,
            'source' => 'add_to_wallet'
        ]);
    }
}
