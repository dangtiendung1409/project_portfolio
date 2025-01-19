<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <div class="gallery-details">
                            <button class="back-button" @click="goBack">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <div class="header" v-if="gallery">
                                <h1 class="gallery-title">{{ gallery.galleries_name }}</h1>
                                <div class="user-info">
                                    <span class="gallery-user">{{ gallery.galleries_description }}</span>
                                    <button class="edit-button">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="gallery-image-container" v-if="gallery">
                                <div class="gallery-images">
                                    <img v-for="photo in gallery.photo_images"
                                         :key="photo.id"
                                         :src="photo.image_url"
                                         :alt="photo.photo.title" />
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
import getUrlList from '../../provider.js';
import axios from 'axios';
import '@assets/css/account.css';

export default {
    name: 'GalleryDetails',
    components: {
        Layout,
        Sidebar,
    },
    data() {
        return {
            gallery: null,
        };
    },
    mounted() {
        const galleries_code = this.$route.params.galleries_code;
        this.fetchGalleryDetails(galleries_code);
    },
    methods: {
        goBack() {
            this.$router.push('/myGallery');
        },
        async fetchGalleryDetails(galleries_code) {
            try {
                const response = await axios.get(`${getUrlList().getGalleryDetails}/${galleries_code}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}` // Nếu cần thiết
                    }
                });
                this.gallery = response.data.data;
            } catch (error) {
                console.error('Failed to fetch gallery details:', error);
            }
        }
    },

};
</script>


<style scoped>
main {
    flex: 1;
    padding-left: 0;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.my-photo-page {
    display: flex;
    padding: 20px;
    background-color: #ffffff;
}
.back-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 20px;
    margin-right: 1000px;
}
.content-layout {
    flex: 1;
    display: flex;
}

.gallery-details {
    flex: 1;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.header {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.gallery-title {
    font-size: 24px;
    margin: 0;
}

.user-info {
    display: flex;
    align-items: center;
    border-bottom: none;
}

.gallery-user {
    margin-right: 10px;
    margin-bottom: 19px;
}

.edit-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;

}

.gallery-image-container {
    overflow-y: auto; /* Cho phép cuộn dọc */
    max-height: 650px; /* Chiều cao tối đa của khu vực hình ảnh */
    width: 100%;
}

.gallery-images {
    display: flex; /* Sử dụng flex để xếp hình ảnh theo hàng */
    flex-wrap: wrap; /* Cho phép hình ảnh xuống dòng */
}

.gallery-images img {
    width: 250px; /* Kích thước hình ảnh */
    height: auto;
    margin: 5px; /* Khoảng cách giữa các hình ảnh */
    border-radius: 8px;
}
</style>
