<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BankDetailUpdateRequest;
use App\Models\BankDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class BankDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bankDetails = BankDetail::whereRaw('1=1');

        $currentStore = admin_get_store();
        if($currentStore) {
            $bankDetails = $bankDetails->where('user_id', $currentStore->owner_id);
        }

        if($request->user) {
            $bankDetails = $bankDetails->where('user_id', $request->user);
        }

        return view('admin.bankdetails.index', ['bankDetails' => $bankDetails->paginate()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * BankDetail a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param BankDetail $bankdetail
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(BankDetail $bankdetail)
    {
        admin_authorize_store($bankdetail->user->store->id);

        return view('admin.bankdetails.show', ['bankDetail' => $bankdetail]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param BankDetail $bankdetail
     * @return \Illuminate\Http\Response
     */
    public function edit(BankDetail $bankdetail)
    {
        admin_authorize_store($bankdetail->user->store->id);

        return view('admin.bankdetails.edit', ['bankDetail' => $bankdetail]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BankDetailUpdateRequest $request
     * @param BankDetail $bankdetail
     * @return mixed
     */
    public function update(BankDetailUpdateRequest $request, BankDetail $bankdetail)
    {
        admin_authorize_store($bankdetail->user->store->id);

        $bankdetail->fill($request->all());
        $bankdetail->save();

        return redirect()->intended(route('admin.bankdetails'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
