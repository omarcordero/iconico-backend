<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MenuItemRequest;
use App\Http\Requests\Admin\MenuItemUpdateRequest;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Validator;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $menuItems = MenuItem::whereRaw('1=1');

        $currentStore = admin_get_store();
        if($currentStore) {
            $menuItems = $menuItems->where('store_id', $currentStore->id);
        }

        if($request->store) {
            $menuItems = $menuItems->where('store_id', $request->store);
        }

        if($request->status) {
            $menuItems = $menuItems->where('status', $request->status);
        }

        return view('admin.menuitems.index', ['menuitems' => $menuItems->paginate()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currentStore = admin_get_store();

        $stores = Store::all();

        if($currentStore) {
            $stores = Store::where('store_id', $currentStore->id)->get();
        }

        $categories = Category::all();
        return view('admin.menuitems.create', ['stores' => $stores, 'categories' => $categories]);
    }

    /**
     * MenuItem a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenuItemRequest $request)
    {
        $menuitem = new MenuItem();

        $menuitem->fill($request->all());

        $menuitem->is_available = $request->is_available == 'on' ? 1 : 0;
        $menuitem->is_non_veg = $request->is_non_veg == 'on' ? 1 : 0;

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

        return redirect()->intended(route('admin.menuitems'));
    }

    /**
     * Display the specified resource.
     *
     * @param MenuItem $menuitem
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(MenuItem $menuitem)
    {
        admin_authorize_store($menuitem->store_id);

        return view('admin.menuitems.show', ['menuitem' => $menuitem]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MenuItem $menuitem
     * @return \Illuminate\Http\Response
     */
    public function edit(MenuItem $menuitem)
    {
        admin_authorize_store($menuitem->store_id);

        $stores = Store::all();
        $categories = Category::all();

        return view('admin.menuitems.edit', ['menuitem' => $menuitem, 'stores' => $stores, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MenuItemUpdateRequest $request
     * @param MenuItem $menuitem
     * @return mixed
     */
    public function update(MenuItemUpdateRequest $request, MenuItem $menuitem)
    {
        admin_authorize_store($menuitem->store_id);

        $menuitem->fill($request->except(['store_id']));

        $menuitem->is_available = $request->is_available == 'on' ? 1 : 0;
        $menuitem->is_non_veg = $request->is_non_veg == 'on' ? 1 : 0;

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

        // attach categories with store
        sync_categories_to_store($menuitem->store_id);

        return redirect()->intended(route('admin.menuitems'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Update the status of menu item
     *
     * @param  \Illuminate\Http\Request $request
     * @param MenuItem $menuitem
     * @return \Illuminate\Http\Response
     */
    public function quickApprove(Request $request, MenuItem $menuitem)
    {
        $menuitem->status = 'approved';
        $menuitem->save();
        return response()->json($menuitem);
    }
}
