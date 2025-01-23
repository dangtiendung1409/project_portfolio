<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <div class="gallery-details">
                            <button class="back-button" @click="goBack">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <div class="header" v-if="gallery">
                                <h1 class="gallery-title">{{ gallery.galleries_name }}</h1>
                                <div class="user-info">
                                    <span class="gallery-user">{{ gallery.galleries_description }}</span>
                                    <button class="edit-button" @click="goToEditGallery(gallery.galleries_code)">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                </div>
                            </div>
                            <div v-if="gallery && gallery.photo.length === 0" class="empty-gallery">
                                <h2>Add photos to this Gallery</h2>
                                <p>Curate inspirational photos, or tell a story with your own photos.</p>
                                <button class="add-photo-button" @click="goToAddPhoto">
                                    Add Photos
                                </button>
                            </div>
                            <div class="gallery-image-container" v-else-if="gallery">
                                <div class="gallery-images">
                                    <div v-for="photo in gallery.photo"
                                         :key="photo.id"
                                         class="photo-item">
                                        <div class="photo-overlay">
                                            <img :src="photo.image_url" :alt="photo.title" class="photo-image" />
                                            <div class="photo-details">
                                                <img :src="photo.user.profile_picture || '/images/imageUserDefault.png'" alt="User Avatar" class="user-avatar" />
                                                <span class="user-name2">{{ photo.user.username || 'Unknown User' }}</span>
                                                <span class="icon-heart2"><i class="fas fa-heart"></i></span>
                                                <span class="icon-heart2" @click="showDeletePhotoConfirm(photo)">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </span>

                                                <button
                                                    class="btn-options"
                                                    @click.stop="toggleDropdown('dropdown-' + photo.id, $event)"
                                                    :class="{'active': activeDropdown === 'dropdown-' + photo.id}"
                                                >
                                                    <i class="fa-solid fa-ellipsis"></i>
                                                </button>
                                            </div>
                                            <div v-if="activeDropdown === 'dropdown-' + photo.id" class="dropdown-content show"  @click.stop>
                                                <ul>
                                                    <li>
                                                        <i class="fa-solid fa-plus"></i> Add to Gallery
                                                    </li>
                                                    <li>
                                                        <i class="fa-solid fa-flag"></i> Report this photo
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </template>
    </Layout>
</template>


<script>
import Layout from '../Layout.vue';
import Sidebar from './components/Sidebar.vue';
import getUrlList from '../../provider.js';
import axios from 'axios';
import '@assets/css/account.css';
import { Modal, notification } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { h } from 'vue';
export default {
    name: 'GalleryDetails',
    components: {
        Layout,
        Sidebar,
    },
    data() {
        return {
            gallery: null,
            activeDropdown: null,
        };
    },
    mounted() {
        const galleries_code = this.$route.params.galleries_code;
        this.fetchGalleryDetails(galleries_code);
    },
    methods: {
        goBack() {
            this.$router.push('/myGallery');
        },
        goToEditGallery(galleries_code) {
            this.$router.push(`/editGallery/${galleries_code}`);
        },
        toggleDropdown(id) {
            this.activeDropdown = this.activeDropdown === id ? null : id;
        },
        async fetchGalleryDetails(galleries_code) {
            try {
                const response = await axios.get(`${getUrlList().getGalleryDetails}/${galleries_code}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}` // Nếu cần thiết
                    }
                });
                this.gallery = response.data.data;
            } catch (error) {
                console.error('Failed to fetch gallery details:', error);
            }
        },
        showDeletePhotoConfirm(photo) {
            Modal.confirm({
                title: 'Are you sure you want to delete this photo?',
                content: 'This action cannot be undone.',
                onOk: () => this.deletePhotoFromGallery(photo),
                onCancel() {
                    console.log('Delete canceled');
                },
            });
        },
        async deletePhotoFromGallery(photo) {
            try {
                const galleries_code = this.$route.params.galleries_code;
                const url = getUrlList().deletePhotoFromGallery(galleries_code, photo.id);

                await axios.delete(url, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    },
                });

                // Xóa ảnh khỏi danh sách gallery
                this.gallery.photo = this.gallery.photo.filter(p => p.id !== photo.id);

                notification.success({
                    message: 'Success',
                    description: 'Photo removed from gallery successfully!',
                });
            } catch (error) {
                console.error('Error deleting photo:', error);
                notification.error({
                    message: 'Error',
                    description: 'Failed to delete the photo. Please try again.',
                });
            }
        },
        goToAddPhoto() {
            this.$router.push('/');
        },
    },
};
</script>


<style scoped>
main {
    flex: 1;
    padding-left: 0;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.empty-gallery {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 300px;
    height: 100%;
    text-align: center;
    color: #555;
}

.empty-gallery h2 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #333;
}

.empty-gallery p {
    font-size: 16px;
    margin-bottom: 20px;
    color: #777;
    text-align: center;
    max-width: 500px;
}


.add-photo-button {
    padding: 10px 20px;
    background-color: #1890ff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.add-photo-button:hover {
    background-color: #1677cc;
}

.my-photo-page {
    display: flex;
    padding: 20px;
    background-color: #ffffff;
}
.back-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 20px;
    margin-right: 1000px;
}
.content-layout {
    flex: 1;
    display: flex;
}

.gallery-details {
    flex: 1;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.header {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.gallery-title {
    font-size: 24px;
    margin: 0;
}

.user-info {
    display: flex;
    align-items: center;
    border-bottom: none;
}

.gallery-user {
    margin-right: 10px;
    margin-bottom: 19px;
}

.edit-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;

}

.gallery-image-container {
    overflow-y: auto;
    max-height: calc(100vh - 100px); /* Điều chỉnh 150px theo thiết kế */
    width: 100%;
}


.gallery-images {
    display: flex; /* Sử dụng flex để xếp hình ảnh theo hàng */
    flex-wrap: wrap; /* Cho phép hình ảnh xuống dòng */
}

.gallery-images img {
    width: 250px; /* Kích thước hình ảnh */
    height: 180px;
    margin: 5px; /* Khoảng cách giữa các hình ảnh */
    border-radius: 8px;
}
.photo-item {
    position: relative;
    width: 250px;
    margin: 5px;
}

.photo-overlay {
    position: relative;
    cursor: pointer;
}

.photo-overlay:hover .photo-details {
    opacity: 1;
    visibility: visible;
}

.photo-details {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    color: white;
    display: flex;
    align-items: center;
    padding: 10px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, visibility 0.3s;
}

.photo-details .user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin-right: 10px;
}

.photo-details .user-name2 {
    font-size: 14px;
    color: white;
    margin-right: auto;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
}

.icon-heart2, .icon-dots2 {
    font-size: 18px;
    margin-left: 10px;
}
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
