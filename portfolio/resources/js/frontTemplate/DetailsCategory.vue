<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="details-category">

                <!-- Breadcrumb Navigation -->
                <nav class="breadcrumb">
                    <router-link to="/categories" class="breadcrumb-back"><i class="fas fa-arrow-left"></i> <strong>Back</strong></router-link>
                    <router-link to="/categories" class="breadcrumb-link">Explore Categories</router-link>
                    <span class="breadcrumb-separator">›</span>
                    <span class="breadcrumb-current">Category Details</span>
                </nav>

                <h1 class="category-title">Popular Photos</h1>

                <div class="filter-sort">
                    <div class="filter-dropdown">
                        <span @click="toggleFilterDropdown">
                            <strong>Filter ({{ selectedFilters.length }})</strong>
                            <i :class="['fas', showFilterDropdown ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                        </span>
                        <div v-if="showFilterDropdown" class="dropdown-menu">
                            <h3 class="dropdown-header">Category</h3>
                            <ul class="filter-list">
                                <li v-for="filter in filters" :key="filter.id" @click="toggleFilter(filter)">
                                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                        <span style="font-weight: normal">{{ filter.category_name }}</span>
                                        <input type="checkbox" :checked="selectedFilters.includes(filter)" />
                                    </div>
                                </li>
                            </ul>
                            <div class="dropdown-footer">
                                <button
                                    class="clear-btn"
                                    @click="clearFilters"
                                    :style="{ color: selectedFilters.length === 0 ? 'rgb(182, 185, 187)' : '#1890ff' }"
                                    :disabled="selectedFilters.length === 0"
                                >
                                    Clear ({{ selectedFilters.length }})
                                </button>
                                <button class="done-btn" @click="applyFilters">Done</button>
                            </div>
                        </div>
                    </div>

                    <div class="sort-dropdown">
                        <span @click="toggleSortDropdown">
                            <strong>Sort ({{ selectedSort }})</strong>
                            <i :class="['fas', showSortDropdown ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                        </span>
                        <div v-if="showSortDropdown" class="sort-dropdown-menu">
                            <ul>
                                <li @click="selectSort({ id: 1, name: 'Pulse' })">
                                    <i class="fas fa-sort-amount-up"></i> Pulse
                                </li>
                                <li @click="selectSort({ id: 2, name: 'Latest' })">
                                    <i class="fas fa-clock"></i> Latest
                                </li>
                                <li @click="selectSort({ id: 3, name: 'Popular' })">
                                    <i class="fas fa-star"></i> Popular
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div v-if="images.length === 0" class="no-data">
                    <p class="no-data-text">No photos available for the selected categories.</p>
                </div>

                <div v-else class="image-grid">
                    <div class="image-item" v-for="(image, index) in images" :key="index">
                        <router-link :to="{ name: 'PhotoDetail', params: { token: image.photo_token } }">
                            <img :src="image.image_url" alt="Category Image" class="category-image" />
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
                                    <i :class="['fas', 'fa-ellipsis-h', { 'active': activeDropdown === 'dotsDropdown-' + image.id }]" @click.stop="toggleDropdown('dotsDropdown-' + image.id)"></i>
                                </span>
                            </div>
                        </div>
                        <div v-if="activeDropdown === 'dotsDropdown-' + image.id" class="dropdown-content show" style="right: 25px">
                            <ul>
                                <li @click="handleClick('addToGallery', image.id)"><i class="fa-regular fa-square-plus"></i> Add to Gallery</li>
                                <li @click="handleClick('blockUser', image.id)"><i class="fas fa-user-slash"></i> Block User</li>
                                <li @click="handleClick('followUser', image.id)"><i class="fas fa-user-plus"></i> Follow User</li>
                                <li @click="handleClick('reportPhoto', image.id)"><i class="fas fa-flag"></i> Report This Photo</li>
                            </ul>
                        </div>
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
import axios from 'axios';
import getUrlList from '../provider.js';
import AddToGalleryModal from "./components/AddToGalleryModal.vue";
import { useLikeStore } from '@/stores/likeStore';
import { useAuthStore } from '@/stores/authStore';

export default {
    name: "DetailsCategory",
    components: {
        Layout,
        AddToGalleryModal,
    },
    data() {
        return {
            showFilterDropdown: false,
            showSortDropdown: false,
            selectedFilters: [],
            selectedSort: "Pulse",
            filters: [],
            activeDropdown: null,
            images: [],
            showAddToGallery: false,
            selectedPhotoId: null,
        };
    },
    async created() {
        await this.fetchCategories();
        await this.fetchPhotos();
        const likeStore = useLikeStore();
        await likeStore.fetchLikedPhotos(); // Lấy danh sách các ảnh đã được like từ store
        this.updateLikedState();
    },
    methods: {
        async fetchCategories() {
            const urlList = getUrlList();
            try {
                const response = await axios.get(urlList.getCategories);
                this.filters = response.data;
            } catch (error) {
                console.error('Failed to fetch categories:', error);
            }
        },
        async fetchPhotos() {
            const { slugs } = this.$route.query;
            if (!slugs) {
                console.error('No slugs provided');
                this.images = [];
                return;
            }
            const urlList = getUrlList();
            try {
                const response = await axios.get(urlList.getPhotosByCategorySlugs(slugs));
                this.images = response.data;
            } catch (error) {
                console.error('Failed to fetch photos:', error);
                this.images = [];
            }
        },
        getProfilePicture(profilePicture) {
            const baseUrl = 'http://127.0.0.1:8000/images/avatars/';
            if (profilePicture.startsWith('http')) {
                return profilePicture;
            }
            return baseUrl + profilePicture.split('/').pop();
        },
        toggleFilterDropdown() {
            this.showFilterDropdown = !this.showFilterDropdown;
            if (this.showFilterDropdown) {
                this.showSortDropdown = false; // Đóng sort dropdown nếu filter dropdown mở
            }
        },
        toggleSortDropdown() {
            this.showSortDropdown = !this.showSortDropdown;
            if (this.showSortDropdown) {
                this.showFilterDropdown = false; // Đóng filter dropdown nếu sort dropdown mở
            }
        },
        toggleFilter(filter) {
            if (this.selectedFilters.includes(filter)) {
                this.selectedFilters = this.selectedFilters.filter(f => f.id !== filter.id);
            } else {
                this.selectedFilters.push(filter);
            }
        },
        applyFilters() {
            const slugs = this.selectedFilters.map(filter => filter.slug).join(',');
            this.$router.push({ name: 'DetailsCategory', query: { slugs } }).then(() => {
                this.fetchPhotos();
            });
            this.showFilterDropdown = false;
        },
        selectSort(sort) {
            this.selectedSort = sort.name;
            this.showSortDropdown = false;
        },
        toggleDropdown(id) {
            if (this.activeDropdown === id) {
                this.activeDropdown = null;
            } else {
                this.activeDropdown = id;
            }
            console.log("Active Dropdown:", this.activeDropdown);
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
        updateLikedState() {
            const likeStore = useLikeStore();
            this.images.forEach(image => {
                image.liked = likeStore.likedPhotos.includes(image.id); // Cập nhật trạng thái liked của từng ảnh
            });
        },
        openAddToGalleryModal(photoId) {
            this.selectedPhotoId = photoId;
            this.showAddToGallery = true; // Mở modal
        },
        closeAddToGalleryModal() {
            this.showAddToGallery = false; // Đóng modal
        },
        clearFilters() {
            this.selectedFilters = [];
        },
    },
    watch: {
        '$route.query.slugs': {
            handler() {
                this.fetchPhotos();
            },
            immediate: true
        },
        images: {
            handler() {
                this.updateLikedState();
            },
            deep: true,
            immediate: true
        }
    }
};
</script>
<style scoped>
.details-category {
    padding: 20px;
    text-align: center;
}
.no-data {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background-color: #f9f9f9; /* Màu nền nhẹ */
    border: 1px solid #ddd; /* Đường viền nhẹ */
    border-radius: 8px; /* Bo góc */
    margin: 20px 0; /* Khoảng cách trên và dưới */
    color: #555; /* Màu chữ */
    text-align: center;
}

.no-data-text {
    font-size: 18px; /* Kích thước chữ cho văn bản */
    font-weight: 500; /* Độ dày chữ */
}
.breadcrumb {
    margin-left: -15px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    margin-bottom: 15px;
    color: #666;
    background: none !important;
}

.breadcrumb-back {
    font-weight: bold;
    color: black;
    text-decoration: none;
}

.breadcrumb-link {
    color: #999;
    text-decoration: none;
    font-weight: 600;
}

.breadcrumb-link:hover {
    color: black;
}

.breadcrumb-separator {
    color: #ccc;
}

.breadcrumb-current {
    font-weight: bold;
    color: black;
}

.filter-sort {
    display: flex;
    justify-content: flex-start;
    gap: 20px;
    margin-bottom: 20px;
}

.filter-dropdown, .sort-dropdown {
    position: relative;
    cursor: pointer;
}

.filter-dropdown span, .sort-dropdown span {
    font-size: 16px;
    font-weight: bold;
    color: black;
}

.dropdown-menu {
    display: block !important;
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    width: 350px; /* Adjust width */
    z-index: 9999;
    max-height: 300px; /* Set a max height for scrolling */
    overflow-y: auto; /* Enable vertical scrolling */
}

.dropdown-header {
    padding: 10px 15px;
    font-size: 16px;
    font-weight: bold;
    color:black;
    border-bottom: 1px solid #eee; /* Optional: add a border below the header */
}

.filter-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.dropdown-menu li {
    padding: 10px 15px; /* Adjust padding */
    cursor: pointer;
    transition: background 0.3s;
}

.dropdown-menu li:hover {
    background: #f0f0f0;
}

.dropdown-footer {
    position: sticky;
    bottom: -10px;
    background: white;
    padding: 10px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    z-index: 10;
}


.clear-btn, .done-btn {
    background-color: #1890ff; /* Primary color */
    color: white;
    border: none;
    border-radius: 5px;
    padding: 8px 12px;
    cursor: pointer;
    transition: background 0.3s;
}

.done-btn:hover {
    background-color: #1677ff; /* Darker shade on hover */
}

.clear-btn {
    background-color: transparent; /* Không có nền */
    color: rgb(182, 185, 187); /* Màu chữ mặc định */
    border: none; /* Không có viền */
    cursor: pointer; /* Con trỏ khi hover */
}

.clear-btn:disabled {
    color: rgb(182, 185, 187); /* Màu chữ khi không có bộ lọc nào được chọn */
    cursor: not-allowed; /* Hiển thị con trỏ không được phép */
}
.sort-dropdown {
    position: relative; /* Để dropdown nằm đúng vị trí */
}

.sort-dropdown-menu {
    list-style-type: none; /* Loại bỏ dấu chấm */
    margin: 0; /* Đặt margin thành 0 */
    padding: 0; /* Đặt padding thành 0 */
    position: absolute; /* Để dropdown không ảnh hưởng đến layout */
    top: 100%; /* Đặt vị trí dropdown ngay dưới phần tử cha */
    left: 0; /* Đảm bảo dropdown không có khoảng cách bên trái */
    background-color: white; /* Màu nền cho dropdown */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Hiệu ứng bóng */
    z-index: 9999; /* Đảm bảo dropdown nằm trên các phần tử khác */
}

.sort-dropdown-menu ul {
    margin: 0; /* Đặt margin cho ul thành 0 */
    padding: 0; /* Đặt padding cho ul thành 0 */
}

.sort-dropdown-menu li {
    padding: 10px; /* Thêm padding cho từng item */
    cursor: pointer; /* Thay đổi con trỏ khi hover */
}

.sort-dropdown-menu li:hover {
    color:white;
    background-color: #2986f7; /* Hiệu ứng hover */
}
.filter-dropdown i,
.sort-dropdown i {
    margin-left: 5px; /* Điều chỉnh khoảng cách */
}

.image-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Tạo grid cho hình ảnh */
    gap: 15px;
}
.image-item {
    position: relative;
}
.category-image {
    width: 100%;
    height: 200px;
    object-fit: cover; /* Đảm bảo hình ảnh phủ đầy thẻ */
    border-radius: 12px;
}
.category-image:hover {
    border-radius: 0;
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
