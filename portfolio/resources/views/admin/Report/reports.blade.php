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
    <section class="section main-section">

        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-account-multiple"></i></span>
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
                        <th>Status</th>
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
                        <td>{{$report->id}}</td>
                        <td>{{$report->photo_id}}</td>
                        <td>
                            <img src="{{ asset('' . $report->photo->image_url) }}" width="450" height="450" style="cursor: pointer;" onclick="showModal(this)">
                        </td>
                        <td>{{$report->reporter->username}}</td>
                        <td>{{$report->violator->username}}</td>
                        <td>{{$report->report_reason}}</td>
                        <td>{{ date('d-m-Y', strtotime($report->report_date)) }}</td>
                        <td>
                            @if ($report->status == 'pending')
                                <span style="color: orange;">{{ $report->status }}</span>
                            @elseif ($report->status == 'resolved')
                                <span style="color: green;">{{ $report->status }}</span>
                            @else
                                <span>{{ $report->status }}</span>
                            @endif
                        </td>
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
                                <button class="button small green --jb-modal"  data-target="sample-modal-2" type="button">
                                    <span class="icon"><i class="mdi mdi-eye"></i></span>
                                </button>
                                <button class="button small red --jb-modal" data-target="sample-modal" type="button">
                                    <span class="icon"><i class="mdi mdi-trash-can"></i></span>
                                </button>
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
@endsection
