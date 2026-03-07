<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;

class CategoryController extends Controller
{
    public function index($type)
    {
        $category = ProductCategory::where('type', $type)->get();

        return view('admin.category.index', compact('type', 'category'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        $category = new ProductCategory();
        $category->title = $validatedData['title'];
        $category->type = $validatedData['type'];
        
        $category->save();

        return redirect()->back()->with('success', 'Category successfully added.');
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $category = ProductCategory::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Product category not found.');
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        $category->title = $validatedData['title'];
        $category->type = $validatedData['type'];
        
        $category->save();

        return redirect()->back()->with('success', 'Category successfully updated.');
    }


    public function delete(Request $request)
    {
        $id = $request->input('id');

        $category = ProductCategory::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Product category not found.');
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category successfully deleted.');
    }

}
