<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ApiRatingCreateRequest;
use App\Models\Favourite;
use App\Models\Rating;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function index()
    {
        return response()->json(Favourite::where('user_id', Auth::user()->id)
            ->paginate(config('constants.paginate_per_page')));
    }

    public function store(Store $store)
    {
        $user = Auth::user();
        $fav = Favourite::where('user_id', $user->id)->where('store_id', $store->id)->first();
        if($fav === null) {
            // mark favourite
            $fav = new Favourite();
            $fav->user_id = $user->id;
            $fav->store_id = $store->id;
            $fav->save();
            return response()->json(["favourite" => 1]);
        } else {
            $fav->delete();
            return response()->json(["favourite" => 0]);
        }
    }
}
