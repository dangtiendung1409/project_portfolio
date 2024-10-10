@extends("admin/layout")
@section("content")
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List galleries</li>
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
                    List galleries for {{ $user->username }}
                </p>
                <a href="#" class="card-header-icon">
                    <span class="icon"><i class="mdi mdi-reload"></i></span>
                </a>
            </header>
            <div class="card-content">
                @if($galleries->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        No galleries exist
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
                        <th>galleries name</th>
                        <th>galleries description</th>
                        <th>creation date</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($galleries as $galleriesUser)
                    <tr>
                        <td class="checkbox-cell">
                            <label class="checkbox">
                                <input type="checkbox">
                                <span class="check"></span>
                            </label>
                        </td>
                        <td>{{$galleriesUser->galleries_name}}</td>
                        <td>{{$galleriesUser->galleries_description}}</td>
                        <td>{{ date('d-m-Y', strtotime($galleriesUser->creation_date)) }}</td>
                        <td class="actions-cell">
                            <div class="buttons right nowrap">
                                <a href="{{ url('/admin/users/galleries/'. 'photos/' .$galleriesUser->id) }}" class="button small green">
                                    <span class="icon"><i class="mdi mdi-image"></i></span>
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
                                @foreach ($galleries->getUrlRange(1, $galleries->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $galleries->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $galleries->currentPage() }} of {{ $galleries->lastPage() }}</small>
                        </div>
                    </div>
                    @endif
            </div>
        </div>
    </section>
@endsection
