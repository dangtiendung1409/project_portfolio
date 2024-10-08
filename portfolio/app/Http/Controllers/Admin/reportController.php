<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\Notification;

class reportController extends Controller
{
    // Hiển thị báo cáo đang ở trạng thái 'pending'
    public function reportPending()
    {
        $reports = Report::where('status', 'pending')->with(['reporter', 'violator', 'photoImage'])->paginate(10);
        return view('Admin/Report.reportPending', compact('reports'));
    }

    // Hiển thị báo cáo đã 'resolved'
    public function reportResolved()
    {
        $reports = Report::where('status', 'resolved')->with(['reporter', 'violator', 'photoImage'])->paginate(10);
        return view('Admin/Report.reportResolved', compact('reports'));
    }


    public function updateStatus(Request $request, $id, $action)
    {
        try {
            $report = Report::findOrFail($id);

            // Kiểm tra nếu hành động hợp lệ
            if (in_array($action, ['removed', 'warning', 'no violation'])) {
                $report->action_taken = $action;
                $report->status = 'resolved';

                // Tăng số lần vi phạm của người bị tố cáo (violator)
                $violator = $report->violator; // Lấy người bị tố cáo
                if ($violator) {
                    // Tăng số lần vi phạm nếu hành động là 'removed' hoặc 'warning'
                    if ($action === 'removed' || $action === 'warning') {
                        $violator->violation_count += 1;

                        // Kiểm tra nếu số lần vi phạm vượt quá 3 thì vô hiệu hóa tài khoản
                        if ($violator->violation_count > 3) {
                            $violator->is_active = 0;
                            session()->flash('errorMessage', 'Violator has been deactivated due to excessive violations.');
                        }

                        // Lưu lại thay đổi cho violator
                        $violator->save();

                        // Tạo thông báo cho người bị vi phạm
                        $notification = new Notification();
                        $notification->user_id = $violator->id;
                        $notification->notification_type = $action;
                        $notification->content = "Your content has been reported and marked as '$action' because it violates social standards. If you violate it more than 3 times, your account will be locked.";
                        $notification->notification_date = now();
                        $notification->save(); // Lưu thông báo
                    }

                    // Nếu hành động là 'removed', xóa mềm ảnh
                    if ($action === 'removed') {
                        $photoImage = $report->photoImage;
                        if ($photoImage) {
                            $photoImage->delete(); // Xóa mềm ảnh
                        }
                        session()->flash('successMessage', 'Photo removed and violator\'s violation count increased successfully!');
                    } else {
                        session()->flash('successMessage', 'Report updated successfully!');
                    }
                }

                // Lưu lại thay đổi của report
                $report->save();
            } else {
                session()->flash('errorMessage', 'Invalid action.');
            }
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Error updating the report: ' . $e->getMessage());
        }

        return redirect()->back();
    }
}
