<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class blogController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->input('size', 10);
        $blogs = Blog::paginate($size);
        return view("admin.Blog.blog", compact('blogs'));
    }

    public function create()
    {
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');
        return view('admin.BLog.addBlog',compact('successMessage', 'errorMessage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Lưu ảnh bìa với tên gốc
        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImageName = $request->file('cover_image')->getClientOriginalName();
            $coverImagePath = 'images/blogs/cover/' . $coverImageName;
            $request->file('cover_image')->move(public_path('images/blogs/cover'), $coverImageName);
        }

        // Lưu ảnh nội dung với tên gốc
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->getClientOriginalName();
            $imagePath = 'images/blogs/content/' . $imageName;
            $request->file('image')->move(public_path('images/blogs/content'), $imageName);
        }

        // Lưu blog
        Blog::create([
            'author_id'   => auth()->id(),
            'title'       => $request->title,
            'slug'        => Str::slug($request->title) . '-' . time(),
            'content'     => $request->input('content'),
            'cover_image' => $coverImagePath,
            'image'       => $imagePath,
        ]);
        Session::flash('successMessage', 'Blog added successfully!');
        return redirect()->route('admin.blogs.index');
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return view('admin.Blog.editBlog', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Cập nhật ảnh bìa nếu có
        if ($request->hasFile('cover_image')) {
            $coverImageName = $request->file('cover_image')->getClientOriginalName();
            $coverImagePath = 'images/blogs/cover/' . $coverImageName;
            $request->file('cover_image')->move(public_path('images/blogs/cover'), $coverImageName);
            $blog->cover_image = $coverImagePath;
        }

        // Cập nhật ảnh nội dung nếu có
        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->getClientOriginalName();
            $imagePath = 'images/blogs/content/' . $imageName;
            $request->file('image')->move(public_path('images/blogs/content'), $imageName);
            $blog->image = $imagePath;
        }

        // Cập nhật dữ liệu blog
        $blog->update([
            'title'   => $request->title,
            'slug'    => Str::slug($request->title) . '-' . time(),
            'content' => $request->input('content'),
        ]);
        Session::flash('successMessage', 'Blog updated successfully!');
        return redirect()->route('admin.blogs.index');
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        // Xóa ảnh bìa nếu tồn tại
        if ($blog->cover_image && file_exists(public_path($blog->cover_image))) {
            unlink(public_path($blog->cover_image));
        }

        // Xóa ảnh nội dung nếu tồn tại
        if ($blog->image && file_exists(public_path($blog->image))) {
            unlink(public_path($blog->image));
        }

        // Xóa blog khỏi database
        $blog->delete();

        Session::flash('successMessage', 'Blog deleted successfully!');
        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted successfully.');
    }

}
