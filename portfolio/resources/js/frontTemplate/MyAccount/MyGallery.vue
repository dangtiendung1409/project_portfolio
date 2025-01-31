<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <header class="header">
                            <div class="header-content">
                                <span>Galleries</span>
                                <p class="header-subtitle">
                                    <span>{{ galleryCount }} galleries</span>
                                </p>
                            </div>
                            <div class="tabs">
                                <span class="tab-item" :class="{ active: activeTab === 'all' }" @click="setActiveTab('all')">All</span>
                                <span class="tab-item" :class="{ active: activeTab === 'public' }" @click="setActiveTab('public')">Public</span>
                                <span class="tab-item" :class="{ active: activeTab === 'private' }" @click="setActiveTab('private')">Private</span>
                            </div>
                        </header>
                        <div class="sort-select">
                            <div class="show-options">
                                <label for="show-select">Show:</label>
                                <select id="show-select">
                                    <option value="all">All</option>
                                    <option value="favorites">Favorites</option>
                                </select>
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
                            <component
                                :is="activeComponent"
                                :galleries="filteredGalleries"
                                @createGallery="goToAddGallery"
                                @goToGalleryDetails="goToGalleryDetails"
                                @editGallery="goToEditGallery"
                                @deleteGallery="showDeleteConfirm"
                                @toggleDropdown="toggleDropdown"
                                :activeDropdown="activeDropdown"
                            />
                        </div>
                    </main>
                </div>
            </div>
        </template>
    </Layout>
</template>

<script>
import { Modal, notification } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import Layout from '../Layout.vue';
import { useGalleryStore } from '../../stores/galleryStore.js';
import Sidebar from './components/Sidebar.vue';
import AllGalleries from './components/galleries/AllGalleries.vue';
import PublicGalleries from './components/galleries/PublicGalleries.vue';
import PrivateGalleries from './components/galleries/PrivateGalleries.vue';
import '@assets/css/account.css';
import { h } from 'vue';
import axios from 'axios';
import getUrlList from "../../provider.js";

export default {
    name: 'MyGallery',
    components: {
        Layout,
        Sidebar,
        Modal,
        AllGalleries,
        PublicGalleries,
        PrivateGalleries,
    },
    data() {
        return {
            activeDropdown: null,
            galleryToDelete: null, // Lưu trữ gallery cần xóa
            activeTab: 'all', // Quản lý tab đang hoạt động
        };
    },
    computed: {
        galleries() {
            const store = useGalleryStore();
            return store.galleries;
        },
        filteredGalleries() {
            if (this.activeTab === 'public') {
                return this.galleries.filter(gallery => gallery.visibility === 0);
            } else if (this.activeTab === 'private') {
                return this.galleries.filter(gallery => gallery.visibility === 1);
            }
            return this.galleries;
        },
        activeComponent() {
            if (this.activeTab === 'public') {
                return 'PublicGalleries';
            } else if (this.activeTab === 'private') {
                return 'PrivateGalleries';
            }
            return 'AllGalleries';
        },
        galleryCount() {
            return this.filteredGalleries.length;
        }
    },
    mounted() {
        const store = useGalleryStore();
        store.fetchGalleries();
    },
    methods: {
        setActiveTab(tab) {
            this.activeTab = tab;
        },
        goToGalleryDetails(galleries_code) {
            this.$router.push(`/galleryDetails/${galleries_code}`);
        },
        goToAddGallery() {
            this.$router.push('/addGallery');
        },
        goToEditGallery(galleries_code) {
            this.$router.push(`/editGallery/${galleries_code}`);
        },
        toggleDropdown(id) {
            this.activeDropdown = this.activeDropdown === id ? null : id;
        },
        showDeleteConfirm(gallery) {
            this.galleryToDelete = gallery;
            Modal.confirm({
                title: 'Are you sure delete this gallery?',
                icon: h(ExclamationCircleOutlined),
                content: 'Once deleted, the photos in the gallery will be deleted, this action cannot be undone',
                onOk: this.deleteGallery,
                onCancel() {},
            });
        },
        deleteGallery() {
            console.log('Gallery to delete:', this.galleryToDelete); // Xem đối tượng gallery

            if (this.galleryToDelete && this.galleryToDelete.galleries_code) {
                const url = getUrlList().deleteGallery; // Lấy URL từ getUrlList()
                const galleriesCode = this.galleryToDelete.galleries_code;

                console.log('Deleting gallery with code:', galleriesCode); // Kiểm tra giá trị galleries_code

                axios
                    .delete(`${url}/${galleriesCode}`, {
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`
                        }
                    })
                    .then(response => {
                        console.log('Gallery deleted successfully:', response);
                        this.galleryToDelete = null;

                        // Cập nhật lại danh sách galleries trong store bằng cách loại bỏ gallery đã xóa
                        const store = useGalleryStore();
                        store.galleries = store.galleries.filter(gallery => gallery.galleries_code !== galleriesCode);
                        // Hiển thị thông báo thành công
                        notification.success({
                            message: 'Success',
                            description: 'Gallery deleted successfully!',
                        });
                    })
                    .catch(error => {
                        console.log('Error deleting gallery:', error);
                        this.galleryToDelete = null;
                    });
            } else {
                console.log('Gallery code is missing or invalid.');
            }
        }
    }
};
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
    font-size: 50px; /* Đặt kích thước cho biểu tượng */
    color: #007bff; /* Màu sắc cho biểu tượng */
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
.gallery-images.empty {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: rgb(215, 216, 219);
    height: 220px; /* Đặt chiều cao để giữ không gian */
}

.gallery-images.empty i {
    font-size: 50px; /* Kích thước biểu tượng */
    color: #b0b0b0; /* Màu sắc cho biểu tượng */
}

.gallery-images.empty p {
    margin: 0;
    color: #b0b0b0; /* Màu sắc cho văn bản */
}
.featured-galleries {
    overflow-y: auto;
    width: 100%;
}
.galleries-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 1rem;
}
.gallery-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    box-sizing: border-box;
    width: 100%;
    padding: 15px;
    background-color: #f5f5f5;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-top: 20px;
}
.gallery-card .gallery-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.gallery-info h4 {
    font-size: 1rem;
    margin: 0;
}
.gallery-images {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px;
    flex-grow: 1;
}
.gallery-images img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
}
.gallery-images.single-image {
    display: flex;
    height: 200px;
    top: 50px;
    width: 200%;
    border-radius: 8px;
    overflow: hidden;
    background-color: #f5f5f5;
}

.gallery-images.single-image img {
    height: 100%;
    width: auto;
    object-fit: cover;
    border-radius: 8px;
}

.image-count {
    max-width: 100%;
    width: fit-content;
    height: fit-content;
    display: flex;
    color: white;
    background-color: rgb(69, 69, 124);
    border-radius: 4px;
    align-items: center;
    padding: 4px 8px;
    margin-left: 4px;
    word-break: break-word;
}
.image-count svg {
    font-size: 20px;
    margin-right: 5px;
}
.image-count span {
    color: whitesmoke;
}
.gallery-footer {
    padding: 10px;
    display: flex;
    align-items: center;
}
.gallery-create {
    padding: 10px;
    display: flex;
    align-items: center;
}
.gallery-footer h4{
    margin-top: 10px;
}
.user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
}
.footer-buttons {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 8px;
}
.btn-favorite,
.btn-options {
    background: none;
    border: none;
    color: gray;
    cursor: pointer;
    font-size: 20px;
    margin-left: 10px;
}
.btn-options.active i {
    color: whitesmoke;
    background-color: #1890ff;
    border-radius: 50%;
    padding: 5px;
}
.create-gallery-button:focus,
.btn-options:focus {
    outline: none;
}

.dropdown-content {
    margin-top: 350px;
    margin-left: 60px;
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

</style>
