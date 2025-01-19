
<template>
    <aside class="sidebar">
        <div class="user-info">
            <img
                class="avatar"
                :src="user.profile_picture
        ? `http://127.0.0.1:8000/images/avatars/${user.profile_picture.split('/').pop()}`
        : 'http://127.0.0.1:8000/images/avatars/imageUserDefault.png'"
                alt="User Avatar"
            />

            <div class="user-details">
                <h2 class="username">{{ user.username }}</h2>
            </div>
        </div>
        <ul>
            <li v-for="item in menuItems" :key="item.path">
                <router-link :to="item.path" :class="{ active: isActive(item.path) }">
                    <i :class="item.icon"></i> {{ item.label }}
                </router-link>
            </li>
        </ul>
    </aside>
</template>

<script>
import { useUserStore } from '@/stores/userStore';
import '@assets/css/account.css';
export default {
    name: 'Sidebar',
    data() {
        return {
            menuItems: [
                { path: '/myPhotos', icon: 'fas fa-camera', label: 'My Photo' },
                { path: '/myGallery', icon: 'fas fa-images', label: 'Galleries' },
                { path: '/Like', icon: 'fas fa-heart', label: 'Like' },
                { path: '/MyAccount', icon: 'fas fa-user', label: 'Profile' },
                { path: '/ChangePassword', icon: 'fas fa-key', label: 'Change Password' },
            ],
        };
    },
    computed: {
        user() {
            const store = useUserStore();
            return store.user;
        },
    },
    created() {
        this.fetchUserData();
    },

    methods: {
        isActive(path) {
            return this.$route.path === path;
        },
        async fetchUserData() {
            const store = useUserStore();
            await store.fetchUserData();
        },
    },
};
</script>
