<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ApiRatingCreateRequest;
use App\Models\Rating;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function index()
    {
        return response()->json(Rating::where('store_id', Auth::user()->store->id)->orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page')));
    }
}
