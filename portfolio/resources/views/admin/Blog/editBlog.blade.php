@extends('admin.layout')

@section('content')
    <script src="{{ asset('ckeditor4/ckeditor.js') }}"></script>
    <script src="{{ asset('ckfinder/ckfinder.js') }}"></script>

    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Edit Blog</li>
            </ul>
        </div>
    </section>

    @if (session('success'))
        <div class="alert alert-success">
            <i class="mdi mdi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <section class="section main-section">
        <div class="card mb-6">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-note-edit"></i></span>
                    Edit Blog
                </p>
            </header>
            <div class="card-content">
                <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="field">
                        <label class="label">Title</label>
                        <div class="control">
                            <input class="input @error('title') is-danger @enderror" type="text" name="title" value="{{ old('title', $blog->title) }}" required>
                        </div>
                        @error('title')<p class="help is-danger">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label class="label">Content</label>
                        <div class="control">
                            <textarea class="textarea @error('content') is-danger @enderror" name="content" id="content" required>{{ old('content', $blog->content) }}</textarea>
                        </div>
                        @error('content')<p class="help is-danger">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label class="label">Cover Image</label>
                        <div class="control">
                            <input class="input @error('cover_image') is-danger @enderror" type="file" name="cover_image" id="cover_image" accept="image/*" onchange="previewImage(event, 'cover-preview')">
                        </div>
                        <div class="image-preview" id="cover-preview">
                            @if($blog->cover_image)
                                <img src="{{ asset($blog->cover_image) }}" alt="Cover Image" style="max-width: 300px; margin-top: 10px;">
                            @endif
                        </div>
                        @error('cover_image')<p class="help is-danger">{{ $message }}</p>@enderror
                    </div>

                    <div class="field">
                        <label class="label">Additional Image (Optional)</label>
                        <div class="control">
                            <input class="input @error('image') is-danger @enderror" type="file" name="image" id="additional_image" accept="image/*" onchange="previewImage(event, 'additional-preview')">
                        </div>
                        <div class="image-preview" id="additional-preview">
                            @if($blog->image)
                                <img src="{{ asset($blog->image) }}" alt="Additional Image" style="max-width: 300px; margin-top: 10px;">
                            @endif
                        </div>
                        @error('image')<p class="help is-danger">{{ $message }}</p>@enderror
                    </div>

                    <div class="field grouped">
                        <div class="control">
                            <button type="submit" class="button blue">Update</button>
                        </div>
                        <div class="control">
                            <a href="{{ route('admin.blogs.index') }}" class="button red">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        var editor = CKEDITOR.replace('content');
        CKFinder.setupCKEditor(editor);
    </script>

    <script>
        function previewImage(event, previewId) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById(previewId);
            let previewImg = previewContainer.querySelector('img');

            if (!previewImg) {
                previewImg = document.createElement('img');
                previewImg.style.maxWidth = '300px';
                previewImg.style.marginTop = '10px';
                previewContainer.appendChild(previewImg);
            }

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                previewImg.src = '';
                previewContainer.innerHTML = '';
            }
        }
    </script>
@endsection
