<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BankDetailUpdateRequest;
use App\Models\BankDetail;
use Illuminate\Http\Request;

class BankDetailController extends Controller
{
    public function index(Request $request)
    {
        $bankDetails = BankDetail::whereRaw('1=1');

        $currentStore = admin_get_store();
        if($currentStore) {
            $bankDetails = $bankDetails->where('user_id', $currentStore->owner_id);
        }

        return response()->json($bankDetails->orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page')));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'bank_name' => 'required',
            'account_number' => 'required',
            'ifsc' => 'required',
            'user_id' => 'required|exists:users,id',
        ]);

        $bankDetail = new BankDetail();
        $bankDetail->fill($request->all());
        $bankDetail->save();

        return response()->json($bankDetail, 201);
    }

    public function show(BankDetail $bankdetail)
    {
        admin_authorize_store($bankdetail->user->store->id);

        return response()->json($bankdetail);
    }

    public function update(BankDetailUpdateRequest $request, BankDetail $bankdetail)
    {
        admin_authorize_store($bankdetail->user->store->id);

        $bankdetail->fill($request->all());
        $bankdetail->save();

        return redirect()->intended(route('admin.bankdetails'));
    }

    public function destroy(BankDetail $bankdetail)
    {
        $bankdetail->delete();

        return response()->json([], 204);
    }
}
