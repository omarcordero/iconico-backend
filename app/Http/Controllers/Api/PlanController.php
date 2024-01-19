<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiStripePaymentRequest;
use App\Models\Auth\User\User;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\UserPreference;
use Carbon\Carbon;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rennokki\Plans\Models\PlanModel;
use Rinvex\Subscriptions\Models\Plan;

class PlanController extends Controller
{
    /**
     * Get list of plans
     */
    public function plans()
    {
        return response()->json(PlanModel::with('features')->get());
    }

    public function planDetails()
    {
        $user = Auth::user();

        return response([
            "subscription" => $user->activeSubscription(),
            "active" => $user->hasActiveSubscription()
        ]);
    }

    public function makeStripePayment(PlanModel $plan, ApiStripePaymentRequest $request)
    {
        if(env('DEMO_SKIP_PAYMENT')) {
            $this->onPaymentSuccess($plan);
            return response()->json(["status" => true]);
        }

        $amount = number_format((float)$plan->price, 2, '.', '');
        $currency = Setting::where('key', 'currency')->first()->value;

        try {
            $token = $request->token;
            $charge = Stripe::charges()->create([
                'amount' => $amount,
                'currency' => strtolower($currency),
                'description' => 'Payment for Plan ' . $plan->name,
                'source' => $token,
            ]);

            $this->onPaymentSuccess($plan);

            return response()->json(["status" => true, 'charge' => $charge]);
        } catch(\Exception $ex) {
            abort(400);
        }
    }

    public function inAppPayment(PlanModel $plan)
    {
        if(!$plan) {
            abort(404);
        }

        $this->onPaymentSuccess($plan);
        return response()->json(["status" => true]);
    }

    private function onPaymentSuccess(PlanModel $plan)
    {
        $user = Auth::user();

        $user->subscribeTo($plan, $plan->duration);

        // create a transaction for admin
        $admin = User::find(1);
        Transaction::create([
            'title' => 'Plan Purchase',
            'description' => 'Purchased ' . $plan->name . ' plan',
            'status' => 'credit',
            'amount' => $plan->price,
            'user_id' => $admin->id,
            'source' => 'store'
        ]);
    }
}
