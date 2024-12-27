<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class categoryController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->input('size', 10);
        $categories = Category::paginate($size);
        return view("admin.Category.category", compact('categories'));
    }

    public function create()
    {
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');
        return view("admin.Category.addCategory", compact('successMessage', 'errorMessage'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ], [
            'category_name.required' => 'The category name is required.',
            'category_name.string' => 'The category name must be a string.',
            'category_name.max' => 'The category name may not be greater than 255 characters.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
        ]);


        // Check if the category name already exists
        if (Category::where('category_name', $validatedData['category_name'])->exists()) {
            Session::flash('errorMessage', 'Category name already exists');
            return redirect()->back()->withInput()->withErrors($request->validator);
        }

        // Create slug from category name
        $validatedData['slug'] = Str::slug($validatedData['category_name'], '-');

        // Save the image to the public/images/categories directory
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('/images/categories'), $imageName);
            $validatedData['image'] = '/images/categories/' . $imageName;
        }

        Category::create($validatedData);

        Session::flash('successMessage', 'Category added successfully!');
        return redirect('admin/category');
    }

    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view("admin.Category.editCategory", compact('category'));
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ], [
            'category_name.required' => 'The category name is required.',
            'category_name.string' => 'The category name must be a string.',
            'category_name.max' => 'The category name may not be greater than 255 characters.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
        ]);

        // Check if the category name already exists, excluding the current category
        if (Category::where('category_name', $validatedData['category_name'])->where('id', '!=', $id)->exists()) {
            Session::flash('errorMessage', 'Category name already exists');
            return redirect()->back()->withInput()->withErrors($request->validator);
        }

        // Create slug from category name
        $validatedData['slug'] = Str::slug($validatedData['category_name'], '-');

        $category = Category::findOrFail($id);

        // Handle new image
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            // Save new image
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('/images/categories'), $imageName);
            $validatedData['image'] = '/images/categories/' . $imageName;
        }

        // Update data
        $category->update($validatedData);

        Session::flash('successMessage', 'Category updated successfully!');
        return redirect('admin/category');
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        // Delete image if exists
        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }
        $category->delete();
        Session::flash('successMessage', 'Category deleted successfully!');
        return redirect('admin/category');
    }
}
