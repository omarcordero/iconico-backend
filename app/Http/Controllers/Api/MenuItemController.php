<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiMenuItemCreateRequest;
use App\Http\Requests\ApiMenuItemUpdateRequest;
use App\Http\Requests\ApiMenuItemUpdateStatusRequest;
use App\Models\MenuItem;
use App\Models\MenuItemChoice;
use App\Models\MenuItemGroup;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status = $request->status && $request->status == 'pending'  ? ['pending', 'rejected'] : ["approved"];
        $items = MenuItem::where('store_id', Auth::user()->store->id)->whereIn('status', $status)
            ->orderBy('title', 'desc')->paginate(config('constants.paginate_per_page'));
        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ApiMenuItemCreateRequest $request)
    {
        $item = new MenuItem();
        $item->fill($request->all());
        $item->store_id = Auth::user()->store->id;
        $item->status = 'pending';
        $item->save();

        /* handle item choices */
        if($request->has('groups')) {
            foreach ($request->groups as $group) {

                // create choice group
                $menuItemGroup = new MenuItemGroup();
                $menuItemGroup->title = $group['title'];
                $menuItemGroup->min_choices = $group['min_choices'];
                $menuItemGroup->max_choices = $group['max_choices'];
                $menuItemGroup->menu_item_id = $item->id;
                $menuItemGroup->save();

                // create choices
                foreach ($group['choices'] as $choice) {
                    $menuItemChoice = new MenuItemChoice();
                    $menuItemChoice->title = $choice['title'];
                    $menuItemChoice->price = $choice['price'];
                    $menuItemChoice->menu_item_group_id = $menuItemGroup->id;
                    $menuItemChoice->save();
                }
            }
        }

        /* handle categories */

        // attach categories with menu item
        foreach($request->categories as $categoryId) {
            $item->categories()->attach($categoryId);
        }

        // attach categories with store
        sync_categories_to_store(Auth::user()->store->id);

        return response()->json(MenuItem::find($item->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MenuItem  $menuItem
     * @return \Illuminate\Http\Response
     */
    public function show(MenuItem $menuItem)
    {
        return response()->json($menuItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MenuItem  $menuItem
     * @return \Illuminate\Http\Response
     */
    public function update(ApiMenuItemUpdateRequest $request, MenuItem $menuItem)
    {
        $menuItem->fill($request->all());
        $menuItem->save();

        /* handle item choices */
        if($request->has('groups')) {
            foreach ($request->groups as $group) {

                // update or create choice group
                if(array_key_exists('id', $group)) {
                    $menuItemGroup = MenuItemGroup::find($group['id']);
                } else {
                    $menuItemGroup = new MenuItemGroup();
                }
                $menuItemGroup->title = $group['title'];
                $menuItemGroup->min_choices = $group['min_choices'];
                $menuItemGroup->max_choices = $group['max_choices'];
                $menuItemGroup->menu_item_id = $menuItem->id;
                $menuItemGroup->save();

                // update or create choices
                foreach ($group['choices'] as $choice) {
                    if(array_key_exists('id', $choice)) {
                        $menuItemChoice = MenuItemChoice::find($choice['id']);
                    } else {
                        $menuItemChoice = new MenuItemChoice();
                    }
                    $menuItemChoice->title = $choice['title'];
                    $menuItemChoice->price = $choice['price'];
                    $menuItemChoice->menu_item_group_id = $menuItemGroup->id;
                    $menuItemChoice->save();
                }
            }
        }

        /* handle categories */

        // detach existing categories
        $menuItem->categories()->detach();

        // attach categories with menu item
        foreach($request->categories as $categoryId) {
            $menuItem->categories()->attach($categoryId);
        }

        // attach categories with store
        sync_categories_to_store(Auth::user()->store->id);

        return response()->json(MenuItem::find($menuItem->id));
    }

    /**
     * Update the status of menu item
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MenuItem  $menuItem
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(ApiMenuItemUpdateStatusRequest $request, MenuItem $menuItem)
    {
        $menuItem->fill($request->all());
        $menuItem->save();
        return response()->json($menuItem);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MenuItem  $menuItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(MenuItem $menuItem)
    {
        try {
            $menuItem->delete();
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 400);
        }
    }
}
