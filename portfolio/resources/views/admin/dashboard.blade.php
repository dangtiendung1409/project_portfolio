@extends('admin/layout')
@section('content')
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Dashboard</li>
            </ul>
            <a href="https://justboil.me/" onclick="alert('Coming soon'); return false" target="_blank" class="button blue">
                <span class="icon"><i class="mdi mdi-credit-card-outline"></i></span>
                <span>Premium Demo</span>
            </a>
        </div>
    </section>

    <section class="section main-section">
        <div class="grid gap-6 grid-cols-1 md:grid-cols-3 mb-6">
            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div class="widget-label">
                            <h3>
                               Total photo
                            </h3>
                            <h1>
                                {{ $approvedPhotosCount }}
                            </h1>
                        </div>
                        <span class="icon widget-icon text-green-500"><i class="mdi mdi-image mdi-48px"></i></span> <!-- Icon ảnh -->
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div class="widget-label">
                            <h3>
                              Total User
                            </h3>
                            <h1>
                               {{$totalUser}}
                            </h1>
                        </div>
                        <span class="icon widget-icon text-blue-500"><i class="mdi mdi-account mdi-48px"></i></span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div class="widget-label">
                            <h3>
                                Total category
                            </h3>
                            <h1>
                                {{$totalCategories}}
                            </h1>
                        </div>
                        <span class="icon widget-icon text-red-500"><i class="mdi mdi-format-list-bulleted mdi-48px"></i></span>
                    </div>
                </div>
            </div>
        </div>
        {{--photo pending--}}
        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-image"></i></span>
                    Photos pending
                </p>
                <a href="#" class="card-header-icon">
                    <span class="icon"><i class="mdi mdi-reload"></i></span>
                </a>
            </header>
            <div class="card-content">
                @if($photoPending->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        There are no photos pending
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
                            <th>title</th>
                            <th>description</th>
                            <th>image</th>
                            <th>location</th>
                            <th>user name</th>
                            <th>category name</th>
                            <th>upload date</th>
                            <th>privacy_status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($photoPending as $photo)
                            @php
                                // Lọc ảnh có trạng thái pending
                                $pendingImages = $photo->images->filter(function($image) {
                                    return $image->photo_status == 'pending';
                                });
                            @endphp

                            @foreach($pendingImages as $image)
                                <tr>
                                    <td class="checkbox-cell">
                                        <label class="checkbox">
                                            <input type="checkbox">
                                            <span class="check"></span>
                                        </label>
                                    </td>
                                    <td>{{ $photo->id }}</td>
                                    <td>{{ $photo->title }}</td>
                                    <td>{{ $photo->description }}</td>
                                    <td>
                                        <img src="{{ asset($image->image_url) }}" width="450" height="450" style="cursor: pointer; margin-bottom: 10px;" onclick="showModal(this)">
                                    </td>
                                    <td>{{ $photo->location }}</td>
                                    <td>{{ $photo->user->username }}</td>
                                    <td>{{ $photo->category->category_name }}</td>
                                    <td>{{ date('d-m-Y', strtotime($photo->upload_date)) }}</td>
                                    <td class="{{ $photo->privacy_status === 'private' ? 'text-private' : 'text-public' }}">
                                        {{ $photo->privacy_status }}
                                    </td>
                                    <td class="actions-cell">
                                        <div class="buttons right nowrap">
                                            <!-- Nút approve -->
                                            <form action="{{ route('admin.photoPending.updateStatus', ['id' => $photo->id, 'status' => 'approved']) }}" method="POST" onsubmit="return confirm('Are you sure you want to update status approve?');" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="button small green">
                                                    <span class="icon"><i class="mdi mdi-check"></i></span> Approve
                                                </button>
                                            </form>

                                            <!-- Nút reject -->
                                            <form action="{{ route('admin.photoPending.updateStatus', ['id' => $photo->id, 'status' => 'rejected']) }}" method="POST" onsubmit="return confirm('Are you sure you want to update status reject?');" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="button small red">
                                                    <span class="icon"><i class="mdi mdi-close"></i></span> Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                @foreach ($photoPending->getUrlRange(1, $photoPending->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $photoPending->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $photoPending->currentPage() }} of {{ $photoPending->lastPage() }}</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        {{--report pending--}}
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
        {{--comment pending--}}
        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-account-multiple"></i></span>
                    List Comment
                </p>
                <a href="#" class="card-header-icon">
                    <span class="icon"><i class="mdi mdi-reload"></i></span>
                </a>
            </header>
            <div class="card-content">
                <table>
                    <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <label class="checkbox">
                                <input type="checkbox">
                                <span class="check"></span>
                            </label>
                        </th>
                        <th>ID</th>
                        <th>Photo ID</th>
                        <th>User name</th>
                        <th>Image</th>
                        <th>Category name</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($comments as $comment)
                        <tr>
                            <td class="checkbox-cell">
                                <label class="checkbox">
                                    <input type="checkbox">
                                    <span class="check"></span>
                                </label>
                            </td>
                            <td>{{ $comment->id }}</td>
                            <td>{{ $comment->photoImage->photo->id ?? 'No id' }}</td>
                            <td>{{ $comment->user->username ?? 'Unknown' }}</td>
                            <td>
                                @if ($comment->photoImage)
                                    <img src="{{ $comment->photoImage->image_url }}" width="450" height="450" style="cursor: pointer; margin-bottom: 10px;" onclick="showModal(this)">
                                @else
                                    No Image
                                @endif
                            </td>
                            <td>{{ $comment->photoImage->photo->category->category_name ?? 'No Location' }}</td>
                            <td>{{ $comment->comment_text }}</td>
{{--                            <td>--}}
{{--                                @if ($comment->comment_status == 'pending')--}}
{{--                                    <span style="color: orange;">{{ $comment->comment_status }}</span>--}}
{{--                                @elseif ($comment->comment_status == 'approved')--}}
{{--                                    <span style="color: green;">{{ $comment->comment_status }}</span>--}}
{{--                                @elseif ($comment->comment_status == 'rejected')--}}
{{--                                    <span style="color: red;">{{ $comment->comment_status }}</span>--}}
{{--                                @else--}}
{{--                                    <span>{{ $comment->comment_status }}</span>--}}
{{--                                @endif--}}
{{--                            </td>--}}
                            <td>{{ date('d-m-Y', strtotime($comment->comment_date)) }}</td>
{{--                            <td class="actions-cell">--}}
{{--                                <div class="buttons right nowrap">--}}
{{--                                    @if($comment->comment_status == 'pending')--}}
{{--                                        <form action="{{ route('admin.comment.updateStatus', ['id' => $comment->id, 'status' => 'approved']) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this comment?');">--}}
{{--                                            @csrf--}}
{{--                                            <button class="button small green" type="submit">--}}
{{--                                                <span class="icon"><i class="mdi mdi-check"></i></span>--}}
{{--                                                Approve--}}
{{--                                            </button>--}}
{{--                                        </form>--}}

{{--                                        <form action="{{ route('admin.comment.updateStatus', ['id' => $comment->id, 'status' => 'rejected']) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this comment?');">--}}
{{--                                            @csrf--}}
{{--                                            <button class="button small red" type="submit">--}}
{{--                                                <span class="icon"><i class="mdi mdi-close"></i></span>--}}
{{--                                                Reject--}}
{{--                                            </button>--}}
{{--                                        </form>--}}
{{--                                    @else--}}
{{--                                        <!-- Nếu comment đã được approved hoặc rejected thì không hiển thị nút nữa -->--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </td>--}}
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="table-pagination">
                    <div class="flex items-center justify-between">
                        <div class="buttons">
                            @foreach ($comments->getUrlRange(1, $comments->lastPage()) as $page => $url)
                                <a href="{{ $url }}" class="button {{ $comments->currentPage() == $page ? 'active' : '' }}">
                                    {{ $page }}
                                </a>
                            @endforeach
                        </div>
                        <small>Page {{ $comments->currentPage() }} of {{ $comments->lastPage() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="imgModal">
        <div id="caption"></div>
    </div>
@endsection
