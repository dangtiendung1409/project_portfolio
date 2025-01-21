@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Photo Rejected</li>
            </ul>
            <a href="https://justboil.me/" target="_blank" class="button blue">
                <span class="icon"><i class="mdi mdi-credit-card-outline"></i></span>
                <span>Premium Demo</span>
            </a>
        </div>
    </section>

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
                @if($photoRejected->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        There are no photo rejected pending
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
                            <th>action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($photoRejected as $photo)
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
                                    @if($photo->image_url)
                                        <img src="{{ asset($photo->image_url) }}" width="150" height="150" style="cursor: pointer; margin-bottom: 10px;" onclick="showModal(this)">
                                    @endif
                                </td>
                                <td>{{ $photo->location }}</td>
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
                                <!-- Link tới trang đầu tiên -->
                                <a href="{{ $photoRejected->url(1) }}" class="button {{ ($photoRejected->currentPage() == 1 && $photoRejected->lastPage() > 1) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-arrow-left-bold"></i>
                                </a>

                                <!-- Link tới trang trước -->
                                <a href="{{ $photoRejected->previousPageUrl() }}" class="button {{ ($photoRejected->currentPage() == 1) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-chevron-left"></i>
                                </a>

                                @if ($photoRejected->lastPage() > 1)
                                    <!-- Hiển thị trang đầu tiên -->
                                    <a href="{{ $photoRejected->url(1) }}" class="button {{ ($photoRejected->currentPage() == 1) ? ' active' : '' }}">1</a>

                                    @if ($photoRejected->currentPage() > 3)
                                        <span class="button disabled">...</span>
                                    @endif

                                    <!-- Hiển thị các liên kết trang -->
                                    @for ($i = max(2, $photoRejected->currentPage() - 1); $i <= min($photoRejected->currentPage() + 1, $photoRejected->lastPage() - 1); $i++)
                                        <a href="{{ $photoRejected->url($i) }}" class="button {{ ($photoRejected->currentPage() == $i) ? ' active' : '' }}">{{ $i }}</a>
                                    @endfor

                                    @if ($photoRejected->currentPage() < $photoRejected->lastPage() - 2)
                                        <span class="button disabled">...</span>
                                    @endif

                                    <!-- Hiển thị trang cuối cùng -->
                                    <a href="{{ $photoRejected->url($photoRejected->lastPage()) }}" class="button {{ ($photoRejected->currentPage() == $photoRejected->lastPage()) ? ' active' : '' }}">{{ $photoRejected->lastPage() }}</a>
                                @else
                                    <!-- Nếu chỉ có 1 trang thì hiển thị trang đầu tiên -->
                                    <a href="{{ $photoRejected->url(1) }}" class="button active">1</a>
                                @endif

                                <!-- Link tới trang kế tiếp -->
                                <a href="{{ $photoRejected->nextPageUrl() }}" class="button {{ ($photoRejected->currentPage() == $photoRejected->lastPage()) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>

                                <!-- Link tới trang cuối cùng -->
                                <a href="{{ $photoRejected->url($photoRejected->lastPage()) }}" class="button {{ ($photoRejected->currentPage() == $photoRejected->lastPage()) ? ' disabled' : '' }}">
                                    <i class="mdi mdi-arrow-right-bold"></i>
                                </a>
                            </div>

                            <!-- Hiển thị thông tin phân trang -->
                            <small>Page {{ $photoRejected->currentPage() }} of {{ $photoRejected->lastPage() }}</small>
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="imgModal">
        <div id="caption"></div>
    </div>
@endsection
