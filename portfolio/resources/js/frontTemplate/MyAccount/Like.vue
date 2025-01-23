<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <header class="header">
                            <div class="header-content">
                                <span>Likes</span>
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
                        <div v-if="likedPhotos.length === 0" class="empty-likes">
                            <h2>Add photos to your likes</h2>
                            <p>Browse and like photos to curate your own collection.</p>
                            <button class="add-photo-button" @click="goToAddPhoto">
                                Add like photos
                            </button>
                        </div>
                        <div v-else class="photo-gallery">
                            <div v-for="like in likedPhotos" :key="like.id" class="photo-item">
                                <div class="photo-overlay">
                                    <router-link :to="{ name: 'PhotoDetail', params: { token: like.photoToken } }">
                                        <img :src="like.imageUrl" alt="photo" class="photo-image" />
                                    </router-link>
                                    <div class="photo-details">
                                        <img
                                            :src="like.userAvatar"
                                            alt="User Avatar"
                                            class="user-avatar"
                                        />
                                        <span class="user-name2">{{ like.username }}</span>
                                        <span class="icon-heart2" @click="showDeleteLikeConfirm(like)">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </span>
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
import axios from 'axios';
import getUrlList from '../../provider.js';
import Layout from '../Layout.vue';
import Sidebar from './components/Sidebar.vue';
import '@assets/css/account.css';
import { Modal, notification } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { h } from 'vue';

export default {
    name: 'Like',
    components: {
        Layout,
        Sidebar,
    },
    data() {
        return {
            likedPhotos: [],
            activeDropdown: null,
        };
    },
    mounted() {
        this.fetchLikedPhotos();
    },
    methods: {
        toggleDropdown(id) {
            this.activeDropdown = this.activeDropdown === id ? null : id;
        },
        async fetchLikedPhotos() {
            const token = localStorage.getItem('token');
            if (!token) {
                console.error('No token found');
                return;
            }

            try {
                const response = await axios.get(getUrlList().getLikedPhotos, {
                    headers: { Authorization: `Bearer ${token}` },
                });

                // Ánh xạ dữ liệu trả về từ API
                this.likedPhotos = response.data.data.map((like) => ({
                    id: like.id,
                    photoId: like.photo?.id || null,
                    photoToken: like.photo?.photo_token || null,
                    imageUrl: like.photo?.image_url || '/images/default-photo.png',
                    username: like.photo?.user?.username || 'Unknown User',
                    userAvatar: like.photo?.user?.profile_picture || '/images/imageUserDefault.png',
                }));
            } catch (error) {
                console.error('Error fetching liked photos:', error);
            }
        },
        showDeleteLikeConfirm(like) {
            Modal.confirm({
                title: 'Are you sure you want to delete this like?',
                icon: h(ExclamationCircleOutlined),
                content: `This action will remove the like from the photo by ${like.username}.`,
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => this.deleteLike(like.photoId),
            });
        },
        async deleteLike(photoId) {
            const token = localStorage.getItem('token');
            if (!token) {
                notification.error({
                    message: 'Error',
                    description: 'No token found, please login.',
                });
                return;
            }

            try {
                const response = await axios.delete(getUrlList().deleteLike(photoId), {
                    headers: { Authorization: `Bearer ${token}` },
                });

                notification.success({
                    message: 'Success',
                    description: response.data.message,
                });

                // Loại bỏ ảnh đã bị xoá khỏi danh sách likedPhotos
                this.likedPhotos = this.likedPhotos.filter((item) => item.photoId !== photoId);
            } catch (error) {
                console.error('Error deleting like:', error);
                notification.error({
                    message: 'Error',
                    description: error.response?.data?.message || 'Failed to delete like.',
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
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.empty-likes {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    height: 400px; /* Chiều cao cho vùng trống */
    color: #777;
}

.empty-likes h2 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #333;
}

.empty-likes p {
    font-size: 16px;
    color: #555;
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
.user-name2 {
    font-size: 18px;
    color: #fff;
    margin-right: auto;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
}

.icon-heart2, .icon-dots2 {
    flex-grow: 0;
    margin-left: 10px;
    font-size: 18px;
    margin-right: 10px;
    position: relative;
}

.icon-heart2:hover {
    color: #ff5a5f;
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
.photo-image {
     height: 200px;
     width: 250px;
     object-fit: cover;
 }

/* Thêm CSS cho trạng thái active */
.icon-dots2 .fa-ellipsis-h.active {
    color: whitesmoke;
    background-color: #1890ff;
    border-radius: 50%;
    padding: 5px;
}
</style>
