@extends('admin.layout')

@section('content')
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Send Contact Email</li>
            </ul>
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
        <div class="card mb-6">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-email-send"></i></span>
                    Send Contact Email
                </p>
            </header>
            <div class="card-content">
                <form action="{{ route('admin.contact.send', $contact->id) }}" method="post">
                    @csrf
                    <div class="field">
                        <label class="label">Message</label>
                        <div class="control">
                            <textarea class="textarea @error('message') is-danger @enderror" name="message" rows="5" placeholder="Enter your message here..." required></textarea>
                        </div>
                        @error('message')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr>

                    <div class="field grouped">
                        <div class="control">
                            <button type="submit" class="button green">
                                Send Email
                            </button>
                        </div>
                        <div class="control">
                            <a href="{{ url('/admin/contact') }}" class="button red">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
