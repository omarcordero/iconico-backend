<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ratings = Rating::whereRaw('1=1');

        $currentStore = admin_get_store();
        if($currentStore) {
            $ratings = $ratings->where('store_id', $currentStore->id);
        }

        if($request->store)  {
            $ratings = $ratings->where('store_id', $request->store);
        }

        return view('admin.ratings.index', ['ratings' => $ratings->sortable(['created_at' => 'asc'])->paginate()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Rating $rating
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Rating $rating)
    {
        admin_authorize_store($rating->store_id);

        $rating->delete();
        return redirect()->intended(route('admin.ratings'));
    }
}
