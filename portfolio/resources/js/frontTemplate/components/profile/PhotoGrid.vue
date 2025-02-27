<template>
    <div class="photo-grid">
        <div class="photo-card" v-for="photo in photos" :key="photo.id">
            <div class="photo-overlay">
                <router-link :to="{ name: 'PhotoDetail', params: { token: photo.photo_token } }">
                    <img :src="photo.image_url" :alt="photo.title" />
                </router-link>
                <div class="photo-info">
                    <p>{{ truncateTitle(photo.title) }}</p>
                    <i class="fa-regular fa-square-plus" @click="openAddToGalleryModal(photo.id)"></i>
                </div>
            </div>
        </div>
    </div>
    <AddToGalleryModal
        :is-visible="showAddToGallery"
        :photo-id="selectedPhotoId"
        @close="closeAddToGalleryModal"
    />
</template>

<script>
import AddToGalleryModal from '../AddToGalleryModal.vue';
export default {
    name: "PhotoGrid",
    props: {
        photos: {
            type: Array,
            required: true,
        },
        checkLogin: {
            type: Function,
            required: true,
        },
    },
    components: {
        AddToGalleryModal,
    },
    data() {
        return {
            showAddToGallery: false,
            selectedPhotoId: null,
        };
    },
    methods: {
        async openAddToGalleryModal(id) {
            // Gọi checkLogin trước khi mở modal
            const isLoggedIn = await this.checkLogin();
            if (!isLoggedIn) {
                // Nếu chưa đăng nhập, checkLogin đã xử lý chuyển hướng (hoặc bạn có thể thông báo)
                return;
            }
            this.selectedPhotoId = id;
            this.showAddToGallery = true;
        },
        closeAddToGalleryModal() {
            this.showAddToGallery = false; // Đóng modal
        },
        truncateTitle(title) {
            const maxLength = 30; // Adjust the max length as needed
            if (!title) {
                return 'Untitled';
            }
            if (title.length > maxLength) {
                return title.substring(0, maxLength) + '...';
            }
            return title;
        },
    },
};
</script>

<style scoped>
.photo-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    padding: 20px;
    background-color: #F7F8FA;
}

.photo-card {
    width: calc(25% - 15px); /* Chia đều cho 4 cột, trừ khoảng cách giữa các cột */
    max-width: 350px; /* Chiều rộng tối đa */
    height: auto;
    overflow: hidden;
    border-radius: 10px;
    position: relative;
}

.photo-card img {
    width: 100%;
    height: 230px; /* Cố định chiều cao */
    object-fit: cover;
    border-radius: 10px;
}

.photo-overlay {
    position: relative;
    height: 100%;
}

.photo-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5); /* Chiếm toàn chiều rộng */
    color: white;
    padding: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
}

.photo-card:hover .photo-info {
    opacity: 1;
}

.photo-info p {
    margin: 0;
    font-size: 14px;
}

.photo-info i {
    font-size: 20px;
    cursor: pointer;
}
</style>
