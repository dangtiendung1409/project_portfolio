@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List of users with locked accounts</li>
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
                    List User
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
                            <th>Followers</th>
                            <th>Total Photos</th>
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
                                <td> {{ $user->followers()->count() }}</td>
                                <td>{{ $user->photos_count }}</td>
                                <td>{{$user->violation_count}}</td>
                                <td class="actions-cell">
                                    <div class="buttons right nowrap">
                                        <form action="{{ route('admin.unlockUser', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to unlock this user account?');">
                                            @csrf
                                            <button type="submit" class="button small green">
                                                <span class="icon"><i class="mdi mdi-lock-open"></i></span>
                                                <span>Unlock</span>
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
