<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class photoController extends Controller
{
    public function index()
    {
        $photos = Photo::where('photo_status', 'approved')->paginate(20);
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');
        return view('admin/Photo.photo', compact('photos', 'successMessage', 'errorMessage'));
    }

    public function create()
    {
        $categories = Category::all();
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');
        return view('admin/Photo.addPhoto', compact('categories','successMessage', 'errorMessage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'privacy_status' => 'required'
        ]);

        try {
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images/photos'), $imageName);

                Photo::create([
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'image_url' => 'images/photos/' . $imageName,
                    'upload_date' => now(),
                    'location' => $request->input('location'),
                    'photo_status' => 'approved',
                    'privacy_status' => $request->input('privacy_status'),
                    'user_id' => 3,
                    'category_id' => $request->input('category_id')
                ]);
                Session::flash('successMessage', 'Photo added successfully!');
            }
        } catch (\Exception $e) {
            Session::flash('errorMessage', 'Error adding the photo.');
        }

        return redirect('/admin/photo');
    }
    public function edit($id)
    {
        $photo = Photo::findOrFail($id);
        $categories = Category::all();
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');
        return view('admin/Photo.editPhoto', compact('photo', 'categories','successMessage', 'errorMessage'));
    }
    public function update(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);

        $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'privacy_status' => 'required'
        ]);

        try {
            $photo->title = $request->input('title');
            $photo->description = $request->input('description');
            $photo->location = $request->input('location');
            $photo->privacy_status = $request->input('privacy_status');
            $photo->category_id = $request->input('category_id');

            if ($request->hasFile('image')) {
                if ($photo->image_url && file_exists(public_path($photo->image_url))) {
                    unlink(public_path($photo->image_url));
                }
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images/photos'), $imageName);
                $photo->image_url = 'images/photos/' . $imageName;
            }

            $photo->save();
            Session::flash('successMessage', 'Photo updated successfully!');
        } catch (\Exception $e) {
            Session::flash('errorMessage', 'Error updating the photo.');
        }

        return redirect('/admin/photo');
    }

    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);

        try {
            if (file_exists(public_path($photo->image_url))) {
                unlink(public_path($photo->image_url));
            }
            $photo->delete();
            Session::flash('successMessage', 'Photo deleted successfully!');
        } catch (\Exception $e) {
            Session::flash('errorMessage', 'Error deleting the photo.');
        }

        return redirect('/admin/photo');
    }
}
