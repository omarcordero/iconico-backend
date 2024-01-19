<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Admin\MenuItemRequest;
use App\Http\Requests\Admin\MenuItemUpdateRequest;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\MenuItemChoice;
use App\Models\MenuItemGroup;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Validator;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $menuItems = MenuItem::whereRaw('1=1');

        $currentStore = admin_get_store();
        if($currentStore) {
            $menuItems = $menuItems->where('store_id', $currentStore->id);
        }

        if ($request->store_like) {
            $name = $request->store_like;
            $menuItems = $menuItems->whereHas('store', function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            });
        }

        if($request->title_like) {
            $menuItems = $menuItems->where('title', 'like', '%' . $request->title_like . '%');
        }

        if($request->price_like) {
            $menuItems = $menuItems->where('price', 'like', '%' . $request->price_like . '%');
        }

        if($request->status_like) {
            $menuItems = $menuItems->where('status', 'like', '%' . $request->status_like . '%');
        }

        return response()->json($menuItems->with('store')->paginate(config('constants.paginate_per_page')));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'detail' => 'required',
            'specification' => 'required',
            'price' => 'required',
            'is_available' => 'in:0,1',
            'is_non_veg' => 'in:0,1',
            'image' => 'file',
            'store_id' => 'required|exists:stores,id',
            'categories' => 'required|array|exists:categories,id',
            'status' => 'required|in:pending,rejected,approved',
        ]);

        $menuitem = new MenuItem();

        $menuitem->fill($request->all());

        if($request->image) {
            $path = $request->file('image')->store('uploads');
            $menuitem->image_url = Storage::url($path);
        }

        $menuitem->save();

        /* handle categories */

        // attach categories with menu item
        foreach($request->categories as $categoryId) {
            $menuitem->categories()->attach($categoryId);
        }

        // attach categories with store
        sync_categories_to_store($request->store_id);

        return response()->json($menuitem->refresh(), 201);
    }

    public function show(MenuItem $menuitem)
    {
        admin_authorize_store($menuitem->store_id);

        return response()->json($menuitem);
    }

    public function update(Request $request, MenuItem $menuitem)
    {
        admin_authorize_store($menuitem->store_id);

        $request->validate([
            'title' => 'required',
            'detail' => 'required',
            'specification' => 'required',
            'price' => 'required',
            'is_available' => 'in:0,1',
            'is_non_veg' => 'in:0,1',
            'image' => 'file',
            'categories' => 'required|array|exists:categories,id',
            'status' => 'required|in:pending,rejected,approved',
            'groups' => 'array',
            'groups.*.title' => 'required',
            'groups.*.min_choices' => 'required|integer',
            'groups.*.max_choices' => 'required|integer',
            'groups.*.choices' => 'array|required',
            'groups.*.choices.*.title' => 'required',
            'groups.*.choices.*.price' => 'required',
        ]);

        $menuitem->fill($request->except(['store_id']));

        if($request->image) {
            $path = $request->file('image')->store('uploads');
            $menuitem->image_url = Storage::url($path);
        }

        $menuitem->save();

        /* handle categories */

        // detach existing categories
        $menuitem->categories()->detach();

        // attach categories with menu item
        foreach($request->categories as $categoryId) {
            $menuitem->categories()->attach($categoryId);
        }

        /* handle item choices */

        // delete all groups first, if we haven't got any group in request we assume that user wants to clear the group
        MenuItemGroup::where('menu_item_id', $menuitem->id)->delete();

        if($request->has('groups')) {
            foreach ($request->groups as $group) {

                $menuItemGroup = new MenuItemGroup();
                $menuItemGroup->title = $group['title'];
                $menuItemGroup->min_choices = $group['min_choices'];
                $menuItemGroup->max_choices = $group['max_choices'];
                $menuItemGroup->menu_item_id = $menuitem->id;
                $menuItemGroup->save();

                // update or create choices
                foreach ($group['choices'] as $choice) {
                    $menuItemChoice = new MenuItemChoice();
                    $menuItemChoice->title = $choice['title'];
                    $menuItemChoice->price = $choice['price'];
                    $menuItemChoice->menu_item_group_id = $menuItemGroup->id;
                    $menuItemChoice->save();
                }
            }
        }

        // attach categories with store
        sync_categories_to_store($menuitem->store_id);

        return response()->json($menuitem->refresh(), 200);
    }

    public function destroy(MenuItem $menuitem)
    {
        admin_authorize_store($menuitem->store_id);

        $menuitem->delete();
        return response()->json([], 204);
    }
}
