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
                                <p class="header-subtitle">
                                    <span>{{ headerSubtitle }}</span>
                                </p>
                            </div>
                            <div class="tabs">
                                <!-- Khi click, thay đổi activeTab -->
                                <span class="tab-item"
                                      :class="{ active: activeTab === 'photo' }"
                                      @click="activeTab = 'photo'">
                                      Photo
                                 </span>
                                <span class="tab-item"
                                      :class="{ active: activeTab === 'gallery' }"
                                      @click="activeTab = 'gallery'">
                                     Gallery
                                 </span>
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
                        <PhotoLikeGrid
                            v-if="activeTab === 'photo'"
                            :likedPhotos="likedPhotos"
                            @delete-like="deleteLikePhoto"
                        />

                        <!-- Nếu tab Gallery đang active -->
                        <GalleryLikeGrid
                            v-else-if="activeTab === 'gallery'"
                            :likedGalleries="likedGalleries"
                            @delete-like="deleteLikeGallery"
                        />
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
import PhotoLikeGrid from './components/like/PhotoLikeGrid.vue';
import GalleryLikeGrid from './components/like/GalleryLikeGrid.vue';
import '@assets/css/account.css';
import { Modal, notification } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { h } from 'vue';

export default {
    name: 'Like',
    components: {
        Layout,
        Sidebar,
        PhotoLikeGrid,
        GalleryLikeGrid
    },
    data() {
        return {
            likedPhotos: [],
            likedGalleries: [],
            activeDropdown: null,
            activeTab: 'photo',

        };
    },
    mounted() {
        this.fetchLikedData();
    },
    computed: {
        headerSubtitle() {
            if (this.activeTab === 'gallery') {
                return `${this.likedGalleries.length} galleries`;
            } else if (this.activeTab === 'photo') {
                return `${this.likedPhotos.length} photos`;
            } else {
                if (this.likedGalleries.length > 0) {
                    return `${this.likedGalleries.length} galleries`;
                } else if (this.likedPhotos.length > 0) {
                    return `${this.likedPhotos.length} photos`;
                } else {
                    return '';
                }
            }
        }
    },
    methods: {
        async fetchLikedData() {
            const token = localStorage.getItem('token');
            if (!token) {
                console.error('Không tìm thấy token');
                return;
            }
            try {
                const response = await axios.get(getUrlList().getLikedPhotos, {
                    headers: { Authorization: `Bearer ${token}` },
                });

                this.likedPhotos = [];
                this.likedGalleries = [];

                response.data.data.forEach((like) => {
                    if (like.photo_id) {
                        this.likedPhotos.push({
                            id: like.id,
                            photoId: like.photo?.id || null,
                            photoToken: like.photo?.photo_token || null,
                            imageUrl: like.photo?.image_url || '/images/default-photo.png',
                            userName: like.photo?.user?.username || 'abc',
                            name: like.photo?.user?.name || 'Người dùng không xác định',
                            userAvatar: like.photo?.user?.profile_picture ? `http://127.0.0.1:8000${like.photo?.user?.profile_picture}` : '/images/imageUserDefault.png',
                        });
                    } else if (like.gallery_id) {
                        this.likedGalleries.push({
                            id: like.id,
                            galleryId: like.gallery?.id || null,
                            galleriesName: like.gallery?.galleries_name || 'Không có tên',
                            galleriesCode: like.gallery?.galleries_code || null,
                            galleriesPhoto: like.gallery?.photo || [],
                            username: like.gallery?.user?.username || 'Unknown',
                            name: like.gallery?.user?.name || 'Unknown',
                            userAvatar: like.gallery?.user?.profile_picture ? `http://127.0.0.1:8000${like.gallery?.user?.profile_picture}` : '/images/imageUserDefault.png',
                        });
                    }
                });

                console.log('Dữ liệu Liked Galleries:', this.likedGalleries);
            } catch (error) {
                console.error('Lỗi khi lấy dữ liệu liked:', error);
            }
        },
        async deleteLikePhoto(like) {
            Modal.confirm({
                title: 'Are you sure you want to delete this like?',
                icon: h(ExclamationCircleOutlined),
                content: `Likes from photos of ${like.name} will be deleted.`,
                okText: 'Yes',
                cancelText: 'No',
                onOk: async () => {
                    const token = localStorage.getItem('token');
                    if (!token) {
                        notification.error({
                            message: 'Lỗi',
                            description: 'Token not found, please log in.',
                        });
                        return;
                    }
                    try {
                        const response = await axios.delete(getUrlList().deleteLike(like.id), {
                            headers: { Authorization: `Bearer ${token}` },
                        });
                        notification.success({
                            message: 'Success',
                            description: response.data.message,
                        });
                        this.likedPhotos = this.likedPhotos.filter(item => item.id !== like.id);
                    } catch (error) {
                        console.error('Error when deleting likes:', error);
                        notification.error({
                            message: 'Error',
                            description: error.response?.data?.message || 'Likes cannot be deleted.',
                        });
                    }
                },
            });
        },
        async deleteLikeGallery(like) {
            // Sử dụng Modal.confirm để hỏi lại người dùng trước khi xoá
            Modal.confirm({
                title: 'Are you sure you want to delete this like?',
                icon: h(ExclamationCircleOutlined),
                content: `Likes from gallery "${like.galleriesName}" will be deleted.`,
                okText: 'Yes',
                cancelText: 'No',
                onOk: async () => {
                    const token = localStorage.getItem('token');
                    if (!token) {
                        notification.error({
                            message: 'Error',
                            description: 'Token not found, please log in.',
                        });
                        return;
                    }
                    try {
                        const response = await axios.delete(getUrlList().deleteLike(like.id), {
                            headers: { Authorization: `Bearer ${token}` },
                        });
                        notification.success({
                            message: 'Success',
                            description: response.data.message,
                        });
                        this.likedGalleries = this.likedGalleries.filter((item) => item.id !== like.id);
                    } catch (error) {
                        console.error('Error deleting likes:', error);
                        notification.error({
                            message: 'Lỗi',
                            description: error.response?.data?.message || 'Likes cannot be deleted.',
                        });
                    }
                },
            });
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
.tabs {
    display: flex;
    gap: 5px;
    margin-top: 10px;
}
.tab-item {
    cursor: pointer;
    color: gray;
    font-size: 16px;
    position: relative;
    padding-bottom: 5px;
}
.tab-item.active {
    color: #0870D1;
    font-weight: bold;
}
.tab-item.active::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #0870D1;
}
.btn-options.active i {
    color: whitesmoke;
    background-color: #1890ff;
    border-radius: 50%;
    padding: 5px;
}


</style>
