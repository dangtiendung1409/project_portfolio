<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Photo;
use App\Models\PhotoImages;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class photoController extends Controller
{
    public function index(Request $request)
    {
        $query = Photo::query();
        $categories = Category::all();

        // Lọc theo title
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        // Lọc theo location
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        // Lọc theo user name
        if ($request->filled('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->input('user_name') . '%');
            });
        }

        // Lọc theo category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Lọc theo ngày upload
        if ($request->filled('start_date')) {
            $query->whereDate('upload_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('upload_date', '<=', $request->input('end_date'));
        }

        // Lọc theo privacy_status
        if ($request->filled('privacy_status')) {
            $query->where('privacy_status', $request->input('privacy_status'));
        }

        $photos = $query->with(['images' => function ($query) {
            $query->where('photo_status', 'approved');
        }])->paginate(10)->appends($request->all());

        return view('admin/Photo.photo', compact('photos', 'categories'));
    }

    public function showComment($photo_image_id){
        $comments = Comment::where('photo_image_id', $photo_image_id)
            ->where('comment_status', 'approved')
            ->paginate(10);

        return view('admin/photo.listComment', compact('comments'));
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
            'images' => 'array|max:3',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',// Đảm bảo rằng có ít nhất một ảnh được upload
            'category_id' => 'required|exists:categories,id',
            'privacy_status' => 'required'
        ]);

        try {
            // Lưu thông tin chính của ảnh
            $photo = Photo::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'upload_date' => now(),
                'location' => $request->input('location'),
                'photo_status' => 'approved',
                'privacy_status' => $request->input('privacy_status'),
                'user_id' => 3,  // Thay đổi user_id theo nhu cầu của bạn
                'category_id' => $request->input('category_id')
            ]);

            // Xử lý upload nhiều ảnh
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                    $image->move(public_path('images/photos'), $imageName);

                    // Lưu thông tin ảnh mới vào bảng photo_images
                    $photo->images()->create([
                        'photo_status' => 'approved',
                        'image_url' => 'images/photos/' . $imageName,
                    ]);
                }
            }

            // Xử lý tags
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

            Session::flash('successMessage', 'Photos added successfully!');
        } catch (\Exception $e) {
            Session::flash('errorMessage', 'Error adding the photos: ' . $e->getMessage());
        }

        return redirect('/admin/photo');
    }

    public function edit($id)
    {
        $photo = Photo::findOrFail($id);
        $categories = Category::all();
        $availableTags = Tag::take(15)->get();
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
            'privacy_status' => 'required',
            'images' => 'array|max:3',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            // Cập nhật thông tin của photo
            $photo->title = $request->input('title');
            $photo->description = $request->input('description');
            $photo->location = $request->input('location');
            $photo->privacy_status = $request->input('privacy_status');
            $photo->category_id = $request->input('category_id');

            // Kiểm tra có ảnh mới hay không
            if ($request->hasFile('images')) {
                // Xóa các ảnh cũ
                foreach ($photo->images as $image) {
                    if (file_exists(public_path($image->image_url))) {
                        unlink(public_path($image->image_url));
                    }
                    $image->delete(); // Xóa ảnh trong cơ sở dữ liệu
                }

                // Xử lý upload nhiều ảnh mới
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                    $image->move(public_path('images/photos'), $imageName);

                    // Lưu thông tin ảnh mới vào bảng photo_images
                    $photo->images()->create([
                        'photo_status' => 'approved',
                        'image_url' => 'images/photos/' . $imageName,
                    ]);
                }
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
            Session::flash('successMessage', 'Photos updated successfully!');
        } catch (\Exception $e) {
            Session::flash('errorMessage', 'Error updating the photos: ' . $e->getMessage());
        }

        return redirect('/admin/photo');
    }



    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);

        try {
            // Xóa mềm các bản ghi liên quan trong bảng photo_images
            $photo->images()->delete();

            // Xóa mềm bản ghi trong bảng photos
            $photo->delete();

            Session::flash('successMessage', 'Photo and its images deleted successfully!');
        } catch (\Exception $e) {
            Session::flash('errorMessage', 'Error deleting the photo and its images.');
        }

        return redirect('/admin/photo'); // Quay về trang quản lý ảnh
    }


    // photo pending
    public function photoPending()
    {
        // Lấy tất cả các ảnh có trạng thái pending và thực hiện phân trang
        $photoPending = Photo::whereHas('images', function($query) {
            $query->where('photo_status', 'pending');
        })->with(['images' => function ($query) {
            $query->where('photo_status', 'pending');
        }])->paginate(10);

        // Lấy thông điệp từ session
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');

        return view('admin/Photo.photoPending', compact('photoPending', 'errorMessage', 'successMessage'));
    }

    public function updateStatus($id, $status)
    {
        // Tìm ảnh theo ID từ bảng photo_images
        $photoImage = PhotoImages::findOrFail($id);

        // Cập nhật trạng thái của ảnh
        $photoImage->photo_status = $status;
        $photoImage->save();

        // Đặt thông báo thành công vào session
        Session::flash('successMessage', "Photo status updated to {$status} successfully!");

        // Redirect lại trang quản lý ảnh pending
        return redirect()->back();
    }


    // photo rejected
    public function photoRejected()
    {
        // Lấy tất cả các ảnh có trạng thái rejected và thực hiện phân trang
        $photoRejected = Photo::whereHas('images', function($query) {
            $query->where('photo_status', 'rejected');
        })->with(['images' => function ($query) {
            $query->where('photo_status', 'rejected');
        }])->paginate(10);

        // Lấy thông điệp từ session
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');

        return view('admin/Photo.photoRejected', compact('photoRejected', 'errorMessage', 'successMessage'));
    }


}

