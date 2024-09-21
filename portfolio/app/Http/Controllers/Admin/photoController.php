<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Tag;
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
        $tags = Tag::take(15)->get();
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');
        return view('admin/Photo.addPhoto', compact('categories','tags','successMessage', 'errorMessage'));
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

                // Lưu thông tin ảnh vào bảng Photo
                $photo = Photo::create([
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'image_url' => 'images/photos/' . $imageName,
                    'upload_date' => now(),
                    'location' => $request->input('location'),
                    'photo_status' => 'approved',
                    'privacy_status' => $request->input('privacy_status'),
                    'user_id' => 3,  // Thay đổi user_id theo nhu cầu của bạn
                    'category_id' => $request->input('category_id')
                ]);

                $tags = explode(',', $request->input('tags')); // Lấy danh sách các tag đã chọn
                foreach ($tags as $tagName) {
                    $tagName = trim($tagName); // Loại bỏ khoảng trắng

                    // Kiểm tra xem tag đã tồn tại hay chưa
                    $tag = Tag::where('tag_name', $tagName)->first();
                    if ($tag) {
                        // Nếu tag đã tồn tại, lấy ID và attach vào bảng trung gian
                        $photo->tags()->attach($tag->id);
                    } else {
                        // Nếu tag chưa tồn tại, thêm vào bảng tag và attach vào bảng trung gian
                        $newTag = Tag::create(['tag_name' => $tagName]);
                        $photo->tags()->attach($newTag->id);
                    }
                }

                Session::flash('successMessage', 'Photo added successfully!');
            }
        } catch (\Exception $e) {
            Session::flash('errorMessage', 'Error adding the photo: ' . $e->getMessage());
        }

        return redirect('/admin/photo');
    }

    public function edit($id)
    {
        $photo = Photo::findOrFail($id);
        $categories = Category::all();
        $availableTags = Tag::all();
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');
        return view('admin/Photo.editPhoto', compact('photo', 'categories','availableTags','successMessage', 'errorMessage'));
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

            // Cập nhật ảnh nếu có
            if ($request->hasFile('image')) {
                if ($photo->image_url && file_exists(public_path($photo->image_url))) {
                    unlink(public_path($photo->image_url));
                }
                $imageName = time() . '.' . $request->image->extension();
                $request->image->move(public_path('images/photos'), $imageName);
                $photo->image_url = 'images/photos/' . $imageName;
            }

            // Cập nhật tags
            $photo->tags()->detach(); // Xóa tất cả các tag hiện tại

            $tags = explode(',', $request->input('tags'));
            foreach ($tags as $tagName) {
                $tagName = trim($tagName); // Loại bỏ khoảng trắng

                // Kiểm tra xem tag đã tồn tại hay chưa
                $tag = Tag::where('tag_name', $tagName)->first();
                if ($tag) {
                    // Nếu tag đã tồn tại, lấy ID và attach vào bảng trung gian
                    $photo->tags()->attach($tag->id);
                } else {
                    // Nếu tag chưa tồn tại, thêm vào bảng tag và attach vào bảng trung gian
                    $newTag = Tag::create(['tag_name' => $tagName]);
                    $photo->tags()->attach($newTag->id);
                }
            }

            $photo->save(); // Lưu các thay đổi vào database
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

    // photo pending
    public function photoPending()
    {
        $photoPending = Photo::where('photo_status', 'pending')->paginate(10);
        return view('admin/Photo.photoPending', compact('photoPending'));
    }

    // photo rejected
    public function photoRejected()
    {
        $photoRejected = Photo::where('photo_status', 'rejected')->paginate(10);
        return view('admin/Photo.photoRejected', compact('photoRejected'));
    }
}

