<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiStoreUpdateRequest;
use App\Http\Requests\Customer\ApiStoreListRequest;
use App\Models\Favourite;
use App\Models\MenuItem;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\User\User;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ApiStoreListRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(ApiStoreListRequest $request)
    {
        $user = $request->user_id ? User::find($request->user_id) : null;

        $stores = Store::search($user, $request)->paginate(config('constants.paginate_per_page'));
        return response()->json($stores);
    }

    public function menuItemSearch(Request $request)
    {
        $user = $request->user_id ? User::find($request->user_id) : null;

        $request->validate([
            'dish_name' => 'required|string',
        ]);

        $menuItems = MenuItem::search($user, $request)->paginate(config('constants.paginate_per_page'));
        return response()->json($menuItems);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        $items = MenuItem::where('store_id', $store->id)->where('status', 'approved')->where('is_available', true)->orderBy('title', 'desc')->get();

        $response = [
            "store" => $store,
            "menu_items" => $items
        ];

        return response()->json($response);
    }

    private function _itemsInCategory($categoryId, $items)
    {
        $menuItems = [];
        foreach ($items as $item) {
            foreach ($item->categories as $cat) {
                if ($cat->id === $categoryId) {
                    $menuItems[] = $item;
                }
            }
        }
        return $menuItems;
    }
}
