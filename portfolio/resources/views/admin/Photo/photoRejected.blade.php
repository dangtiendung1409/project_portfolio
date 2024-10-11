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
                        No photos were rejected
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
                        @foreach($photoRejected as $photo)
                            @php
                                $rejectedImages = $photo->images->filter(function($image) {
                                    return $image->photo_status == 'rejected';
                                });
                            @endphp

                            @if($rejectedImages->isNotEmpty())
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
                                        @foreach($rejectedImages as $image)
                                            <img src="{{ asset($image->image_url) }}" width="450" height="450" style="cursor: pointer; margin-bottom: 10px;" onclick="showModal(this)">
                                        @endforeach
                                    </td>
                                    <td>{{ $photo->location }}</td>
                                    <td>{{ $photo->user->username }}</td>
                                    <td>{{ $photo->category->category_name }}</td>
                                    <td>{{ date('d-m-Y', strtotime($photo->upload_date)) }}</td>
                                    <td class="{{ $photo->privacy_status === 'private' ? 'text-private' : 'text-public' }}">
                                        {{ $photo->privacy_status }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                @foreach ($photoRejected->getUrlRange(1, $photoRejected->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $photoRejected->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
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
