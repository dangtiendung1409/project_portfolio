@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List galleries photo</li>
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
                    <span class="icon"><i class="mdi mdi-account-multiple"></i></span>
                    Photo in {{ $gallery->galleries_name }}
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
                        No images exist
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
                            <th>Title</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Location</th>
                            <th>Category Name</th>
                            <th>Upload Date</th>
                            <th>Privacy Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($photos as $photoImage)
                            <tr>
                                <td class="checkbox-cell">
                                    <label class="checkbox">
                                        <input type="checkbox">
                                        <span class="check"></span>
                                    </label>
                                </td>
                                <td>{{ $photoImage->photo->title }}</td>
                                <td>{{ $photoImage->photo->description }}</td>
                                <td>
                                    <img src="{{ asset($photoImage->image_url) }}" width="450" height="450" style="cursor: pointer; margin-bottom: 10px;" onclick="showModal(this)">
                                </td>
                                <td>{{ $photoImage->photo->location }}</td>
                                <td>{{ $photoImage->photo->category->category_name }}</td>
                                <td>{{ date('d-m-Y', strtotime($photoImage->photo->upload_date)) }}</td>
                                <td class="{{ $photoImage->photo->privacy_status === 'private' ? 'text-private' : 'text-public' }}">
                                    {{ $photoImage->photo->privacy_status }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <!-- Phân trang -->
                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                @foreach ($photos->getUrlRange(1, $photos->lastPage()) as $page => $url)
                                    <a href="{{ $url . '&size=' . request('size') }}" class="button {{ $photos->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $photos->currentPage() }} of {{ $photos->lastPage() }}</small>
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
