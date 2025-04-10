<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="site-section site-portfolio">
                <div class="container">
                    <TabBar :activeItem="activeItem" :setActive="setActive" :isLoggedIn="isLoggedIn" />
                    <ForYou v-if="activeItem === 'forYou'" :photos="photos" />
                    <Following v-else-if="activeItem === 'following' && isLoggedIn" :users="followingUsers"/>
                    <Explore v-else-if="activeItem === 'explore'" :users="followingUsers"/>
                </div>

            </div>


        </template>
    </Layout>
</template>
<script>
import axios from 'axios';
import Layout from './Layout.vue'
import TabBar from './components/TabBar.vue';
import ForYou from './components/ForYou.vue';
import Following from './components/Following.vue';
import Explore from './components/Explore.vue';
import getUrlList from "../provider.js";
import { useAuthStore } from '@/stores/authStore';
export default {
    name: 'Index',
    components : {
        Layout,
        TabBar,
        ForYou,
        Following,
        Explore,
    },
    data() {
        return {
            activeItem: 'forYou',
            photos: [],
            followingUsers: [],
            isLoggedIn: false,
        }
    },
    watch: {
        activeItem(newItem) {
            if (newItem === 'forYou') {
                this.getPhoto();
            }
            if (newItem === 'following' && this.isLoggedIn) {
                this.getFollow();
            }
        }
    },
    async mounted() {
        await this.checkLogin();
        if (this.activeItem === 'forYou') {
            this.getPhoto();
        }
        if (this.activeItem === 'following' && this.isLoggedIn) {
            this.getFollow();
        }
    },
    methods: {
        setActive(item) {
            this.activeItem = item;
        },
        async checkLogin() {
            const authStore = useAuthStore();
            await authStore.checkLoginStatus();
            this.isLoggedIn = authStore.isLoggedIn;
            if (!this.isLoggedIn && this.activeItem === 'following') {
                this.activeItem = 'forYou';
                this.$router.push({ name: 'Login' });
            }
            return this.isLoggedIn;
        },
        async getPhoto() {
            try {
                const token = localStorage.getItem("token"); // Lấy token nếu có

                let headers = {};
                if (token) {
                    headers = {
                        Authorization: `Bearer ${token}`,
                    };
                }
                const response = await axios.get(getUrlList().getPhotoData, { headers });
                this.photos = response.data;
                // console.log(this.photos);
            } catch (error) {
                console.error(error);
            }
        },
        async getFollow() {
            try {
                const token = localStorage.getItem("token"); // Lấy token nếu có

                let headers = {};
                if (token) {
                    headers = {
                        Authorization: `Bearer ${token}`,
                    };
                }

                const response = await axios.get(getUrlList().getFollowData, { headers });
                this.followingUsers = response.data.data || [];
            } catch (error) {
                console.error("Lỗi khi lấy danh sách follow:", error);
            }
        }
    }
}
</script>
