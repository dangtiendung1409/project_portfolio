
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
                    <i class="fa-regular fa-user" style="font-size: 24px;" @click="toggleDropdown('dropdownMenu')"></i>
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
import axios from 'axios';
import getUrlList from "../provider.js";

export default {
    name: 'Layout',
    data() {
        return {
            isLoggedIn: false,
        };
    },
    mounted() {
        // Kiểm tra xem token có tồn tại trong localStorage hay không
        this.isLoggedIn = !!localStorage.getItem('token');

        // Thêm các script cần thiết
        const src = [
            '/front_assets/vendor/jquery/jquery.min.js',
            '/front_assets/vendor/jquery/jquery-migrate.min.js',
            '/front_assets/vendor/bootstrap/js/bootstrap.min.js',
            '/front_assets/vendor/easing/easing.min.js',
            '/front_assets/vendor/php-email-form/validate.js',
            '/front_assets/vendor/isotope/isotope.pkgd.min.js',
            '/front_assets/vendor/aos/aos.js',
            '/front_assets/vendor/owlcarousel/owl.carousel.min.js',
            '/front_assets/js/main.js'
        ];
        src.forEach(srcFile => {
            const script = document.createElement('script');
            script.src = srcFile;
            script.async = false;
            document.getElementById('scripts').appendChild(script);
        });
    },
    methods: {
        async handleLogout() {
            try {
                await axios.post(getUrlList().logout);
                localStorage.removeItem('token');
                this.isLoggedIn = false;
                window.location.href = '/login';
            } catch (error) {
                console.error('Logout failed', error);
                alert('Logout failed. Please try again.');
            }
        },
        toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }
    }

}
</script>
<style scoped>
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
