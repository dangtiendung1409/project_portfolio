<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="site-section">
                <div class="cover-photo">
                    <img :src="coverPhotoUrl" alt="Cover Photo" class="cover-img" />
                </div>
                <div class="profile-info">
                    <div class="profile-avatar">
                        <img :src="profilePictureUrl" alt="Profile Avatar" class="avatar-img" />
                    </div>
                    <div class="screen-right-icons">
                        <i @click="openUpdateProfileModal(user.id)" class="fa-solid fa-pencil" v-if="isMyProfile"></i>
                        <i class="fa-solid fa-share-nodes" @click="copyProfileLink"></i>
                        <i class="fa-solid fa-ellipsis" @click.stop="toggleDropdown('dropdown-' + user.id, $event)"
                           :class="{'active': activeDropdown === 'dropdown-' + user.id}"></i>
                    </div>
                    <div v-if="activeDropdown === 'dropdown-' + user.id" class="dropdown-content show" @click.stop>
                        <ul>
                            <li v-if="isMyProfile" @click="goToMyPhotos">
                                <i class="fas fa-camera"></i> My Photos
                            </li>
                            <li v-if="isMyProfile" @click="goToMyGalleries">
                                <i class="fas fa-images"></i> My Galleries
                            </li>
                            <li v-if="!isMyProfile" @click="toggleBlockUser">
                                <i class="fa-solid fa-user-large-slash"></i> {{ isBlocked ? 'Unblock user' : 'Block user' }}
                            </li>
                            <li v-if="!isMyProfile">
                                <i class="fa-regular fa-flag"></i> Report this profile
                            </li>
                        </ul>
                    </div>
                    <div class="profile-details">
                        <h1 class="profile-name">{{ user.name ? user.name : user.username }}</h1>
                        <p class="profile-location">
                            <i class="fa-solid fa-location-dot"></i> {{ user.location ? user.location : 'no location' }}
                        </p>
                        <button v-if="!isMyProfile && isBlocked" @click="toggleBlockUser" class="unblock-button">
                            Unblock
                        </button>
                        <button v-if="!isMyProfile && !isBlocked" @click="toggleFollow" class="follow-button">
                            {{ isFollowing ? 'Unfollow' : 'Follow' }}
                        </button>

                        <p class="profile-bio">
                            <span v-if="user.bio">
                                <span v-if="isBioExpanded">{{ user.bio }}</span>
                                <span v-else>{{ truncatedBio }}</span>
                                <a v-if="user.bio.length > 100" href="#" class="read-more" @click.prevent="toggleBio">
                                    {{ isBioExpanded ? 'Read less' : 'Read more' }}
                                </a>
                            </span>
                            <span v-else>No bio</span>
                        </p>
                        <div class="profile-stats">
                            <span @click="showFollowersPopup"><strong>{{ userFollowersCount }}</strong> Followers</span>
                            <span @click="showFollowingPopup"><strong>{{ userFollowingCount }}</strong> Following</span>
                            <span><strong>{{ formattedPhotoLikes }}</strong> Photo Likes</span>
                        </div>
                    </div>
                </div>
                <div class="tabs" v-if="!isBlocked && (photos.length > 0 || galleries.length > 0)">
                    <a class="tab" :class="{ active: activeTab === 'photos' }" @click.prevent="activeTab = 'photos'" v-if="photos.length > 0">
                        Photos <span>{{ photos.length }}</span>
                    </a>
                    <a class="tab" :class="{ active: activeTab === 'galleries' }" @click.prevent="activeTab = 'galleries'" v-if="galleries.length > 0">
                        Galleries <span>{{ galleries.length }}</span>
                    </a>
                </div>
                <div v-if="activeTab === 'photos' && !isBlocked">
                    <PhotoGrid :photos="photos" :checkLogin="checkLogin" />
                </div>
                <div v-else-if="activeTab === 'galleries' && !isBlocked">
                    <GalleryGrid :galleries="galleries" />
                </div>
            </div>
        </template>
    </Layout>
    <UpdateProfileModal
        :isVisible="showUpdateModal"
        :user-id="selectedUserId"
        @close="closeUpdateProfileModal"
        @update="reloadProfileData"
    />
    <!-- Popup hiển thị danh sách Followers -->
    <div v-if="followersPopupVisible" class="popup-overlay" @click.self="closeFollowersPopup">
        <div class="popup-content">
            <div class="popup-header">
                <h3>{{ user.name || user.username }}'s Followers</h3>
                <button @click="closeFollowersPopup" class="popup-close">×</button>
            </div>
            <div v-if="followersData.length > 0" class="popup-list">
                <div v-for="follower in followersData" :key="follower.id" class="popup-item">
                    <router-link :to="{ name: 'MyProfile', params: { username: follower.username } }">
                    <img :src="follower.profile_picture ? `http://127.0.0.1:8000/images/avatars/${follower.profile_picture.split('/').pop()}` : '/images/imageUserDefault.png'" alt="Avatar" class="popup-avatar" />
                    </router-link>
                    <div class="popup-user-info">
                        <span class="popup-username">{{ follower.username }}</span>
                        <span class="popup-followers">{{ follower.followers_count || 0 }} Followers</span>
                    </div>
                    <button @click.stop="toggleFollowUser(follower.username)" class="popup-follow-button">
                        {{ followStore.followingList.includes(follower.id) ? 'Unfollow' : 'Follow' }}
                    </button>
                </div>
            </div>
            <div v-else class="popup-no-data">No followers found.</div>
        </div>
    </div>
    <!-- Popup hiển thị danh sách Following -->
    <div v-if="followingPopupVisible" class="popup-overlay" @click.self="closeFollowingPopup">
        <div class="popup-content">
            <div class="popup-header">
                <h3>{{ user.name || user.username }}'s Following</h3>
                <button @click="closeFollowingPopup" class="popup-close">×</button>
            </div>
            <div v-if="followingData.length > 0" class="popup-list">
                <div v-for="following in followingData" :key="following.id" class="popup-item">
                    <router-link :to="{ name: 'MyProfile', params: { username: following.username } }">
                    <img :src="following.profile_picture ? `http://127.0.0.1:8000/images/avatars/${following.profile_picture.split('/').pop()}` : '/images/imageUserDefault.png'" alt="Avatar" class="popup-avatar" />
                    </router-link>
                    <div class="popup-user-info">
                        <span class="popup-username">{{ following.username }}</span>
                        <span class="popup-followers">{{ following.followers_count || 0 }} Followers</span>
                    </div>
                    <button @click.stop="toggleFollowUser(following.username)" class="popup-follow-button">
                        {{ followStore.followingList.includes(following.id) ? 'Unfollow' : 'Follow' }}
                    </button>
                </div>
            </div>
            <div v-else class="popup-no-data">No following found.</div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Layout from './Layout.vue';
import UpdateProfileModal from './MyAccount/components/UpdateProfileModal.vue';
import PhotoGrid from './components/profile/PhotoGrid.vue';
import GalleryGrid from './components/profile/GalleryGrid.vue';
import getUrlList from '../provider.js';
import { useAuthStore } from '@/stores/authStore';
import { useUserStore } from '../stores/userStore.js';
import { useFollowStore } from '../stores/followStore.js';
import { useBlockStore } from '../stores/blockStore';
import { notification, Modal } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { h } from 'vue';

export default {
    name: "MyProfile",
    components: {
        Layout,
        UpdateProfileModal,
        PhotoGrid,
        GalleryGrid
    },
    data() {
        return {
            activeTab: "photos",
            activeDropdown: null,
            user: {},
            photos: [],
            galleries: [],
            photoLikesCount: 0,
            isBioExpanded: false,
            showUpdateModal: false,
            selectedUserId: null,
            isMyProfile: false,
            isFollowing: false,
            isBlocked: false,
            followersPopupVisible: false,
            followingPopupVisible: false,
        };
    },
    computed: {
        profilePictureUrl() {
            return this.user.profile_picture ? `http://127.0.0.1:8000/images/avatars/${this.user.profile_picture.split('/').pop()}` : '/images/imageUserDefault.png';
        },
        coverPhotoUrl() {
            return this.user.cover_photo ? `http://127.0.0.1:8000/images/covers/${this.user.cover_photo.split('/').pop()}` : '/images/blackImage.jpeg';
        },
        truncatedBio() {
            if (this.user.bio && this.user.bio.length > 100) {
                return this.user.bio.substring(0, 100) + '...';
            }
            return this.user.bio;
        },
        userStore() {
            return useUserStore();
        },
        isLoggedIn() {
            const authStore = useAuthStore();
            return authStore.isLoggedIn;
        },
        followStore() {
            return useFollowStore();
        },
        userFollowersCount() {
            return this.followStore.userFollowersList.length;
        },
        userFollowingCount() {
            return this.followStore.userFollowingList.length;
        },
        followersData() {
            return this.followStore.userFollowersList;
        },
        followingData() {
            return this.followStore.userFollowingList;
        },
        formattedPhotoLikes() {
            const likes = this.photoLikesCount;
            if (likes >= 1000000) {
                return (likes / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
            } else if (likes >= 1000) {
                return (likes / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
            } else {
                return likes;
            }
        },
    },
    watch: {
        '$route.params.username': {
            immediate: true,
            handler() {
                this.reloadProfileData();
            }
        }
    },
    async mounted() {
        await this.reloadProfileData();
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
        async reloadProfileData() {
            await this.fetchUserData();
            this.fetchPhotos();
            this.fetchGalleries();
            await this.checkIfBlocked();
            await this.checkIfMyProfile();
            if (!this.isMyProfile) {
                await this.checkFollowingStatus();
            }
            const followStore = useFollowStore();
            const username = this.$route.params.username;
            await followStore.fetchUserFollowersList(username);
            await followStore.fetchUserFollowingList(username);

            await this.fetchTotalLikes();
        },
        toggleDropdown(id) {
            this.activeDropdown = this.activeDropdown === id ? null : id;
        },
        async fetchUserData() {
            const username = this.$route.params.username;
            try {
                const response = await axios.get(getUrlList().getUserByUserName(username));
                this.user = response.data;
            } catch (error) {
                console.error('Error fetching user data:', error);
            }
        },
        async fetchTotalLikes() {
            const username = this.$route.params.username;
            try {
                const response = await axios.get(getUrlList().getTotalLikes(username));
                if (response.data.success) {
                    this.photoLikesCount = response.data.data.total_likes;
                } else {
                    this.photoLikesCount = 0;
                }
            } catch (error) {
                console.error('Error fetching total likes:', error);
                this.photoLikesCount = 0;
            }
        },
        async checkIfBlocked() {
            const blockStore = useBlockStore();
            await blockStore.fetchBlockedUsers();
            this.isBlocked = blockStore.blockedUsers.includes(this.user.id);
        },
        async toggleBlockUser() {
            if (!await this.checkLogin()) return;
            const blockStore = useBlockStore();
            if (this.isBlocked) {
                await blockStore.unblockUser(this.user.id);
                this.isBlocked = false;
                notification.success({
                    message: 'Success',
                    description: `${this.user.username} is unblocked.`,
                    placement: 'topRight',
                    duration: 3,
                });
            } else {
                await blockStore.blockUser(this.user.id);
                this.isBlocked = true;
                this.isFollowing = false;
                notification.success({
                    message: 'Success',
                    description: `${this.user.username} has been blocked. All their related content will not be visible going forward.`,
                    placement: 'topRight',
                    duration: 3,
                });
            }
        },
        openUpdateProfileModal(id) {
            this.selectedUserId = id;
            this.showUpdateModal = true;
        },
        closeUpdateProfileModal() {
            this.showUpdateModal = false;
        },
        async checkFollowingStatus() {
            const followStore = useFollowStore();
            await followStore.fetchFollowingList();
            this.isFollowing = followStore.followingList.includes(this.user.id);
        },
        async toggleFollow() {
            if (!await this.checkLogin()) return;
            if (this.isBlocked) return;

            const followStore = useFollowStore();
            const username = this.$route.params.username;

            if (this.isFollowing) {
                Modal.confirm({
                    title: 'Are you sure you want to unfollow this user?',
                    icon: h(ExclamationCircleOutlined),
                    content: 'This will unfollow the photographer. You will no longer see their content in your For You feed.',
                    onOk: async () => {
                        try {
                            await followStore.unfollowUser(this.user.id, username);
                            this.isFollowing = false;
                            notification.success({
                                message: 'Success',
                                description: `You have unfollowed ${this.user.username}.`,
                                placement: 'topRight',
                                duration: 3,
                            });
                        } catch (error) {
                            console.error('Error unfollowing user:', error);
                        }
                    },
                    onCancel() {},
                });
            } else {
                try {
                    await followStore.followUser(this.user.id, username);
                    this.isFollowing = true;
                    notification.success({
                        message: 'Success',
                        description: `You are now following ${this.user.username}.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                } catch (error) {
                    console.error('Error following user:', error);
                }
            }
        },
        async fetchPhotos() {
            const username = this.$route.params.username;
            try {
                const response = await axios.get(getUrlList().getPhotosByUserName(username));
                this.photos = response.data;
            } catch (error) {
                console.error('Error fetching photos:', error);
            }
        },
        async fetchGalleries() {
            const username = this.$route.params.username;
            try {
                const token = localStorage.getItem("token");
                let headers = {};
                if (token) {
                    headers = { Authorization: `Bearer ${token}` };
                }
                const response = await axios.get(getUrlList().getGalleriesByUserName(username), { headers });
                this.galleries = response.data;
            } catch (error) {
                console.error('Error fetching galleries:', error);
            }
        },
        toggleBio() {
            this.isBioExpanded = !this.isBioExpanded;
        },
        copyProfileLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url)
                .then(() => {
                    notification.success({
                        message: 'Success',
                        description: 'Link copied successfully!',
                        placement: 'topRight',
                    });
                })
                .catch(err => {
                    notification.error({
                        message: 'Error',
                        description: 'Failed to copy the link.',
                        placement: 'topRight',
                    });
                    console.error("Failed to copy: ", err);
                });
        },
        async checkIfMyProfile() {
            const userStore = useUserStore();
            await userStore.fetchUserData();
            const authUser = userStore.user;
            if (authUser.username === this.$route.params.username) {
                this.isMyProfile = true;
            }
        },
        goToMyPhotos() {
            this.$router.push({ name: 'MyPhoto' });
        },
        goToMyGalleries() {
            this.$router.push({ name: 'MyGallery' });
        },
        showFollowersPopup() {
            console.log('Opening Followers Popup', this.followersData);
            this.followersPopupVisible = true;
        },
        closeFollowersPopup() {
            this.followersPopupVisible = false;
        },
        showFollowingPopup() {
            console.log('Opening Following Popup', this.followingData);
            this.followingPopupVisible = true;
        },
        closeFollowingPopup() {
            this.followingPopupVisible = false;
        },
        async toggleFollowUser(username) {
            if (!await this.checkLogin()) return;

            const followStore = useFollowStore();
            try {
                const userData = await axios.get(getUrlList().getUserByUserName(username));
                const userId = userData.data.id;

                if (followStore.followingList.includes(userId)) {
                    await followStore.unfollowUser(userId);
                    notification.success({
                        message: 'Success',
                        description: `You have unfollowed ${username}.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                } else {
                    await followStore.followUser(userId);
                    notification.success({
                        message: 'Success',
                        description: `You are now following ${username}.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                }
                // Làm mới danh sách followers/following sau khi thay đổi
                const profileUsername = this.$route.params.username;
                await followStore.fetchUserFollowersList(profileUsername);
                await followStore.fetchUserFollowingList(profileUsername);
            } catch (error) {
                console.error('Error toggling follow for user:', error);
                notification.error({
                    message: 'Error',
                    description: `Failed to toggle follow for ${username}.`,
                    placement: 'topRight',
                    duration: 3,
                });
            }
        },
        async fetchUserByUsername(username) {
            try {
                const response = await axios.get(getUrlList().getUserByUserName(username));
                return {
                    id: response.data.id,
                    username: response.data.username,
                    profile_picture: response.data.profile_picture,
                    followers_count: response.data.followers_count || 0 // Lấy trực tiếp từ API
                };
            } catch (error) {
                console.error(`Error fetching user data for ${username}:`, error);
                return null;
            }
        },
    }
};
</script>

<style scoped>
.dropdown-content {
    position: absolute;
    right: 75px;
    top: 83%;
    margin-top: 10px;
    z-index: 1001;
    background-color: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    border-radius: 4px;
    overflow: hidden;
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
    color: whitesmoke;
    background-color: #1890ff;
}

.dropdown-content li i {
    margin-right: 8px;
}

.dropdown-content li:hover i {
    color: whitesmoke;
    background-color: #1890ff;
}

.unblock-button {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    font-size: 16px;
    line-height: 20px;
    font-weight: bold;
    margin: 0px;
    border-radius: 28px;
    cursor: pointer;
    text-align: center;
    border: 2px solid red;
    color: red;
    background-color: white;
    width: auto;
    min-width: 110px;
    max-height: 32px;
    padding: 4px 14px;
}

.unblock-button:hover {
    background-color: rgb(230, 230, 230);
}

.site-section {
    color: #333;
    display: flex;
    flex-direction: column;
    padding: 5px;
}

.screen-right-icons {
    position: absolute;
    top: 80%;
    right: 80px;
    transform: translateY(-50%);
    display: flex;
    gap: 20px;
}

.screen-right-icons i {
    font-size: 20px;
    cursor: pointer;
    transition: color 0.3s;
}

.screen-right-icons i:hover {
    color: #0870D1;
}

.cover-photo {
    margin-top: 65px;
    word-break: break-word;
    max-width: 100%;
    z-index: 1;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    width: 100%;
    height: 400px;
    margin-bottom: 40px;
}

.cover-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: -50px;
    padding: 0 20px;
}

.profile-avatar {
    position: relative;
    flex-shrink: 0;
    margin-bottom: 20px;
    margin-top: 30px;
    text-align: center;
}

.avatar-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid white;
    object-fit: cover;
}

.profile-details {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.profile-name {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
}

.profile-location {
    color: gray;
    margin: 5px 0;
}

.profile-location i {
    margin-right: 2px;
    color: black;
    font-size: 16px;
}

.profile-bio {
    margin: 10px 0;
}

.read-more {
    color: blue;
    text-decoration: underline;
    cursor: pointer;
}

.profile-stats {
    display: flex;
    gap: 10px;
    margin: 10px 0;
    justify-content: center;
    flex-wrap: wrap;
}

.profile-stats span {
    font-size: 14px;
    color: black;
    font-weight: bold;
    cursor: pointer;
}

.profile-stats span:hover {
    color: #007bff;
}

.profile-stats span strong {
    color: gray;
}

.follow-button {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    font-size: 16px;
    line-height: 20px;
    font-weight: bold;
    margin: 0px;
    border-width: 2px;
    border-radius: 28px;
    border-style: solid;
    cursor: pointer;
    text-align: center;
    background-color: rgb(8, 112, 209);
    border-color: rgb(8, 112, 209);
    color: rgb(255, 255, 255);
    width: auto;
    min-width: 110px;
    max-height: 32px;
    padding: 4px 14px;
}

.follow-button:hover {
    background-color: #0056b3;
}

.tabs {
    display: flex;
    gap: 20px;
    padding: 10px 20px;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
    justify-content: center;
}

.tab {
    text-decoration: none;
    color: gray;
    font-size: 16px;
    position: relative;
}

.tab.active {
    color: #0870D1;
    font-weight: bold;
}

.tab.active::after {
    content: "";
    position: absolute;
    bottom: -5px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #0870D1;
}

/* Style cho popup */
.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    display: flex;
    justify-content: center;
    align-items: center;
}

.popup-content {
    background-color: white;
    border-radius: 12px; /* Tăng độ cong */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* Bóng lớn hơn */
    width: 500px; /* Mở rộng độ rộng */
    max-height: 90vh; /* Tăng chiều cao tối đa */
    overflow-y: auto;
    position: relative;
}

.popup-header {
    padding: 15px 20px; /* Tăng padding */
    border-bottom: 2px solid #eee; /* Dày hơn */
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.popup-header h3 {
    margin: 0;
    font-size: 20px; /* Tăng kích thước chữ */
    color: #333;
    font-weight: bold;
}

.popup-close {
    background: none;
    border: none;
    font-size: 34px; /* Tăng kích thước nút đóng */
    cursor: pointer;
    color: #666;
    padding: 0;
    line-height: 1;
    width: 24px;
    height: 24px;
}

.popup-list {
    padding: 15px; /* Tăng padding */
}

.popup-item {
    display: flex;
    align-items: center;
    padding: 15px 0; /* Tăng padding */
    border-bottom: 2px solid #eee; /* Dày hơn */
}

.popup-avatar {
    width: 40px; /* Tăng kích thước avatar */
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px; /* Tăng khoảng cách */
}

.popup-user-info {
    flex-grow: 1;
}

.popup-username {
    font-size: 16px; /* Tăng kích thước chữ */
    color: #333;
    display: block;
    font-weight: bold;
}

.popup-followers {
    font-size: 14px; /* Tăng kích thước chữ */
    color: #666;
    display: block;
}

.popup-follow-button {
    background-color: #007bff;
    border: none;
    border-radius: 24px; /* Tăng độ cong */
    color: white;
    padding: 8px 16px; /* Tăng padding */
    font-size: 14px; /* Tăng kích thước chữ */
    cursor: pointer;
    margin-left: 15px; /* Tăng khoảng cách */
}

.popup-follow-button:hover {
    background-color: #0056b3;
}

.popup-no-data {
    padding: 30px; /* Tăng padding */
    text-align: center;
    color: #666;
    font-size: 16px; /* Tăng kích thước chữ */
}
</style>
