@extends('admin/layout')
@section('content')
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Photo</li>
            </ul>
            <a href="https://justboil.me/" onclick="alert('Coming soon'); return false" target="_blank" class="button blue">
                <span class="icon"><i class="mdi mdi-credit-card-outline"></i></span>
                <span>Premium Demo</span>
            </a>
        </div>
    </section>

    <section class="is-hero-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0"></div>
        <div class="col-sm-2">
            <a class="btn btn-add" href="/admin/photo/create" title="Thêm">
                <i class="fas fa-plus"></i>
                Add Photo
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
    <form style="display: flex; align-items: center; border-radius: 5px; margin-top: 10px; flex-wrap: wrap;" action="{{url("admin/photo")}}" method="get">
        <!-- Lọc theo title -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input class="form-control" type="text" name="title" placeholder="Title" style=" margin-left:25px; height: 45px; font-size: 0.765625rem; padding: 4px 8px; background-color: #F1F1F1; border-radius: 5px; width: 120px;" />
        </div>

        <!-- Lọc theo location -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input class="form-control" type="text" name="location" placeholder="Location" style="height: 45px; font-size: 0.765625rem; padding: 4px 8px; background-color: #F1F1F1; border-radius: 5px; width: 120px;" />
        </div>
        <!-- Lọc theo category -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <select name="category_id" class="form-control" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 150px;">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input type="date" class="form-control" name="start_date" placeholder="From Date" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 150px;" />
        </div>
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input type="date" class="form-control" name="end_date" placeholder="To Date" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 150px;" />
        </div>

        <!-- Lọc theo privacy_status -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <select name="privacy_status" class="form-control" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 150px;">
                <option value="">Select Privacy</option>
                <option value="0">Public</option>
                <option value="1">Private</option>
            </select>
        </div>

        <!-- Nút lọc -->
        <div class="input-group input-group-sm" style="margin-bottom: 10px; margin-left: 1px;">
            <button style="height: 45px; background-color: #F1F1F1; border: none; border-radius: 5px;" type="submit" class="btn btn-default">
                <i class="mdi mdi-magnify" style="padding: 10px;"></i>
            </button>
        </div>
    </form>
    <!-- Hiển thị danh sách -->
    <section class="section main-section">
        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-image"></i></span>
                    Photos
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
                @if($photos->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        No photos found
                    </div>
                @else
                    <table>
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Upload Date</th>
                            <th>Privacy</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($photos as $photo)
                            <tr>
                                <td>{{ $photo->title }}</td>
                                <td>
                                    <img src="{{ asset($photo->image_url) }}" width="100" height="100" style="cursor: pointer;" onclick="showModal(this)">
                                </td>
                                <td>{{ $photo->location }}</td>
                                <td>{{ $photo->category->category_name ?? 'Uncategorized' }}</td>
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

                                    <a href="{{ url('/admin/photo/comment/' . $photo->id ) }}" class="button small blue">
                                        <span class="icon"><i class="mdi mdi-comment-text-multiple"></i></span>
                                    </a>

                                    <a href="{{ url('/admin/photo/edit/' . $photo->id) }}" class="button small" style="background-color: #FF5733; color: white;">
                                        <span class="icon"><i class="mdi mdi-pencil"></i></span>
                                    </a>

                                    <form action="{{ url('/admin/photo/delete/' . $photo->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline-block;">
                                        @csrf
                                        <button class="button small red" type="submit">
                                            <span class="icon"><i class="mdi mdi-trash-can"></i></span>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                <!-- Link tới trang đầu tiên -->
                                <a href="{{ $photos->url(1) }}" class="button {{ ($photos->currentPage() == 1 && $photos->lastPage() > 1) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-arrow-left-bold"></i>
                                </a>

                                <!-- Link tới trang trước -->
                                <a href="{{ $photos->previousPageUrl() }}" class="button {{ ($photos->currentPage() == 1) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-chevron-left"></i>
                                </a>

                                @if ($photos->lastPage() > 1)
                                    <!-- Hiển thị trang đầu tiên -->
                                    <a href="{{ $photos->url(1) }}" class="button {{ ($photos->currentPage() == 1) ? ' active' : '' }}">1</a>

                                    @if ($photos->currentPage() > 3)
                                        <span class="button disabled">...</span>
                                    @endif

                                    <!-- Hiển thị các liên kết trang -->
                                    @for ($i = max(2, $photos->currentPage() - 1); $i <= min($photos->currentPage() + 1, $photos->lastPage() - 1); $i++)
                                        <a href="{{ $photos->url($i) }}" class="button {{ ($photos->currentPage() == $i) ? ' active' : '' }}">{{ $i }}</a>
                                    @endfor

                                    @if ($photos->currentPage() < $photos->lastPage() - 2)
                                        <span class="button disabled">...</span>
                                    @endif

                                    <!-- Hiển thị trang cuối cùng -->
                                    <a href="{{ $photos->url($photos->lastPage()) }}" class="button {{ ($photos->currentPage() == $photos->lastPage()) ? ' active' : '' }}">{{ $photos->lastPage() }}</a>
                                @else
                                    <!-- Nếu chỉ có 1 trang thì hiển thị trang đầu tiên -->
                                    <a href="{{ $photos->url(1) }}" class="button active">1</a>
                                @endif

                                <!-- Link tới trang kế tiếp -->
                                <a href="{{ $photos->nextPageUrl() }}" class="button {{ ($photos->currentPage() == $photos->lastPage()) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>

                                <!-- Link tới trang cuối cùng -->
                                <a href="{{ $photos->url($photos->lastPage()) }}" class="button {{ ($photos->currentPage() == $photos->lastPage()) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-arrow-right-bold"></i>
                                </a>
                            </div>

                            <!-- Hiển thị thông tin phân trang -->
                            <small>Page {{ $photos->currentPage() }} of {{ $photos->lastPage() }}</small>
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
