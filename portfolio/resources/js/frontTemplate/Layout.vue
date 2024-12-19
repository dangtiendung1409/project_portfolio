
<template>
    <nav class="navbar">
        <div class="navbar-left">
            <router-link class="navbar-brand" :to="'/'">MyPortfolio</router-link>
            <div class="nav-links">
                <router-link :to="'/'" class="nav-link" style="color: #007bff">Discover</router-link>
                <a href="#" class="nav-link">About us</a>
                <a href="#" class="nav-link">Blog</a>
                <a href="#" class="nav-link">Category</a>
            </div>
        </div>
        <div class="navbar-right">
            <div class="search-bar">
                <input type="text" placeholder="Search powered by AI">
                <i class="fas fa-search search-icon"></i>
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
                        <router-link :to="'/MyPhoto'">
                            <i class="fa-solid fa-user-circle"></i>
                            <span>My Profile</span>
                        </router-link>
                        <router-link :to="'/MyPhoto'">
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
                <i v-if="isLoggedIn" class="fa-regular fa-envelope" style="font-size: 24px;"></i>
                <i v-if="isLoggedIn" class="fa-regular fa-bell" style="font-size: 24px;"></i>
            </div>
            <button v-if="isLoggedIn" class="upload-button">
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
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <p class="mb-1">&copy; Copyright MyPortfolio. All Rights Reserved</p>
                    <div class="credits">
                        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                    </div>
                </div>
                <div class="col-sm-6 social text-md-right">
                    <a href="#"><span class="icofont-twitter"></span></a>
                    <a href="#"><span class="icofont-facebook"></span></a>
                    <a href="#"><span class="icofont-dribbble"></span></a>
                    <a href="#"><span class="icofont-behance"></span></a>
                </div>
            </div>
        </div>
    </footer>
    <div id="scripts"></div>
</template>
<script>
import jwt_decode from 'jwt-decode';
import axios from 'axios';
import getUrlList from "../provider.js";
import { useUserStore } from '@/stores/userStore';
import { mapState } from 'pinia';
export default {
    name: 'Layout',
    data() {
        return {
            isLoggedIn: false,
        };
    },
    computed: {
        ...mapState(useUserStore, ['user']),
        userProfilePicture() {
            return this.user.profile_picture;
        }
    },
    mounted() {
        // Kiểm tra và thiết lập trạng thái đăng nhập
        this.checkLoginStatus();
        this.loadExternalScripts();
    },
    methods: {
        async checkLoginStatus() {
            const token = localStorage.getItem('token');
            if (token) {
                try {
                    const decodedToken = jwt_decode(token);
                    const currentTime = Date.now() / 1000; // Thời gian hiện tại tính bằng giây
                    const expTime = decodedToken.exp; // Thời gian hết hạn của token

                    // Kiểm tra token đã hết hạn chưa
                    if (expTime > currentTime) {
                        this.isLoggedIn = true;
                        const remainingTime = expTime - currentTime; // Thời gian còn lại trước khi token hết hạn

                        // Gọi refreshToken trước khi token hết hạn 1 phút
                        setTimeout(async () => {
                            await this.refreshToken();
                        }, (remainingTime - 60) * 1000);

                        const store = useUserStore();
                        await store.fetchUserData();
                    } else {
                        this.isLoggedIn = false;
                        localStorage.removeItem('token');
                    }
                } catch (error) {
                    console.error('Token decode error:', error);
                    this.isLoggedIn = false;
                    localStorage.removeItem('token');
                }
            } else {
                this.isLoggedIn = false;
            }
        },
        async refreshToken() {
            try {
                const refreshToken = localStorage.getItem('refresh_token');
                if (!refreshToken) {
                    // Nếu không có refresh_token thì yêu cầu đăng nhập lại
                    alert('token does not exist');
                    return;
                }
                const response = await axios.post(getUrlList().refreshToken, {}, {
                    headers: { Authorization: `Bearer ${refreshToken}` },
                });

                const newToken = response.data.token;
                localStorage.setItem('token', newToken);
                this.checkLoginStatus(); // Kiểm tra lại trạng thái đăng nhập
            } catch (error) {
                // console.error('Failed to refresh token:', error.response?.data || error.message);
                // alert('Session expired. Please log in again.');
                this.handleLogout();
            }
        },
        async handleLogout() {
            try {
                const token = localStorage.getItem('token');
                if (token) {
                    await axios.post(getUrlList().logout, {}, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                }
                localStorage.removeItem('token');
                localStorage.removeItem('refresh_token');
                this.isLoggedIn = false;
                window.location.href = '/login';
            } catch (error) {
                console.error('Logout failed:', error);
            }
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
    },
};
</script>

<style scoped>
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
</style>
