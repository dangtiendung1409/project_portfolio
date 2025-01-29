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
                                    {
                                        title: 'Images',

                                    },
                                    {
                                        title: 'Details',

                                    },
                                    {
                                        title: 'Finish',

                                    },
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
                                @input="updateCharCount('title')"
                                maxlength="255"
                            />
                            <span class="char-count">
        {{ selectedFile.details.title.length || 0 }}/255
    </span>
                        </div>
                        <div class="input-group">
                            <span class="input-label">Location</span>
                            <input
                                type="text"
                                placeholder="Enter location"
                                v-model="selectedFile.details.location"
                                @input="updateCharCount('location')"
                                maxlength="255"
                            />
                            <span class="char-count">
        {{ selectedFile.details.location.length || 0 }}/255
    </span>
                        </div>
                        <div class="input-group">
                            <span class="input-label">Description</span>
                            <textarea
                                placeholder="Enter description"
                                rows="4"
                                v-model="selectedFile.details.description"
                                @input="updateCharCount('description')"
                                maxlength="500"
                            ></textarea>
                            <span class="char-count">
        {{ selectedFile.details.description.length || 0 }}/500
    </span>
                        </div>

                        <div class="category">
                            <div class="input-group">
                                <span class="input-label">Category</span>
                                <select v-model="selectedFile.details.category">
                                    <option disabled value="">Choose Category</option>
                                    <option>Category 1</option>
                                    <option>Category 2</option>
                                    <option>Category 3</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <span class="input-label">Photo Privacy</span>
                                <select v-model="selectedFile.details.privacy">
                                    <option value="public">Public</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-label">Add Custom Tags</span>
                            <div class="keywords-input" style="margin-top: 2px; margin-left: -3px">
                                <input type="text" v-model="keywordInput" @keyup.enter="addKeyword(keywordInput)" placeholder="Add your keywords" style="width: 500px" />
                                <button @click="addKeyword(keywordInput)" style="cursor: pointer;  background-color: #0870D1; color: white; border: none; border-radius: 4px; padding: 11px 10px;">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-label">Tag Suggestions</span>
                            <div class="keywords-input">
                                <div class="suggested-keywords" style="margin-top: -20px; margin-left: 105px">
                                  <span v-for="keyword in suggestedKeywords" :key="keyword" class="keyword-tag" @click="addKeyword(keyword)">
                                     {{ keyword }}
                                 </span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="selected-tags-container">
        <span
            v-for="keyword in selectedFile.details.keywords"
            :key="keyword"
            class="selected-tag"
        >
            {{ keyword }}
            <span class="remove-tag" @click="removeKeyword(keyword)">×</span>
        </span>
                            </div>
                        </div>


                        <div class="upload-button-container" style="display: flex; justify-content: flex-end; margin-top: 20px;">
                            <button @click="resetDetails" class="reset-button">
                                Reset
                            </button>
                            <button class="upload-button">
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
import { ref } from 'vue';
import Layout from './Layout.vue';
import { PlusOutlined, FileOutlined } from '@ant-design/icons-vue';
import { Upload, Modal } from 'ant-design-vue';

function getBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
    });
}

export default {
    name: 'AddPhotos',
    components: {
        Layout,
        'a-upload': Upload,
        'a-modal': Modal,
        PlusOutlined,
        FileOutlined,
    },
    setup() {
        const previewVisible = ref(false);
        const previewImage = ref('');
        const previewTitle = ref('');
        const fileList = ref([]);
        const selectedFile = ref(null);
        const title = ref('');
        const location = ref('');
        const description = ref('');
        const keywordInput = ref('');
        const keywords = ref([]);
        const suggestedKeywords = ref([
            'Square - Composition',
            'No People',
            'Sofa',
            'Indoors',
            'Living Room',
            'Photography',
            'Modern',
            'Furniture',
            'Home Decor',
            'Home Interior',
            'Table',
            'Design',
            'Domestic Room',
            'Apartment',
            'Cozy',
        ]);

        const current = ref(1);
        const exceededLimit = ref(false); // Cờ để theo dõi trạng thái hiển thị thông báo

        const handleCancel = () => {
            previewVisible.value = false;
        };

        const resetDetails = () => {
            if (selectedFile.value) {
                selectedFile.value.details = {
                    title: '',
                    location: '',
                    description: '',
                    category: '',
                    privacy: 'public',
                    keywords: [],
                };
                keywordInput.value = '';
            }
        };

        const handlePreview = async (file) => {
            if (!file.url && !file.preview) {
                file.preview = await getBase64(file.originFileObj);
            }
            previewImage.value = file.url || file.preview;
            previewVisible.value = true;
            previewTitle.value = file.name || file.url.substring(file.url.lastIndexOf('/') + 1);
        };

        const handleChange = (info) => {
            const { fileList: updatedList } = info;

            // Lọc các file để tránh trùng lặp và giới hạn số lượng file
            const uniqueFiles = updatedList.filter(
                (newFile, index, self) =>
                    index === self.findIndex((f) => f.name === newFile.name && f.size === newFile.size)
            );

            if (uniqueFiles.length > 10) {
                if (!exceededLimit.value) {
                    // Hiển thị thông báo lỗi
                    Modal.error({
                        title: 'Upload Limit Exceeded',
                        content: 'You can only upload up to 10 images!',
                    });
                    exceededLimit.value = true; // Đặt cờ để thông báo chỉ hiển thị một lần
                }
                fileList.value = uniqueFiles.slice(0, 10); // Giới hạn tối đa 10 file
                return; // Ngăn không cho phép thêm ảnh mới
            } else {
                exceededLimit.value = false; // Đặt lại cờ khi số lượng file hợp lệ
                fileList.value = uniqueFiles.map((file) => ({
                    ...file,
                    details: file.details || {
                        title: '',
                        location: '',
                        description: '',
                        category: '',
                        privacy: 'public',
                        keywords: [],
                    },
                }));
            }

            // Chọn file đầu tiên nếu chưa chọn file nào
            if (!selectedFile.value && fileList.value.length > 0) {
                selectedFile.value = fileList.value[0];
            }
        };

        const beforeUpload = (file) => {
            if (fileList.value.length >= 10) {
                if (!exceededLimit.value) {
                    Modal.error({
                        title: 'Upload Limit Exceeded',
                        content: 'You can only upload up to 10 images!',
                    });
                    exceededLimit.value = true;
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

            const isDuplicate = fileList.value.some(
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
        };

        const addKeyword = (keyword) => {
            if (typeof keyword !== 'string') {
                keyword = keywordInput.value.trim();
            }
            if (keyword && !selectedFile.value.details.keywords.includes(keyword)) {
                selectedFile.value.details.keywords.push(keyword);
                keywordInput.value = '';
            }
        };


        const removeKeyword = (keyword) => {
            selectedFile.value.details.keywords = selectedFile.value.details.keywords.filter((k) => k !== keyword);
        };

        const selectFile = (file) => {
            selectedFile.value = file;
        };

        const updateDetails = (field, value) => {
            if (selectedFile.value) {
                selectedFile.value.details[field] = value;
            }
        };

        const updateCharCount = (field) => {
            if (selectedFile.value) {
                selectedFile.value.details[field] = selectedFile.value.details[field].substring(0, 255); // Đảm bảo không vượt quá giới hạn
            }
        };

        return {
            current,
            previewVisible,
            previewImage,
            previewTitle,
            fileList,
            selectedFile,
            resetDetails,
            handleCancel,
            handlePreview,
            handleChange,
            beforeUpload,
            title,
            location,
            description,
            keywordInput,
            keywords,
            suggestedKeywords,
            selectFile,
            updateDetails,
            addKeyword,
            removeKeyword,
            updateCharCount,
        };
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
