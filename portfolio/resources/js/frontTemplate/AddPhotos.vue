<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="container">
                <div class="header">
                    <div class="header-title">
                        <h1>Upload Photo</h1>
                    </div>
                </div>
                <div class="content">
                    <div class="upload-section">
                        <div class="clearfix">
                            <a-upload
                                v-model:file-list="fileList"
                                action="https://httpbin.org/post"
                                list-type="picture-card"
                                multiple
                                @preview="handlePreview"
                                @change="handleChange"
                                :before-upload="beforeUpload"
                            >
                                <div v-if="fileList.length < 8">
                                    <plus-outlined />
                                    <div style="margin-top: 8px">Upload</div>
                                </div>
                            </a-upload>
                            <ul style="display: flex; flex-wrap: wrap; gap: 12px; padding: 0; list-style: none; margin-top: 16px;">
                                <span style="width: 100%; font-size: 16px; font-weight: bold; color: #333; margin-bottom: 8px;">Select photo to edit</span>
                                <li
                                    v-for="file in fileList"
                                    :key="file.uid"
                                    @click="selectFile(file)"
                                    :class="{ selected: file === selectedFile }"
                                    style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 8px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; width: 150px; height: 150px;"
                                >
                                    <span style="margin-bottom: 8px;">
                                        <file-outlined style="font-size: 24px; color: #007bff;" />
                                    </span>
                                    <div style="font-size: 14px; font-weight: 500; text-align: center;">{{ file.name }}</div>
                                </li>
                            </ul>
                            <a-modal :open="previewVisible" :title="previewTitle" :footer="null" @cancel="handleCancel">
                                <img alt="example" style="width: 100%" :src="previewImage" />
                            </a-modal>
                        </div>
                    </div>
                    <div class="details-section" v-if="selectedFile">
                        <div style="margin-bottom: 30px">
                            <a-steps
                                v-model:current="current"
                                :items="[
                                    { title: 'Images' },
                                    { title: 'Details' },
                                    { title: 'Finish' }
                                ]"
                            >
                                <template #progressDot="{ index, status, prefixCls }">
                                    <a-popover>
                                        <template #content>
                                            <span>step {{ index }} status: {{ status }}</span>
                                        </template>
                                        <span :class="`${prefixCls}-icon-dot`" />
                                    </a-popover>
                                </template>
                            </a-steps>
                        </div>
                        <span>Add or confirm your photo(s) information. This helps with the discoverability and search of your photo(s).</span>
                        <span class="multiple-selection">Use the shift key or command/ctrl key to multiple select, up to 10 photos per upload</span>
                        <h3>Editing: {{ selectedFile.name }}</h3>
                        <div class="input-group">
                            <span class="input-label">Title</span>
                            <input
                                type="text"
                                placeholder="Enter title"
                                v-model="selectedFile.details.title"
                                maxlength="255"
                            />
                            <span class="char-count">{{ selectedFile.details.title.length || 0 }}/255</span>
                        </div>
                        <div class="input-group">
                            <span class="input-label">Location</span>
                            <input
                                type="text"
                                placeholder="Enter location"
                                v-model="selectedFile.details.location"
                                maxlength="255"
                            />
                            <span class="char-count">{{ selectedFile.details.location.length || 0 }}/255</span>
                        </div>
                        <div class="input-group">
                            <span class="input-label">Description</span>
                            <textarea
                                placeholder="Enter description"
                                rows="4"
                                v-model="selectedFile.details.description"
                                maxlength="500"
                            ></textarea>
                            <span class="char-count">{{ selectedFile.details.description.length || 0 }}/500</span>
                        </div>

                        <div class="category">
                            <div class="input-group">
                                <span class="input-label">Category</span>
                                <select v-model="selectedFile.details.category">
                                    <option disabled value="">Choose Category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.category_name }}</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <span class="input-label">Photo Privacy</span>
                                <select v-model="selectedFile.details.privacy">
                                    <option value="0">Public</option>
                                    <option value="1">Private</option>
                                </select>
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-label">Add Custom Tags</span>
                            <div class="keywords-input" style="margin-top: 2px; margin-left: -3px">
                                <input type="text" v-model="keywordInput" @keyup.enter="addKeyword(keywordInput)" placeholder="Add your keywords" style="width: 500px" />
                                <button @click="addKeyword(keywordInput)" style="cursor: pointer; background-color: #0870D1; color: white; border: none; border-radius: 4px; padding: 11px 10px;">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-label">Tag Suggestions</span>
                            <div class="keywords-input">
                                <div class="suggested-keywords" style="margin-top: -20px; margin-left: 105px">
                                    <span v-for="keyword in suggestedKeywords.slice(0, 15)" :key="keyword.id" class="keyword-tag" @click="addKeyword(keyword.tag_name)">
                                        {{ keyword.tag_name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="selected-tags-container">
                                <span v-for="keyword in selectedFile.details.keywords" :key="keyword" class="selected-tag">
                                    {{ keyword }}
                                    <span class="remove-tag" @click="removeKeyword(keyword)">×</span>
                                </span>
                            </div>
                        </div>

                        <div class="upload-button-container" style="display: flex; justify-content: flex-end; margin-top: 20px;">
                            <button @click="resetDetails" class="reset-button">
                                Reset
                            </button>
                            <button @click="uploadPhotos" class="upload-button">
                                Upload
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Layout>
</template>

<script>
import Layout from './Layout.vue';
import { PlusOutlined, FileOutlined } from '@ant-design/icons-vue';
import { Upload, Modal, notification } from 'ant-design-vue';
import axios from 'axios';
import getUrlList from '../provider.js';

export default {
    name: 'AddPhotos',
    components: {
        Layout,
        'a-upload': Upload,
        'a-modal': Modal,
        PlusOutlined,
        FileOutlined,
    },
    data() {
        return {
            previewVisible: false,
            previewImage: '',
            previewTitle: '',
            fileList: [],
            selectedFile: null,
            title: '',
            location: '',
            description: '',
            keywordInput: '',
            keywords: [],
            categories: [],
            tags: [],
            suggestedKeywords: [],
            current: 1,
            exceededLimit: false, // Cờ để theo dõi trạng thái hiển thị thông báo
        };
    },

    mounted() {
        this.fetchCategoriesAndTags();
    },
    methods: {
        async fetchCategoriesAndTags() {
            try {
                const urlList = getUrlList();
                const [categoriesResponse, tagsResponse] = await Promise.all([
                    axios.get(urlList.getCategories),
                    axios.get(urlList.getTags)
                ]);
                this.categories = categoriesResponse.data;
                this.tags = tagsResponse.data;
                this.suggestedKeywords = this.tags.slice(0, 15); // Lấy 15 tag suggestions đầu tiên
            } catch (error) {
                console.error('Error fetching categories or tags:', error);
            }
        },
        handleCancel() {
            this.previewVisible = false;
        },
        resetDetails() {
            if (this.selectedFile) {
                this.selectedFile.details = {
                    title: '',
                    location: '',
                    description: '',
                    category: '',
                    privacy: '0',
                    keywords: [],
                };
                this.keywordInput = '';
            }
        },
        async handlePreview(file) {
            if (!file.url && !file.preview) {
                file.preview = await this.getBase64(file.originFileObj);
            }
            this.previewImage = file.url || file.preview;
            this.previewVisible = true;
            this.previewTitle = file.name || file.url.substring(file.url.lastIndexOf('/') + 1);
        },
        handleChange(info) {
            const { fileList: updatedList } = info;

            // Lọc các file để tránh trùng lặp và giới hạn số lượng file
            const uniqueFiles = updatedList.filter(
                (newFile, index, self) =>
                    index === self.findIndex((f) => f.name === newFile.name && f.size === newFile.size)
            );

            if (uniqueFiles.length > 10) {
                if (!this.exceededLimit) {
                    // Hiển thị thông báo lỗi
                    Modal.error({
                        title: 'Upload Limit Exceeded',
                        content: 'You can only upload up to 10 images!',
                    });
                    this.exceededLimit = true; // Đặt cờ để thông báo chỉ hiển thị một lần
                }
                this.fileList = uniqueFiles.slice(0, 10); // Giới hạn tối đa 10 file
                return; // Ngăn không cho phép thêm ảnh mới
            } else {
                this.exceededLimit = false; // Đặt lại cờ khi số lượng file hợp lệ
                this.fileList = uniqueFiles.map((file) => ({
                    ...file,
                    details: file.details || {
                        title: '',
                        location: '',
                        description: '',
                        category: '',
                        privacy: '0', // Mặc định là Public
                        keywords: [],
                    },
                }));
            }

            // Chọn file đầu tiên nếu chưa chọn file nào
            if (!this.selectedFile && this.fileList.length > 0) {
                this.selectedFile = this.fileList[0];
            }
        },
        beforeUpload(file) {
            if (this.fileList.length >= 10) {
                if (!this.exceededLimit) {
                    Modal.error({
                        title: 'Upload Limit Exceeded',
                        content: 'You can only upload up to 10 images!',
                    });
                    this.exceededLimit = true;
                }
                return false; // Không cho phép upload nếu đã đủ 10 ảnh
            }

            const isImage = file.type.startsWith('image/');
            if (!isImage) {
                Modal.error({
                    title: 'Invalid File Type',
                    content: 'You can only upload image files!',
                });
                return false;
            }

            const isDuplicate = this.fileList.some(
                (existingFile) => existingFile.name === file.name && existingFile.size === file.size
            );

            if (isDuplicate) {
                Modal.error({
                    title: 'Duplicate File',
                    content: 'This file has already been uploaded!',
                });
                return false;
            }

            return true;
        },
        addKeyword(keyword) {
            if (typeof keyword !== 'string') {
                keyword = this.keywordInput.trim();
            }
            if (keyword && !this.selectedFile.details.keywords.includes(keyword)) {
                this.selectedFile.details.keywords.push(keyword);
                this.keywordInput = '';
            }
        },
        removeKeyword(keyword) {
            this.selectedFile.details.keywords = this.selectedFile.details.keywords.filter((k) => k !== keyword);
        },
        selectFile(file) {
            this.selectedFile = file;
        },
        async uploadPhotos() {
            if (!this.selectedFile.details.category) {
                Modal.error({
                    title: 'Upload Failed',
                    content: 'Please choose a category for the photo.',
                });
                return;
            }

            try {
                const urlList = getUrlList();
                const formData = new FormData();
                this.fileList.forEach((file, index) => {
                    formData.append(`photos[${index}][title]`, file.details.title || '');
                    formData.append(`photos[${index}][description]`, file.details.description || '');
                    formData.append(`photos[${index}][location]`, file.details.location || '');
                    formData.append(`photos[${index}][category_id]`, file.details.category);
                    formData.append(`photos[${index}][privacy_status]`, file.details.privacy);
                    formData.append(`photos[${index}][tags]`, file.details.keywords.join(','));
                    formData.append(`photos[${index}][image]`, file.originFileObj);
                });

                const response = await axios.post(urlList.addPhoto, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });

                if (response.status === 201) {
                    notification.success({
                        message: 'Success',
                        description: 'Photo added successfully, your photo will wait for processing',
                    });
                    this.$router.push({ name: 'MyPhoto' });
                }
            } catch (error) {
                Modal.error({
                    title: 'Upload Failed',
                    content: 'There was an error uploading the photos.',
                });
                console.error('Upload error:', error);
            }
        },
        getBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = (error) => reject(error);
            });
        },
    },
};
</script>


<style scoped>
.container {
    max-width: 1300px;
    margin: 0 auto;
    background: white;
    padding: 40px 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    height: 100vh; /* Đặt chiều cao tổng thể cho container */
    display: flex;
    flex-direction: column;
}
.selected {
    border-color: #007bff;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
    background-color: #e6f7ff;
}

.header {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
}

.header-title h1 {
    font-size: 30px;
    color: #333;
}

.content {
    display: flex;
    gap: 20px;
    flex: 1; /* Để phần nội dung chiếm không gian còn lại */
    overflow: hidden; /* Ẩn phần tràn ra ngoài */
}

.upload-section,
.details-section {
    flex: 1;
    background: #f7f7f7;
    padding: 20px;
    border-radius: 4px;
    overflow-y: auto; /* Thêm tính năng cuộn dọc */
    max-height: 100%; /* Đảm bảo chiều cao không vượt quá container */
}

.input-group {
    margin-bottom: 20px;
}

.input-group .input-label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.char-count {
    display: block;
    margin-top: 5px;
    font-size: 15px;
    color: #666;
    text-align: right;
}

.details-section input,
.details-section select,
.details-section textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.keywords-input {
    display: flex;
    flex-wrap: wrap; /* Cho phép các thẻ xuống dòng khi không đủ chỗ */
    gap: 8px; /* Tạo khoảng cách giữa các thẻ */
    margin-top: 27px;
    margin-left: -105px;
}

.keyword-tag {
    background-color: #007BFF;
    border: 1px solid #81d4fa;
    border-radius: 4px;
    padding: 5px 10px;
    display: inline-flex;
    align-items: center;
    white-space: nowrap; /* Giữ thẻ trên một dòng nội bộ */
}


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
}

.selected-tags-container {
    display: flex;
    flex-wrap: wrap; /* Cho phép các thẻ xuống dòng khi không đủ chỗ */
    gap: 8px; /* Tạo khoảng cách giữa các thẻ */
    margin-top: 10px;
}

.selected-tag {
    background-color: #e1f5fe;
    border: 1px solid #81d4fa;
    border-radius: 4px;
    padding: 5px 10px;
    display: inline-flex;
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


.category {
    display: flex;
    justify-content: space-between;
}

.category .input-group {
    width: 48%;
}

.multiple-selection {
    display: block; /* Để xuống dòng */
    color: #0870D1; /* Màu chữ yêu cầu */
    margin-top: 10px;
    margin-bottom: 10px;
}
.upload-button {
    padding: 12px 24px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    background-color: #007bff;
    color: white;
    box-shadow: 0 2px 5px rgba(0, 123, 255, 0.5);
    transition: background-color 0.3s, transform 0.2s;
}
.reset-button{
    padding: 12px 24px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
    margin-left: 10px;
    background-color: transparent;
    color: #007bff;
    transition: background-color 0.3s;
}
.upload-button:hover {
    background-color: #2563eb; /* Màu tối hơn cho hiệu ứng hover */
}
</style>
