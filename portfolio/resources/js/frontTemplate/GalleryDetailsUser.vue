<template>
    <Layout>
        <template v-slot:content="slotProps">
            <!-- Nếu chủ gallery bị chặn, chỉ hiển thị blocked-content -->
            <div v-if="isGalleryOwnerBlocked" class="blocked-content">
                <i class="fa-solid fa-circle-xmark blocked-icon"></i>
                <h2>Something went wrong</h2>
                <p>Please refresh the page to try again.</p>
            </div>
            <!-- Nếu không bị chặn, hiển thị nội dung gallery bình thường -->
            <div v-else class="gallery-container">
                <h1 class="gallery-title">{{ gallery.galleries_name || 'no title' }}</h1>
                <h1 class="gallery-title">{{ gallery.galleries_description || 'no description' }}</h1>
                <div class="icon-container">
                    <span class="icon"><i class="fas fa-heart"></i> 54 Likes</span>
                    <span class="icon" @click="copyUrl"><i class="fa-solid fa-share-nodes"></i></span>
                    <i class="fa-regular fa-flag"></i>
                </div>

                <!-- Thông tin chủ gallery -->
                <div class="user-info" v-if="gallery.user">
                    <router-link :to="{ name: 'MyProfile', params: { username: gallery.user.username } }">
                        <img
                            class="avatar"
                            :src="gallery.user.profile_picture || '/images/imageUserDefault.png'"
                            alt="User Avatar"
                        />
                    </router-link>
                    <span class="username">{{ gallery.user.name || gallery.user.username }}</span>
                </div>

                <!-- Danh sách ảnh trong gallery -->
                <div class="gallery-grid" v-if="gallery.photo && gallery.photo.length">
                    <div class="gallery-item" v-for="photo in gallery.photo" :key="photo.id">
                        <img :src="photo.image_url" :alt="photo.title" />
                        <div class="work-info">
                            <router-link :to="{ name: 'MyProfile', params: { username: photo.user.username } }">
                                <img
                                    class="user-image"
                                    :src="photo.user?.profile_picture || '/images/imageUserDefault.png'"
                                    alt="User Avatar"
                                />
                            </router-link>
                            <span class="user-name">{{ photo.user?.name || photo.user?.username || 'Unknown' }}</span>
                            <span class="icon-heart" @click.stop="toggleLike(photo)">
                                <i :class="['fas', 'fa-heart', { 'liked': photo.liked }]"></i>
                            </span>
                            <span
                                class="icon-dots"
                                @click.stop="toggleDropdown('dropdown-' + photo.id)"
                                :class="{ 'active': activeDropdown === 'dropdown-' + photo.id }"
                            >
                                <i class="fas fa-ellipsis-h"></i>
                            </span>
                        </div>
                        <div
                            v-if="activeDropdown === 'dropdown-' + photo.id"
                            class="dropdown-content show"
                            @click.stop
                        >
                            <ul>
                                <li @click="handleClick('addToGallery', photo.id)">
                                    <i class="fa-solid fa-plus"></i> Add to Gallery
                                </li>
                                <li
                                    v-if="photo.user && userStore.user && photo.user.id !== userStore.user.id"
                                    @click="toggleBlockUser(photo.user)"
                                >
                                    <i class="fas fa-user-slash"></i>
                                    {{ photo.blocked ? 'Unblock' : 'Block' }}
                                </li>
                                <li
                                    v-if="photo.user && userStore.user && photo.user.id !== userStore.user.id"
                                    @click="toggleFollow(photo)"
                                >
                                    <i :class="['fas', photo.following ? 'fa-user-minus' : 'fa-user-plus']"></i>
                                    {{ photo.following ? 'Unfollow' : 'Follow' }}
                                </li>
                                <li
                                    v-if="photo.user && userStore.user && photo.user.id !== userStore.user.id"
                                    @click="handleClick('reportPhoto', photo.id)"
                                >
                                    <i class="fa-solid fa-flag"></i> Report this photo
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Nếu không có ảnh -->
                <div v-else class="no-photos">
                    <p>No photos in this gallery.</p>
                </div>

                <!-- Modal Add to Gallery -->
                <AddToGalleryModal
                    :is-visible="showAddToGallery"
                    :photo-id="selectedPhotoId"
                    @close="closeAddToGalleryModal"
                />
            </div>
        </template>
    </Layout>
</template>

<script>
import Layout from './Layout.vue';
import axios from 'axios';
import getUrlList from '../provider.js';
import AddToGalleryModal from "./components/AddToGalleryModal.vue";
import { useLikeStore } from '@/stores/likeStore';
import { useAuthStore } from '@/stores/authStore';
import { useFollowStore } from '@/stores/followStore';
import { useUserStore } from '@/stores/userStore';
import { useBlockStore } from '@/stores/blockStore';
import { Modal, notification } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { h } from 'vue';

export default {
    name: "GalleryDetailsUser",
    components: { Layout, AddToGalleryModal },
    data() {
        return {
            gallery: {},
            activeDropdown: null,
            showAddToGallery: false,
            selectedPhotoId: null,
        };
    },
    async mounted() {
        const galleries_code = this.$route.params.galleries_code || 'abc123';
        this.fetchGalleryDetails(galleries_code);

        const userStore = useUserStore();
        await userStore.fetchUserData();

        const likeStore = useLikeStore();
        await likeStore.fetchLikedPhotos();
        this.updateLikedState();

        const followStore = useFollowStore();
        await followStore.fetchFollowingList();
        this.updateFollowingState();

        const blockStore = useBlockStore();
        await blockStore.fetchBlockedUsers();
        this.updateBlockedState();
    },
    computed: {
        userStore() {
            return useUserStore();
        },
        blockStore() {
            return useBlockStore();
        },
        // Kiểm tra xem chủ gallery có bị chặn hay không
        isGalleryOwnerBlocked() {
            if (this.gallery.user && this.blockStore.blockedUsers) {
                return this.blockStore.blockedUsers.includes(this.gallery.user.id);
            }
            return false;
        },
    },
    methods: {
        toggleDropdown(id) {
            this.activeDropdown = this.activeDropdown === id ? null : id;
        },
        async fetchGalleryDetails(code) {
            try {
                const token = localStorage.getItem("token");
                let headers = {};
                if (token) {
                    headers = { Authorization: `Bearer ${token}` };
                }
                const response = await axios.get(getUrlList().getGalleryDetailUser(code), { headers });
                if (response.data.success) {
                    this.gallery = response.data.data;
                    this.updateLikedState();
                    this.updateFollowingState();
                    this.updateBlockedState();
                } else {
                    console.error(response.data.message);
                }
            } catch (error) {
                console.error("Error fetching gallery details:", error);
            }
        },
        async checkLogin() {
            const authStore = useAuthStore();
            await authStore.checkLoginStatus();
            if (!authStore.isLoggedIn) {
                this.$router.push({ name: 'Login' });
                return false;
            }
            return true;
        },
        updateLikedState() {
            const likeStore = useLikeStore();
            if (this.gallery.photo) {
                this.gallery.photo.forEach(photo => {
                    photo.liked = likeStore.likedPhotos.includes(photo.id);
                });
            }
        },
        updateFollowingState() {
            const followStore = useFollowStore();
            if (this.gallery.photo) {
                this.gallery.photo.forEach(photo => {
                    photo.following = followStore.followingList.includes(photo.user.id);
                });
            }
        },
        updateBlockedState() {
            const blockStore = useBlockStore();
            if (this.gallery.photo) {
                this.gallery.photo.forEach(photo => {
                    photo.blocked = blockStore.blockedUsers.includes(photo.user.id);
                });
            }
        },
        async toggleLike(photo) {
            if (!await this.checkLogin()) return;
            const photo_id = photo.id;
            const photo_user_id = photo.user.id;
            const likeStore = useLikeStore();
            try {
                if (photo.liked) {
                    await likeStore.unlikePhoto(photo_id);
                } else {
                    await likeStore.likePhoto(photo_id, photo_user_id);
                }
                photo.liked = !photo.liked;
            } catch (error) {
                console.error('Failed to toggle like:', error);
                notification.error({
                    message: 'Error',
                    description: 'Failed to toggle like.',
                    placement: 'topRight',
                    duration: 3,
                });
            }
        },
        async toggleFollow(photo) {
            if (!await this.checkLogin()) return;
            const followStore = useFollowStore();
            const userId = photo.user.id;
            const username = photo.user.username;
            if (photo.following) {
                Modal.confirm({
                    title: 'Are you sure you want to unfollow this user?',
                    icon: h(ExclamationCircleOutlined),
                    content: 'This will unfollow the photographer.',
                    onOk: async () => {
                        try {
                            await followStore.unfollowUser(userId);
                            photo.following = false;
                            notification.success({
                                message: 'Success',
                                description: `You have unfollowed ${username}.`,
                                placement: 'topRight',
                                duration: 3,
                            });
                        } catch (error) {
                            console.error('Error unfollowing user:', error);
                            notification.error({
                                message: 'Error',
                                description: 'Failed to unfollow the user.',
                                placement: 'topRight',
                                duration: 3,
                            });
                        }
                    },
                });
            } else {
                try {
                    await followStore.followUser(userId);
                    photo.following = true;
                    notification.success({
                        message: 'Success',
                        description: `You are now following ${username}.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                } catch (error) {
                    console.error('Error following user:', error);
                    notification.error({
                        message: 'Error',
                        description: 'Failed to follow the user.',
                        placement: 'topRight',
                        duration: 3,
                    });
                }
            }
        },
        async toggleBlockUser(user) {
            if (!await this.checkLogin()) return;
            const blockStore = useBlockStore();
            const userId = user.id;
            try {
                if (blockStore.blockedUsers.includes(userId)) {
                    await blockStore.unblockUser(userId);
                    localStorage.setItem('blockNotification', JSON.stringify({
                        message: 'Success',
                        description: `${user.username} is unblocked.`,
                        duration: 3,
                    }));
                } else {
                    await blockStore.blockUser(userId);
                    localStorage.setItem('blockNotification', JSON.stringify({
                        message: 'Success',
                        description: `${user.username} has been blocked.`,
                        duration: 3,
                    }));
                }
                this.updateBlockedState();
                window.location.reload();
            } catch (error) {
                console.error("Error toggling block:", error);
                notification.error({
                    message: 'Error',
                    description: 'Failed to toggle block.',
                    placement: 'topRight',
                    duration: 3,
                });
            }
        },
        copyUrl() {
            const url = window.location.href;
            navigator.clipboard.writeText(url)
                .then(() => {
                    notification.success({
                        message: 'Success',
                        description: 'Link copied successfully!',
                        placement: 'topRight',
                        duration: 3,
                    });
                })
                .catch(err => {
                    console.error("Failed to copy URL:", err);
                    notification.error({
                        message: 'Error',
                        description: 'Failed to copy the URL.',
                        placement: 'topRight',
                        duration: 3,
                    });
                });
        },
        openAddToGalleryModal(photoId) {
            this.selectedPhotoId = photoId;
            this.showAddToGallery = true;
        },
        closeAddToGalleryModal() {
            this.showAddToGallery = false;
        },
        handleClick(action, photoId) {
            if (!this.checkLogin()) return;
            switch (action) {
                case 'addToGallery':
                    this.openAddToGalleryModal(photoId);
                    break;
                case 'reportPhoto':
                    notification.success({
                        message: 'Success',
                        description: 'Photo reported successfully.',
                        placement: 'topRight',
                        duration: 3,
                    });
                    break;
                default:
                    console.error('Unknown action:', action);
            }
        },
    },
    created() {
        const notifData = localStorage.getItem('blockNotification');
        if (notifData) {
            const { message, description, duration } = JSON.parse(notifData);
            notification.success({
                message,
                description,
                placement: 'topRight',
                duration,
            });
            localStorage.removeItem('blockNotification');
        }
    },
    watch: {
        'gallery.photo': {
            handler() {
                this.updateLikedState();
                this.updateFollowingState();
                this.updateBlockedState();
            },
            deep: true,
            immediate: true,
        },
    },
};
</script>

<style scoped>
.blocked-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh; /* Chiếm toàn bộ chiều cao màn hình */
    text-align: center;
    background-color: #f5f5f5;
}
.blocked-icon {
    font-size: 50px;
    color: #ff4d4f; /* Màu đỏ để nổi bật */
    margin-bottom: 20px;
}
.blocked-content h2 {
    font-size: 28px;
    color: #333;
    margin-bottom: 10px;
}
.blocked-content p {
    font-size: 16px;
    color: #666;
}
.dropdown-content {
    position: absolute;
    z-index: 9999; /* Đảm bảo dropdown luôn ở trên cùng */
    margin-left: 60px;
    background-color: #fff; /* Có thể thêm nền nếu cần */
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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

.gallery-container {
    padding: 20px;
    background-color: #fff;
}
.gallery-title {
    text-align: center;
    color: #333;
    font-size: 24px;
    margin-top: 10px;
}
.icon-container {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}
.icon {
    font-size: 20px;
    cursor: pointer;
}
.icon:hover {
    color: #007bff;
}
.icon:first-child i {
    color: #ff0000;
}
.user-info {
    display: flex;
    align-items: center;
    margin-top: 10px;
}
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid #ccc;
}
.username {
    margin-left: 10px;
    color: #333;
    font-size: 16px;
    font-weight: bold;
}
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 10px;
    margin-top: 20px;
}
.gallery-item {
    position: relative;
    width: 100%;
    height: 200px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.gallery-item img {
    width: 100%;
    height: 100%;
    border-radius: 8px;
    object-fit: cover;
}
.work-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.6));
    color: #fff;
    padding: 10px;
    display: flex;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    height: 50px;
}
.gallery-item:hover .work-info {
    opacity: 1;
}
.work-info .user-image {
    width: 30px !important;
    height: 30px !important;
    border-radius: 50%;
    margin-right: 10px;
    object-fit: cover;
}
.user-name {
    margin-right: auto;
    font-size: 17px;
    margin-top: 10px;
    color: #fff;
    display: inline-block;
    max-width: 100px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.icon-heart,
.icon-dots {
    font-size: 20px;
    color: #fff;
    margin-left: 15px;
    cursor: pointer;
    flex-shrink: 0;
}
.icon-heart:hover,
.icon-dots:hover {
    color: #ff5a5f;
}

.icon-heart .fa-heart.liked {
    color: #ff5a5f; /* Màu khi đã like */
}

.no-photos {
    text-align: center;
    margin-top: 20px;
    color: #888;
}
</style>
