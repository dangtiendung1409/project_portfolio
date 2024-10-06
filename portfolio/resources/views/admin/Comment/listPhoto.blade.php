@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List Photos</li>
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
                    Photos
                </p>
                <a href="#" class="card-header-icon">
                    <span class="icon"><i class="mdi mdi-reload"></i></span>
                </a>
            </header>
            <div class="card-content">
                @if($photos->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        There are no photos available.
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
                            <th>Photo id</th>
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
                        @foreach($photos as $photo)
                            @foreach($photo->images as $image)
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
                                            <a href="{{ url('/admin/photo/comments/'. $photo->id ) }}" class="button small blue">
                                                <span class="icon"><i class="mdi mdi-comment"></i></span>
                                            </a>
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
                                @foreach ($photos->getUrlRange(1, $photos->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $photos->currentPage() == $page ? 'active' : '' }}">
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

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="imgModal">
        <div id="caption"></div>
    </div>
@endsection
