<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="site-section">
                <!-- Cover Photo -->
                <div class="cover-photo">
                    <img :src="coverPhotoUrl" alt="Cover Photo" class="cover-img" />
                </div>

                <!-- Profile Information -->
                <div class="profile-info">
                    <div class="profile-avatar">
                        <img :src="profilePictureUrl" alt="Profile Avatar" class="avatar-img" />
                    </div>
                    <div class="screen-right-icons">
                        <i @click="openUpdateProfileModal(user.id)" class="fa-solid fa-pencil" v-if="isMyProfile"></i>
                        <i class="fa-solid fa-share-nodes" @click="copyProfileLink"></i>
                        <i class="fa-solid fa-ellipsis"  @click.stop="toggleDropdown('dropdown-' + user.id, $event)"
                           :class="{'active': activeDropdown === 'dropdown-' + user.id}"></i>
                    </div>
                    <div v-if="activeDropdown === 'dropdown-' + user.id" class="dropdown-content show" @click.stop>
                        <ul>
                            <li v-if="isMyProfile">
                                <i class="fas fa-camera"></i> My Photos
                            </li>
                            <li v-if="isMyProfile">
                                <i class="fas fa-images"></i> My Galleries
                            </li>
                            <!-- Sử dụng v-if để kiểm tra điều kiện -->
                            <li v-if="!isMyProfile">
                                <i class="fa-solid fa-user-large-slash"></i> Block user
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
                        <button class="follow-button" v-if="!isMyProfile">Follow</button>
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
                            <span><strong>58.1K</strong> Followers</span>
                            <span><strong>120</strong> Following</span>
                            <span><strong>2.1M</strong> Photo Likes</span>
                        </div>
                    </div>
                </div>
                <!-- Tabs -->
                <div class="tabs">
                    <a href="#" class="tab" :class="{ active: activeTab === 'photos' }" @click.prevent="activeTab = 'photos'">
                        Photos <span>{{ photos.length }}</span>
                    </a>
                    <a href="#" class="tab" :class="{ active: activeTab === 'galleries' }" @click.prevent="activeTab = 'galleries'">
                        Galleries <span>{{ galleries.length }}</span>
                    </a>
                </div>

                <!-- Content based on active tab -->
                <div v-if="activeTab === 'photos'">
                    <PhotoGrid :photos="photos" />
                </div>
                <div v-else-if="activeTab === 'galleries'">
                    <GalleryGrid :galleries="galleries" />
                </div>
            </div>
        </template>
    </Layout>
    <UpdateProfileModal
        :isVisible="showUpdateModal"
        :user-id="selectedUserId"
        @close="closeUpdateProfileModal"
        @update="fetchUserData"
    />
</template>

<script>
import axios from 'axios';
import Layout from './Layout.vue';
import UpdateProfileModal from './MyAccount/components/UpdateProfileModal.vue';
import PhotoGrid from './components/profile/PhotoGrid.vue';
import GalleryGrid from './components/profile/GalleryGrid.vue';
import getUrlList from '../provider.js';
import { notification } from 'ant-design-vue';
import { useUserStore } from '../stores/userStore.js';

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
            isBioExpanded: false,
            showUpdateModal: false,
            selectedUserId: null,
            isMyProfile: false, // Thêm biến này để xác định trang cá nhân của mình
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
        }
    },
    mounted() {
        this.fetchUserData();
        this.fetchPhotos();
        this.fetchGalleries();
        this.checkIfMyProfile();
    },
    methods: {
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
        openUpdateProfileModal(id) {
            this.selectedUserId = id;
            this.showUpdateModal = true; // Mở modal
        },
        closeUpdateProfileModal() {
            this.showUpdateModal = false; // Close modal
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
                const response = await axios.get(getUrlList().getGalleriesByUserName(username));
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
        }
    }
};
</script>

<style scoped>
.dropdown-content {
    position: absolute;
    right: 75px;
    top: 83%;
    margin-top: 10px;
    z-index: 1000;
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
    margin-right: 2px; /* Tạo khoảng cách giữa icon và chữ */
    color: black; /* Màu đỏ cho icon (tuỳ chỉnh) */
    font-size: 16px; /* Kích thước icon */
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
}

.profile-stats span strong {
    color: gray; /* Màu xám cho số */
}

.follow-button {
    display: inline-flex;
    -webkit-box-pack: center;
    justify-content: center;
    -webkit-box-align: center;
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
</style>
