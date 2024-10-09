@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List comment</li>
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
                    <span class="icon"><i class="mdi mdi-comment-text-multiple"></i></span>
                    List comment
                </p>
                <a href="#" class="card-header-icon">
                    <span class="icon"><i class="mdi mdi-reload"></i></span>
                </a>
            </header>
            <div class="card-content">
                @if($comments->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        There are no comments
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
                        <th>User name</th>
                        <th>Comment text</th>
                        <th>Comment date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($comments as $comment)
                        <tr>
                            <td class="checkbox-cell">
                                <label class="checkbox">
                                    <input type="checkbox">
                                    <span class="check"></span>
                                </label>
                            </td>
                            <td>{{ $comment->user->username }}</td>
                            <td>{{ $comment->comment_text }}</td>
                            <td>{{ date('d-m-Y', strtotime($comment->comment_date)) }}</td>
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
