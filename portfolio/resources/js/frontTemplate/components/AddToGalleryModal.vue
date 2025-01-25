<template>
    <div v-if="isVisible" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" @click="closeModal">
                    <i class="fa-solid fa-xmark"></i>
                </span>
                <h2 class="modal-title">Add to Gallery</h2>
            </div>

            <button class="create-gallery-btn" @click="goToAddGallery">Create new Gallery</button>
            <hr class="divider" />
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input
                    class="search-gallery"
                    type="text"
                    placeholder="Search Galleries"
                    v-model="searchTerm"
                />
            </div>

            <ul class="gallery-list">
                <li v-for="gallery in filteredGalleries" :key="gallery.id" class="gallery-item" @click="addPhoto(gallery.id)">
                    <img :src="gallery.photo && gallery.photo.length ? gallery.photo[0].image_url : '/images/galleryDefaultImage.png'"
                         alt="Gallery Image"
                         class="gallery-image" />
                    <span class="gallery-name">{{ gallery.galleries_name }}</span>
                    <i v-if="gallery.visibility === 1" class="fa-solid fa-lock"></i>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { useGalleryStore } from '../../stores/galleryStore.js';

export default {
    props: {
        isVisible: {
            type: Boolean,
            required: true,
        },
        photoId: {
            type: Number,
            required: true,
        }
    },
    data() {
        return {
            searchTerm: "",
        };
    },
    computed: {
        filteredGalleries() {
            return this.galleries.filter((gallery) =>
                gallery.galleries_name.toLowerCase().includes(this.searchTerm.toLowerCase())
            );
        },
        galleries() {
            return this.galleryStore.galleries;
        }
    },
    methods: {
        closeModal() {
            this.$emit("close");
        },
        goToAddGallery() {
            this.$router.push({ name: 'AddGallery' });
        },
        async addPhoto(galleryId) {
            try {
                await this.galleryStore.addPhotoToGallery(galleryId, this.photoId);
                this.closeModal();
            } catch (error) {
                console.error('Failed to add photo to gallery:', error);
            }
        }
    },
    mounted() {
        this.galleryStore.fetchGalleries();
    },
    setup() {
        const galleryStore = useGalleryStore();
        return { galleryStore };
    }
};
</script>


<style scoped>
.modal {
    display: flex;
    align-items: center;
    justify-content: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Tạo lớp nền mờ */
}

.modal-content {
    background-color: white;
    padding: 6px;
    border-radius: 8px;
    width: 450px;
    overflow-y: auto;
    max-height: 600px;
    max-width: 90%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    position: relative; /* Cho phép định vị các thành phần con */
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    position: sticky; /* Cố định phần header */
    top: -10px; /* Đặt vị trí cố định ở đầu */
    z-index: 10; /* Đảm bảo nằm trên các nội dung khác */
    background-color: white; /* Đặt màu nền để tránh nội dung cuộn qua */
    padding: 10px;
    border-bottom: 1px solid #ccc; /* Đường kẻ dưới header */
}

.close {
    cursor: pointer;
}
.modal-title {
    text-align: center;
    flex-grow: 1;
    font-size: 20px;
    font-weight: bold;
    margin: 5px;
    color: black;
    margin-right: 35px;
}
.divider {
    border: none; /* Xóa viền mặc định */
    height: 1px; /* Độ dày đường kẻ */
    background-color: #ccc; /* Màu sắc của đường kẻ */
    margin: 20px 0; /* Khoảng cách trên và dưới đường kẻ */
}

.search-wrapper {
    position: relative;
    width: 100%;
}

.search-icon {
    position: absolute;
    top: 50%;
    left: 10px; /* Khoảng cách icon với bên trái */
    transform: translateY(-50%);
    color: #888; /* Màu của icon */
    font-size: 16px; /* Kích thước icon */
    pointer-events: none; /* Vô hiệu hóa sự kiện click vào icon */
}

.search-gallery {
    width: 100%;
    padding: 10px 10px 10px 35px; /* Thêm khoảng trống bên trái để tránh icon */
    border: 1px solid #ccc;
    background-color: rgb(238, 239, 242);
    border-radius: 100px;
    font-size: 14px;
    box-sizing: border-box; /* Đảm bảo padding không làm tăng kích thước input */
}

.search-gallery:focus {
    border-color: #0078FF; /* Màu viền khi focus */
    outline: none; /* Bỏ viền mặc định */
}


.create-gallery-btn {
    display: inline-flex;
    -webkit-box-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    align-items: center;
    font-size: 16px;
    line-height: 20px;
    font-weight: bold;
    margin: 0px;
    border-width: 2px;
    border-radius: 28px;
    border-style: solid;
    cursor: pointer;
    text-align: center;
    max-height: 48px;
    padding: 12px 22px;
    width: 100%;
    background-color: rgb(255, 255, 255);
    border-color: rgb(8, 112, 209);
    color: rgb(8, 112, 209);
}

.create-gallery-btn:hover {
    background-color: #DDDDDD;
}
.gallery-list {
    list-style-type: none; /* Bỏ dấu chấm cho danh sách */
    padding: 0; /* Bỏ padding */
    margin-top: 12px;
}

.gallery-item {
    display: flex;
    align-items: center; /* Căn giữa theo chiều dọc */
    padding: 15px;
    cursor: pointer;
}

.gallery-item:hover {
    background-color: #f1f1f1; /* Màu nền khi hover */
}

.gallery-image {
    width: 60px; /* Kích thước ảnh */
    height: 60px; /* Kích thước ảnh */
    border-radius: 5px; /* Bo góc ảnh */
    margin-right: 10px; /* Khoảng cách giữa ảnh và tên */
}

.gallery-name {
    flex-grow: 1; /* Chiếm không gian còn lại */
    color: #222; /* Màu chữ tên gallery */
    font-size: 16px; /* Kích thước chữ tên gallery */
}

</style>
