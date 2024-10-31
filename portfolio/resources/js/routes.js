import { createWebHistory, createRouter } from 'vue-router'
import Index from './frontTemplate/Index.vue'
import MyPhoto from './frontTemplate/MyAccount/MyPhoto.vue'
import MyAccount from "./frontTemplate/MyAccount/MyAccount.vue";
import ChangePassword from "./frontTemplate/MyAccount/ChangePassword.vue"
import Like from "./frontTemplate/MyAccount/Like.vue"
import MyGallery from "./frontTemplate/MyAccount/MyGallery.vue";
import AddGallery from "./frontTemplate/MyAccount/AddGallery.vue";

const routes = [

    {
        name: 'Index',
        path: '/',
        component: Index,

    },
    {
        name: 'MyPhoto',
        path: '/myPhotos',
        component: MyPhoto,

    },
    {
        name: 'MyGallery',
        path: '/myGallery',
        component: MyGallery,

    },
    {
        name: 'AddGallery',
        path: '/addGallery',
        component: AddGallery,

    },
    {
        name: 'Like',
        path: '/like',
        component: Like,

    },
    {
        name: 'MyAccount',
        path: '/myAccount',
        component: MyAccount,

    },
    {
        name: 'ChangePassword',
        path: '/changePassword',
        component: ChangePassword,

    },

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
