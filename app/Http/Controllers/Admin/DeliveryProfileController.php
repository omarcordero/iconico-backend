<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\DeliveryProfileUpdateRequest;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\DeliveryProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class DeliveryProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $deliveryProfiles = DeliveryProfile::whereRaw('1=1');

        if($request->search) {
            $deliveryProfiles = $deliveryProfiles->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $request->search . '%');
            });
        }

        if($request->delivery_profile_id) {
            $deliveryProfiles = $deliveryProfiles->where('id', $request->delivery_profile_id);
        }

        return view('admin.delivery_profiles.index', ['deliveryProfiles' => $deliveryProfiles->sortable(['created_at' => 'asc'])->paginate()]);
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
     * DeliveryProfile a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function deliveryProfile(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param DeliveryProfile $deliveryProfile
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(DeliveryProfile $deliveryProfile)
    {
        $earnings = [
            'total_earnings' => (clone $deliveryProfile->user->earnings)->sum('amount'),
            'unpaid_earnings' => (clone $deliveryProfile->user->earnings)->where('paid', 0)->sum('amount')
        ];
        return view('admin.delivery_profiles.show', ['deliveryProfile' => $deliveryProfile, 'earnings' => $earnings]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DeliveryProfile $deliveryProfile
     * @return \Illuminate\Http\Response
     */
    public function edit(DeliveryProfile $deliveryProfile)
    {
        return view('admin.delivery_profiles.edit', ['deliveryProfile' => $deliveryProfile]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DeliveryProfileUpdateRequest $request
     * @param DeliveryProfile $deliveryProfile
     * @return mixed
     */
    public function update(DeliveryProfileUpdateRequest $request, DeliveryProfile $deliveryProfile)
    {
        $deliveryProfile->fill($request->all());
        $deliveryProfile->save();

        return redirect()->intended(route('admin.deliveryProfiles'));
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
