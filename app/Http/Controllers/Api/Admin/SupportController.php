<?php

namespace App\Http\Controllers\Api\Admin;

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
        return response()->json(Support::orderBy('created_at', 'desc')->paginate(config('constants.paginate_per_page')));
    }
}
