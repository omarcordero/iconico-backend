<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\Role\Role;
use App\Models\Auth\User\User;
use App\Models\Support;
use Illuminate\Http\Request;
use Rennokki\Plans\Models\PlanModel;
use Rinvex\Subscriptions\Models\Plan;
use Validator;


class PlanController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(PlanModel::paginate(config('constants.paginate_per_page')));
    }

    public function show($id)
    {
        $planModel = PlanModel::find($id);

        return response()->json($planModel);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|numeric'
        ]);

        $planModel = PlanModel::find($id);
        $planModel->fill($request->only(['name', 'description', 'price', 'duration']));
        $planModel->save();

        return response()->json($planModel);
    }
}
