<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Gallery;
use App\Models\Photo;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    public function dashboard() {
        // Đếm số ảnh được phê duyệt
        $approvedPhotosCount = Photo::where('photo_status', 'approved')->count();

        // Đếm tổng số người dùng
        $totalUser = User::count();

        // Đếm tổng số danh mục
        $totalCategories = Category::count();

        // Lấy danh sách ảnh đang chờ phê duyệt
        $photoPending = Photo::where('photo_status', 'pending')->paginate(10);

        // Lấy danh sách báo cáo ảnh (bao gồm cả ảnh đã bị xóa mềm)
        $photoReports = Report::where('status', 'pending')
            ->whereNotNull('photo_id')
            ->with([
                'photo' => function ($query) {
                    $query->withTrashed(); // Lấy cả ảnh đã bị xóa mềm
                },
                'reporter',
                'violator'
            ])
            ->paginate(10);

        // Lấy danh sách báo cáo gallery (bao gồm cả gallery đã bị xóa mềm)
        $galleryReports = Report::where('status', 'pending')
            ->whereNotNull('gallery_id')
            ->with([
                'gallery' => function ($query) {
                    $query->withTrashed(); // Lấy cả gallery đã bị xóa mềm
                },
                'reporter',
                'violator'
            ])
            ->paginate(10);

        // Lấy danh sách báo cáo bình luận (bao gồm cả bình luận đã bị xóa mềm)
        $commentReports = Report::where('status', 'pending')
            ->whereNotNull('comment_id')
            ->with([
                'comment' => function ($query) {
                    $query->withTrashed(); // Lấy cả bình luận đã bị xóa mềm
                },
                'reporter',
                'violator'
            ])
            ->paginate(10);

        return view('admin/dashboard', compact(
            'approvedPhotosCount',
            'totalUser',
            'totalCategories',
            'photoPending',
            'photoReports',
            'galleryReports',
            'commentReports'
        ));
    }
}
