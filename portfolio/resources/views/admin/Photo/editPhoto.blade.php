@extends('admin/layout')

@section('content')
    <link rel="stylesheet" href="{{ asset('Admin/css/add.css') }}">
    <section class="is-title-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <ul>
                <li>Admin</li>
                <li>Forms</li>
                <li>Edit Photo</li>
            </ul>
        </div>
    </section>

    <section class="is-hero-bar">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">
            <h1 class="title">Edit Photo</h1>
        </div>
    </section>

    <section class="section main-section">
        <div class="card mb-6">
            <header class="card-header">
                <p class="card-header-title">
                    <span class="icon"><i class="mdi mdi-camera"></i></span>
                    Edit Photo Form
                </p>
            </header>
            <div class="card-content">
                <form action="{{ url('/admin/photo/update', $photo->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Title -->
                    <div class="field">
                        <label class="label">Title</label>
                        <div class="control icons-left">
                            <input class="input" type="text" name="title" value="{{ $photo->title }}" >
                            <span class="icon left"><i class="mdi mdi-title"></i></span>
                        </div>
                        @error('title')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="field">
                        <label class="label">Description</label>
                        <div class="control">
                            <textarea class="textarea" name="description">{{ $photo->description }}</textarea>
                        </div>
                    </div>

                    <!-- Upload New Image -->
                    <div class="field">
                        <label class="label">Upload New Image</label>
                        <div class="file has-name">
                            <label class="file-label">
                                <input class="file-input" type="file" name="image" onchange="displayThumbnail(this);">
                                <img id="thumbnailImage" src="{{ asset($photo->image_url) }}" style="max-width: 50%;" alt="Thumbnail image" />
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
                            <input class="input" type="text" name="location" value="{{ $photo->location }}">
                            <span class="icon left"><i class="mdi mdi-map-marker"></i></span>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="field">
                        <label class="label">Category</label>
                        <div class="control">
                            <div class="select">
                                <select name="category_id">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @if($category->id == $photo->category_id) selected @endif>{{ $category->category_name }}</option>
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
                                <select name="privacy_status">
                                    <option value="public" @if($photo->privacy_status == 'public') selected @endif>Public</option>
                                    <option value="private" @if($photo->privacy_status == 'private') selected @endif>Private</option>
                                </select>
                            </div>
                        </div>
                        @error('privacy_status')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tag Suggestions -->
                    <div class="field">
                        <label class="label">Tag Suggestions</label>
                        <div class="control">
                            <div id="tag-suggestions" class="tags">
                                @foreach ($availableTags as $tag)
                                    <span class="tag" onclick="addTag('{{ $tag->tag_name }}')">{{ $tag->tag_name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Input để nhập thêm tag -->
                    <div class="field">
                        <label class="label">Add Custom Tags</label>
                        <div class="control">
                            <input id="tag-input" class="input" type="text" placeholder="Enter a custom tag" onkeypress="addTagOnEnter(event)">
                            <button type="button" class="button" onclick="addCustomTag()">Add Tag</button>
                        </div>
                    </div>
                    <!-- Selected Tags -->
                    <div class="field">
                        <label class="label">Selected Tags</label>
                        <div class="control">
                            <div id="selected-tags" class="tags">
                                @foreach ($photo->tags as $tag)
                                    <span class="tag is-primary">
                                    {{ $tag->tag_name }}
                                    <span class="mdi mdi-close delete-icon" onclick="removeTag('{{ $tag->tag_name }}')"></span>
                                </span>
                                @endforeach
                            </div>
                            <input type="hidden" name="tags" id="tags" value="{{ implode(',', $photo->tags->pluck('tag_name')->toArray()) }}">
                        </div>
                    </div>
                    <!-- Submit button -->
                    <div class="field grouped">
                        <div class="control">
                            <button type="submit" class="button green">
                                Update Photo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        let selectedTags = @json($photo->tags->pluck('tag_name')->toArray());

        function addTag(tagName) {
            if (!selectedTags.includes(tagName)) {
                selectedTags.push(tagName);
                updateTagList();
            }
        }

        function removeTag(tag) {
            selectedTags = selectedTags.filter(t => t !== tag);
            updateTagList();
        }

        function updateTagList() {
            let tagList = document.getElementById('selected-tags');
            let tagsInput = document.getElementById('tags');
            tagList.innerHTML = '';  // Clear previous list

            selectedTags.forEach(tag => {
                let tagElement = document.createElement('span');
                tagElement.className = 'tag is-primary';
                tagElement.innerText = tag;

                // Tạo icon xóa
                let removeButton = document.createElement('span');
                removeButton.className = 'mdi mdi-close delete-icon';
                removeButton.onclick = () => removeTag(tag);

                // Thêm icon vào tag
                tagElement.appendChild(removeButton);
                tagList.appendChild(tagElement);
            });

            // Cập nhật giá trị cho input ẩn
            tagsInput.value = selectedTags.join(',');
        }

        function addCustomTag() {
            const tagInput = document.getElementById('tag-input');
            const tagName = tagInput.value.trim();
            if (tagName && !selectedTags.includes(tagName)) {
                addTag(tagName);
                tagInput.value = ''; // Xóa ô nhập sau khi thêm
            }
        }

        function addTagOnEnter(event) {
            if (event.key === 'Enter') {
                addCustomTag();
            }
        }
    </script>
@endsection
