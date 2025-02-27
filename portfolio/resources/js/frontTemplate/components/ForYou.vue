<template>
    <div id="portfolio-grid" class="row no-gutter" data-aos="fade-up" data-aos-delay="200">
        <div v-for="item in photos" :key="item.id" class="item web col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="item-wrap fancybox">
                <router-link :to="{ name: 'PhotoDetail', params: { token: item.photo_token } }">
                    <img v-lazy="`${item.image_url}`" class="img-fluid rounded-image" alt="">
                </router-link>
                <div class="work-info">
                    <div class="user-info2">
                        <router-link :to="{ name: 'MyProfile', params: { username: item.user.username } }">
                            <img class="user-image2" :src="item.user.profile_picture || '/images/userDefault.png'" style="width: 30px; height: 30px">
                        </router-link>
                        <span class="user-name2">{{ item.user.username }}</span>
                        <span class="icon-heart2" @click="toggleLike(item)">
                           <i :class="['fas', 'fa-heart', { 'liked': item.liked }]"></i>
                        </span>
                        <span class="icon-dots2">
                           <i :class="['fas', 'fa-ellipsis-h', { 'active': activeDropdown === 'dotsDropdown-' + item.id }]" @click.stop="toggleDropdown('dotsDropdown-' + item.id)"></i>
                       </span>
                    </div>
                </div>
            </div>
            <div v-if="activeDropdown === 'dotsDropdown-' + item.id" class="dropdown-content show" style="right: 25px">
                <ul>
                    <li @click="handleClick('addToGallery', item.id)"><i class="fa-regular fa-square-plus"></i> Add to Gallery</li>
                    <li v-if="item.user && userStore.user && item.user.id !== userStore.user.id" @click="toggleBlockUser(item.user)">
                        <i class="fas fa-user-slash"></i> {{ item.blocked ? 'Unblock' : 'Block' }}
                    </li>
                    <li v-if="item.user && userStore.user && item.user.id !== userStore.user.id" @click="toggleFollow(item)">
                        <i class="fas" :class="item.following ? 'fa-user-minus' : 'fa-user-plus'"></i>
                        {{ item.following ? 'Unfollow' : 'Follow' }}
                    </li>
                    <li v-if="item.user && userStore.user && item.user.id !== userStore.user.id" @click="handleClick('reportPhoto', item.id)">
                        <i class="fas fa-flag"></i>
                        Report This Photo
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <AddToGalleryModal
        :is-visible="showAddToGallery"
        :photo-id="selectedPhotoId"
        @close="closeAddToGalleryModal"
    />
</template>

<script>
import lazyDirective from '../../lazy.js';
import AddToGalleryModal from "../components/AddToGalleryModal.vue";
import { useLikeStore } from '@/stores/likeStore';
import { useAuthStore } from '@/stores/authStore';
import { useFollowStore } from '@/stores/followStore';
import { useUserStore } from '@/stores/userStore';
import { useBlockStore } from '@/stores/blockStore';
import { Modal,notification } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { h } from 'vue';

export default {
    directives: {
        lazy: lazyDirective,
    },
    components: {
        AddToGalleryModal,
    },
    props: {
        photos: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            activeDropdown: null, // Biến lưu trữ dropdown đang mở
            showAddToGallery: false,
            selectedPhotoId: null,
        };
    },
    async mounted() {
        const userStore = useUserStore();
        await userStore.fetchUserData();

        const likeStore = useLikeStore();
        await likeStore.fetchLikedPhotos();
        this.updateLikedState();

        const followStore = useFollowStore();
        await followStore.fetchFollowingList();
        this.updateFollowingState();

        const blockStore = useBlockStore();
        blockStore.fetchBlockedUsers().then(() => {
            this.updateBlockedState();
        });
    },
    computed: {
        userStore() {
            return useUserStore();
        }
    },
    methods: {
        async checkLogin() {
            const authStore = useAuthStore();
            await authStore.checkLoginStatus();
            if (!authStore.isLoggedIn) {
                this.$router.push({ name: 'Login' });
                return false;
            }
            return true;
        },
        async handleClick(action, itemId) {
            if (!await this.checkLogin()) {
                return; // Nếu chưa đăng nhập, dừng thực hiện các hành động khác
            }

            switch (action) {
                case 'addToGallery':
                    this.openAddToGalleryModal(itemId);
                    break;
                case 'blockUser':
                    this.blockUser(itemId);
                    break;
                case 'followUser':
                    this.followUser(itemId);
                    break;
                case 'reportPhoto':
                    this.reportPhoto(itemId);
                    break;
                default:
                    console.error('Unknown action:', action);
            }
        },
        updateFollowingState() {
            const followStore = useFollowStore();
            this.photos.forEach(photo => {
                photo.following = followStore.followingList.includes(photo.user.id);
            });
        },
        async toggleFollow(item) {
            if (!await this.checkLogin()) return;

            const followStore = useFollowStore();
            const userId = item.user.id;
            const username = item.user.username; // Lấy username để hiển thị trong thông báo

            // Nếu đang follow, hiển thị modal xác nhận unfollow
            if (item.following) {
                Modal.confirm({
                    title: 'Are you sure you want to unfollow this user?',
                    icon: h(ExclamationCircleOutlined),
                    content: 'This will unfollow the photographer. You will no longer see their content in your For You feed.',
                    onOk: async () => {
                        try {
                            await followStore.unfollowUser(userId);
                            item.following = false;
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
                    onCancel() {
                        // Không làm gì nếu hủy
                    },
                });
            } else {
                // Nếu chưa follow thì follow luôn
                try {
                    await followStore.followUser(userId);
                    item.following = true;
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
        updateBlockedState() {
            const blockStore = useBlockStore();
            this.photos.forEach(photo => {
                photo.blocked = blockStore.blockedUsers.includes(photo.user.id);
            });
        },
        updateLikedState() {
            const likeStore = useLikeStore();
            this.photos.forEach(photo => {
                photo.liked = likeStore.likedPhotos.includes(photo.id); // Cập nhật trạng thái liked của từng ảnh
            });
        },
        async toggleLike(item) {
            if (!await this.checkLogin()) {
                return;
            }

            const photo_id = item.id; // ID của ảnh
            const photo_user_id = item.user.id; // ID của người sở hữu ảnh
            const likeStore = useLikeStore();

            try {
                if (item.liked) {
                    await likeStore.unlikePhoto(photo_id);
                } else {
                    await likeStore.likePhoto(photo_id, photo_user_id); // Gửi thêm photo_user_id
                }
                item.liked = !item.liked; // Đảo ngược trạng thái liked
            } catch (error) {
                console.error('Failed to toggle like:', error);
            }
        },
        toggleDropdown(id) {
            if (this.activeDropdown === id) {
                this.activeDropdown = null;
            } else {
                this.activeDropdown = id;
            }
        },
        openAddToGalleryModal(photoId) {
            this.selectedPhotoId = photoId;
            this.showAddToGallery = true; // Mở modal
        },
        closeAddToGalleryModal() {
            this.showAddToGallery = false; // Đóng modal
        },
    },
    created() {
        const notifData = localStorage.getItem('blockNotification');
        if (notifData) {
            const {message, description, duration} = JSON.parse(notifData);
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
        photos: {
            handler() {
                this.updateLikedState();
                this.updateFollowingState();
            },
            deep: true,
            immediate: true
        }
    }
};
</script>

<style scoped>
.item .item-wrap {
    display: block;
    position: relative;
}

.user-info2 {
    display: flex;
    align-items: center;
    flex-direction: row;
    margin-left: 10px;
}

.user-image2, .user-name2, .icon-heart2, .icon-dots2 {
    pointer-events: auto; /* Cho phép tương tác */
}

.user-image2 {
    border-radius: 50%;
    margin-right: 10px;
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
    cursor: pointer;
}

.icon-heart2 .fa-heart.liked {
    color: #ff5a5f; /* Màu khi đã like */
}

.icon-dots2 {
    position: relative;
    display: inline-block;
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

/* Thêm CSS cho trạng thái active */
.icon-dots2 .fa-ellipsis-h.active {
    color: whitesmoke;
    background-color: #1890ff;
    border-radius: 50%;
    padding: 5px;
}

</style>
