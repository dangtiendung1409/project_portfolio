@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List of reports</li>
            </ul>
            <a href="https://justboil.me/"  target="_blank" class="button blue">
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
    <form style="display: flex; align-items: center; border-radius: 5px; margin-top: 10px; flex-wrap: wrap;" action="{{url('admin/reportPending')}}" method="get">
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
                <option value="warning">Warning</option>
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
                    Reports
                </p>
                <a href="#" class="card-header-icon">
                    <span class="icon"><i class="mdi mdi-reload"></i></span>
                </a>
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
                        <th>id</th>
                        <th>Photo id</th>
                        <th>Image</th>
                        <th>Reporter name</th>
                        <th>Violator name</th>
                        <th>Report reason</th>
                        <th>Report date</th>
                        <th>Action taken</th>
                        <th></th>
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
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->photo_id }}</td>

                            <!-- Kiểm tra xem $report->photoImage có tồn tại hay không trước khi hiển thị -->
                            <td>
                                @if($report->photoImage && $report->photoImage->image_url)
                                    <img src="{{ asset('' . $report->photoImage->image_url) }}" width="450" height="450" style="cursor: pointer;" onclick="showModal(this)">
                                @else
                                    <span style="color: red;">Photo not available</span>
                                @endif
                            </td>

                            <td>{{ $report->reporter->username }}</td>
                            <td>{{ $report->violator->username }}</td>
                            <td>{{ $report->report_reason }}</td>
                            <td>{{ date('d-m-Y', strtotime($report->report_date)) }}</td>
                            <td>
                                @if ($report->action_taken == 'none')
                                    <span style="color: black;">{{ $report->action_taken }}</span>
                                @elseif ($report->action_taken == 'removed')
                                    <span style="color: red;">{{ $report->action_taken }}</span>
                                @elseif($report->action_taken == 'warning')
                                    <span style="color: orange;">{{ $report->action_taken }}</span>
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
                                        <form action="{{ route('admin.report.updateStatus', ['id' => $report->id, 'action' => 'warning']) }}" method="POST" onsubmit="return confirm('Are you sure you want to warn this user?');">
                                            @csrf
                                            <button class="button small warning-button" type="submit">
                                                <span class="icon"><i class="mdi mdi-alert-circle"></i></span>
                                                Warning
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
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="imgModal">
        <div id="caption"></div>
    </div>
    <style>
        .warning-button {
            background-color: #f1c40f !important; /* Màu vàng cảnh báo */
            color: #fff !important; /* Màu chữ trắng */
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .warning-button:hover {
            background-color: #d4ac0d !important; /* Đổi màu khi hover để nổi bật hơn */
        }
    </style>
@endsection
