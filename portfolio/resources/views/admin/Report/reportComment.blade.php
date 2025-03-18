@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List of report comment</li>
            </ul>
            <a href="https://justboil.me/" target="_blank" class="button blue">
                <span class="icon"><i class="mdi mdi-credit-card-outline"></i></span>
                <span>Premium Demo</span>
            </a>
        </div>
    </section>

    @if (session('successMessage') || session('errorMessage'))
        <div id="alertsContainer">
            @if (session('successMessage'))
                <div class="alert alert-success" id="successAlert">
                    <i class="mdi mdi-check-circle" style="margin-right: 8px;"></i>
                    <span>{{ session('successMessage') }}</span>
                </div>
            @endif
            @if (session('errorMessage'))
                <div class="alert alert-danger" id="errorAlert">
                    <i class="mdi mdi-alert-circle" style="margin-right: 8px;"></i>
                    <span>{{ session('errorMessage') }}</span>
                </div>
            @endif
        </div>
    @endif
    <form style="display: flex; align-items: center; border-radius: 5px; margin-top: 10px; flex-wrap: wrap;" action="{{url('admin/reports/comment')}}" method="get">
        <!-- Lọc theo name violator -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input class="form-control" type="text" name="violator_name" placeholder="Violator Name" style=" margin-left:22px; height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 120px;" />
        </div>

        <!-- Lọc theo name reporter -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input class="form-control" type="text" name="reporter_name" placeholder="Reporter Name" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 120px;" />
        </div>

        <!-- Lọc theo lý do báo cáo -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input class="form-control" type="text" name="report_reason" placeholder="Report Reason" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 120px;" />
        </div>

        <!-- Lọc theo ngày báo cáo từ ngày nào đến ngày nào -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input type="date" class="form-control" name="start_date" placeholder="From Date" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 150px;" />
        </div>
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input type="date" class="form-control" name="end_date" placeholder="To Date" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 150px;" />
        </div>

        <!-- Lọc theo action taken -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <select name="action_taken" class="form-control" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 150px;">
                <option value="">Select Action</option>
                <option value="none">None</option>
                <option value="removed">Removed</option>
                <option value="no violation">No Violation</option>
            </select>
        </div>

        <!-- Nút lọc -->
        <div class="input-group input-group-sm" style="margin-bottom: 10px; margin-left: 1px;">
            <button style="height: 45px; background-color: #F1F1F1; border: none; border-radius: 5px;" type="submit" class="btn btn-default">
                <i class="mdi mdi-magnify" style="padding: 10px;"></i>
            </button>
        </div>
    </form>
    <section class="section main-section">
        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-file-chart"></i></span>
                    Comment Reports
                </p>
                <form class="card-header-icon" method="get" onchange="this.submit()">
                    <select style="margin-left:-10px; padding: 5px 10px; border: 1px solid #F1F1F1" name="size">
                        <option value="10" {{ request('size') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('size') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('size') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('size') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <input type="hidden" name="page" value="{{ request('page', 1) }}">
                </form>
            </header>
            <div class="card-content">
                @if($reports->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        There are no reports
                    </div>
                @else
                    <table>
                        <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <label class="checkbox">
                                    <input type="checkbox">
                                    <span class="check"></span>
                                </label>
                            </th>
                            <th>Comment ID</th>
                            <th>Comment Text</th>
                            <th>Reporter Name</th>
                            <th>Violator Name</th>
                            <th>Report Reason</th>
                            <th>Report Date</th>
                            <th>Action Taken</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td class="checkbox-cell">
                                    <label class="checkbox">
                                        <input type="checkbox">
                                        <span class="check"></span>
                                    </label>
                                </td>
                                <td>{{ $report->comment_id }}</td>
                                <td>{{ $report->comment ? $report->comment->comment_text : 'Comment not available' }}</td>
                                <td>{{ $report->reporter->username }}</td>
                                <td>{{ $report->violator->username }}</td>
                                <td>{{ $report->report_reason }}</td>
                                <td>{{ date('d-m-Y', strtotime($report->report_date)) }}</td>
                                <td>
                                    @if ($report->action_taken == 'none')
                                        <span style="color: black;">{{ $report->action_taken }}</span>
                                    @elseif ($report->action_taken == 'removed')
                                        <span style="color: red;">{{ $report->action_taken }}</span>
                                    @elseif($report->action_taken == 'no violation')
                                        <span style="color: green;">{{ $report->action_taken }}</span>
                                    @else
                                        <span>{{ $report->action_taken }}</span>
                                    @endif
                                </td>
                                <td class="actions-cell">
                                    <div class="buttons right nowrap">
                                        @if ($report->status == 'pending')
                                            <!-- Nút Remove -->
                                            <form action="{{ route('admin.report.updateStatus', ['id' => $report->id, 'action' => 'removed']) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this report?');">
                                                @csrf
                                                <button class="button small red" type="submit">
                                                    <span class="icon"><i class="mdi mdi-delete"></i></span>
                                                    Remove
                                                </button>
                                            </form>
                                            <!-- Nút No Violation -->
                                            <form action="{{ route('admin.report.updateStatus', ['id' => $report->id, 'action' => 'no violation']) }}" method="POST" onsubmit="return confirm('Are you sure this report has no violation?');">
                                                @csrf
                                                <button class="button small green" type="submit">
                                                    <span class="icon"><i class="mdi mdi-check-circle"></i></span>
                                                    No Violation
                                                </button>
                                            </form>
                                        @else
                                            <!-- Nếu report đã có trạng thái khác, không hiển thị nút -->
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                @foreach ($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $reports->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $reports->currentPage() }} of {{ $reports->lastPage() }}</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
