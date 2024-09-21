@extends('admin/layout')
@section('content')
    <link rel="stylesheet" href="{{ asset('Admin/css/add.css') }}">
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
            <a class="btn btn-add" href="/admin/photo/create"  title="Thêm">
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
                        No photos have been approved
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
                    @foreach($photos as $photo)
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
                                    <a href="{{ url('/admin/photo/' . $photo->id . '/edit') }}" class="button small green">
                                        <span class="icon"><i class="mdi mdi-pencil"></i></span>
                                    </a>
                                    <form action="{{ url('/admin/photo/delete/'.$photo->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this photo? This action cannot be undone.');" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button small red" type="submit">
                                            <span class="icon"><i class="mdi mdi-trash-can"></i></span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
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
