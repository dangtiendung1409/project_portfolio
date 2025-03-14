@extends('admin/layout')
@section('content')
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List Blog</li>
            </ul>
            <a href="https://justboil.me/" target="_blank" class="button blue">
                <span class="icon"><i class="mdi mdi-credit-card-outline"></i></span>
                <span>Premium Demo</span>
            </a>
        </div>
    </section>

    <section class="is-hero-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0"></div>
        <div class="col-sm-2">
            <a class="btn btn-add" href="/admin/blogs/create" title="Thêm">
                <i class="fas fa-plus"></i>
                Add Blog
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
                    <span class="icon"><i class="mdi mdi-book-open-page-variant"></i></span>
                    Blog
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
                @if($blogs->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        No blogs available
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
                            <th>ID</th>
                            <th>Title</th>
                            <th>Image Content</th>
                            <th>Cover Image</th>
                            <th>Content</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($blogs as $blog)
                            <tr>
                                <td class="checkbox-cell">
                                    <label class="checkbox">
                                        <input type="checkbox">
                                        <span class="check"></span>
                                    </label>
                                </td>
                                <td>{{ $blog->id }}</td>
                                <td>{{ $blog->title }}</td>
                                <td>
                                    @if($blog->image)
                                        <img src="{{ asset($blog->image) }}"
                                             alt="{{ $blog->title }}"
                                             width="100" height="100"
                                             style="cursor: pointer; margin-bottom: 10px;"
                                             onclick="showModal(this)">
                                    @else
                                        No Image
                                    @endif
                                </td>
                                <td>
                                    @if($blog->cover_image)
                                        <img src="{{ asset($blog->cover_image) }}"
                                             alt="{{ $blog->title }}"
                                             width="100" height="100"
                                             style="cursor: pointer; margin-bottom: 10px;"
                                             onclick="showModal(this)">
                                    @else
                                        No Image
                                    @endif
                                </td>
                                <td>{!! Str::limit($blog->content, 50) !!}</td>

                                <td class="actions-cell">
                                    <div class="buttons right nowrap">
                                        <a href="{{ url('/admin/blogs/edit/'. $blog->id ) }}" class="button small green">
                                            <span class="icon"><i class="mdi mdi-pencil"></i></span>
                                        </a>
                                        <form action="{{ url('/admin/blogs/delete/'.$blog->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this blog?');"
                                              style="display:inline-block;">
                                            @csrf
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

                    <!-- Pagination -->
                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                @foreach ($blogs->getUrlRange(1, $blogs->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $blogs->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $blogs->currentPage() }} of {{ $blogs->lastPage() }}</small>
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
