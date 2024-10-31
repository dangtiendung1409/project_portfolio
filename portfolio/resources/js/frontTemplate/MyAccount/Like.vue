<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <header class="header">
                            <div class="header-content">
                                <span>Likes</span>
                            </div>
                        </header>
                        <div class="sort-select">
                            <div class="show-options">
                                <label for="show-select">Show:</label>
                                <select id="show-select">
                                    <option value="all">All</option>
                                    <option value="favorites">Favorites</option>
                                </select>
                            </div>
                            <div class="sort-options">
                                <label for="sort-select">Sort by:</label>
                                <select id="sort-select">
                                    <option value="date">Date</option>
                                    <option value="name">Name</option>
                                    <option value="size">Size</option>
                                </select>
                            </div>
                        </div>
                        <!-- Horizontal scrollable photo container -->
                        <div class="photo-gallery">
                            <div v-for="photo in photos" :key="photo.id" class="photo-item">
                                <div class="photo-overlay">
                                    <img :src="photo.url" alt="photo" class="photo-image" />
                                    <div class="photo-details">
                                        <img :src="photo.userAvatar" alt="User Avatar" class="user-avatar" />
                                        <span class="user-name">{{ photo.userName }}</span>
                                        <i class="fa-regular fa-heart"></i>
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </template>
    </Layout>
</template>

<script>
import Layout from '../Layout.vue';
import Sidebar from './components/Sidebar.vue';
import '@assets/css/account.css';
export default {
    name: 'Like',
    components: {
        Layout,
        Sidebar
    },
    data() {
        return {
            photos: [
                { id: 1, url: '/front_assets/img/img_5.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'John Doe' },
                { id: 2, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 3, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 4, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 5, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 6, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 7, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 8, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 9, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 10, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 11, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 12, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 13, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 14, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 15, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 16, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 17, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 18, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 19, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },
                { id: 20, url: '/front_assets/img/img_1.jpg', userAvatar: '/front_assets/img/user1.jpeg', userName: 'Jane Smith' },

            ]
        };
    }
};
</script>
<style scoped>
main {
    flex: 1;
    padding-left: 0;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
</style>
