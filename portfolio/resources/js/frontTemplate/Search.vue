<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="site-section">
                <div class="results-container">
                    <div class="results">
                        Results: <strong>{{ totalResults }} photos</strong>
                    </div>

                    <!-- Hiển thị nếu có kết quả -->
                    <div v-if="totalResults > 0" class="image-grid" :class="{ 'single-result': totalResults === 1 }">
                        <div class="image-item" v-for="(image, index) in images" :key="index">
                            <router-link :to="{ name: 'PhotoDetail', params: { token: image.photo_token } }">
                            <img :src="image.image_url" alt="Search Result" class="search-image" />
                            </router-link>
                            <div class="work-info">
                                <div class="user-info2">
                                    <router-link :to="{ name: 'MyProfile', params: { username: image.user.username } }">
                                    <img class="user-image2" :src="getProfilePicture(image.user.profile_picture)" style="width: 30px; height: 30px">
                                    </router-link>
                                    <span class="user-name2">{{ image.user.username }}</span>
                                    <span class="icon-heart2" @click="toggleLike(image)">
                                        <i :class="['fas', 'fa-heart', { 'liked': image.liked }]"></i>
                                    </span>
                                    <span class="icon-dots2">
                                       <i @click="handleClick('addToGallery', image.id)" class="fa-regular fa-square-plus"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hiển thị nếu không có kết quả -->
                    <div v-else class="no-results">
                        <i class="fas fa-search"></i>
                        <p>No results for "<strong>{{ $route.query.q }}</strong>"</p>
                        <p>Check the spelling or try modifying your search</p>
                    </div>
                </div>
            </div>
        </template>
    </Layout>
    <AddToGalleryModal
        :is-visible="showAddToGallery"
        :photo-id="selectedPhotoId"
        @close="closeAddToGalleryModal"
    />
</template>

<script>
import Layout from './Layout.vue';
import AddToGalleryModal from "./components/AddToGalleryModal.vue";
import { useAuthStore } from '@/stores/authStore';
import { useLikeStore } from '@/stores/likeStore';
import axios from 'axios';
import getUrlList from '../provider.js';

export default {
    name: "Search",
    components: {
        Layout,
        AddToGalleryModal,
    },
    data() {
        return {
            images: [],
            totalResults: 0,
            activeDropdown: null,
            showAddToGallery: false,
            selectedPhotoId: null,
        };
    },
    async mounted() {
        const likeStore = useLikeStore();
        await likeStore.fetchLikedPhotos(); // Tải danh sách ảnh đã like trước
        await this.fetchSearchResults(); // Sau đó tải ảnh tìm kiếm
        this.updateLikedState(); // Cập nhật trạng thái like
    },

    watch: {
        '$route.query.q': {
            async handler() {
                await this.fetchSearchResults();
                this.updateLikedState(); // Đảm bảo trạng thái "liked" được cập nhật
            },
            immediate: true
        }
    },


    methods: {
        async fetchSearchResults() {
            const urlList = getUrlList();
            const searchTerm = this.$route.query.q || '';
            const token = localStorage.getItem('token'); // Lấy token từ localStorage hoặc Vuex nếu bạn lưu ở đó

            try {
                const response = await axios.get(`${urlList.searchPhotos}?q=${searchTerm}`, {
                    headers: {
                        Authorization: token ? `Bearer ${token}` : '', // Gửi token nếu có
                    }
                });
                this.images = response.data;
                this.totalResults = this.images.length;
            } catch (error) {
                console.error('Failed to fetch search results:', error);
                this.images = [];
                this.totalResults = 0;
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
        async handleClick(action, itemId) {
            if (!await this.checkLogin()) {
                return; // Nếu chưa đăng nhập, dừng thực hiện các hành động khác
            }

            switch (action) {
                case 'addToGallery':
                    this.openAddToGalleryModal(itemId);
                    break;
                default:
                    console.error('Unknown action:', action);
            }
        },
        openAddToGalleryModal(id) {
            this.selectedPhotoId = id;
            this.showAddToGallery = true; // Mở modal
        },
        closeAddToGalleryModal() {
            this.showAddToGallery = false; // Đóng modal
        },
        updateLikedState() {
            const likeStore = useLikeStore();
            this.images.forEach(image => {
                image.liked = likeStore.likedPhotos.includes(image.id);
            });
        },
        async toggleLike(image) {
            if (!await this.checkLogin()) {
                return;
            }

            const photo_id = image.id; // ID của ảnh
            const photo_user_id = image.user.id; // ID của người sở hữu ảnh
            const likeStore = useLikeStore();

            try {
                if (image.liked) {
                    await likeStore.unlikePhoto(photo_id);
                } else {
                    await likeStore.likePhoto(photo_id, photo_user_id); // Gửi thêm photo_user_id
                }
                image.liked = !image.liked; // Đảo ngược trạng thái liked
            } catch (error) {
                console.error('Failed to toggle like:', error);
            }
        },
        getProfilePicture(profilePicture) {
            const baseUrl = 'http://127.0.0.1:8000/images/avatars/';
            if (profilePicture.startsWith('http')) {
                return profilePicture;
            }
            return baseUrl + profilePicture.split('/').pop();
        },
        toggleDropdown(id) {
            if (this.activeDropdown === id) {
                this.activeDropdown = null;
            } else {
                this.activeDropdown = id;
            }
        }
    }
};
</script>

<style scoped>
.site-section {
    padding: 20px;
}

.no-results {
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 60vh; /* Chiều cao 60% màn hình */
    text-align: center;
    color: #666;
   margin-left: 500px;
}

.no-results i {
    font-size: 48px;
    margin-bottom: 10px;
    display: block;
    color: #888;
}

.no-results p {
    font-size: 18px;
    margin: 5px 0;
}

.results-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.results {
    font-size: 18px;
    margin-bottom: 20px;
    color: #333;
}

.image-grid {
    display: flex;
    flex-wrap: wrap; /* Cho phép hình ảnh xuống dòng */
    gap: 15px; /* Khoảng cách giữa các hình ảnh */
    width: 100%;
}

.image-grid.single-result .image-item {
    flex: 1 1 100%; /* Chiếm toàn bộ chiều rộng khi chỉ có một kết quả */
    max-width: 100%; /* Đảm bảo không vượt quá 100% chiều rộng */
}

.image-item {
    position: relative;
    flex: 1 1 calc(25% - 15px); /* Mỗi ảnh chiếm 25% chiều rộng, trừ khoảng cách */
    max-width: calc(25% - 15px); /* Đảm bảo không vượt quá 25% chiều rộng */
}

.search-image {
    width: 100%;
    height: 250px; /* Điều chỉnh chiều cao */
    object-fit: cover; /* Đảm bảo hình ảnh phủ đầy thẻ */
    border-radius: 8px; /* Bo góc */
}
.icon-heart2 .fa-heart.liked {
    color: #ff5a5f; /* Màu khi đã like */
}
.work-info {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 25%;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.6));
    padding: 10px;
    border-radius: 0 0 8px 8px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px); /* Dịch xuống ban đầu */
    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out, transform 0.3s ease-in-out;
}

/* Khi hover vào image-item, hiện gradient và di chuyển lên */
.image-item:hover .work-info {
    opacity: 1;
    visibility: visible;
    transform: translateY(0); /* Di chuyển lên */
}

.user-info2 {
    display: flex;
    align-items: center;
    flex-direction: row;
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
    color: #fff; /* Icon màu trắng */
}

.icon-heart2:hover {
    cursor: pointer;
}

.icon-heart2 .fa-heart {
    color: #fff; /* Màu trắng ban đầu */
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
