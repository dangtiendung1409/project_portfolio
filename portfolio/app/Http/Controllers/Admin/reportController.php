<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\Notification;

class reportController extends Controller
{
    public function getPhotoReports(Request $request)
    {
        $query = Report::whereNotNull('photo_id')
            ->with([
                'photo' => function ($query) {
                    $query->withTrashed(); // Lấy cả ảnh đã bị xóa mềm
                },
                'reporter',
                'violator'
            ]);

        // Lọc theo violator name
        if ($request->filled('violator_name')) {
            $query->whereHas('violator', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->input('violator_name') . '%');
            });
        }

        // Lọc theo reporter name
        if ($request->filled('reporter_name')) {
            $query->whereHas('reporter', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->input('reporter_name') . '%');
            });
        }

        // Lọc theo report reason
        if ($request->filled('report_reason')) {
            $query->where('report_reason', 'like', '%' . $request->input('report_reason') . '%');
        }

        // Lọc theo report date
        if ($request->filled('start_date')) {
            $query->whereDate('report_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('report_date', '<=', $request->input('end_date'));
        }

        // Lọc theo action_taken
        if ($request->filled('action_taken')) {
            $query->where('action_taken', $request->input('action_taken'));
        }

        // Số lượng bản ghi trên mỗi trang (mặc định 10)
        $size = $request->input('size', 10);

        // Lấy danh sách báo cáo với phân trang và giữ nguyên sắp xếp
        $reports = $query->orderByRaw("FIELD(status, 'pending', 'resolved')")
            ->orderByDesc('report_date')
            ->paginate($size)
            ->appends($request->all());

        return view('admin.Report.reportPhoto', compact('reports'));
    }

    public function getCommentReports(Request $request)
    {
        $query = Report::whereNotNull('comment_id')
            ->with([
                'comment' => function ($query) {
                    $query->withTrashed(); // Lấy cả comment đã bị xóa mềm
                },
                'reporter',
                'violator'
            ]);

        // Lọc theo violator name
        if ($request->filled('violator_name')) {
            $query->whereHas('violator', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->input('violator_name') . '%');
            });
        }

        // Lọc theo reporter name
        if ($request->filled('reporter_name')) {
            $query->whereHas('reporter', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->input('reporter_name') . '%');
            });
        }

        // Lọc theo report reason
        if ($request->filled('report_reason')) {
            $query->where('report_reason', 'like', '%' . $request->input('report_reason') . '%');
        }

        // Lọc theo report date
        if ($request->filled('start_date')) {
            $query->whereDate('report_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('report_date', '<=', $request->input('end_date'));
        }

        // Lọc theo action_taken
        if ($request->filled('action_taken')) {
            $query->where('action_taken', $request->input('action_taken'));
        }

        // Số lượng bản ghi trên mỗi trang (mặc định 10)
        $size = $request->input('size', 10);

        // Lấy danh sách báo cáo với phân trang và giữ nguyên sắp xếp
        $reports = $query->orderByRaw("FIELD(status, 'pending', 'resolved')")
            ->orderByDesc('report_date')
            ->paginate($size)
            ->appends($request->all());

        return view('admin.Report.reportComment', compact('reports'));
    }

    public function getGalleryReports(Request $request)
    {
        $query = Report::whereNotNull('gallery_id')
            ->with([
                'gallery' => function ($query) {
                    $query->withTrashed(); // Lấy cả gallery đã bị xóa mềm
                },
                'reporter',
                'violator'
            ]);

        // Lọc theo violator name
        if ($request->filled('violator_name')) {
            $query->whereHas('violator', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->input('violator_name') . '%');
            });
        }

        // Lọc theo reporter name
        if ($request->filled('reporter_name')) {
            $query->whereHas('reporter', function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->input('reporter_name') . '%');
            });
        }

        // Lọc theo report reason
        if ($request->filled('report_reason')) {
            $query->where('report_reason', 'like', '%' . $request->input('report_reason') . '%');
        }

        // Lọc theo report date
        if ($request->filled('start_date')) {
            $query->whereDate('report_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('report_date', '<=', $request->input('end_date'));
        }

        // Lọc theo action_taken
        if ($request->filled('action_taken')) {
            $query->where('action_taken', $request->input('action_taken'));
        }

        // Số lượng bản ghi trên mỗi trang (mặc định 10)
        $size = $request->input('size', 10);

        // Lấy danh sách báo cáo với phân trang và giữ nguyên sắp xếp
        $reports = $query->orderByRaw("FIELD(status, 'pending', 'resolved')")
            ->orderByDesc('report_date')
            ->paginate($size)
            ->appends($request->all());

        return view('admin.Report.reportGallery', compact('reports'));
    }

    public function updateStatus(Request $request, $id, $action)
    {
        try {
            $report = Report::findOrFail($id);

            // Chỉ chấp nhận các hành động hợp lệ
            if (!in_array($action, ['removed', 'no violation'])) {
                session()->flash('errorMessage', 'Invalid action.');
                return redirect()->back();
            }

            $report->action_taken = $action;
            $report->status = 'resolved';
            $report->save();

            // Lấy thông tin người báo cáo & người bị vi phạm
            $reporter = $report->reporter;
            $violator = $report->violator;

            // Xác định loại nội dung vi phạm
            $violationType = 'content';
            if (!is_null($report->photo_id)) {
                $violationType = 'photo';
            } elseif (!is_null($report->comment_id)) {
                $violationType = 'comment';
            } elseif (!is_null($report->gallery_id)) {
                $violationType = 'gallery';
            }

            // Nếu không vi phạm, chỉ gửi thông báo cho người báo cáo và kết thúc
            if ($action === 'no violation') {
                if ($reporter) {
                    Notification::create([
                        'user_id' => '1',
                        'recipient_id' => $reporter->id,
                        'type' => '4',
                        'content' => "Your report against user {$violator->name} for '{$report->report_reason}' has been reviewed. We found no violations, so no actions were taken.",
                        'notification_date' => now(),
                    ]);
                }

                session()->flash('successMessage', 'Report reviewed: No violation found.');
                return redirect()->back();
            }

            // Nếu là "removed", xử lý vi phạm
            if ($violator) {
                $violator->violation_count += 1;

                // Nếu vi phạm > 3 lần, vô hiệu hóa tài khoản
                if ($violator->violation_count > 3) {
                    $violator->is_active = 0;
                }

                $violator->save(); // Lưu thay đổi violator

                // Xóa mềm nội dung liên quan
                if (!is_null($report->photo_id) && $report->photo) {
                    $report->photo->delete();
                }
                if (!is_null($report->comment_id) && $report->comment) {
                    $report->comment->delete();
                }
                if (!is_null($report->gallery_id) && $report->gallery) {
                    $report->gallery->delete();
                }

                session()->flash('successMessage', ucfirst($violationType) . " removed successfully!");
            }

            // Gửi thông báo cho người báo cáo
            if ($reporter) {
                Notification::create([
                    'user_id' => '1',
                    'recipient_id' => $reporter->id,
                    'type' => '4',
                    'content' => "Your report against user {$violator->name} for '{$report->report_reason}' has been reviewed. We have decided to remove {$violationType} from the site.",
                    'notification_date' => now(),
                ]);
            }

            // Gửi thông báo cho người vi phạm
            if ($violator) {
                Notification::create([
                    'user_id' => '1',
                    'recipient_id' => $violator->id,
                    'type' => '4',
                    'content' => "Your {$violationType} has been reviewed and marked as 'removed' due to '{$report->report_reason}'. More than 3 violations will result in account suspension.",
                    'notification_date' => now(),
                ]);
            }
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Error updating the report: ' . $e->getMessage());
        }

        return redirect()->back();
    }

}
