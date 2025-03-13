@extends('admin/layout')
@section('content')
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>List Contact</li>
            </ul>
            <a href="https://justboil.me/" target="_blank" class="button blue">
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
                    <span class="icon"><i class="mdi mdi-email-outline"></i></span>
                    Contact
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
                @if($contacts->isEmpty())
                    <div class="notification is-warning" style="text-align: center; color: red; font-size: 20px;">
                        No contacts found
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Contact Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($contacts as $contact)
                            <tr>
                                <td class="checkbox-cell">
                                    <label class="checkbox">
                                        <input type="checkbox">
                                        <span class="check"></span>
                                    </label>
                                </td>
                                <td>{{ $contact->id }}</td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->subject }}</td>
                                <td>{{ Str::limit($contact->message, 50) }}</td>
                                <td>{{ date('d-m-Y', strtotime($contact->contact_date)) }}</td>
                                <td><span style="color: {{ $contact->status == 'pending' ? 'orange' : ($contact->status == 'processed' ? 'green' : 'red') }}; font-weight: bold;">
                                {{ ucfirst($contact->status) }}
                                </span>
                                </td>

                                @if ($contact->status !== 'processed')
                                    <td class="actions-cell">
                                        <div class="buttons right nowrap">
                                            <a href="{{ route('admin.contact.send', $contact->id) }}" class="button small blue">
                                                <span class="icon"><i class="mdi mdi-send"></i></span>
                                            </a>
                                        </div>
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="table-pagination">
                        <div class="flex items-center justify-between">
                            <div class="buttons">
                                @foreach ($contacts->getUrlRange(1, $contacts->lastPage()) as $page => $url)
                                    <a href="{{ $url }}" class="button {{ $contacts->currentPage() == $page ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <small>Page {{ $contacts->currentPage() }} of {{ $contacts->lastPage() }}</small>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </section>
@endsection
