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
    public function listComment()
    {
        $comments = Comment::with(['photoImage.photo', 'user'])
        ->orderByRaw("CASE WHEN comment_status = 'pending' THEN 0 ELSE 1 END")
        ->orderBy('comment_date', 'desc')
        ->paginate(10);

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
