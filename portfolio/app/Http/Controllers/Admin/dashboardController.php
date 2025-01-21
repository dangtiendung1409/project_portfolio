<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Photo;
use App\Models\PhotoImages;
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

        // Lấy danh sách báo cáo đang chờ xử lý
        $reports = Report::where('status', 'pending')
            ->with(['reporter', 'violator', 'photo'])
            ->paginate(10);

        return view('admin/dashboard', compact(
            'approvedPhotosCount',
            'totalUser',
            'totalCategories',
            'photoPending',
            'reports'
        ));
    }
}

