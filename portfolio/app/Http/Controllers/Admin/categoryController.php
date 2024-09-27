<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class categoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);
        return view("admin/Category.category",compact('categories'));
    }

    public function create()
    {
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');
        return view("Admin/Category.addCategory",compact('successMessage','errorMessage'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255'
        ]);

        // Kiểm tra xem tên danh mục đã tồn tại chưa
        if (Category::where('category_name', $validatedData['category_name'])->exists()) {
            Session::flash('errorMessage', 'Category name already exists');
            return redirect()->back()->withInput()->withErrors($request->validator);
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
            'category_name' => 'required|string|max:255'
        ]);

        // Kiểm tra xem tên danh mục đã tồn tại chưa, loại trừ danh mục hiện tại
        if (Category::where('category_name', $validatedData['category_name'])->where('id', '!=', $id)->exists()) {
            Session::flash('errorMessage', 'Category name already exists');
            return redirect()->back()->withInput()->withErrors($request->validator);
        }

        // Cập nhật danh mục
        $category = Category::findOrFail($id);
        $category->update($validatedData);
        Session::flash('successMessage', 'Category updated successfully!');
        return redirect('admin/category');
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        Session::flash('successMessage', 'Category deleted successfully!');
        return redirect('admin/category');
    }

}
