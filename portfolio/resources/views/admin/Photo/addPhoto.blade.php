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
                                <input class="file-input" type="file" name="image" onchange="displayThumbnail(this);">
                            </label>
                        </div>
                        @error('image')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Thumbnail Display -->
                    <div id="thumbnail-container" style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <!-- Thumbnail images will be displayed here -->
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
                                    <option value="0">Public</option>
                                    <option value="1">Private</option>
                                </select>
                            </div>
                        </div>
                        @error('privacy_status')
                        <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr>
                    <div class="field">
                        <label class="label">Tag Suggestions</label>
                        <div class="control">
                            <div id="tag-suggestions" class="tags">
                                <!-- Các tag gợi ý được thêm tự động từ controller -->
                                @foreach ($tags as $tag)
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

                    <!-- Tag List -->
                    <div class="field">
                        <label class="label">Selected Tags</label>
                        <div class="control">
                            <div id="selected-tags" class="tags">
                                <!-- Các tag đã chọn sẽ hiển thị ở đây -->
                            </div>
                            <input type="hidden" name="tags" id="tags" value="">
                        </div>
                    </div>
                    <!-- Submit and Reset buttons -->
                    <div class="field grouped">
                        <div class="control">
                            <button type="submit" class="button green">
                                Submit
                            </button>
                        </div>
                        <div class="control">
                            <a type="reset" href="/admin/photo" style="background-color: black; color: white;display: inline-block;padding: 10px 20px;border: none;border-radius: 5px;cursor: pointer;transition: background-color 0.3s ease;" class="button">
                                Back
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script>
        let selectedTags = [];
        console.log(selectedTags);
        function addTag(tag) {
            if (!selectedTags.includes(tag)) {
                selectedTags.push(tag);
                updateTagList();
            }
        }

        function addTagOnEnter(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                addCustomTag();
            }
        }

        function addCustomTag() {
            let tagInput = document.getElementById('tag-input');
            let tag = tagInput.value.trim();
            if (tag !== '' && !selectedTags.includes(tag)) {
                selectedTags.push(tag);
                updateTagList();
                tagInput.value = '';  // Clear input
            }
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
        function removeTag(tag) {
            selectedTags = selectedTags.filter(t => t !== tag);
            updateTagList();
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Đặt hàm displayThumbnail ở đây
            function displayThumbnail(input) {
                console.log(input.files); // Add this line to check if files are being selected
                const container = document.getElementById('thumbnail-container');
                container.innerHTML = '';  // Clear previous thumbnails

                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '200px';
                        img.style.height = 'auto';
                        img.style.border = '1px solid #ccc';
                        img.style.borderRadius = '4px';
                        container.appendChild(img);
                    };
                    reader.readAsDataURL(input.files[0]);  // Read the file as a Data URL
                }
            }

            // Gán displayThumbnail cho input
            const fileInput = document.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.onchange = function () {
                    displayThumbnail(this);
                };
            }
        });

    </script>

@endsection

