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
                        <!-- Horizontal scrollable photo container -->
                        <div class="photo-gallery">
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
                                        <span class="icon-heart2"><i class="fas fa-heart"></i></span>
                                        <span class="icon-dots2">
                    <i
                        :class="['fas', 'fa-ellipsis-h', { 'active': activeDropdown === 'dotsDropdown-' + like.id }]"
                        @click="toggleDropdown('dotsDropdown-' + like.id)"
                    ></i>
                </span>
                                    </div>
                                    <div
                                        v-if="activeDropdown === 'dotsDropdown-' + like.id"
                                        class="dropdown-content show"
                                        style="right: 25px"
                                    >
                                        <ul>
                                            <li><i class="fa-regular fa-square-plus"></i> Add to Gallery</li>
                                            <li><i class="fas fa-user-slash"></i> Block User</li>
                                            <li><i class="fas fa-user-plus"></i> Follow User</li>
                                            <li><i class="fas fa-flag"></i> Report This Photo</li>
                                        </ul>
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

export default {
    name: 'Like',
    components: {
        Layout,
        Sidebar,
    },
    data() {
        return {
            likedPhotos: [],
            activeDropdown: null
        };
    },
    mounted() {
        this.fetchLikedPhotos();
    },
    methods: {
        toggleDropdown(id) {
            if (this.activeDropdown === id) {
                this.activeDropdown = null;
            } else {
                this.activeDropdown = id;
            }
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
                    photoToken: like.photo?.photo_token || null,
                    imageUrl: like.photo?.image_url || '/images/default-photo.png',
                    username: like.photo?.user?.username || 'Unknown User',
                    userAvatar: like.photo?.user?.profile_picture || '/images/imageUserDefault.png',
                }));
            } catch (error) {
                console.error('Error fetching liked photos:', error);
            }
        }
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
