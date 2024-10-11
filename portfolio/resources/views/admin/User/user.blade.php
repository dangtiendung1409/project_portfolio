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
    <form style="display: flex; align-items: center; border-radius: 5px; margin-top: 10px; flex-wrap: wrap;" action="{{url("admin/users")}}" method="get">
        <!-- Lọc theo username -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input class="form-control" type="text" name="username" placeholder="User Name" style="margin-left:21px; height: 45px; font-size: 0.765625rem; padding: 4px 8px; background-color: #F1F1F1; border-radius: 5px; width: 120px;" />
        </div>

        <!-- Lọc theo email -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input class="form-control" type="text" name="email" placeholder="Email" style="height: 45px; font-size: 0.765625rem; padding: 4px 8px; background-color: #F1F1F1; border-radius: 5px; width: 120px;" />
        </div>

        <!-- Lọc theo join_date từ ngày nào đến ngày nào -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input type="date" class="form-control" name="start_date" placeholder="From Date" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 150px;" />
        </div>
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input type="date" class="form-control" name="end_date" placeholder="To Date" style="height: 45px; font-size: 0.765625rem; background-color: #F1F1F1; border-radius: 5px; width: 150px;" />
        </div>

        <!-- Lọc theo violation_count -->
        <div class="input-group input-group-sm" style="margin-right: 5px; margin-bottom: 10px;">
            <input class="form-control" type="number" name="violation_count" placeholder="Violation Count" style="height: 45px; font-size: 0.765625rem; padding: 4px 8px; background-color: #F1F1F1; border-radius: 5px; width: 150px;" />
        </div>

        <!-- Nút lọc -->
        <div class="input-group input-group-sm" style="margin-bottom: 10px; margin-left: 1px;">
            <button style="height: 45px; background-color: #F1F1F1; border: none; border-radius: 5px;" type="submit" class="btn btn-default">
                <i class="mdi mdi-magnify" style="padding: 10px;"></i>
            </button>
        </div>
    </form>

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
                                <a href="{{ url('/admin/users/'. 'photos/' .$user->id) }}" class="button small green">
                                    <span class="icon"><i class="mdi mdi-image"></i></span>
                                </a>
                                <a href="{{ url('admin/users/'. 'galleries/' . $user->id) }}" class="button small blue">
                                    <span class="icon"><i class="mdi mdi-folder-image"></i></span>
                                </a>
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
