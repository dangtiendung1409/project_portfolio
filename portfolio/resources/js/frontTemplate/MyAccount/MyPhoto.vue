<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <header class="header">
                            <div class="header-content">
                                <span>My Photos</span>
                                <p class="header-subtitle">
                                    <span>{{ photoCount }} photos</span>
                                </p>
                            </div>
                            <div class="tabs">
                                <span class="tab-item" :class="{ active: activeTab === 'all' }" @click="setActiveTab('all')">All</span>
                                <span class="tab-item" :class="{ active: activeTab === 'public' }" @click="setActiveTab('public')">Public</span>
                                <span class="tab-item" :class="{ active: activeTab === 'private' }" @click="setActiveTab('private')">Private</span>
                            </div>
                        </header>
                        <div class="sort-select">
                            <div class="search-options">
                                <input type="text" v-model="searchQuery" placeholder="Search photos..." />
                                <button @click="searchPhotos"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                            <div class="sort-options">
                                <label for="sort-select">Sort by:</label>
                                <select id="sort-select">
                                    <option value="date">Date</option>
                                    <option value="name">Name</option>
                                    <option value="size">Size</option>
                                </select>
                            </div>
                        </div>
                        <div class="featured-galleries mb-4">
                            <div v-if="filteredPhotos.length === 0" class="trial-info">
                                <p>You have not posted any pictures yet?</p>
                                <p>Share your beautiful photos now!.</p>
                                <button class="trial-button" @click="goToAddPhoto">Upload photo</button>
                            </div>
                            <component
                                v-else
                                :is="activeComponent"
                                :photos="filteredPhotos"
                                @createPhoto="goToAddPhoto"
                                @goToPhotoDetails="goToPhotoDetails"
                                @editPhoto="openEditPhotoModal"
                                @addGallery="openAddToGalleryModal"
                                @deletePhoto="showDeletePhotoConfirm"
                                @toggleDropdown="toggleDropdown"
                                :activeDropdown="activeDropdown"
                                :downloadImage="downloadImage"
                            />
                        </div>
                    </main>
                </div>
            </div>
        </template>
    </Layout>
    <AddToGalleryModal
        :is-visible="showAddToGallery"
        :photo-id="selectedPhotoId"
        @close="closeAddToGalleryModal"
    />
    <EditPhotoModal
        :isVisible="showEditModal"
        :photo-id="selectedPhotoId"
        @close="closeEditModal"
        @save="fetchApprovedPhotos"
    />
</template>

<script>
import axios from 'axios';
import { Modal, notification } from 'ant-design-vue';
import { h } from 'vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import Layout from '../Layout.vue';
import AddToGalleryModal from '../components/AddToGalleryModal.vue';
import EditPhotoModal from './components/photos/EditPhotoModal.vue';
import AllPhotos from './components/photos/AllPhotos.vue';
import PublicPhotos from './components/photos/PublicPhotos.vue';
import PrivatePhotos from './components/photos/PrivatePhotos.vue';
import Sidebar from './components/Sidebar.vue';
import getUrlList from '../../provider.js';
import '@assets/css/account.css';

export default {
    name: 'MyPhoto',
    components: {
        Layout,
        Sidebar,
        AddToGalleryModal,
        EditPhotoModal,
        AllPhotos,
        PublicPhotos,
        PrivatePhotos,
    },
    data() {
        return {
            photos: [],
            activeDropdown: null,
            showAddToGallery: false,
            showEditModal: false,
            selectedPhotoId: null,
            activeTab: 'all',
            searchQuery: '',
        };
    },
    mounted() {
        this.fetchApprovedPhotos();
    },
    computed: {
        filteredPhotos() {
            let filtered = this.photos;
            if (this.activeTab === 'public') {
                filtered = filtered.filter(photo => photo.privacy_status === 0); // Public photos
            } else if (this.activeTab === 'private') {
                filtered = filtered.filter(photo => photo.privacy_status === 1); // Private photos
            }
            if (this.searchQuery) {
                const searchQueryLower = this.searchQuery.toLowerCase();
                filtered = filtered.filter(photo =>
                    photo.title.toLowerCase().includes(searchQueryLower) ||
                    photo.description.toLowerCase().includes(searchQueryLower)||
                    photo.location.toLowerCase().includes(searchQueryLower)||
                    photo.category.category_name.toLowerCase().includes(searchQueryLower) ||
                    photo.tags.some(tag => tag.tag_name.toLowerCase().includes(searchQueryLower))
                );
            }
            return filtered;
        },
        activeComponent() {
            if (this.activeTab === 'public') {
                return 'PublicPhotos';
            } else if (this.activeTab === 'private') {
                return 'PrivatePhotos';
            }
            return 'AllPhotos';
        },
        photoCount() {
            return this.filteredPhotos.length;
        }
    },
    methods: {
        setActiveTab(tab) {
            this.activeTab = tab;
        },
        goToPhotoDetails(photoToken) {
            this.$router.push({ name: 'PhotoDetail', params: { token: photoToken } });
        },
        toggleDropdown(id) {
            this.activeDropdown = this.activeDropdown === id ? null : id;
        },
        async fetchApprovedPhotos() {
            try {
                const urlList = getUrlList();
                const response = await axios.get(urlList.getApprovedPhotos, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                this.photos = response.data.data;
            } catch (error) {
                console.error('Error fetching approved photos:', error);
            }
        },
        openAddToGalleryModal(id) {
            this.selectedPhotoId = id;
            this.showAddToGallery = true; // Mở modal
        },
        closeAddToGalleryModal() {
            this.showAddToGallery = false; // Đóng modal
        },
        downloadImage(photoUrl, photoTitle) {
            const link = document.createElement('a');
            link.href = photoUrl;
            link.download = photoTitle || 'downloaded-photo.jpg'; // Tên mặc định nếu không có tiêu đề
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        showDeletePhotoConfirm(photo) {
            Modal.confirm({
                title: 'Are you sure you want to delete this photo?',
                icon: h(ExclamationCircleOutlined),
                content: `This action will permanently delete the photo titled "${photo.title}".`,
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => this.deletePhoto(photo.id),
            });
        },
        async deletePhoto(photoId) {
            try {
                const urlList = getUrlList();
                await axios.delete(urlList.deletePhoto(photoId), {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                notification.success({
                    message: 'Success',
                    description: 'Photo deleted successfully!',
                });
                this.fetchApprovedPhotos(); // Refresh the photo list
            } catch (error) {
                console.error('Error deleting photo:', error);
                notification.error({
                    message: 'Error',
                    description: 'There was an error deleting the photo.',
                });
            }
        },
        openEditPhotoModal(id) {
            this.selectedPhotoId = id;
            this.showEditModal = true; // Mở modal
        },
        closeEditModal() {
            this.showEditModal = false; // Close modal
        },
        goToAddPhoto() {
            this.$router.push({ name: 'AddPhotos' });
        },
        searchPhotos() {
        }
    }
}
</script>

<style scoped>
main {
    flex: 1;
    padding-left: 0;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.trial-info {
    margin-top: 30px;
    padding: 290px;
    text-align: center;
    background-color: #fff;
    flex-shrink: 0;
    border-radius: 8px;
}

.trial-button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.trial-button:hover {
    background-color: #0056b3;
}
.photo-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    background-color: #f5f5f5;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: visible;
    width: 100%;
    transition: transform 0.3s, box-shadow 0.3s;
}

.photo-card img {
    width: 100%;
    height: 300px; /* Đặt chiều cao cố định cho ảnh */
    object-fit: cover; /* Đảm bảo ảnh không bị méo */
    border-radius: 12px 12px 0 0; /* Bo góc cho ảnh */
}
.gallery-info {
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.gallery-info h4 {
    font-size: 1rem;
    margin: 0;
}
.gallery-info span {
    font-size: 0.9rem;
    color: #777; /* Màu sắc cho trạng thái công khai */
}
.create-gallery-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: #f5f5f5;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
}
.icon-container {
    font-size: 50px;
    color: #007bff;
    margin-bottom: 10px;
}
.create-gallery-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}
.create-gallery-button:hover {
    background-color: #0056b3;
}
.featured-galleries {
    overflow-y: auto;
    width: 100%;
}
.galleries-grid {
    margin-top: 30px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 1rem;
}
.ellipsis-icon {
    position: absolute;
    bottom: 60px;
    min-width: auto;
    min-height: auto;
    right: 10px;
    background-color: #ffffff;
    border-radius: 50%;
    padding: 7px 13px;
    border: 0;
    box-shadow: rgba(0, 0, 0, 0.08) 0px 2px 6px;
}
.ellipsis-icon.active {
    background-color: #2986f7; /* Màu nền xanh */
}
.ellipsis-icon.active i {
    color: #ffffff; /* Màu icon trắng */
}
.ellipsis-icon i {
    color: #007bff; /* Màu của biểu tượng */
    font-size: 16px; /* Kích thước biểu tượng */
}
.dropdown-content {
    margin-left: 60px;
    margin-top: 300px;
}
.dropdown-content ul {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    margin: 0;
}
.dropdown-content li {
    padding: 15px 15px 15px 25px;
    display: flex;
    align-items: center;
    color: #222222;
    white-space: nowrap;
    z-index: 1000;
}
.dropdown-content li:hover {
    color: whitesmoke; /* Màu chữ khi hover */
    background-color: #1890ff; /* Màu nền khi hover */
}
.dropdown-content li i {
    margin-right: 8px;
}
.dropdown-content li:hover i {
    color: whitesmoke;
    background-color: #1890ff;
}
.sort-select {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.search-options {
    display: flex;
    align-items: center;
}
.search-options input {
    padding: 10px;
    width: 350px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-right: 10px;
}
.search-options button {
    padding: 10px 10px;
    border: none;
    background-color: #007bff;
    color: white;
    border-radius: 4px;
    cursor: pointer;
}
.search-options button:hover {
    background-color: #0056b3;
}
</style>
