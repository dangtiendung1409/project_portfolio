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
    <section class="section main-section">

        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-file-chart"></i></span>
                    Reports
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
                            <th>id</th>
                            <th>Photo id</th>
                            <th>Image</th>
                            <th>Reporter name</th>
                            <th>Violator name</th>
                            <th>Report reason</th>
                            <th>Report date</th>
                            <th>Action taken</th>
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
