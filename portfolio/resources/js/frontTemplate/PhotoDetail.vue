<template>
    <Layout>
        <template v-slot:content="slotProps">
            <!-- Nếu user của ảnh bị block, hiển thị placeholder -->
                <div v-if="isPhotoUserBlocked" class="blocked-content">
                    <i class="fa-solid fa-circle-xmark blocked-icon"></i>
                    <h2>Something went wrong</h2>
                    <p>Please refresh the page to try again.</p>
                </div>
            <div v-else class="site-section">
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
                                </div>
                                <div class="similar-photos-grid">
                                    <div v-for="photo in similarPhotos" :key="photo.id" class="photo-card">
                                        <router-link :to="{ name: 'PhotoDetail', params: { token: photo.photo_token } }">
                                        <img :src="photo.image_url" alt="Similar Photo" class="photo-thumbnail" />
                                        </router-link>
                                        <div class="photo-info">
                                            <div class="user-info2">
                                                <router-link :to="{ name: 'MyProfile', params: { username: photo.user.username } }">
                                                    <img class="user-image2" :src="photo.user.profile_picture" style="width: 30px; height: 30px">
                                                </router-link>
                                                <span class="user-name2">{{ photo.user.name }}</span>
                                                <span class="icon-heart2" @click="handleClick('toggleLike', photo)">
                                                <i :class="['fas', 'fa-heart', { 'liked': photo.liked }]"></i>
                                                 </span>
                                                <span class="icon-dots2">
                                             <i @click="handleClick('addToGallery', photo.id)" class="fa-regular fa-square-plus"></i>
                                             </span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Featured in Galleries Section -->
                            <div class="featured-galleries-section" v-if="relatedGalleries.length > 0">
                                <div class="featured-galleries mb-4">
                                    <div class="top-gallery-header d-flex justify-content-between align-items-center">
                                        <h4 style="margin-left: 20px" class="section-title">Featured in these galleries</h4>
                                    </div>
                                    <div class="galleries-grid">
                                        <div v-for="gallery in relatedGalleries" :key="gallery.id" class="gallery-card" @click="goToGalleryDetails(gallery.galleries_code)" style="margin-left: 15px">
                                            <div class="gallery-info">
                                                <h4>{{ gallery.galleries_name }}</h4>
                                                <div class="image-count">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z" fill="white"></path>
                                                        <path d="M12.4003 5.33337C13.3337 5.33337 14.1337 4.53337 14.1337 3.60003C14.1337 2.6667 13.3337 1.8667 12.4003 1.8667C11.467 1.8667 10.667 2.6667 10.667 3.60003C10.667 4.53337 11.467 5.33337 12.4003 5.33337ZM12.4003 2.80003C12.8003 2.80003 13.2003 3.13337 13.2003 3.60003C13.2003 4.0667 12.867 4.40003 12.4003 4.40003C12.0003 4.40003 11.6003 4.0667 11.6003 3.60003C11.6003 3.13337 11.9337 2.80003 12.4003 2.80003Z" fill="white"></path>
                                                    </svg>
                                                    <span>{{ gallery.photos.length }}</span>
                                                </div>
                                            </div>
                                            <div class="gallery-images">
                                                <img v-for="(photo, index) in gallery.photos.slice(0, 4)" :key="photo.id" :src="photo.image_url" :alt="`Gallery ${gallery.id} Image ${index + 1}`">
                                            </div>
                                            <div class="gallery-footer">
                                                <router-link :to="{ name: 'MyProfile', params: { username: gallery.user.username } }" @click.stop>
                                                    <img class="user-avatar" :src="gallery.user?.profile_picture || '/front_assets/img/user1.jpeg'" alt="User Avatar">
                                                </router-link>
                                                <h4>{{ gallery.user?.name || 'Unknown' }}</h4>
                                                <div class="footer-buttons">
                                                    <button class="btn-favorite" @click.stop="toggleLikeGallery(gallery)">
                                                        <i :class="['fa-heart', gallery.liked ? 'fas liked' : 'far']"></i>
                                                    </button>
                                                    <button class="btn-options" @click.stop="openReportGalleryModal(gallery.id, gallery.user.id)">
                                                        <i class="fa-regular fa-flag"></i>
                                                    </button>
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
                                    <button class="btn icon-btn" @click="handleClick('toggleLike', photoDetail)">
                                        <i :class="['fa-heart', photoDetail.liked ? 'fas liked' : 'far']"></i>
                                    </button>
                                    <button class="btn icon-btn" @click="copyUrlToClipboard">
                                        <i class="fa-solid fa-share-nodes"></i>
                                    </button>
                                    <button class="btn icon-btn" @click="handleClick('addToGallery', photoDetail.id)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button v-if="photoDetail.user.id !== userStore.user.id" class="btn icon-btn" @click.stop="toggleDropdown('dropdown-' + photoDetail.id, $event)"
                                            :class="{'active': activeDropdown === 'dropdown-' + photoDetail.id}">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>
                                    <div style="margin-top: 60px; margin-left: 40px" v-if="activeDropdown === 'dropdown-' + photoDetail.id" class="dropdown-content show" @click.stop>
                                        <ul>
                                            <li @click="handleClick('reportPhoto', photoDetail.id, photoDetail.user.id)">
                                                <i class="fa-regular fa-flag"></i> Report this photo
                                            </li>
                                            <li @click="toggleBlockUser(photoDetail.user)">
                                                <i class="fas fa-user-slash"></i> Block User
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Details Section -->
                                <div class="details-wrapper">
                                    <h3 class="h3">{{ photoDetail.title }}</h3>
                                    <p class="text-muted">{{ photoDetail.description }}</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fa-solid fa-location-dot"></i> {{ photoDetail.location }}</li>
                                        <li><i class="fa-solid fa-calendar-days"></i>  {{ formatDate(photoDetail.upload_date) }}</li>
                                        <li @click="showLikesPopup">
                                            <i class="fa-regular fa-heart"></i>
                                            {{ formattedPhotoLikes }} <span>Likes</span>
                                            <span class="arrow">&gt;</span>
                                        </li>
                                        <li><i class="fa-regular fa-eye"></i> {{ formattedViews }} <span>Impressions</span></li>
                                        <li>
                                            <i class="fa-solid fa-arrow-up"></i> {{ getTimeAgo(photoDetail.upload_date) }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="user-profile-wrapper">
                                    <router-link :to="{ name: 'MyProfile', params: { username: photoDetail.user.username || 'defaultUsername' } }">
                                    <img :src="photoDetail.user.profile_picture ? 'http://127.0.0.1:8000' +
                                    photoDetail.user.profile_picture : '/images/imageUserDefault.png'"
                                         alt="User Avatar" class="user-avatar-details" />
                                    </router-link>
                                    <div class="user-info-follow">
                                        <h3 class="user-name">{{ photoDetail.user.username }}</h3>
                                        <p class="user-bio">{{ photoDetail.user.location }}</p>

                                        <!-- Kiểm tra nếu không phải chính mình thì mới hiển thị nút -->
                                        <button v-if="photoDetail.user.id !== userStore.user?.id"
                                                class="btn-follow"
                                                :class="{ 'following': isFollowing }"
                                                @click="toggleFollow">
                                            {{ isFollowing ? 'Unfollow' : 'Follow' }}
                                        </button>
                                    </div>
                                </div>
                                <!-- Chỉ sửa phần comment section trong template -->
                                <div class="comments-section">
                                    <div class="comment-input-wrapper">
                                        <img :src="userStore.user.profile_picture ? 'http://127.0.0.1:8000' + userStore.user.profile_picture : '/images/imageUserDefault.png'"
                                             alt="User Avatar"
                                             class="comment-avatar-auth" />

                                        <input
                                            type="text"
                                            class="comment-input"
                                            placeholder="Write your comment here"
                                            v-model="newComment"
                                            @focus="showButtons = true"
                                            @keydown.enter.prevent
                                        />

                                        <div v-if="showButtons" class="comment-buttons">
                                            <button class="cancel-btn" @click="cancelComment">Cancel</button>
                                            <button class="post-btn" :disabled="!newComment.trim()" @click="postComment">Post</button>
                                        </div>
                                    </div>

                                    <!-- Kiểm tra nếu không có comment -->
                                    <div v-if="comments.length === 0" class="no-comment">
                                        <i class="fa-regular fa-comment comment-icon"></i>
                                        <h3>No comment yet</h3>
                                        <p>There is no comment for this artwork yet. Make your first comment here!</p>
                                    </div>

                                    <!-- Hiển thị comment nếu có -->
                                    <div v-else>
                                        <h5 class="comments-header">{{ comments.length }} Comments</h5>
                                        <div v-for="(comment, index) in displayedComments" :key="comment.id" class="comment">
                                            <img :src="comment?.user?.profile_picture ? 'http://127.0.0.1:8000' + comment.user.profile_picture : '/images/imageUserDefault.png'"
                                                 alt="User Avatar"
                                                 class="comment-avatar" />
                                            <div class="comment-content">
                                                <div class="comment-header">
                                                    <span class="comment-author">{{ comment?.user?.name || 'Unknown User' }}</span>
                                                    <i class="fa-solid fa-ellipsis comment-options"
                                                       @click.stop="toggleDropdown('dropdown-' + comment.id, $event)"
                                                       :class="{'active': activeDropdown === 'dropdown-' + comment.id}"></i>
                                                </div>
                                                <div v-if="activeDropdown === 'dropdown-' + comment.id" class="dropdown-content show" @click.stop>
                                                    <ul>
                                                        <li v-if="comment.user.id !== userStore.user.id" @click="handleClick('reportComment', comment.id, comment.user.id)">
                                                            <i class="fa-regular fa-flag"></i> Report
                                                        </li>
                                                        <li v-if="comment.user.id === userStore.user.id" @click="showDeleteConfirm(comment)">
                                                            <i class="fa-solid fa-trash-can"></i> Delete
                                                        </li>
                                                    </ul>
                                                </div>

                                                <p class="comment-text">{{ comment.comment_text }}</p>
                                                <div class="comment-footer">
                                                    <span class="comment-time">{{ getTimeAgo(comment.created_at) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button v-if="comments.length > 3" @click="toggleComments" class="read-more-button">
                                            {{ showAllComments ? "Read Less" : "Read More" }}
                                        </button>
                                    </div>
                                </div>
                                <!-- Categories Section -->
                                <div class="categories-section">
                                    <h5 class="categories-header">
                                        <span>Category:</span>
                                        <strong>{{ photoDetail.category.category_name }}</strong>
                                    </h5>
                                    <div class="categories-wrapper">
                                        <router-link v-for="category in categories" :key="category.id" :to="{ name: 'DetailsCategory', query: { slugs: category.slug } }" class="category-tag">
                                            {{ category.category_name }}
                                        </router-link>
                                    </div>
                                </div>
                            </div>
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
            <ReportCommentModal
                :is-visible="showReportCommentModal"
                :comment-id="selectedCommentId"
                :violator-id="selectedViolatorId"
                @close="closeReportCommentModal"
            />
            <ReportGalleryModal
                :is-visible="showReportGalleryModal"
                :gallery-id="selectedGalleryId"
                :violator-id="selectedViolatorId"
                @close="closeReportGalleryModal"
            />
            <div v-if="likesPopupVisible" class="popup-overlay" @click.self="closeLikesPopup">
                <div class="popup-content">
                    <div class="popup-header">
                        <h3>Liked by</h3>
                        <button @click="closeLikesPopup" class="popup-close">×</button>
                    </div>
                    <div v-if="likedUsers.length > 0" class="popup-list">
                        <div v-for="user in likedUsers" :key="user.id" class="popup-item">
                            <router-link :to="{ name: 'MyProfile', params: { username: user.username } }">
                                <img :src="user.profile_picture ? `http://127.0.0.1:8000/images/avatars/${user.profile_picture.split('/').pop()}` : '/images/imageUserDefault.png'" alt="Avatar" class="popup-avatar" />
                            </router-link>
                            <div class="popup-user-info">
                                <span class="popup-username">{{ user.username }}</span>
                                <span class="popup-followers">{{ user.followers_count || 0 }} Followers</span>
                            </div>
                            <button v-if="userStore.user && user.id !== userStore.user.id"
                                    @click.stop="toggleFollowUser(user.username)"
                                    class="popup-follow-button">
                                {{ followStore.followingList.includes(user.id) ? 'Unfollow' : 'Follow' }}
                            </button>
                        </div>
                    </div>
                    <div v-else class="popup-no-data">No likes found.</div>
                </div>
            </div>
        </template>
    </Layout>
</template>
<script>
import axios from "axios";
import Layout from "./Layout.vue";
import getUrlList from "../provider.js";
import { useFollowStore } from '@/stores/followStore';
import { useCommentStore } from '@/stores/commentStore';
import { useLikeStore } from '@/stores/likeStore';
import { useAuthStore } from '@/stores/authStore';
import { useUserStore } from '@/stores/userStore';
import { useBlockStore } from '@/stores/blockStore';
import AddToGalleryModal from './components/AddToGalleryModal.vue';
import ReportPhotoModal from "./components/ReportPhotoModal.vue";
import ReportCommentModal from "./components/ReportCommentModal.vue";
import ReportGalleryModal from "./components/ReportGalleryModal.vue";
import { storeToRefs } from 'pinia';
import { Modal,notification } from 'ant-design-vue';
import { h } from 'vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';

export default {
    name: "PhotoDetail",
    components: {
        Layout,
        AddToGalleryModal,
        ReportPhotoModal,
        ReportCommentModal,
        ReportGalleryModal
    },
    data() {
        return {
            isHovered: false,
            photoDetail: {
                title: "",
                description: "",
                location: "",
                upload_date:"",
                total_views:"",
                image_url: "",
                liked: false,
                user: {
                    username: "",
                    profile_picture: "",
                    bio: "",
                },
                category: {
                    category_name: "",
                },
            },
            newComment: '',
            isPosting: false, // Ngăn chặn spam comment
            showButtons: false,
            showAllComments: false,
            activeDropdown: null,
            categories: [],
            showAddToGallery: false,
            selectedPhotoId: null,
            isPhotoUserBlocked: false,
            photoLikesCount: 0,
            likesPopupVisible: false,
            likedUsers: [],
            showReportModal: false,
            selectedCommentId: null,
            showReportCommentModal: false,
            showReportGalleryModal: false,
            selectedGalleryId: null,
            selectedViolatorId: null,
            similarPhotos: [],
            relatedGalleries: [],
        };
    },
    watch: {
        '$route.params.token': {
            immediate: true,
            async handler(newToken) {
                // Gọi các phương thức fetch khi token thay đổi
                await this.fetchPhotoDetail(newToken);
                await this.fetchSimilarPhotos(newToken);
                await this.fetchRelatedGalleries(newToken);
                const commentStore = useCommentStore();
                await commentStore.fetchComments(newToken); // Làm mới danh sách bình luận
            },
        },
    },
    computed: {
        isFollowing() {
            return this.photoDetail.user?.id
                && useFollowStore().followingList.includes(this.photoDetail.user.id);
        },
        comments() {
            const commentStore = useCommentStore();
            return commentStore.comments;
        },
        displayedComments() {
            return this.showAllComments ? this.comments : this.comments.slice(0, 3);
        },
        formattedViews() {
            const views = this.photoDetail.total_views;
            if (views >= 1000000) {
                return (views / 1000000).toFixed(1) + "M";
            } else if (views >= 1000) {
                return (views / 1000).toFixed(1) + "K";
            }
            return views;
        },
        formattedPhotoLikes() {
            const likes = this.photoLikesCount;
            if (likes >= 1000000) {
                return (likes / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
            } else if (likes >= 1000) {
                return (likes / 1000).toFixed(1).replace(/\.0$/, '') + 'k';
            }
            return likes;
        },
        followStore() {
            return useFollowStore();
        },
        userStore() {
            return useUserStore();
        },
    },
    async mounted() {
        try {
            const token = this.$route.params.token;

            // Fetch comments và categories không cần auth
            const commentStore = useCommentStore();
            await commentStore.fetchComments(token);
            await this.fetchCategories();

            // Những operation cần auth
            const tokenFromLocalStorage = localStorage.getItem("token");
            if (tokenFromLocalStorage) {
                const likeStore = useLikeStore();
                await likeStore.fetchLikedPhotos();
                await likeStore.fetchLikedGalleries();
                this.updateLikedState();

                // Fetch danh sách người dùng đã follow nếu đã đăng nhập
                const followStore = useFollowStore();
                await followStore.fetchFollowingList();
            }
            await this.fetchPhotoDetail(token);
            await this.fetchPhotoLikes(token);
            await this.fetchSimilarPhotos(token);
            await this.fetchRelatedGalleries(token);

            this.updateLikedGalleriesState();

            // 2. Fetch danh sách user bị block
            const blockStore = useBlockStore();
            await blockStore.fetchBlockedUsers();

            // 3. Kiểm tra xem user của ảnh có nằm trong blockStore.blockedUsers không
            this.isPhotoUserBlocked = blockStore.blockedUsers.includes(this.photoDetail.user.id);
        } catch (error) {
            console.error("Error in mounted:", error);
        }
    },
    methods: {
        async fetchPhotoDetail(token) {
            try {
                const tokenFromLocalStorage = localStorage.getItem("token");
                const headers = tokenFromLocalStorage
                    ? { Authorization: `Bearer ${tokenFromLocalStorage}` }
                    : {};

                const response = await axios.get(`${getUrlList().getPhotoDetail}/${token}`, {
                    headers: headers
                });
                this.photoDetail = response.data.data;
                this.updateLikedState();
            } catch (error) {
                console.error("Error fetching photo details:", error);
            }
        },
        async fetchPhotoLikes(token) {
            try {
                const response = await axios.get(getUrlList().getPhotoLikes(token));
                if (response.data.success) {
                    this.photoLikesCount = response.data.data.total_likes;
                } else {
                    console.error(response.data.message);
                    this.photoLikesCount = 0;
                }
            } catch (error) {
                console.error("Error fetching photo likes:", error);
                this.photoLikesCount = 0;
            }
        },
        async fetchSimilarPhotos(token) {
            try {
                const authToken = localStorage.getItem("token"); // Lấy token nếu có

                let headers = {};
                if (authToken) {
                    headers = {
                        Authorization: `Bearer ${authToken}`,
                    };
                }

                const response = await axios.get(getUrlList().getRelatedPhotos(token), { headers });

                if (response.data) {
                    this.similarPhotos = response.data;
                    this.updateLikedState();
                }
            } catch (error) {
                console.error("Error fetching similar photos:", error);
                this.similarPhotos = [];
            }
        },
        async fetchRelatedGalleries(token) {
            try {
                const authToken = localStorage.getItem("token");
                let headers = {};
                if (authToken) {
                    headers = { Authorization: `Bearer ${authToken}` };
                }

                const response = await axios.get(getUrlList().getRelatedGalleries(token), { headers });

                if (response.data && Array.isArray(response.data)) {
                    this.relatedGalleries = response.data;
                    this.updateLikedGalleriesState(); // Cập nhật trạng thái liked sau khi gán dữ liệu
                    console.log('Related Galleries:', this.relatedGalleries); // Debug
                } else {
                    this.relatedGalleries = [];
                    console.error("No related galleries found or invalid data:", response.data?.message);
                }
            } catch (error) {
                console.error("Error fetching related galleries:", error);
                this.relatedGalleries = [];
            }
        },
        async toggleLikeGallery(gallery) {
            if (!await this.checkLogin()) return;

            const likeStore = useLikeStore();
            const galleryId = gallery.id;
            const galleryUserId = gallery.user?.id;

            try {
                if (gallery.liked) {
                    await likeStore.unlikeGallery(galleryId);
                    gallery.liked = false;
                } else {
                    await likeStore.likeGallery(galleryId, galleryUserId);
                    gallery.liked = true;
                }
                this.updateLikedGalleriesState(); // Cập nhật lại trạng thái cho tất cả galleries
            } catch (error) {
                console.error('Failed to toggle like for gallery:', error);
                notification.error({
                    message: 'Error',
                    description: 'Failed to toggle like. Please try again.',
                });
            }
        },
        closeLikesPopup() {
            this.likesPopupVisible = false;
        },
        //Dùng trong các danh sách như popup hiển thị danh sách liked users
        async toggleFollowUser(username) {
            // Kiểm tra đăng nhập trước
            const authStore = useAuthStore();
            await authStore.checkLoginStatus();
            if (!authStore.isLoggedIn) {
                this.$router.push({ name: 'Login' });
                return;
            }
            try {
                // Lấy thông tin user dựa vào username
                const userData = await axios.get(getUrlList().getUserByUserName(username));
                const userId = userData.data.id;
                // Nếu đã theo dõi, hiện modal xác nhận unfollow
                if (this.followStore.followingList.includes(userId)) {
                    Modal.confirm({
                        title: 'Are you sure you want to unfollow this user',
                        icon: h(ExclamationCircleOutlined),
                        content: `This will unfollow the photographer. You will no longer see their content in your For You feed.`,
                        onOk: async () => {
                            try {
                                await this.followStore.unfollowUser(userId);
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
                        },
                        onCancel() {
                            // Không làm gì nếu hủy
                        },
                    });
                } else {
                    // Nếu chưa theo dõi, thực hiện follow luôn
                    await this.followStore.followUser(userId);
                    notification.success({
                        message: 'Success',
                        description: `You are now following ${username}.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                }
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
        // Dùng cho trường hợp cụ thể khi thao tác với user của ảnh chi tiết
        async toggleFollow() {
            if (!await this.checkLogin()) return;

            const followStore = useFollowStore();
            const userId = this.photoDetail.user.id;
            const username = this.photoDetail.user.username; // Lấy username để hiển thị trong thông báo

            if (this.isFollowing) {
                // Nếu đang follow, hiển thị modal xác nhận unfollow
                Modal.confirm({
                    title: 'Are you sure you want to unfollow this user?',
                    icon: h(ExclamationCircleOutlined),
                    content: 'This will unfollow the photographer. You will no longer see their content in your For You feed.',
                    onOk: async () => {
                        try {
                            await followStore.unfollowUser(userId);
                            this.isFollowing = false;
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
                    },
                    onCancel() {
                        // Không làm gì nếu hủy
                    },
                });
            } else {
                // Nếu chưa follow thì thực hiện follow luôn
                try {
                    await followStore.followUser(userId);
                    this.isFollowing = true;
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
        // like ảnh chi tiết
        async showLikesPopup() {
            try {
                const token = this.$route.params.token;
                const response = await axios.get(getUrlList().getPhotoLikes(token));
                if (response.data.success) {
                    this.likedUsers = response.data.data.liked_users;
                    this.likesPopupVisible = true;
                } else {
                    console.error(response.data.message);
                }
            } catch (error) {
                console.error("Error fetching liked users:", error);
            }
        },
        async toggleLike(item) {
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
        updateLikedState() {
            const likeStore = useLikeStore();
            // Cập nhật trạng thái liked của ảnh chi tiết
            this.photoDetail.liked = likeStore.likedPhotos.includes(this.photoDetail.id);
            // Cập nhật trạng thái liked của các ảnh tương tự
            if (this.similarPhotos.length > 0) {
                this.similarPhotos.forEach(photo => {
                    photo.liked = likeStore.likedPhotos.includes(photo.id);
                });
            }
        },
        updateLikedGalleriesState() {
            const likeStore = useLikeStore();
            if (this.relatedGalleries.length > 0) {
                this.relatedGalleries.forEach(gallery => {
                    gallery.liked = likeStore.likedGalleries.includes(gallery.id);
                });
            }
        },

        // blcok user
        async toggleBlockUser(user) {
            if (!await this.checkLogin()) return;

            const blockStore = useBlockStore();
            const userId = user.id;

            try {
                if (blockStore.blockedUsers.includes(userId)) {
                    await blockStore.unblockUser(userId);
                    notification.success({
                        message: 'Success',
                        description: `${user.username} is unblocked.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                } else {
                    await blockStore.blockUser(userId);
                    notification.success({
                        message: 'Success',
                        description: `${user.username} has been blocked. All their related content will not be visible going forward.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                }

                this.$router.push({ name: 'Index' });
            } catch (error) {
                console.error("Error toggling block:", error);
            }
        },
        // comment
        async postComment() {
            if (!this.newComment.trim()) {
                return;
            }

            if (!await this.checkLogin()) {
                return;
            }

            const token = this.$route.params.token;
            const commentStore = useCommentStore();

            commentStore.postComment(token, this.newComment);
            this.newComment = ''; // Xóa nội dung
            this.showButtons = false; // Ẩn nút sau khi gửi
        },
        showDeleteConfirm(comment) {
            Modal.confirm({
                title: 'Are you sure you want to delete this comment?',
                icon: h(ExclamationCircleOutlined),
                content: 'This action cannot be undone. Once deleted, this comment will be permanently removed.',
                onOk: () => this.deleteComment(comment.id),
                onCancel() {},
            });
        },
        async deleteComment(commentId) {
            const commentStore = useCommentStore();
            const photoToken = this.$route.params.token;
            await commentStore.deleteComment(commentId, photoToken);
        },
        cancelComment() {
            this.newComment = ''; // Xóa nội dung
            this.showButtons = false; // Ẩn nút
        },
        toggleComments() {
            this.showAllComments = !this.showAllComments;
        },

        // category
        async fetchCategories() {
            try {
                const response = await axios.get(getUrlList().getCategories);
                this.categories = response.data;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        },

        // check login
        async checkLogin() {
            const authStore = useAuthStore();
            await authStore.checkLoginStatus();
            if (!authStore.isLoggedIn) {
                this.$router.push({ name: 'Login' });
                return false;
            }
            return true;
        },
        async handleClick(action, id, violatorId) {
            if (!await this.checkLogin()) {
                return;
            }

            switch (action) {
                case 'toggleLike':
                    this.toggleLike(id); // id là photoDetail
                    break;
                case 'addToGallery':
                    this.openAddToGalleryModal(id); // id là photoId
                    break;
                case 'reportPhoto':
                    this.openReportModal(id, violatorId); // id là photoId, violatorId là userId
                    break;
                case 'reportComment':
                    this.openReportCommentModal(id, violatorId); // id là commentId, violatorId là userId
                    break;
                default:
                    console.error('Unknown action:', action);
            }
        },
        copyUrlToClipboard() {
            const url = window.location.href; // Lấy URL hiện tại của trình duyệt
            navigator.clipboard.writeText(url).then(() => {
                notification.success({
                    message: 'Success',
                    description: 'URL has been copied to the clipboard.',
                    placement: 'topRight',
                    duration: 3,
                });
            }).catch(err => {
                console.error('Failed to copy URL:', err);
                notification.error({
                    message: 'Error',
                    description: 'Failed to copy URL.',
                    placement: 'topRight',
                    duration: 3,
                });
            });
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
        toggleDropdown(id) {
            this.activeDropdown = this.activeDropdown === id ? null : id;
        },

        // open gallery modal
        openAddToGalleryModal(photoId) {
            this.selectedPhotoId = photoId;
            this.showAddToGallery = true; // Mở modal
        },
        closeAddToGalleryModal() {
            this.showAddToGallery = false; // Đóng modal
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
        openReportCommentModal(commentId, violatorId) {
            this.selectedCommentId = commentId;
            this.selectedViolatorId = violatorId;
            this.showReportCommentModal = true;
        },
        closeReportCommentModal() {
            this.showReportCommentModal = false;
            this.selectedCommentId = null;
            this.selectedViolatorId = null;
        },
        async openReportGalleryModal(galleryId, violatorId) {
            if (!await this.checkLogin()) return;
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
        },
    },
};
</script>
<style src="../public/front_assets/css/details.css"></style>
<style scoped>
.similar-photos-section {
    margin-top: 30px;
    margin-bottom: 30px;
}
.liked {
    color: #ff5a5f;
}
.section-title {
    font-size: 18px;
    margin-bottom: 16px;
    margin-left: 5px;
    font-weight: bold;
}
.similar-photos-section {
    margin-left: 20px;
}
.similar-photos-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr); /* 5 columns */
    gap: 10px; /* Gap between photos */
}

.photo-card {
    position: relative;
    width: 100%;
    max-width: 250px;
    overflow: hidden;
    border-radius: 10px;
}

.photo-thumbnail {
    width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 8px;
}
.photo-card {
    width: 200px;
    height: 150px;
    overflow: hidden;
    border-radius: 8px;
}

.photo-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.photo-info {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 30%;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.6));
    padding: 10px;
    border-radius: 0 0 8px 8px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out, transform 0.3s ease-in-out;
}
.galleries-grid{
    gap:0px;
}
/* Khi hover vào photo-card, hiển thị photo-info */
.photo-card:hover .photo-info {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
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

.icon-btn {
    background: none;
    cursor: pointer;
    font-size: 20px; /* Icon size */
}
.icon-btn .fa-heart.liked {
    color: #ff5a5f; /* Màu khi đã like */
}
.no-comment {
    text-align: center;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    margin-top: 20px;
}

.comment-icon {
    font-size: 48px;
    color: #1877f2;
    margin-bottom: 10px;
}

.no-comment h3 {
    font-size: 20px;
    color: #333;
    font-weight: bold;
}

.no-comment p {
    font-size: 14px;
    color: #666;
}

.comment-buttons {
    margin-top: 10px;
    display: flex;
    gap: 10px;
    margin-left: 90px; /* Dịch sang phải */
}

.cancel-btn, .post-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
}

.cancel-btn {
    background-color: transparent;
    color: #1877f2;
}

.post-btn {
    background-color: #1877f2;
    color: white;
}

.post-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.read-more-button {
    display: block;
    margin: 10px auto;
    padding: 8px 16px;
    border: 1px solid #007bff;
    background-color: white;
    color: #007bff;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
}

.read-more-button:hover {
    background-color: #007bff;
    color: white;
}
.dropdown-content {
   margin-left: -5px;
    margin-top: -40px;
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
