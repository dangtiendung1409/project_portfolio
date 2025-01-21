<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Photo;
use Illuminate\Support\Str;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class photoController extends Controller
{
    public function index(Request $request)
    {
        $query = Photo::query();
        $categories = Category::all();

        // Chỉ lấy ra các ảnh có photo_status là approved
        $query->where('photo_status', 'approved');

        // Lọc theo title
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        // Lọc theo location
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
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

        // Số lượng bản ghi trên mỗi trang
        $size = $request->input('size', 20);

        // Paginate dữ liệu
        $photos = $query->paginate($size)->appends($request->all());

        return view('admin/Photo.photo', compact('photos', 'categories'));
    }
    public function showDetails($id)
    {
        $photo = Photo::with(['category', 'tags','user'])->findOrFail($id);
        return view('admin.Photo.detailsPhoto', compact('photo'));
    }


    public function showComment($photo_id,Request $request){
        $size = $request->input('size', 20);

        // Lấy các bình luận có photo_id
        $comments = Comment::where('photo_id', $photo_id)
            ->paginate($size);

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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',  // Chỉ cho phép upload 1 ảnh
            'category_id' => 'required|exists:categories,id',
            'privacy_status' => 'required'
        ]);

        try {
            // Xử lý upload ảnh
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move(public_path('images/photos'), $imageName);

                // Lưu URL ảnh
                $imageUrl = 'images/photos/' . $imageName;
            }

            // Tạo photo_token dưới dạng UUID
            $photoToken = Str::uuid();  // Tạo UUID tự động cho photo_token

            // Lưu thông tin chính của ảnh, bao gồm cả image_url và photo_token
            $photo = Photo::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'upload_date' => now(),
                'location' => $request->input('location'),
                'photo_status' => 'approved',
                'privacy_status' => $request->input('privacy_status'),
                'user_id' => 3,  // Thay đổi user_id theo nhu cầu của bạn
                'category_id' => $request->input('category_id'),
                'image_url' => $imageUrl,  // Lưu URL ảnh vào cơ sở dữ liệu
                'photo_token' => $photoToken  // Lưu UUID vào cơ sở dữ liệu
            ]);

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

            Session::flash('successMessage', 'Photo added successfully!');
        } catch (\Exception $e) {
            Session::flash('errorMessage', 'Error adding the photo: ' . $e->getMessage());
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Chỉ cho phép 1 ảnh
        ]);

        try {
            // Cập nhật thông tin của photo
            $photo->title = $request->input('title');
            $photo->description = $request->input('description');
            $photo->location = $request->input('location');
            $photo->privacy_status = (int) $request->input('privacy_status'); // Chuyển đổi về số nguyên
            $photo->category_id = $request->input('category_id');

            // Kiểm tra nếu có ảnh mới
            if ($request->hasFile('image')) {
                // Xóa ảnh cũ trong cơ sở dữ liệu (nếu có)
                if ($photo->image_url && file_exists(public_path($photo->image_url))) {
                    unlink(public_path($photo->image_url)); // Xóa ảnh cũ
                }

                // Xử lý upload ảnh mới
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move(public_path('images/photos'), $imageName);

                // Cập nhật ảnh mới
                $photo->image_url = 'images/photos/' . $imageName;
            }

            // Cập nhật tags
            $photo->tags()->detach(); // Xóa tất cả các tag hiện tại

            // Xử lý tags từ form
            $tags = explode(',', $request->input('tags', ''));
            foreach ($tags as $tagName) {
                $tagName = trim($tagName); // Loại bỏ khoảng trắng

                if (!empty($tagName)) {
                    // Kiểm tra xem tag đã tồn tại chưa
                    $tag = Tag::firstOrCreate(['tag_name' => $tagName]);

                    // Gắn tag vào ảnh
                    $photo->tags()->attach($tag->id);
                }
            }

            $photo->save(); // Lưu các thay đổi vào database
            Session::flash('successMessage', 'Photo updated successfully!');
        } catch (\Exception $e) {
            Session::flash('errorMessage', 'Error updating the photo: ' . $e->getMessage());
        }

        return redirect('/admin/photo');
    }


    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);

        try {
            // Xóa mềm bản ghi trong bảng photos
            $photo->delete();

            // Thông báo thành công
            Session::flash('successMessage', 'Photo deleted successfully!');
        } catch (\Exception $e) {
            // Thông báo lỗi
            Session::flash('errorMessage', 'Error deleting the photo: ' . $e->getMessage());
        }

        return redirect('/admin/photo'); // Quay về trang quản lý ảnh
    }


    // photo pending
    public function photoPending(Request $request)
    {
        $size = $request->input('size', 20);

        // Lấy các ảnh có trạng thái 'pending'
        $photoPending = Photo::where('photo_status', 'pending')
        ->paginate($size);

        // Lấy thông điệp từ session
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');

        // Trả về view với dữ liệu ảnh và thông điệp
        return view('admin/Photo.photoPending', compact('photoPending', 'errorMessage', 'successMessage'));
    }


    public function updateStatus($id, $status)
    {
        // Tìm ảnh theo ID từ bảng photo_images
        $photoImage = Photo::findOrFail($id);

        // Cập nhật trạng thái của ảnh
        $photoImage->photo_status = $status;
        $photoImage->save();

        // Đặt thông báo thành công vào session
        Session::flash('successMessage', "Photo status updated successfully!");

        // Redirect lại trang quản lý ảnh pending
        return redirect()->back();
    }


    // photo rejected
    public function photoRejected(Request $request)
    {
        $size = $request->input('size', 20);

        // Lấy các ảnh có trạng thái 'pending'
        $photoRejected = Photo::where('photo_status', 'rejected')
            ->paginate($size);

        // Lấy thông điệp từ session
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');

        return view('admin/Photo.photoRejected', compact('photoRejected', 'errorMessage', 'successMessage'));
    }


}

