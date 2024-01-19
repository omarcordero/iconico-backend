<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ApiRatingCreateRequest;
use App\Models\Rating;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function index(Store $store)
    {
        return response()->json(Rating::where('store_id', $store->id)->orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page')));
    }

    public function store(ApiRatingCreateRequest $request, Store $store)
    {
        $rating = new Rating();
        $rating->fill($request->all());
        $rating->store_id = $store->id;
        $rating->user_id = Auth::user()->id;
        $rating->save();

        return response()->json($rating);
    }

    public function show()
    {
        $ratings = Rating::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page'));
        return response()->json($ratings);
    }
}
