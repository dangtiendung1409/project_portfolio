import { createWebHistory, createRouter } from 'vue-router';
import Index from './frontTemplate/Index.vue';
import MyPhoto from './frontTemplate/MyAccount/MyPhoto.vue';
import MyAccount from "./frontTemplate/MyAccount/MyAccount.vue";
import ChangePassword from "./frontTemplate/MyAccount/ChangePassword.vue";
import Like from "./frontTemplate/MyAccount/Like.vue";
import MyGallery from "./frontTemplate/MyAccount/MyGallery.vue";
import AddGallery from "./frontTemplate/MyAccount/AddGallery.vue";
import PhotoDetail from "./frontTemplate/PhotoDetail.vue";
import Login from "./frontTemplate/Login.vue";
import Register from "./frontTemplate/Register.vue";
import jwt_decode from 'jwt-decode';
const routes = [
    { name: 'Index', path: '/', component: Index },
    { name: 'PhotoDetail', path: '/photoDetail/:token', component: PhotoDetail },
    { name: 'MyPhoto', path: '/myPhotos', component: MyPhoto, meta: { requiresAuth: true } },
    { name: 'MyGallery', path: '/myGallery', component: MyGallery, meta: { requiresAuth: true } },
    { name: 'AddGallery', path: '/addGallery', component: AddGallery, meta: { requiresAuth: true } },
    { name: 'Like', path: '/like', component: Like, meta: { requiresAuth: true } },
    { name: 'MyAccount', path: '/myAccount', component: MyAccount, meta: { requiresAuth: true } },
    { name: 'ChangePassword', path: '/changePassword', component: ChangePassword, meta: { requiresAuth: true } },
    { name: 'Login', path: '/login', component: Login },
    { name: 'Register', path: '/register', component: Register },
];

// Tạo router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation Guard
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    let isLoggedIn = false;

    if (token) {
        try {
            const decodedToken = jwt_decode(token); // Giải mã token
            const currentTime = Date.now() / 1000; // Thời gian hiện tại (tính bằng giây)

            // Kiểm tra thời gian hết hạn của token
            if (decodedToken.exp > currentTime) {
                isLoggedIn = true; // Token còn hạn
            } else {
                localStorage.removeItem('token'); // Token hết hạn, xóa khỏi localStorage
                isLoggedIn = false;
            }
        } catch (error) {
            console.error("Token decode error:", error);
            localStorage.removeItem('token'); // Token không hợp lệ, xóa khỏi localStorage
            isLoggedIn = false;
        }
    }

    // Nếu route yêu cầu xác thực và người dùng không đăng nhập (hoặc token hết hạn)
    if (to.matched.some(record => record.meta.requiresAuth) && !isLoggedIn) {
        next({ name: 'Login' });
    } else {
        next();
    }
});

export default router;
