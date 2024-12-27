@extends('admin.layout')
@section('content')
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Add Category</li>
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
        <div class="card mb-6">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-ballot"></i></span>
                    Add Category
                </p>
            </header>
            <div class="card-content">
                <form action="{{ url('admin/category/store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="field">
                        <label class="label">Category Name</label>
                        <div class="field-body">
                            <div class="field">
                                <div class="control icons-left">
                                    <input class="input @error('category_name') is-danger @enderror" name="category_name" type="text" placeholder="Name" value="{{ old('category_name') }}">
                                    <span class="icon left"><i class="mdi mdi-account"></i></span>
                                </div>
                                @error('category_name')
                                <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Image</label>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <input class="input @error('image') is-danger @enderror" type="file" name="image" id="imageInput">
                                </div>
                                <div id="imagePreview" style="margin-top: 10px;">
                                    <!-- Preview ảnh sẽ hiển thị ở đây -->
                                </div>
                                @error('image')
                                <p class="help is-danger">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="field grouped">
                        <div class="control">
                            <button type="submit" class="button green">
                                Submit
                            </button>
                        </div>
                        <div class="control">
                            <button type="reset" class="button red">
                                Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Script to preview image --}}
    <script>
        document.getElementById('imageInput').addEventListener('change', function(event) {
            const input = event.target;
            const previewContainer = document.getElementById('imagePreview');
            previewContainer.innerHTML = ''; // Xóa preview cũ nếu có

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '500px';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>
@endsection
