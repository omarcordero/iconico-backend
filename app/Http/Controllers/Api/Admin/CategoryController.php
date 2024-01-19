<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::whereRaw("1=1");

        if ($request->title_like) {
            $categories = Category::where('title', 'like', "%" . $request->title_like . "%");
        }

        return response()->json($categories->orderBy('title', 'desc')->paginate(config('constants.paginate_per_page')));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'file'
        ]);

        $category = new Category();

        $category->fill($request->all());

        if ($request->image) {
            $path = $request->file('image')->store('uploads');
            $category->image_url = Storage::url($path);
        }

        $category->save();

        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'file'
        ]);

        $category->fill($request->all());

        if ($request->image) {
            $path = $request->file('image')->store('uploads');
            $category->image_url = Storage::url($path);
        }

        $category->save();

        return response()->json($category, 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([], 204);
    }

    public function allCategories(Request $request)
    {
        return response()->json(Category::orderBy('title')->get());
    }
}
