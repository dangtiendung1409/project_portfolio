@extends('admin/layout')

@section('content')
    <link rel="stylesheet" href="{{ asset('Admin/css/add.css') }}">
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Forms</li>
                <li>Add Photo</li>
            </ul>
        </div>
    </section>

    <section class="is-hero-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <h1 class="title">
                Add New Photo
            </h1>
        </div>
    </section>

    <section class="section main-section">
        <div class="card mb-6">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-camera"></i></span>
                    Add Photo Form
                </p>
            </header>
            <div class="card-content">
                <form action="{{ url('/admin/photo/store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Title -->
                    <div class="field">
                        <label class="label">Title</label>
                        <div class="control">
                            <input class="input" type="text" name="title" placeholder="Photo Title" >
                        </div>
                        @error('title')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="field">
                        <label class="label">Description</label>
                        <div class="control">
                            <textarea class="textarea" name="description" placeholder="Describe the photo"></textarea>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="field">
                        <label class="label">Upload Image</label>
                        <div class="file has-name">
                            <label class="file-label">
                                <input class="file-input" type="file"  name="image" onchange="displayThumbnail(this);" >
                                <img id="thumbnailImage" style="display: none; max-width: 50%;" alt="Thumbnail image" />
                            </label>
                        </div>
                        @error('image')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="field">
                        <label class="label">Location</label>
                        <div class="control icons-left">
                            <input class="input" type="text" name="location" placeholder="Photo Location">
                            <span class="icon left"><i class="mdi mdi-map-marker"></i></span>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="field">
                        <label class="label">Category</label>
                        <div class="control">
                            <div class="select">
                                <select name="category_id" >
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @error('category_id')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Privacy Status -->
                    <div class="field">
                        <label class="label">Privacy Status</label>
                        <div class="control">
                            <div class="select">
                                <select name="privacy_status" >
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>
                        </div>
                        @error('privacy_status')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr>

                    <!-- Submit and Reset buttons -->
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

@endsection
