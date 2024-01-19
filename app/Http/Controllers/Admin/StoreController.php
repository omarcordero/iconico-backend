<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreUpdateRequest;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Validator;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $stores = Store::whereRaw('1=1');

        $currentStore = admin_get_store();
        if($currentStore) {
            $stores = $stores->where('id', $currentStore->id);
        }

        if($request->search) {
            $stores = $stores->where('name', 'like', '%'. $request->search . '%')
                ->orWhere('tagline', 'like', '%'. $request->search . '%')
                ->orWhere('address', 'like', '%'. $request->search . '%')
                ->orWhere('area', 'like', '%'. $request->search . '%')
                ->orWhere('details', 'like', '%'. $request->search . '%');
        }

        if($request->store) {
            $stores = $stores->where('id', $request->store);
        }

        return view('admin.stores.index', ['stores' => $stores->sortable(['created_at' => 'asc'])->paginate()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Store $store
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Store $store)
    {
        admin_authorize_store($store->id);

        $earnings = [
            'total_earnings' => (clone $store->user->earnings)->sum('amount'),
            'unpaid_earnings' => (clone $store->user->earnings)->where('paid', 0)->sum('amount')
        ];
        return view('admin.stores.show', ['store' => $store, 'earnings' => $earnings]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Store $store
     * @return \Illuminate\Http\Response
     */
    public function edit(Store $store)
    {
        admin_authorize_store($store->id);

        return view('admin.stores.edit', ['store' => $store]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreUpdateRequest $request
     * @param Store $store
     * @return mixed
     */
    public function update(StoreUpdateRequest $request, Store $store)
    {
        admin_authorize_store($store->id);

        $store->fill($request->all());
        $store->save();

        return redirect()->intended(route('admin.stores'));
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
}
