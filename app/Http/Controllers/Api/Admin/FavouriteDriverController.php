<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ApiRatingCreateRequest;
use App\Models\DeliveryProfile;
use App\Models\Favourite;
use App\Models\FavouriteDriver;
use App\Models\Rating;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class FavouriteDriverController extends Controller
{
    public function store(DeliveryProfile $deliveryprofile)
    {
        $currentStore = admin_get_store();
        if($currentStore) {
            $fav = FavouriteDriver::where('store_id', $currentStore->id)->where('delivery_profile_id', $deliveryprofile->id)->first();
            if($fav === null) {
                // mark favourite
                $fav = new Favourite();
                $fav->delivery_profile_id = $deliveryprofile->id;
                $fav->store_id = $currentStore->id;
                $fav->save();
                return response()->json(["favourite" => 1]);
            } else {
                $fav->delete();
                return response()->json(["favourite" => 0]);
            }
        }
        abort(403);
    }
}
