@extends('admin.layout')

@section('content')
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Profile</li>
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
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
            <!-- Form Edit Profile -->
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        <span class="icon"><i class="mdi mdi-account-circle"></i></span>
                        Edit Profile
                    </p>
                </header>
                <div class="card-content">
                    <form method="POST" action="{{ route('admin.updateProfile') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="field">
                            <label class="label">Avatar</label>
                            <div class="field-body">
                                <div class="field file">
                                    <label class="upload control">
                                        <a class="button blue">Upload</a>
                                        <input type="file" name="profile_picture" id="avatarInput" accept="image/*">
                                    </label>
                                </div>
                                <div class="image-preview" style="margin-top: 10px;">
                                    <img id="avatarPreview" src="" alt="Avatar Preview" class="rounded-full" style="max-width: 150px; display: none;">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="field">
                            <label class="label">Name</label>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <input type="text" name="username" value="{{ $user->username }}" class="input" required>
                                    </div>
                                    <p class="help">Required. Your name</p>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">E-mail</label>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <input type="email" name="email" value="{{ $user->email }}" class="input" required>
                                    </div>
                                    <p class="help">Required. Your e-mail</p>
                                </div>
                            </div>
                        </div>

                        <!-- Trường bio -->
                        <div class="field">
                            <label class="label">Bio</label>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <textarea name="bio" class="textarea">{{ $user->bio }}</textarea>
                                    </div>
                                    <p class="help">Optional. Your bio</p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Nút submit -->
                        <div class="field">
                            <div class="control">
                                <button type="submit" class="button green">Submit</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <!-- View Profile -->
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">
                        <span class="icon"><i class="mdi mdi-account"></i></span>
                        Profile
                    </p>
                </header>
                <div class="card-content">
                    <div class="image w-48 h-48 mx-auto">
                        <img src="{{ $user->profile_picture ? asset($user->profile_picture) : 'https://avatars.dicebear.com/v2/initials/'.$user->username.'.svg' }}" alt="{{ $user->username }}" class="rounded-full">
                    </div>
                    <hr>
                    <div class="field">
                        <label class="label">Name</label>
                        <div class="control">
                            <input type="text" readonly value="{{ $user->username }}" class="input is-static">
                        </div>
                    </div>
                    <hr>
                    <div class="field">
                        <label class="label">E-mail</label>
                        <div class="control">
                            <input type="text" readonly value="{{ $user->email }}" class="input is-static">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label">Bio</label>
                        <div class="control">
                            <input type="text" readonly value="{{ $user->bio }}" class="input is-static">
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.getElementById('avatarInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const avatarPreview = document.getElementById('avatarPreview');
                    avatarPreview.src = e.target.result;
                    avatarPreview.style.display = 'block';
                }

                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
