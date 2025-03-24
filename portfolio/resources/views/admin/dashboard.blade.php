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
                            <th>image</th>
                            <th>user name</th>
                            <th>category name</th>
                            <th>upload date</th>
                            <th>privacy_status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($photoPending as $photo)
                            <tr>
                                <td class="checkbox-cell">
                                    <label class="checkbox">
                                        <input type="checkbox">
                                        <span class="check"></span>
                                    </label>
                                </td>
                                <td>{{ $photo->id }}</td>
                                <td>{{ $photo->title }}</td>
                                <td>
                                    @if($photo->image_url)
                                        <img src="{{ asset($photo->image_url) }}" width="150" height="150" style="cursor: pointer; margin-bottom: 10px;" onclick="showModal(this)">
                                    @endif
                                </td>
                                <td>{{ $photo->user->username }}</td>
                                <td>{{ $photo->category->category_name }}</td>
                                <td>{{ date('d-m-Y', strtotime($photo->upload_date)) }}</td>
                                <td>
                                    <span style="color: {{ $photo->privacy_status == 0 ? 'green' : 'red' }}; font-weight: bold;">
                                         {{ $photo->privacy_status == 0 ? 'Public' : 'Private' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ url('/admin/photo/details/' . $photo->id) }}" class="button small green">
                                        <span class="icon"><i class="mdi mdi-eye"></i></span>
                                    </a>
                                </td>
                            </tr>
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
        {{--report photo pending--}}
        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-file-chart"></i></span>
                    Photo Reports
                </p>
            </header>
            <div class="card-content">
                @if($photoReports->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        There are no reports
                    </div>
                @else
                    <table>
                        <thead>
                        <tr>
                            <th>Photo id</th>
                            <th>Image</th>
                            <th>Reporter name</th>
                            <th>Violator name</th>
                            <th>Report reason</th>
                            <th>Report date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($photoReports as $report)
                            <tr>
                                <td>{{ $report->photo_id }}</td>
                                <!-- Kiểm tra xem $report->photoImage có tồn tại hay không trước khi hiển thị -->
                                <td>
                                    @if($report->photo && $report->photo->image_url)
                                        <img src="{{ asset('' . $report->photo->image_url) }}" width="450" height="450" style="cursor: pointer;" onclick="showModal(this)">
                                    @else
                                        <span style="color: red;">Photo not available</span>
                                    @endif
                                </td>

                                <td>{{ $report->reporter->username }}</td>
                                <td>{{ $report->violator->username }}</td>
                                <td>{{ $report->report_reason }}</td>
                                <td>{{ date('d-m-Y', strtotime($report->report_date)) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                @foreach ($photoReports->getUrlRange(1, $photoReports->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $photoReports->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $photoReports->currentPage() }} of {{ $photoReports->lastPage() }}</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        {{--report gallery pending--}}
        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-file-chart"></i></span>
                    Gallery Reports
                </p>
            </header>
            <div class="card-content">
                @if($galleryReports->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        There are no reports
                    </div>
                @else
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gallery ID</th>
                            <th>Reporter</th>
                            <th>Violator</th>
                            <th>Reason</th>
                            <th>Report Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($galleryReports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->gallery->id ?? 'N/A' }}</td>
                                <td>{{ $report->reporter->username ?? 'N/A' }}</td>
                                <td>{{ $report->violator->username ?? 'N/A' }}</td>
                                <td>{{ $report->report_reason }}</td>
                                <td>{{ date('d-m-Y', strtotime($report->report_date)) }}</td>
                                <td>
                                    <a href="{{ url('/admin/users/galleries/'. 'photos/' .$report->gallery->id) }}" class="button small green">
                                        <span class="icon"><i class="mdi mdi-eye"></i></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                @foreach ($galleryReports->getUrlRange(1, $galleryReports->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $galleryReports->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $galleryReports->currentPage() }} of {{ $galleryReports->lastPage() }}</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        {{--report comment pending--}}
        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-file-chart"></i></span>
                    Comment Reports
                </p>
            </header>
            <div class="card-content">
                @if($commentReports->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        There are no reports
                    </div>
                @else
                    <table>
                        <thead>
                        <tr>
                            <th>Comment ID</th>
                            <th>Comment Text</th>
                            <th>Reporter Name</th>
                            <th>Violator Name</th>
                            <th>Report Reason</th>
                            <th>Report Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($commentReports as $report)
                            <tr>
                                <td>{{ $report->comment_id }}</td>
                                <td>{{ $report->comment ? $report->comment->comment_text : 'Comment not available' }}</td>
                                <td>{{ $report->reporter->username }}</td>
                                <td>{{ $report->violator->username }}</td>
                                <td>{{ $report->report_reason }}</td>
                                <td>{{ date('d-m-Y', strtotime($report->report_date)) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                @foreach ($commentReports->getUrlRange(1, $commentReports->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $commentReports->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $commentReports->currentPage() }} of {{ $commentReports->lastPage() }}</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="imgModal">
        <div id="caption"></div>
    </div>
@endsection
