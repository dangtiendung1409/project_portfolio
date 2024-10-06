<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class reportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['reporter', 'violator', 'photo'])->paginate(10);
        return view('Admin/Report.reports', compact('reports'));
    }

}
