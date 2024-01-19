<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SupportUpdateRequest;
use App\Models\Support;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class SupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.supports.index', ['supports' => Support::paginate()]);
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
     * Support a newly created resource in storage.
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
     * @param Support $support
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Support $support)
    {
        return view('admin.supports.show', ['support' => $support]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Support $support
     * @return \Illuminate\Http\Response
     */
    public function edit(Support $support)
    {
        return view('admin.supports.edit', ['support' => $support]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SupportUpdateRequest $request
     * @param Support $support
     * @return mixed
     */
    public function update(SupportUpdateRequest $request, Support $support)
    {
        $support->fill($request->all());
        $support->save();

        return redirect()->intended(route('admin.supports'));
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
