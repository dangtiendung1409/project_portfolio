<template>
    <div v-if="recentPhotos.length > 0" class="photo-header">
        <h3>Photos of people you follow</h3>
        <p style="font-size: 16px">Recent photos of people you follow</p>
    </div>
    <div class="popular-photos-container row">
        <div v-for="photo in recentPhotos" :key="photo.id" class="item-explore col-3 mb-4">
            <div class="photo-wrapper">
                <router-link :to="{ name: 'PhotoDetail', params: { token: photo.photo_token } }">
                    <img class="img-thin" :src="photo.image_url" :alt="photo.title">
                </router-link>
                <div class="explore-info">
                    <div class="user-info2">
                        <router-link :to="{ name: 'MyProfile', params: { username: photo.user.username } }">
                            <img class="user-image2" :src="photo.user.profile_picture" :alt="photo.user.name" style="width: 30px; height: 30px;">
                        </router-link>
                        <span class="user-name2">{{ photo.user.name }}</span>
                        <span class="icon-heart2" @click="toggleLike(photo)">
                            <i :class="['fas', 'fa-heart', { 'liked': photo.liked }]"></i>
                        </span>
                        <span class="icon-dots2">
                            <i :class="['fas', 'fa-ellipsis-h', { 'active': activeDropdown === 'dotsDropdown-' + photo.id }]"
                               @click.stop="toggleDropdown('dotsDropdown-' + photo.id)"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div v-if="activeDropdown === 'dotsDropdown-' + photo.id" class="dropdown-content show" style="right: 30px">
                <ul>
                    <li @click="handleClick('addToGallery', photo.id)">
                        <i class="fa-regular fa-square-plus"></i> Add to Gallery
                    </li>
                    <li v-if="photo.user && userStore.user && photo.user.id !== userStore.user.id"
                        @click="toggleBlockUser(photo.user)">
                        <i class="fas fa-user-slash"></i> {{ photo.blocked ? 'Unblock' : 'Block' }}
                    </li>
                    <li v-if="photo.user && userStore.user && photo.user.id !== userStore.user.id"
                        @click="toggleFollowPhoto(photo)">
                        <i class="fas" :class="photo.following ? 'fa-user-minus' : 'fa-user-plus'"></i>
                        {{ photo.following ? 'Unfollow' : 'Follow' }}
                    </li>
                    <li v-if="photo.user && userStore.user && photo.user.id !== userStore.user.id"
                        @click="handleClick('reportPhoto', photo.id, photo.user.id)">
                        <i class="fas fa-flag"></i> Report This Photo
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div v-if="recentGalleries.length > 0" class="featured-galleries mb-4">
        <h3>Recent Followed Galleries</h3>
        <div class="top-gallery-header d-flex justify-content-between align-items-center">
            <p style="font-size: 16px">Galleries from people you follow</p>
        </div>
        <div class="galleries-grid">
            <div v-for="gallery in recentGalleries" :key="gallery.id" class="gallery-card" @click="goToGalleryDetails(gallery.galleries_code)">
                <div class="gallery-info">
                    <h4>{{ gallery.galleries_name }}</h4>
                    <div class="image-count">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z" fill="white"></path>
                            <path d="M12.4003 5.33337C13.3337 5.33337 14.1337 4.53337 14.1337 3.60003C14.1337 2.6667 13.3337 1.8667 12.4003 1.8667C11.467 1.8667 10.667 2.6667 10.667 3.60003C10.667 4.53337 11.467 5.33337 12.4003 5.33337ZM12.4003 2.80003C12.8003 2.80003 13.2003 3.13337 13.2003 3.60003C13.2003 4.0667 12.867 4.40003 12.4003 4.40003C12.0003 4.40003 11.6003 4.0667 11.6003 3.60003C11.6003 3.13337 11.9337 2.80003 12.4003 2.80003Z" fill="white"></path>
                        </svg>
                        <span>{{ gallery.photo.length }}</span>
                    </div>
                </div>
                <div class="gallery-images">
                    <img v-for="(photo, index) in gallery.photo.slice(0, 4)" :key="index" :src="photo.image_url" :alt="'Gallery Image ' + (index + 1)">
                </div>
                <div class="gallery-footer">
                    <img class="user-avatar" :src="gallery.user.profile_picture" :alt="gallery.user.name">
                    <h4>{{ gallery.user.name }}</h4>
                    <div class="footer-buttons">
                        <button class="btn-favorite" @click.stop="toggleLikeGallery(gallery)">
                            <i :class="[gallery.liked ? 'fas' : 'fa-regular', 'fa-heart', { liked: gallery.liked }]"></i>
                        </button>
                        <button class="btn-options" v-if="gallery.user && userStore.user && gallery.user.id !== userStore.user.id"
                                @click.stop="handleClick('reportGallery', gallery.id, gallery.user.id)">
                            <i class="fa-regular fa-flag"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="following-content">
        <h3>You’re not following anyone yet</h3>
        <p>Start following photographers and stay updated with their latest photos here.</p>
        <div class="following-users">
            <div v-for="user in users" :key="user.id" class="user-card">
                <div class="card">
                    <div :class="[
                        'background-images',
                        user.photos.length === 1 ? 'one-image' : '',
                        user.photos.length === 2 ? 'two-images' : '',
                        user.photos.length === 4 ? 'four-images' : '',
                        user.photos.length === 0 ? 'no-image' : ''
                    ]">
                        <template v-if="user.photos.length > 0">
                            <img v-for="bg in user.photos.slice(0, 4)" :key="bg.id" :src="bg.image_url" class="bg-img" />
                        </template>
                        <div v-else class="content-unavailable">
                            <i class="fa-regular fa-images"></i>
                            <p>Content Unavailable</p>
                        </div>
                    </div>
                    <router-link :to="{ name: 'MyProfile', params: { username: user.username } }">
                        <img :src="user.profile_picture" alt="User Image" class="profile-img" />
                    </router-link>
                    <h4>{{ user.name }}</h4>
                    <button class="btn-follow" @click="toggleFollowUser(user)">
                        {{ user.following ? 'Unfollow' : 'Follow' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <AddToGalleryModal
        :is-visible="showAddToGallery"
        :photo-id="selectedPhotoId"
        @close="closeAddToGalleryModal"
    />
    <ReportPhotoModal
        :is-visible="showReportModal"
        :photo-id="selectedPhotoId"
        :violator-id="selectedViolatorId"
        @close="closeReportModal"
    />
    <ReportGalleryModal
        :is-visible="showReportGalleryModal"
        :gallery-id="selectedGalleryId"
        :violator-id="selectedViolatorId"
        @close="closeReportGalleryModal"
    />
</template>

<script>
import axios from 'axios';
import getUrlList from '../../provider.js';
import { useLikeStore } from '@/stores/likeStore';
import { useAuthStore } from '@/stores/authStore';
import { useFollowStore } from '@/stores/followStore';
import { useUserStore } from '@/stores/userStore';
import { useBlockStore } from '@/stores/blockStore';
import AddToGalleryModal from "../components/AddToGalleryModal.vue";
import ReportPhotoModal from "../components/ReportPhotoModal.vue";
import ReportGalleryModal from "../components/ReportGalleryModal.vue";
import { Modal, notification } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { h } from 'vue';

export default {
    props: {
        users: Array,
    },
    components: {
        AddToGalleryModal,
        ReportPhotoModal,
        ReportGalleryModal,
    },
    data() {
        return {
            recentPhotos: [],
            recentGalleries: [],
            activeDropdown: null,
            showAddToGallery: false,
            showReportModal: false,
            showReportGalleryModal: false,
            selectedPhotoId: null,
            selectedViolatorId: null,
            selectedGalleryId: null,
        };
    },
    async mounted() {
        await this.fetchRecentFollowedPhotos();
        await this.fetchRecentFollowedGalleries();
        const userStore = useUserStore();
        await userStore.fetchUserData();

        const likeStore = useLikeStore();
        await likeStore.fetchLikedPhotos();
        await likeStore.fetchLikedGalleries();
        this.updateLikedState();
        this.updateLikedGalleriesState();

        const followStore = useFollowStore();
        await followStore.fetchFollowingList();
        this.updateFollowingStateForPhotos();
        this.updateFollowingStateForUsers();

        const blockStore = useBlockStore();
        await blockStore.fetchBlockedUsers();
        this.updateBlockedState();
    },
    computed: {
        likeStore() {
            return useLikeStore();
        },
        userStore() {
            return useUserStore();
        },
    },
    methods: {
        async fetchRecentFollowedPhotos() {
            try {
                const token = localStorage.getItem("token");
                if (!token) throw new Error("Không tìm thấy token, không thể lấy dữ liệu.");

                const response = await axios.get(getUrlList().getRecentFollowedPhotos, {
                    headers: { Authorization: `Bearer ${token}` },
                });

                this.recentPhotos = Array.isArray(response.data)
                    ? response.data.map(photo => ({
                        ...photo,
                        liked: false,
                        following: false,
                        blocked: false,
                    }))
                    : (console.warn("Dữ liệu từ API không phải là mảng:", response.data), []);
            } catch (error) {
                console.error("Lỗi khi lấy danh sách ảnh gần đây từ người theo dõi:", error);
                this.recentPhotos = [];
            }
        },

        async fetchRecentFollowedGalleries() {
            try {
                const token = localStorage.getItem("token");
                if (!token) throw new Error("Không tìm thấy token, không thể lấy dữ liệu.");

                const response = await axios.get(getUrlList().getRecentFollowedGalleries, {
                    headers: { Authorization: `Bearer ${token}` },
                });

                this.recentGalleries = Array.isArray(response.data)
                    ? response.data.map(gallery => ({
                        ...gallery,
                        liked: false,
                    }))
                    : (console.warn("Dữ liệu galleries từ API không phải là mảng:", response.data), []);
            } catch (error) {
                console.error("Lỗi khi lấy danh sách gallery gần đây từ người theo dõi:", error);
                this.recentGalleries = [];
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
            this.recentPhotos.forEach(photo => {
                photo.liked = likeStore.likedPhotos.includes(photo.id);
            });
        },
        updateLikedGalleriesState() {
            const likeStore = useLikeStore();
            this.recentGalleries.forEach(gallery => {
                gallery.liked = likeStore.likedGalleries.includes(gallery.id);
            });
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
            }
        },
        async toggleLikeGallery(gallery) {
            if (!await this.checkLogin()) return;

            const likeStore = useLikeStore();
            try {
                if (gallery.liked) {
                    await likeStore.unlikeGallery(gallery.id);
                    gallery.liked = false;
                } else {
                    await likeStore.likeGallery(gallery.id, gallery.user ? gallery.user.id : null);
                    gallery.liked = true;
                }
            } catch (error) {
                console.error('Failed to toggle like on gallery:', error);
            }
        },
        updateBlockedState() {
            const blockStore = useBlockStore();
            this.recentPhotos.forEach(photo => {
                photo.blocked = blockStore.blockedUsers.includes(photo.user.id);
            });
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
                        description: `${user.username} has been blocked. All their related content will not be visible going forward.`,
                        duration: 3,
                    }));
                }

                this.updateBlockedState();

                // Reload trang sau khi lưu thông báo
                window.location.reload();
            } catch (error) {
                console.error("Error toggling block:", error);
            }
        },
        toggleDropdown(id) {
            this.activeDropdown = this.activeDropdown === id ? null : id;
        },
        updateFollowingStateForPhotos() {
            const followStore = useFollowStore();
            this.recentPhotos.forEach(photo => {
                photo.following = followStore.followingList.includes(photo.user.id);
            });
        },
        updateFollowingStateForUsers() {
            const followStore = useFollowStore();
            this.users.forEach(user => {
                user.following = followStore.followingList.includes(user.id);
            });
        },
        async toggleFollowPhoto(photo) {
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
                            notification.success({
                                message: 'Success',
                                description: `You have unfollowed ${username}.`,
                                placement: 'topRight',
                                duration: 3,
                            });
                            window.location.reload(); // Reload lại trang
                        } catch (error) {
                            console.error('Error unfollowing user:', error);
                            notification.error({
                                message: 'Error',
                                description: 'Failed to unfollow the user.',
                                placement: 'topRight',
                                duration: 3,
                            });
                        }
                    }
                });
            } else {
                try {
                    await followStore.followUser(userId);
                    notification.success({
                        message: 'Success',
                        description: `You are now following ${username}.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                    window.location.reload(); // Reload lại trang
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
        async toggleFollowUser(user) {
            if (!await this.checkLogin()) return;

            const followStore = useFollowStore();
            const username = user.username;

            if (user.following) {
                Modal.confirm({
                    title: 'Are you sure you want to unfollow this user?',
                    icon: h(ExclamationCircleOutlined),
                    content: 'This will unfollow the photographer. You will no longer see their content in your For You feed.',
                    onOk: async () => {
                        try {
                            await followStore.unfollowUser(user.id);
                            user.following = false;
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
                                description: `Failed to unfollow ${username}.`,
                                placement: 'topRight',
                                duration: 3,
                            });
                        }
                    }
                });
            } else {
                try {
                    await followStore.followUser(user.id);
                    user.following = true;
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
                        description: `Failed to follow ${username}.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                }
            }
        },
        async handleClick(action, itemId, violatorId) {
            if (!await this.checkLogin()) return;

            switch (action) {
                case 'addToGallery':
                    this.openAddToGalleryModal(itemId);
                    break;
                case 'reportPhoto':
                    this.openReportModal(itemId, violatorId);
                    break;
                case 'reportGallery':
                    this.openReportGalleryModal(itemId, violatorId);
                    break;
            }
        },
        openAddToGalleryModal(photoId) {
            this.selectedPhotoId = photoId;
            this.showAddToGallery = true;
        },
        closeAddToGalleryModal() {
            this.showAddToGallery = false;
        },
        openReportModal(photoId, violatorId) {
            this.selectedPhotoId = photoId;
            this.selectedViolatorId = violatorId;
            this.showReportModal = true;
        },
        closeReportModal() {
            this.showReportModal = false;
            this.selectedPhotoId = null;
            this.selectedViolatorId = null;
        },
        openReportGalleryModal(galleryId, violatorId) {
            this.selectedGalleryId = galleryId;
            this.selectedViolatorId = violatorId;
            this.showReportGalleryModal = true;
        },
        closeReportGalleryModal() {
            this.showReportGalleryModal = false;
            this.selectedGalleryId = null;
            this.selectedViolatorId = null;
        },
        goToGalleryDetails(galleries_code) {
            this.$router.push({ name: 'GalleryDetailsUser', params: { galleries_code } });
        }
    },
    watch: {
        recentPhotos: {
            handler() {
                this.updateLikedState();
                this.updateFollowingStateForPhotos();
                this.updateBlockedState();
            },
            deep: true,
            immediate: true
        },
        recentGalleries: {
            handler() {
                this.updateLikedGalleriesState();
            },
            deep: true,
            immediate: true
        },
        users: {
            handler() {
                this.updateFollowingStateForUsers();
            },
            deep: true,
            immediate: true
        }
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
    }
};
</script>

<style scoped>
.photo-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.popular-photos-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.item-explore {
    flex: 0 0 calc(25% - 15px);
}

.photo-wrapper {
    position: relative;
}

.explore-info {
    padding: 10px;
}

.user-info2 {
    display: flex;
    align-items: center;
    gap: 10px;
}

.icon-heart2:hover {
    color: #ff5a5f;
}

.dropdown-content {
    position: absolute;
    bottom: -185px;
    background-color: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    z-index: 1000;
}

.dropdown-content ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.dropdown-content li {
    padding: 10px 15px;
    display: flex;
    align-items: center;
    color: #222;
    white-space: nowrap;
    cursor: pointer;
}

.dropdown-content li:hover {
    color: #fff;
    background-color: #1890ff;
}

.dropdown-content li i {
    margin-right: 8px;
}

.dropdown-content li:hover i {
    color: #fff;
}

.icon-dots2 .fa-ellipsis-h.active {
    color: whitesmoke;
    background-color: #1890ff;
    border-radius: 50%;
    padding: 5px;
}

.btn-favorite .fa-heart.liked {
    color: #ff5a5f;
}
.featured-galleries h3,
.top-gallery-header {
    text-align: center;
    margin: 0 auto;
    width: fit-content;
}

</style>
