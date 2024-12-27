@extends('admin.layout')
@section('content')
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Edit Category</li>
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
                    <span class="icon"><i class="mdi mdi-ballot"></i></span>
                    Edit Category
                </p>
            </header>
            <div class="card-content">
                <form action="{{ route('category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="field">
                        <label class="label">Category Name</label>
                        <div class="control">
                            <input class="input @error('category_name') is-danger @enderror" type="text" name="category_name" value="{{ old('category_name', $category->category_name) }}">
                            @error('category_name')
                            <p class="help is-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Image</label>
                        <div class="control">
                            <input class="input @error('image') is-danger @enderror" type="file" name="image" id="imageInput">
                            <div id="imagePreview" style="margin-top: 10px;">
                                @if ($category->image)
                                    <img src="{{ asset($category->image) }}" style="max-width: 500px;">
                                @endif
                            </div>
                            @error('image')
                            <p class="help is-danger">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="field grouped">
                        <div class="control">
                            <button type="submit" class="button green">
                                Update
                            </button>
                        </div>
                        <div class="control">
                            <a href="{{ url('admin/category') }}" class="button red">Cancel</a>
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
            previewContainer.innerHTML = ''; // Clear previous preview

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
