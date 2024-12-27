<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Photo;
use App\Models\PhotoImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class commentController extends Controller
{
    public function listComment(Request $request)
    {
        // Tạo query ban đầu
        $query = Comment::with(['photoImage.photo', 'user']);

        // Lọc theo username
        if ($request->filled('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->input('user_name') . '%');
            });
        }

        // Lọc theo comment text
        if ($request->filled('comment_text')) {
            $query->where('comment_text', 'like', '%' . $request->input('comment_text') . '%');
        }

//        // Lọc theo comment status
//        if ($request->filled('comment_status')) {
//            $query->where('comment_status', $request->input('comment_status'));
//        }

        // Lọc theo comment date từ ngày nào đến ngày nào
//        if ($request->filled('start_date')) {
//            $query->whereDate('created_at', '>=', $request->input('start_date'));
//        }
//        if ($request->filled('end_date')) {
//            $query->whereDate('comment_date', '<=', $request->input('end_date'));
//        }
        $size = $request->input('size', 10);
        $comments = $query
            ->orderBy('updated_at', 'desc')
            ->paginate($size)
            ->appends($request->all());

        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');

        // Truyền dữ liệu vào view
        return view('admin.Comment.commentPhoto', compact('comments', 'successMessage', 'errorMessage'));
    }

    public function updateStatus($id, $status)
    {
        $comment = Comment::findOrFail($id);

        // Kiểm tra trạng thái hiện tại
        if ($comment->comment_status === 'pending') {
            $comment->comment_status = $status; // Cập nhật trạng thái
            $comment->save();

            // Thêm thông điệp thành công vào session
            Session::flash('successMessage', 'Comment status updated successfully.');
        } else {
            Session::flash('errorMessage', 'Cannot update comment status. Current status is not pending.');
        }

        return redirect()->route('admin.comment.listComment');
    }
}
