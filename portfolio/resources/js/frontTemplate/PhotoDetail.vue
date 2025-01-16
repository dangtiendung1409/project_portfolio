<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="site-section">
                <div>
                    <div class="row">
                        <!-- Main Image Section -->
                        <div class="col-md-9" data-aos="fade-up">
                            <!-- Ảnh chính -->
                            <div class="photo-container"
                                 @mouseenter="isHovered = true"
                                 @mouseleave="isHovered = false"
                                 @click="openFullScreen">
                                <img :src="photoDetail.image_url" alt="Image" class="img-fluid photo-img" />
                                <div class="zoom-icon" v-if="isHovered" @click.stop="openFullScreen">
                                    <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                                </div>
                            </div>

                            <div class="similar-photos-section">
                                <div class="top-gallery-header d-flex justify-content-between align-items-center">
                                <h4 class="section-title">Similar photos</h4>
                                <a href="#" class="view-all-details">View All</a>
                                </div>
                                <div class="similar-photos-grid">
                                    <div class="photo-card">
                                        <img src="/front_assets/img/img_1.jpg" alt="Similar Photo" class="photo-thumbnail" />
                                    </div>
                                    <div class="photo-card">
                                        <img src="/front_assets/img/img_1.jpg" alt="Similar Photo" class="photo-thumbnail" />
                                    </div>
                                    <div class="photo-card">
                                        <img src="/front_assets/img/img_1.jpg" alt="Similar Photo" class="photo-thumbnail" />
                                    </div>
                                    <div class="photo-card">
                                        <img src="/front_assets/img/img_1.jpg" alt="Similar Photo" class="photo-thumbnail" />
                                    </div>
                                    <div class="photo-card">
                                        <img src="/front_assets/img/img_1.jpg" alt="Similar Photo" class="photo-thumbnail" />
                                    </div>
                                </div>
                            </div>

                            <!-- Featured in Galleries Section -->
                            <div class="featured-galleries-section">
                                <div class="featured-galleries mb-4">
                                    <div class="top-gallery-header d-flex justify-content-between align-items-center">
                                        <h4 class="section-title">Featured in these galleries</h4>
                                        <a href="#" class="view-all-details">View All</a>
                                    </div>
                                    <div class="galleries-grid">
                                        <div class="gallery-card" style="margin-left: 15px">
                                            <div class="gallery-info">
                                                <h4>Insan</h4>
                                                <div class="image-count">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z" fill="white"></path><path d="M12.4003 5.33337C13.3337 5.33337 14.1337 4.53337 14.1337 3.60003C14.1337 2.6667 13.3337 1.8667 12.4003 1.8667C11.467 1.8667 10.667 2.6667 10.667 3.60003C10.667 4.53337 11.467 5.33337 12.4003 5.33337ZM12.4003 2.80003C12.8003 2.80003 13.2003 3.13337 13.2003 3.60003C13.2003 4.0667 12.867 4.40003 12.4003 4.40003C12.0003 4.40003 11.6003 4.0667 11.6003 3.60003C11.6003 3.13337 11.9337 2.80003 12.4003 2.80003Z" fill="white"></path>
                                                    </svg>
                                                    <span>27</span>
                                                </div>
                                            </div>
                                            <div class="gallery-images">
                                                <img src="/front_assets/img/img_1.jpg" alt="Gallery 1 Image 1">
                                                <img src="/front_assets/img/img_2.jpg" alt="Gallery 1 Image 2">
                                                <img src="/front_assets/img/img_3.jpg" alt="Gallery 1 Image 3">
                                                <img src="/front_assets/img/img_4.jpg" alt="Gallery 1 Image 4">
                                            </div>
                                            <div class="gallery-footer">
                                                <img class="user-avatar" src="/front_assets/img/user1.jpeg" alt="User Avatar">
                                                <h4>Dung</h4>
                                                <div class="footer-buttons">
                                                    <button class="btn-favorite"><i class="fas fa-heart"></i></button>
                                                    <button class="btn-options"><i class="fa-solid fa-ellipsis"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gallery-card">
                                            <div class="gallery-info">
                                                <h4>Insan</h4>
                                                <div class="image-count">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z" fill="white"></path><path d="M12.4003 5.33337C13.3337 5.33337 14.1337 4.53337 14.1337 3.60003C14.1337 2.6667 13.3337 1.8667 12.4003 1.8667C11.467 1.8667 10.667 2.6667 10.667 3.60003C10.667 4.53337 11.467 5.33337 12.4003 5.33337ZM12.4003 2.80003C12.8003 2.80003 13.2003 3.13337 13.2003 3.60003C13.2003 4.0667 12.867 4.40003 12.4003 4.40003C12.0003 4.40003 11.6003 4.0667 11.6003 3.60003C11.6003 3.13337 11.9337 2.80003 12.4003 2.80003Z" fill="white"></path>
                                                    </svg>
                                                    <span>27</span>
                                                </div>
                                            </div>
                                            <div class="gallery-images">
                                                <img src="/front_assets/img/img_1.jpg" alt="Gallery 1 Image 1">
                                                <img src="/front_assets/img/img_2.jpg" alt="Gallery 1 Image 2">
                                                <img src="/front_assets/img/img_3.jpg" alt="Gallery 1 Image 3">
                                                <img src="/front_assets/img/img_4.jpg" alt="Gallery 1 Image 4">
                                            </div>
                                            <div class="gallery-footer">
                                                <img class="user-avatar" src="/front_assets/img/user1.jpeg" alt="User Avatar">
                                                <h4>Dung</h4>
                                                <div class="footer-buttons">
                                                    <button class="btn-favorite"><i class="fas fa-heart"></i></button>
                                                    <button class="btn-options"><i class="fa-solid fa-ellipsis"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gallery-card">
                                            <div class="gallery-info">
                                                <h4>Insan</h4>
                                                <div class="image-count">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z" fill="white"></path><path d="M12.4003 5.33337C13.3337 5.33337 14.1337 4.53337 14.1337 3.60003C14.1337 2.6667 13.3337 1.8667 12.4003 1.8667C11.467 1.8667 10.667 2.6667 10.667 3.60003C10.667 4.53337 11.467 5.33337 12.4003 5.33337ZM12.4003 2.80003C12.8003 2.80003 13.2003 3.13337 13.2003 3.60003C13.2003 4.0667 12.867 4.40003 12.4003 4.40003C12.0003 4.40003 11.6003 4.0667 11.6003 3.60003C11.6003 3.13337 11.9337 2.80003 12.4003 2.80003Z" fill="white"></path>
                                                    </svg>
                                                    <span>27</span>
                                                </div>
                                            </div>
                                            <div class="gallery-images">
                                                <img src="/front_assets/img/img_1.jpg" alt="Gallery 1 Image 1">
                                                <img src="/front_assets/img/img_2.jpg" alt="Gallery 1 Image 2">
                                                <img src="/front_assets/img/img_3.jpg" alt="Gallery 1 Image 3">
                                                <img src="/front_assets/img/img_4.jpg" alt="Gallery 1 Image 4">
                                            </div>
                                            <div class="gallery-footer">
                                                <img class="user-avatar" src="/front_assets/img/user1.jpeg" alt="User Avatar">
                                                <h4>Dung</h4>
                                                <div class="footer-buttons">
                                                    <button class="btn-favorite"><i class="fas fa-heart"></i></button>
                                                    <button class="btn-options"><i class="fa-solid fa-ellipsis"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Info Section on the Right -->
                        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                            <div class="info-container">
                                <!-- Header with Icons in a Separate Div -->
                                <div class="icon-wrapper">
                                    <button class="btn icon-btn" @click="toggleLike(photoDetail)">
                                        <i :class="['fa-heart', photoDetail.liked ? 'fas liked' : 'far']"></i>
                                    </button>
                                    <button class="btn icon-btn">
                                        <i class="fa-solid fa-share-nodes"></i>
                                    </button>
                                    <button class="btn icon-btn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button class="btn icon-btn">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>
                                </div>
                                <!-- Details Section -->
                                <div class="details-wrapper">
                                    <h3 class="h3">{{ photoDetail.photo.title }}</h3>
                                    <p class="text-muted">{{ photoDetail.photo.description }}</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fa-solid fa-location-dot"></i> {{ photoDetail.photo.location }}</li>
                                        <li><i class="fa-solid fa-calendar-days"></i>  {{ formatDate(photoDetail.photo.upload_date) }}</li>
                                        <li>
                                            <i class="fa-regular fa-heart"></i>
                                            400 <span>Likes</span>
                                            <span class="arrow">&gt;</span>
                                        </li>
                                        <li><i class="fa-regular fa-eye"></i> 27.5K <span>Impressions</span> </li>
                                        <li>
                                            <i class="fa-solid fa-arrow-up"></i> {{ getTimeAgo(photoDetail.photo.upload_date) }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="user-profile-wrapper">
                                    <img :src="photoDetail.photo.user.profile_picture ? 'http://127.0.0.1:8000/' +
                                    photoDetail.photo.user.profile_picture : '/images/imageUserDefault.png'"
                                         alt="User Avatar" class="user-avatar-details" />
                                    <div class="user-info-follow">
                                        <h3 class="user-name">{{ photoDetail.photo.user.username }}</h3>
                                        <p class="user-bio">{{ photoDetail.photo.user.bio }}</p>
                                        <button class="btn-follow">Follow</button>
                                    </div>
                                </div>
                                <div class="comments-section">
                                    <div class="comment-input-wrapper">
                                        <img src="/front_assets/img/img_1.jpg" alt="User Avatar" class="comment-avatar-auth" />
                                        <input type="text" class="comment-input" placeholder="Write your comment here" />
                                    </div>
                                    <h5 class="comments-header">20 Comments</h5>

                                    <div class="comment">
                                        <img src="/front_assets/img/img_1.jpg" alt="User Avatar" class="comment-avatar" />
                                        <div class="comment-content">
                                            <div class="comment-header">
                                                <span class="comment-author">06. Đặng tiến dũng dung</span>
                                                <i class="fa-solid fa-ellipsis comment-options"></i>
                                            </div>
                                            <p class="comment-text">so magical</p>
                                            <div class="comment-footer">
                                                <span class="comment-time">1 second</span>
                                                <span class="comment-reply">Reply</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="comment">
                                        <img src="/front_assets/img/img_1.jpg" alt="User Avatar" class="comment-avatar" />
                                        <div class="comment-content">
                                            <div class="comment-header">
                                                <span class="comment-author">christian maurel</span>
                                                <i class="fa-solid fa-ellipsis comment-options"></i>
                                            </div>
                                            <p class="comment-text">Awesome landscape 💯💯💯 beautiful colors ✨💥✨</p>
                                            <div class="comment-footer">
                                                <span class="comment-time">4 days</span>
                                                <span class="comment-reply">Reply</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Categories Section -->
                                <div class="categories-section">
                                    <h5 class="categories-header">
                                        <span>Category:</span>
                                        <strong>{{ photoDetail.photo.category.category_name }}</strong>
                                    </h5>
                                    <div class="categories-wrapper">
                                        <span class="category-tag">Horizontal</span>
                                        <span class="category-tag">Photography</span>
                                        <span class="category-tag">Outdoors</span>
                                        <span class="category-tag">Sea</span>
                                        <span class="category-tag">Rock - Object</span>
                                        <span class="category-tag">Nature</span>
                                        <span class="category-tag">Sky</span>
                                        <span class="category-tag">Beauty In Nature</span>
                                        <span class="category-tag">Scenics - Nature</span>
                                        <span class="category-tag">Water</span>
                                        <span class="category-tag">Beach</span>
                                        <span class="category-tag">One Person</span>
                                        <span class="category-tag">Coastline</span>
                                        <span class="category-tag">Sunset</span>
                                        <span class="category-tag">Tranquil Scene</span>
                                        <span class="category-tag">Utakleiv beach</span>
                                        <span class="category-tag">Lofoten</span>
                                        <span class="category-tag">Norway</span>
                                        <span class="category-tag">Nordic</span>
                                        <span class="category-tag">Canon</span>
                                        <span class="category-tag">travel</span>
                                        <span class="category-tag">landscapes</span>
                                        <span class="category-tag">sunset</span>
                                        <span class="category-tag">rocks</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Layout>
</template>
<script>
import axios from "axios";
import Layout from "./Layout.vue";
import getUrlList from "../provider.js";
import { useLikeStore } from '@/stores/likeStore';

export default {
    name: "PhotoDetail",
    components: {
        Layout,
    },
    data() {
        return {
            isHovered: false,
            photoDetail: {
                photo: {
                    title: "",
                    description: "",
                    location: "",
                    upload_date:"",
                    user: {
                        username: "",
                        profile_picture: "",
                        bio: "",
                    },
                    category: {
                        category_name: "",
                    }
                },
                image_url: "",
                liked: false // trạng thái liked của ảnh chi tiết
            },
        };
    },
    watch: {
        '$route.params.token': {
            immediate: true,
            handler(newToken) {
                this.fetchPhotoDetail(newToken);
            },
        },
    },
    methods: {
        async fetchPhotoDetail(token) {
            try {
                const response = await axios.get(`${getUrlList().getPhotoDetail}/${token}`);
                this.photoDetail = response.data.data;
                this.updateLikedState(); // Cập nhật trạng thái liked sau khi lấy chi tiết ảnh
            } catch (error) {
                console.error("Error fetching photo details:", error);
            }
        },
        openFullScreen() {
            const img = document.createElement('img');
            img.src = this.photoDetail.image_url;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'contain';

            const overlay = document.createElement('div');
            overlay.className = 'full-screen-overlay';

            // Tạo icon "X"
            const closeIcon = document.createElement('div');
            closeIcon.className = 'close-icon';
            closeIcon.innerHTML = '<i class="fa-solid fa-xmark"></i>'; // Sử dụng icon Font Awesome cho "X"

            // Thêm sự kiện click để đóng overlay
            closeIcon.onclick = () => document.body.removeChild(overlay);

            // Thêm ảnh và icon "X" vào overlay
            overlay.appendChild(img);
            overlay.appendChild(closeIcon);
            document.body.appendChild(overlay);
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        },
        getTimeAgo(uploadDate) {
            const now = new Date();
            const uploadTime = new Date(uploadDate);
            const diffInMs = now - uploadTime; // Độ chênh lệch thời gian (milliseconds)
            const diffInSeconds = Math.floor(diffInMs / 1000);

            if (diffInSeconds < 60) {
                return `${diffInSeconds} seconds ago`;
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                return `${hours} hour${hours > 1 ? 's' : ''} ago`;
            } else {
                const days = Math.floor(diffInSeconds / 86400);
                return `${days} day${days > 1 ? 's' : ''} ago`;
            }
        },
        async toggleLike(item) {
            const photo_image_id = item.photo.id; // ID của ảnh
            const photo_user_id = item.photo.user.id; // ID của người sở hữu ảnh
            const likeStore = useLikeStore();

            try {
                if (item.liked) {
                    await likeStore.unlikePhoto(photo_image_id);
                } else {
                    await likeStore.likePhoto(photo_image_id, photo_user_id); // Gửi thêm photo_user_id
                }
                item.liked = !item.liked; // Đảo ngược trạng thái liked
            } catch (error) {
                console.error('Failed to toggle like:', error);
            }
        },
        updateLikedState() {
            const likeStore = useLikeStore();
            this.photoDetail.liked = likeStore.likedPhotos.includes(this.photoDetail.photo.id); // Cập nhật trạng thái liked của ảnh chi tiết
        },
    },
    async mounted() {
        const likeStore = useLikeStore();
        await likeStore.fetchLikedPhotos(); // Lấy danh sách các ảnh đã được like từ store
        this.updateLikedState();
    },
};
</script>
<style src="../public/front_assets/css/details.css"></style>
<style scoped>
.icon-btn {
    background: none;
    cursor: pointer;
    font-size: 20px; /* Icon size */
}
.icon-btn .fa-heart.liked {
    color: #ff5a5f; /* Màu khi đã like */
}
</style>
