@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List User</li>
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
                    <span class="icon"><i class="mdi mdi-account-multiple"></i></span>
                    List User
                </p>
                <a href="#" class="card-header-icon">
                    <span class="icon"><i class="mdi mdi-reload"></i></span>
                </a>
            </header>
            <div class="card-content">
                @if($users->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        No users exist
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
                        <th>User name</th>
                        <th>Email</th>
                        <th>Profile Picture</th>
                        <th>Bio</th>
                        <th>Join Date</th>
                        <th>Status</th>
                        <th>Violation count</th>
                        <th>Actions</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="checkbox-cell">
                            <label class="checkbox">
                                <input type="checkbox">
                                <span class="check"></span>
                            </label>
                        </td>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->profile_picture)
                                <img src="{{ asset($user->profile_picture) }}" alt="Profile Picture" width="50">
                            @else
                                No Picture
                            @endif
                        </td>
                        <td>{{ $user->bio ?? 'No Bio' }}</td>
                        <td>{{ date('d-m-Y', strtotime($user->join_date)) }}</td>
                        <td>
                            @if($user->is_active)
                                <span style="color: green;">Active</span>
                            @else
                                <span style="color: red;">Inactive</span>
                            @endif
                        </td>
                        <td>{{$user->violation_count}}</td>
                        <td class="actions-cell">
                            <div class="buttons right nowrap">
                                <button class="button small green --jb-modal"  data-target="sample-modal-2" type="button">
                                    <span class="icon"><i class="mdi mdi-eye"></i></span>
                                </button>
                                <button class="button small red --jb-modal" data-target="sample-modal" type="button">
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
                                @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $users->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
