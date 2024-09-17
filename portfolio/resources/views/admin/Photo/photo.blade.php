@extends('admin/layout')
@section('content')
    <style>
        .text-private {
            color: red;
        }

        .text-public {
            color: green;
        }
        .btn-add {
            color: white;
            background-color: black;

            border-radius: .357rem;
            border: none;
            font-weight: 600;
            padding: 10px 20px;
        }

        .btn-danger {
            color: #FFF;
            background-color: #dc3545;
            border-color: #dc3545;

            border-radius: .357rem;
            border: none;
            font-weight: 600;
            padding: 5px 20px;
        }
        .btn-danger:hover {
            color: #FFF;
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-danger:focus, .btn-danger.focus {
            -webkit-box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.5);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.5);
        }

        .btn-danger.disabled, .btn-danger:disabled {
            color: #FFF;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .button-group {
            display: flex;
        }

        .button-group > * {
            margin-right: 10px;
        }
    </style>
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
            <a class="btn btn-add" href="/admin/addBlog"  title="Thêm">
                <i class="fas fa-plus"></i>
                Add Photo
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
                                <img src="{{ asset('' . $photo->image_url) }}" width="500" height="500">
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
                                    <button class="button small green --jb-modal"  type="button">
                                        <span class="icon"><i class="mdi mdi-pencil"></i></span>
                                    </button>
                                    <button class="button small red --jb-modal"  type="button">
                                        <span class="icon"><i class="mdi mdi-trash-can"></i></span>
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
@endsection
