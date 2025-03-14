import { createWebHistory, createRouter } from 'vue-router';
import { useAuthStore } from './stores/authStore.js';
import Index from './frontTemplate/Index.vue';
import MyPhoto from './frontTemplate/MyAccount/MyPhoto.vue';
import AddPhotos from './frontTemplate/AddPhotos.vue';
import ListUserBlock from "./frontTemplate/MyAccount/ListUserBlock.vue";
import ChangePassword from "./frontTemplate/MyAccount/ChangePassword.vue";
import Like from "./frontTemplate/MyAccount/Like.vue";
import MyGallery from "./frontTemplate/MyAccount/MyGallery.vue";
import GalleryDetails from "./frontTemplate/MyAccount/GalleryDetails.vue";
import AddGallery from "./frontTemplate/MyAccount/AddGallery.vue";
import EditGallery from "./frontTemplate/MyAccount/EditGallery.vue";
import PhotoDetail from "./frontTemplate/PhotoDetail.vue";
import MyProfile from "./frontTemplate/MyProfile.vue";
import Contact from "./frontTemplate/Contact.vue";
import Blog from "./frontTemplate/Blog.vue";
import BlogDetails from "./frontTemplate/BlogDetails.vue";
import GalleryDetailsUser from "./frontTemplate/GalleryDetailsUser.vue";
import Category from "./frontTemplate/Category.vue";
import Search from "./frontTemplate/Search.vue";
import DetailsCategory from "./frontTemplate/DetailsCategory.vue";
import Login from "./frontTemplate/Login.vue";
import Register from "./frontTemplate/Register.vue";
import jwt_decode from 'jwt-decode';
const routes = [
    { name: 'Index', path: '/', component: Index },
    { name: 'Contact', path: '/contact', component: Contact },
    { name: 'Blog', path: '/blog', component: Blog },
    { name: 'BlogDetails', path: '/blog/:slug', component: BlogDetails },
    { name: 'PhotoDetail', path: '/photoDetail/:token', component: PhotoDetail },
    { name: 'DetailsCategory', path: '/detailsCategory', component: DetailsCategory },
    { name: 'GalleryDetailsUser',  path: '/galleryDetailsUser/:galleries_code', component: GalleryDetailsUser },
    { name: 'Category', path: '/categories', component: Category },
    { name: 'Search', path: '/search', component: Search },
    { name: 'MyProfile', path: '/myProfile/:username', component: MyProfile },
    { name: 'MyPhoto', path: '/myPhotos', component: MyPhoto, meta: { requiresAuth: true } },
    { name: 'AddPhotos', path: '/addPhotos', component: AddPhotos, meta: { requiresAuth: true } },

    { name: 'MyGallery', path: '/myGallery', component: MyGallery, meta: { requiresAuth: true } },
    { name: 'GalleryDetails',  path: '/galleryDetails/:galleries_code', component: GalleryDetails, meta: { requiresAuth: true } },
    { name: 'AddGallery', path: '/addGallery', component: AddGallery, meta: { requiresAuth: true } },
    { name: 'EditGallery', path: '/editGallery/:galleries_code', component: EditGallery, meta: { requiresAuth: true } },

    { name: 'Like', path: '/like', component: Like, meta: { requiresAuth: true } },
    { name: 'ListUserBlock', path: '/listUserBlock', component: ListUserBlock, meta: { requiresAuth: true } },
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
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    await authStore.checkLoginStatus();

    // Nếu route yêu cầu xác thực và người dùng không đăng nhập
    if (to.matched.some(record => record.meta.requiresAuth) && !authStore.isLoggedIn) {
        next({ name: 'Login', query: { redirect: to.fullPath } }); // Lưu đường dẫn muốn truy cập
    } else {
        next(); // Tiếp tục điều hướng
    }
});

export default router;
