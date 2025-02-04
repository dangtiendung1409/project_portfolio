<template>
    <div v-if="isVisible" class="modal-overlay">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h2>Edit photo</h2>
                <span class="close" @click="closeModal">
                    <i class="fa-solid fa-xmark"></i>
                </span>
            </div>

            <!-- Content -->
            <div class="content">
                <!-- Display photo above the description -->
                <div class="photo-preview">
                    <img :src="localPhoto.image_url || '/front_assets/img/img_5.jpg'" alt="Photo preview" />
                </div>

                <p>
                    Add or confirm your photo(s) information. This helps with the
                    discoverability and search of your photo(s).
                </p>

                <div class="form-group">
                    <label for="title">Title:</label>
                    <input
                        type="text"
                        id="title"
                        v-model="localPhoto.title"
                        placeholder="Enter photo title"
                        maxlength="255"
                    />
                    <small class="char-count">
                        {{ localPhoto.title.length }}/255
                    </small>
                </div>
                <div class="form-group">
                    <label for="location">Location:</label>
                    <input
                        type="text"
                        id="location"
                        v-model="localPhoto.location"
                        placeholder="Enter photo location"
                        maxlength="255"
                    />
                    <small class="char-count">
                        {{ localPhoto.location.length }}/255
                    </small>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea
                        id="description"
                        v-model="localPhoto.description"
                        placeholder="Enter description"
                        maxlength="500"
                    ></textarea>
                    <small class="char-count">
                        {{ localPhoto.description.length }}/500
                    </small>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" v-model="localPhoto.category_id">
                        <option disabled value="">Choose Category</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.category_name }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="privacy_status">Photo Privacy:</label>
                    <select id="privacy_status" v-model="localPhoto.privacy_status">
                        <option value="0">Public</option>
                        <option value="1">Private</option>
                    </select>
                </div>

                <!-- Add Custom Tags -->
                <div class="form-group">
                    <label for="custom-tags">Add Custom Tags:</label>
                    <div class="keywords-input">
                        <input
                            type="text"
                            v-model="keywordInput"
                            @keyup.enter="addKeyword(keywordInput)"
                            placeholder="Add your keywords"
                        />
                        <button @click="addKeyword(keywordInput)">
                            <i class="fa-solid fa-plus"></i> Add
                        </button>
                    </div>
                </div>

                <!-- Tag Suggestions -->
                <div class="form-group">
                    <label for="tag-suggestions">Tag Suggestions:</label>
                    <div class="suggested-keywords">
                        <span
                            v-for="keyword in suggestedKeywords"
                            :key="keyword.id"
                            class="keyword-tag"
                            @click="addKeyword(keyword.tag_name)"
                        >
                            {{ keyword.tag_name }}
                        </span>
                    </div>
                </div>

                <!-- Selected Tags -->
                <div class="form-group">
                    <div class="selected-tags-container">
                        <span
                            v-for="keyword in localPhoto.keywords"
                            :key="keyword"
                            class="selected-tag"
                        >
                            {{ keyword }}
                            <span class="remove-tag" @click="removeKeyword(keyword)">×</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button class="cancel-btn" @click="closeModal">Cancel</button>
                <button class="save-btn" @click="saveChanges">Save</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { notification } from 'ant-design-vue'; // Import notification
import { ref, watch, onMounted } from 'vue';
import getUrlList from '../../../../provider.js';

export default {
    props: {
        isVisible: {
            type: Boolean,
            required: true,
        },
        photoId: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            localPhoto: {
                title: "",
                description: "",
                location: "",
                category_id: "",
                privacy_status: "0",
                keywords: [],
            },
            categories: [],
            keywordInput: '',
            suggestedKeywords: [],
        };
    },
    watch: {
        photoId: {
            immediate: true,
            handler(newPhotoId) {
                if (newPhotoId) {
                    this.fetchPhotoDetails(newPhotoId);
                }
            },
        },
    },
    mounted() {
        this.fetchCategoriesAndTags();
    },
    methods: {
        async fetchPhotoDetails(photoId) {
            const urlList = getUrlList();
            try {
                const response = await axios.get(urlList.getPhoto(photoId), {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                this.localPhoto = response.data;
                // Chuyển đổi tags từ đối tượng sang mảng chuỗi
                this.localPhoto.keywords = response.data.tags.map(tag => tag.tag_name);
            } catch (error) {
                console.error('Error fetching photo details:', error);
            }
        },
        async fetchCategoriesAndTags() {
            const urlList = getUrlList();
            try {
                const [categoriesResponse, tagsResponse] = await Promise.all([
                    axios.get(urlList.getCategories),
                    axios.get(urlList.getTags)
                ]);
                this.categories = categoriesResponse.data;
                this.suggestedKeywords = tagsResponse.data.slice(0, 15); // Lấy 15 tag suggestions đầu tiên
            } catch (error) {
                console.error('Error fetching categories and tags:', error);
            }
        },
        addKeyword(keyword) {
            if (typeof keyword !== 'string') {
                keyword = this.keywordInput.trim();
            }
            if (keyword && !this.localPhoto.keywords.includes(keyword)) {
                this.localPhoto.keywords.push(keyword);
                this.keywordInput = '';
            }
        },
        removeKeyword(keyword) {
            this.localPhoto.keywords = this.localPhoto.keywords.filter(k => k !== keyword);
        },
        closeModal() {
            this.$emit("close");
        },
        async saveChanges() {
            const urlList = getUrlList();
            const payload = {
                ...this.localPhoto,
                privacy_status: String(this.localPhoto.privacy_status),
                tags: this.localPhoto.keywords.join(','),
            };
            try {
                await axios.put(urlList.editPhoto(this.photoId), payload, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                notification.success({
                    message: 'Success',
                    description: 'Photo updated successfully!',
                });
                this.$emit("save", this.localPhoto);
                this.closeModal();
            } catch (error) {
                console.error('Error saving photo changes:', error);
                notification.error({
                    message: 'Error',
                    description: 'There was an error saving the photo changes.',
                });
            }
        },
    },
};
</script>

<style scoped>
/* Overlay to dim the background */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Dark overlay */
    z-index: 1000;
    display: flex;
    justify-content: flex-end; /* Align modal to the right */
}

/* Modal content */
.modal-content {
    background-color: white;
    width: 500px;
    height: 100%; /* Full height */
    padding: 20px;
    box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    position: relative;
}

/* Photo preview */
.photo-preview {
    text-align: center;
    margin-bottom: 15px;
}

.photo-preview img {
    width: 100%;
    height: 500px;
    object-fit: contain;
    border-radius: 8px;
}

/* Header */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.close {
    cursor: pointer;
    font-size: 28px;
}

/* Content */
.content {
    flex: 1;
    overflow-y: auto;
    margin-bottom: 20px;
}

.form-group {
    margin: 15px 0;
}

.char-count {
    font-size: 12px;
    color: gray;
    margin-top: 5px;
}

/* Keywords Input */
.keywords-input {
    display: flex;
    gap: 8px; /* Tạo khoảng cách giữa các thẻ */
}

.keywords-input input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.keywords-input button {
    cursor: pointer;
    background-color: #0870D1;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 11px 10px;
    display: flex;
    align-items: center;
}

.keywords-input button i {
    margin-right: 5px;
}

/* Suggested Keywords */
.suggested-keywords {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.keyword-tag {
    background-color: #e1f5fe;
    border: 1px solid #81d4fa;
    border-radius: 4px;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    white-space: nowrap;
    cursor: pointer;
}

.keyword-tag:hover {
    background-color: #b3e5fc;
}

/* Selected Tags */
.selected-tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.selected-tag {
    background-color: #e1f5fe;
    border: 1px solid #81d4fa;
    border-radius: 4px;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    white-space: nowrap;
    font-size: 14px;
    font-weight: 500;
    cursor: default;
}

.remove-tag {
    cursor: pointer;
    margin-left: 8px;
    color: #ff5252;
    font-size: 16px;
    line-height: 1;
}

.remove-tag:hover {
    color: #ff1744; /* Hiệu ứng hover cho nút xóa */
}

/* Footer */
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.cancel-btn {
    padding: 12px 24px; /* Kích thước nút */
    border: none; /* Xóa viền */
    border-radius: 20px; /* Bo góc tròn */
    cursor: pointer;
    font-size: 16px; /* Kích thước font chữ */
    margin-left: 10px; /* Khoảng cách giữa các nút */
    background-color: transparent; /* Nền trong suốt cho nút Cancel */
    color: #007bff; /* Màu chữ xanh */
    transition: background-color 0.3s; /* Hiệu ứng chuyển màu */
}

.save-btn {
    padding: 12px 24px; /* Kích thước nút */
    border: none;
    border-radius: 20px; /* Bo góc tròn */
    cursor: pointer;
    font-size: 16px; /* Kích thước font chữ */
    font-weight: bold; /* Đậm chữ */
    background-color: #007bff; /* Nền xanh cho nút Create */
    color: white; /* Màu chữ trắng */
    box-shadow: 0 2px 5px rgba(0, 123, 255, 0.5); /* Hiệu ứng bóng */
    transition: background-color 0.3s, transform 0.2s; /* Hiệu ứng chuyển màu và phóng to */
}
.save-btn:hover {
    background-color: #0056b3; /* Màu xanh đậm khi hover */
}
.cancel-btn:hover {
    background-color: rgba(0, 123, 255, 0.1); /* Nền sáng lên khi hover */
}

/* Input styles */
input,
textarea,
select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

textarea {
    resize: vertical;
}
</style>
