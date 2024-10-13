@extends('admin/layout')
@section('content')
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Change Password</li>
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
        <div class="card">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-lock"></i></span>
                    Change Password
                </p>
            </header>
            <div class="card-content">
                <form method="POST" action="{{ route('admin.updatePassword') }}">
                    @csrf
                    <div class="field">
                        <label class="label">Current password</label>
                        <div class="control">
                            <input type="password" name="password_current" autocomplete="current-password" class="input" required>
                        </div>
                        @error('password_current')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                        <p class="help">Required. Your current password</p>
                    </div>
                    <hr>
                    <div class="field">
                        <label class="label">New password</label>
                        <div class="control">
                            <input type="password" autocomplete="new-password" name="password" class="input" required>
                        </div>
                        @error('password')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                        <p class="help">Required. New password</p>
                    </div>
                    <div class="field">
                        <label class="label">Confirm password</label>
                        <div class="control">
                            <input type="password" autocomplete="new-password" name="password_confirmation" class="input" required>
                        </div>
                        <p class="help">Required. New password one more time</p>
                    </div>
                    <hr>
                    <div class="field">
                        <div class="control">
                            <button type="submit" class="button green">
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
