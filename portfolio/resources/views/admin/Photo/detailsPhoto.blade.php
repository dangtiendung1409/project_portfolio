@extends('admin/layout')
@section('content')
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Photo Details</li>
            </ul>
            <a href="https://justboil.me/" target="_blank" class="button blue">
                <span class="icon"><i class="mdi mdi-credit-card-outline"></i></span>
                <span>Premium Demo</span>
            </a>
        </div>
    </section>

    <section class="section main-section">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
            <!-- Photo Details -->
            <div class="card" style="width: 1100px">
                <header class="card-header">
                    <p class="card-header-title">
                        <span class="icon"><i class="mdi mdi-image-outline"></i></span>
                        Photo Details
                    </p>
                </header>
                <div class="card-content">
                    <form enctype="multipart/form-data">
                        <!-- Photo Image -->
                        <div class="field">
                            <label class="label">Photo</label>
                            <div class="field-body">
                                <div class="image-preview" style="margin-top: 10px;">
                                    <img id="photoPreview" src="{{ asset($photo->image_url) }}" alt="Photo Preview" class="rounded" style="max-width: 300px;">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Title and Description in 2 columns -->
                        <div class="field is-horizontal">
                            <div class="field-body">
                                <div class="field">
                                    <label class="label">Title</label>
                                    <div class="control">
                                        <input type="text" name="title" value="{{ $photo->title }}" class="input" readonly>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Description</label>
                                    <div class="control">
                                        <textarea name="description" class="textarea" readonly>{{ $photo->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Date and Location in 2 columns -->
                        <div class="field is-horizontal">
                            <div class="field-body">
                                <div class="field">
                                    <label class="label">Upload Date</label>
                                    <div class="control">
                                        <input type="text" name="upload_date" value="{{ date('d-m-Y', strtotime($photo->upload_date)) }}" class="input" readonly>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Location</label>
                                    <div class="control">
                                        <input type="text" name="location" value="{{ $photo->location }}" class="input" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Category and Tags in 2 columns -->
                        <div class="field is-horizontal">
                            <div class="field-body">
                                <div class="field">
                                    <label class="label">Category</label>
                                    <div class="control">
                                        <input type="text" value="{{ $photo->category->category_name }}" class="input" readonly>
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Tags</label>
                                    <div class="control">
                                        <input type="text" value="{{ implode(', ', $photo->tags->pluck('tag_name')->toArray()) }}" class="input" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Privacy Status and Email in 2 rows -->
                        <div class="field is-horizontal">
                            <div class="field-body">
                                <div class="field">
                                    <label class="label">Privacy Status</label>
                                    <div class="control">
                                        <input type="text" name="privacy_status" value="{{ $photo->privacy_status == 0 ? 'Public' : 'Private' }}" class="input" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field is-horizontal">
                            <div class="field-body">
                                <div class="field">
                                    <label class="label">Email</label>
                                    <div class="control">
                                        <input type="text" name="email" value="{{ $photo->user->email }}" class="input" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field grouped">
                            <div class="control">
                                <a type="reset" href="/admin/photo" style="background-color: black; color: white;display: inline-block;padding: 10px 20px;border: none;border-radius: 5px;cursor: pointer;transition: background-color 0.3s ease;" class="button">
                                    Back
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
