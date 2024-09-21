@extends("admin/layout")
@section("content")
    <link rel="stylesheet" href="{{ asset('Admin/css/add.css') }}">
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Photo Pending</li>
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
                    <span class="icon"><i class="mdi mdi-image"></i></span>
                    Photos
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
                    <tr>
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
                            <td>{{ $photo->description }}</td>
                            <td>
                                <img src="{{ asset('' . $photo->image_url) }}" width="450" height="450" style="cursor: pointer;" onclick="showModal(this)">
                            </td>
                            <td>{{ $photo->location }}</td>
                            <td>{{ $photo->user->username }}</td>
                            <td>{{ $photo->category->category_name}}</td>
                            <td>{{ date('d-m-Y', strtotime($photo->upload_date)) }}</td>
                            <td class="{{ $photo->privacy_status === 'private' ? 'text-private' : 'text-public' }}">
                                {{ $photo->privacy_status }}
                            </td>
                            <td class="actions-cell">
                                <div class="buttons right nowrap">
                                    <button class="button small green">
                                        <span class="icon"><i class="mdi mdi-eye"></i></span>
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
    </section>
    <!-- The Modal -->
    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="imgModal">
        <div id="caption"></div>
    </div>
@endsection
