<template>
    <nav class="navbar">
        <div class="navbar-left">
            <router-link class="navbar-brand" :to="'/'">MyPortfolio</router-link>
            <div class="nav-links">
                <router-link :to="'/'" class="nav-link" style="color: #007bff">Discover</router-link>
                <router-link :to="'/contact'" class="nav-link">Contact</router-link>
                <router-link :to="'/blog'" class="nav-link">Blog</router-link>
                <router-link :to="{ name: 'Category' }" class="nav-link">Category</router-link>
            </div>
        </div>
        <div class="navbar-right">
            <div class="search-bar">
                <input type="text" placeholder="Search powered by AI" v-model="searchQuery" @keyup.enter="searchPhotos">
                <i class="fas fa-search search-icon" @click="searchPhotos"></i>
            </div>
            <div class="icon-container">
                <div v-if="isLoggedIn" class="user-dropdown">
                    <img v-if="userProfilePicture"
                         :src="'http://127.0.0.1:8000/' + userProfilePicture"
                         alt="User"
                         style="width: 30px; height: 30px; border-radius: 50%;"
                         @click="toggleDropdown('dropdownMenu')" />
                    <img v-else
                         src="/images/imageUserDefault.png"
                         alt="Default User"
                         style="width: 30px; height: 30px; border-radius: 50%;"
                         @click="toggleDropdown('dropdownMenu')" />
                    <div id="dropdownMenu" class="dropdown-content">
                        <router-link :to="'/myPhotos'">
                            <i class="fa-solid fa-camera"></i>
                            <span>My Photo</span>
                        </router-link>
                        <router-link :to="'/myGallery'">
                            <i class="fa-solid fa-images"></i>
                            <span>My Gallery</span>
                        </router-link>
                        <router-link :to="'/login'" @click.prevent="handleLogout">
                            <i class="fa-solid fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </router-link>
                    </div>
                </div>
                <div v-else>
                    <router-link :to="'/login'" class="btn-custom login-btn">Log in</router-link>
                    <router-link :to="'/register'" class="btn-custom signup-btn">Sign up</router-link>
                </div>
                <router-link v-if="userName" :to="{ name: 'MyProfile', params: { username: userName } }">
                    <i v-if="isLoggedIn" class="fa-regular fa-user" style="font-size: 24px; color: black"></i>
                </router-link>
                <div class="notification-wrapper">
                    <i v-if="isLoggedIn" class="fa-regular fa-bell" style="font-size: 24px;" @click="toggleDropdown('notificationDropdown')">
                        <span v-if="unreadCount > 0" class="notification-dot"></span>
                    </i>
                    <div id="notificationDropdown" class="notification-dropdown">
                        <div class="notification-header">Notifications</div>
                        <div v-for="notification in notifications" :key="notification.id"
                             :class="['notification-item', { 'unread': !notification.read }]">
                            <img :src="notification.image" alt="User" class="notification-image">
                            <div class="notification-content">
                                <p class="notification-message" @click="navigateToPhoto(notification)">
                                    {{ notification.message }}
                                </p>
                                <small class="notification-time">{{ notification.time }}</small>
                            </div>
                        </div>
                        <div v-if="hasMoreNotifications" class="see-more" @click="loadMoreNotifications">
                            View more
                        </div>
                    </div>
                </div>
            </div>
            <button v-if="isLoggedIn" class="upload-button" @click="goToAddPhotos">
                <i class="fa-solid fa-arrow-up"></i> Upload
            </button>
            <button class="hamburger" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
            <button class="close-menu" aria-label="Close menu" style="display: none;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </nav>

    <main>
        <slot name="content"></slot>
    </main>
    <footer class="footer" role="contentinfo">

    </footer>
    <div id="scripts"></div>
</template>

<script>
import { useUserStore } from '@/stores/userStore';
import { useAuthStore } from '@/stores/authStore';
import { useNotificationStore } from '@/stores/notificationStore';
import { mapState } from 'pinia';

export default {
    name: 'Layout',
    data() {
        return {
            searchQuery: '',
        };
    },
    computed: {
        ...mapState(useAuthStore, ['isLoggedIn']),
        ...mapState(useUserStore, ['user']),
        ...mapState(useNotificationStore, ['notifications', 'unreadCount', 'currentPage', 'lastPage']),
        userProfilePicture() {
            return this.user ? this.user.profile_picture : '';
        },
        userName() {
            return this.user ? this.user.username : '';
        },
        hasMoreNotifications() {
            return this.currentPage < this.lastPage; // Còn trang để tải
        },
    },
    async mounted() {
        const authStore = useAuthStore();
        await authStore.checkLoginStatus();
        if (authStore.isLoggedIn) {
            const userStore = useUserStore();
            await userStore.fetchUserData();
            const notificationStore = useNotificationStore();
            await notificationStore.fetchNotifications(1); // Tải trang đầu tiên
        }
        this.loadExternalScripts();
    },
    methods: {
        async navigateToPhoto(notification) {
            if (notification.id) {
                const notificationStore = useNotificationStore();
                await notificationStore.markNotificationAsRead(notification.id);
            }

            // Nếu type = 2 (follow) hoặc type = 4, chỉ đánh dấu đã đọc, không chuyển trang
            if (notification.type === 2 || notification.type === 4) {
                return;
            }

            // Nếu type = 3 (Gallery), lấy galleries_code từ store và điều hướng
            if (notification.type === 3 && notification.galleriesCode) {
                this.$router.push({ name: 'GalleryDetailsUser', params: { galleries_code: notification.galleriesCode } });
                return;
            }

            // Nếu có photoToken, chuyển hướng đến trang chi tiết ảnh
            if (notification.photoToken) {
                this.$router.push({ name: 'PhotoDetail', params: { token: notification.photoToken } });
            } else {
                alert('Invalid notification data.');
            }
        },
        async loadMoreNotifications() {
            const notificationStore = useNotificationStore();
            await notificationStore.fetchNotifications(this.currentPage + 1); // Tải trang tiếp theo
        },
        toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        },
        loadExternalScripts() {
            const src = [
                '/front_assets/vendor/jquery/jquery.min.js',
                '/front_assets/vendor/jquery/jquery-migrate.min.js',
                '/front_assets/vendor/bootstrap/js/bootstrap.min.js',
                '/front_assets/vendor/easing/easing.min.js',
                '/front_assets/vendor/php-email-form/validate.js',
                '/front_assets/vendor/isotope/isotope.pkgd.min.js',
                '/front_assets/vendor/aos/aos.js',
                '/front_assets/vendor/owlcarousel/owl.carousel.min.js',
                '/front_assets/js/main.js',
            ];
            src.forEach(srcFile => {
                const script = document.createElement('script');
                script.src = srcFile;
                script.async = false;
                document.getElementById('scripts').appendChild(script);
            });
        },
        async handleLogout() {
            const authStore = useAuthStore();
            await authStore.handleLogout();
        },
        goToAddPhotos() {
            this.$router.push({ name: 'AddPhotos' });
        },
        searchPhotos() {
            this.$router.push({ name: 'Search', query: { q: this.searchQuery } });
        },
    },
};
</script>

<style scoped>
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1000;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
main {
    margin-top: 60px;
}
.user-dropdown img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}
.dropdown-content {
    top: 40px;
    right: -110px;
}

.notification-wrapper .notification-dropdown {
    display: none;
    position: absolute;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 10px 0;
    z-index: 10;
    width: 300px;
    top: 70px;
    right: 70px;
    overflow-y: auto;
    max-height: 600px;
}
.notification-header {
    text-align: center;
    font-size: 17px;
    background-color: rgb(255, 255, 255);
    padding: 13px 0px;
    height: 52px;
    color: rgb(34, 34, 34);
    font-weight: bold;
    position: sticky;
    z-index: 1;
    top: -12px;
}

.notification-dot {
    position: absolute;
    top: 38px;
    right: 153px;
    width: 8px;
    height: 8px;
    background-color: #ff0000;
    border-radius: 50%;
    z-index: 1;
}
.notification-wrapper .notification-dropdown.show {
    display: block;
}

.notification-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #f1f1f1;
    transition: background-color 0.3s;
}
.notification-item.unread {
    background-color: #e7f3ff;
}
.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background-color: #f9f9f9;
}
.see-more {
    text-align: center;
    padding: 7px;
    cursor: pointer;
    transition: 0.5s;
    height: 40px;
    background-color: rgb(255, 255, 255);
    box-shadow: rgba(0, 0, 0, 0.16) 0px -1px 4px;
    font-size: 14px;
    margin-bottom: -8px;
    color: rgb(8, 112, 209);
}

.notification-image {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
}

.notification-content {
    flex: 1;
}

.notification-message {
    font-size: 14px;
    font-weight: 500;
    margin: 0;
    color: #333;
}

.notification-time {
    font-size: 12px;
    color: #777;
}

.btn-custom {
    border: 1px solid black;
    border-radius: 30px;
    padding: 5px 15px;
    font-size: 16px;
    text-decoration: none;
    color: black;
    margin-left: 10px;
    transition: background-color 0.3s, color 0.3s;
}

.btn-custom:hover {
    background-color: black;
    color: white;
}

.login-btn {
    font-weight: bold;
    border: none; /* Remove border for login button */
    background-color: transparent;
}

.signup-btn {
    font-weight: bold;
}
footer {
    padding: 30px 0; /* Khoảng cách trên và dưới */
    text-align: center;
}
</style>
