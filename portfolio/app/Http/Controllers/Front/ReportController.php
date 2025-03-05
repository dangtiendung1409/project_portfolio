<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    /**
     * API để tạo báo cáo vi phạm
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reporter_id' => 'required|exists:users,id',
            'violator_id' => 'required|exists:users,id',
            'report_reason' => 'required|string',
            'photo_id' => 'nullable|exists:photos,id',
            'comment_id' => 'nullable|exists:comments,id',
            'gallery_id' => 'nullable|exists:galleries,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Xác định type dựa trên dữ liệu được gửi
        $type = null;
        if ($request->has('photo_id')) {
            $type = 0;
        } elseif ($request->has('comment_id')) {
            $type = 1;
        } elseif ($request->has('gallery_id')) {
            $type = 2;
        }

        if ($type === null) {
            return response()->json(['error' => 'You must provide at least one of the following IDs: photo_id, comment_id, gallery_id'], 400);
        }

        // Tạo bản ghi report
        $report = Report::create([
            'reporter_id' => $request->reporter_id,
            'violator_id' => $request->violator_id,
            'photo_id' => $request->photo_id,
            'comment_id' => $request->comment_id,
            'gallery_id' => $request->gallery_id,
            'type' => $type,
            'report_reason' => $request->report_reason,
            'report_date' => now(),
            'status' => 'pending',
            'action_taken' => 'none',
        ]);

        return response()->json(['message' => 'We will process your request shortly.', 'report' => $report], 201);
    }
}
