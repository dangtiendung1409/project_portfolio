@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Photo Pending</li>
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

    <section class="section main-section">
        <div class="card has-table">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-image"></i></span>
                    Photo
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
                @if($photoPending->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        There are no photo pending pending
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
                            <th>update status</th>
                            <th>action</th>
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
                                <!-- Link tới trang đầu tiên -->
                                <a href="{{ $photoPending->url(1) }}" class="button {{ ($photoPending->currentPage() == 1 && $photoPending->lastPage() > 1) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-arrow-left-bold"></i>
                                </a>

                                <!-- Link tới trang trước -->
                                <a href="{{ $photoPending->previousPageUrl() }}" class="button {{ ($photoPending->currentPage() == 1) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-chevron-left"></i>
                                </a>

                                @if ($photoPending->lastPage() > 1)
                                    <!-- Hiển thị trang đầu tiên -->
                                    <a href="{{ $photoPending->url(1) }}" class="button {{ ($photoPending->currentPage() == 1) ? ' active' : '' }}">1</a>

                                    @if ($photoPending->currentPage() > 3)
                                        <span class="button disabled">...</span>
                                    @endif

                                    <!-- Hiển thị các liên kết trang -->
                                    @for ($i = max(2, $photoPending->currentPage() - 1); $i <= min($photoPending->currentPage() + 1, $photoPending->lastPage() - 1); $i++)
                                        <a href="{{ $photoPending->url($i) }}" class="button {{ ($photoPending->currentPage() == $i) ? ' active' : '' }}">{{ $i }}</a>
                                    @endfor

                                    @if ($photoPending->currentPage() < $photoPending->lastPage() - 2)
                                        <span class="button disabled">...</span>
                                    @endif

                                    <!-- Hiển thị trang cuối cùng -->
                                    <a href="{{ $photoPending->url($photoPending->lastPage()) }}" class="button {{ ($photoPending->currentPage() == $photoPending->lastPage()) ? ' active' : '' }}">{{ $photoPending->lastPage() }}</a>
                                @else
                                    <!-- Nếu chỉ có 1 trang thì hiển thị trang đầu tiên -->
                                    <a href="{{ $photoPending->url(1) }}" class="button active">1</a>
                                @endif

                                <!-- Link tới trang kế tiếp -->
                                <a href="{{ $photoPending->nextPageUrl() }}" class="button {{ ($photoPending->currentPage() == $photoPending->lastPage()) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>

                                <!-- Link tới trang cuối cùng -->
                                <a href="{{ $photoPending->url($photoPending->lastPage()) }}" class="button {{ ($photoPending->currentPage() == $photoPending->lastPage()) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-arrow-right-bold"></i>
                                </a>
                            </div>

                            <!-- Hiển thị thông tin phân trang -->
                            <small>Page {{ $photoPending->currentPage() }} of {{ $photoPending->lastPage() }}</small>
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
