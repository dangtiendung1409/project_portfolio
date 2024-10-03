@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List Comment</li>
            </ul>
            <a href="https://justboil.me/"  target="_blank" class="button blue">
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
                    <span class="icon"><i class="mdi mdi-account-multiple"></i></span>
                    List Comment User
                </p>
                <a href="#" class="card-header-icon">
                    <span class="icon"><i class="mdi mdi-reload"></i></span>
                </a>
            </header>
            <div class="card-content">
                @if($photo->comments->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        No comments found for this photo
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
                        <th>photo id</th>
                        <th>user name</th>
                        <th>email</th>
                        <th>comment</th>
                        <th>comment date</th>
                        <th>status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($photo->comments as $comment)
                    <tr>
                        <td class="checkbox-cell">
                            <label class="checkbox">
                                <input type="checkbox">
                                <span class="check"></span>
                            </label>
                        </td>
                        <td>{{ $comment->id }}</td>
                        <td>{{ $comment->user->username }}</td>
                        <td>{{ $comment->user->email }}</td>
                        <td>{{ $comment->comment_text }}</td>
                        <td>{{ date('d-m-Y', strtotime($comment->comment_date)) }}</td>
                        <td>
                            @if ($comment->comment_status == 'pending')
                                <span style="color: orange;">{{ $comment->comment_status }}</span>
                            @elseif ($comment->comment_status == 'approved')
                                <span style="color: green;">{{ $comment->comment_status }}</span>
                            @elseif ($comment->comment_status == 'rejected')
                                <span style="color: red;">{{ $comment->comment_status }}</span>
                            @else
                                <span>{{ $comment->comment_status }}</span>
                            @endif
                        </td>
                        <td class="actions-cell">
                            <div class="buttons right nowrap">
                                @if($comment->comment_status == 'pending')
                                    <form action="{{ route('admin.comment.updateStatus', ['id' => $comment->id, 'status' => 'approved']) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this comment?');">
                                        @csrf
                                        <button class="button small green" type="submit">
                                            <span class="icon"><i class="mdi mdi-check"></i></span>
                                            Approve
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.comment.updateStatus', ['id' => $comment->id, 'status' => 'rejected']) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this comment?');">
                                        @csrf
                                        <button class="button small red" type="submit">
                                            <span class="icon"><i class="mdi mdi-close"></i></span>
                                            Reject
                                        </button>
                                    </form>
                                @else
                                    <!-- Nếu comment đã được approved hoặc rejected thì không hiển thị nút nữa -->
                                @endif
                            </div>
                        </td>

                    </tr>
                    @endforeach
                    </tbody>
                </table>
                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                @foreach ($comments->getUrlRange(1, $comments->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $comments->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $comments->currentPage() }} of {{ $comments->lastPage() }}</small>
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </section>

@endsection
