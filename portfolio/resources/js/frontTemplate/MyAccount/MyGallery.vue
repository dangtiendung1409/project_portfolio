<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <header class="header">
                            <div class="header-content">
                                <span>Galleries</span>
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

                        <div class="featured-galleries mb-4">
                            <div class="galleries-grid">
                                <!-- Gallery cards -->
                                <div class="gallery-card create-gallery-card">
                                    <div class="icon-container">
                                        <i class="fa-regular fa-square-plus"></i>
                                    </div>
                                    <div class="gallery-info">
                                        <h4>Curate your inspiration</h4>
                                    </div>
                                    <div class="gallery-create">
                                        <button @click="goToAddGallery" class="create-gallery-button">Create a Gallery</button>
                                    </div>
                                </div>

                                <div
                                    class="gallery-card"
                                    v-for="gallery in galleries"
                                    :key="gallery.id"
                                    :data-visibility="gallery.visibility"
                                    @click="goToGalleryDetails(gallery.galleries_code)"
                                >
                                    <div class="gallery-info">
                                        <h4>{{ gallery.galleries_name }}</h4>
                                        <div class="image-count">
                                            <svg
                                                width="16"
                                                height="16"
                                                viewBox="0 0 16 16"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z"
                                                    fill="white"
                                                ></path>
                                            </svg>
                                            <span>{{ gallery.photo.length }}</span>
                                        </div>
                                    </div>
                                    <div class="gallery-images" :class="{ empty: gallery.photo.length === 0 }">
                                        <template v-if="gallery.photo.length === 0">
                                            <i class="fa-regular fa-image"></i>
                                            <p>Gallery is empty</p>
                                        </template>
                                        <template v-else-if="gallery.photo.length === 1">
                                            <div class="gallery-images single-image">
                                                <img
                                                    :src="gallery.photo[0].image_url"
                                                    :alt="gallery.photo[0].title"
                                                />
                                            </div>
                                        </template>
                                        <template v-else>
                                            <img
                                                v-for="(photo, index) in gallery.photo.slice(0, 4)"
                                                :key="photo.id"
                                                :src="photo.image_url"
                                                :alt="photo.title"
                                            />
                                        </template>
                                    </div>
                                    <div class="gallery-footer" @click.stop>
                                        <img
                                            class="user-avatar"
                                            :src="gallery.user?.profile_picture || '/default-avatar.jpg'"
                                            alt="User Avatar"
                                        />
                                        <h4>{{ gallery.user?.username || 'Anonymous' }}</h4>
                                        <div class="footer-buttons">
                                            <button class="btn-favorite">
                                                <i :class="gallery.visibility === 0 ? 'fa-regular fa-eye' : 'fa-solid fa-lock'"></i>
                                            </button>
                                            <button
                                                class="btn-options"
                                                @click.stop="toggleDropdown('dropdown-' + gallery.id, $event)"
                                                :class="{'active': activeDropdown === 'dropdown-' + gallery.id}"
                                            >
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="activeDropdown === 'dropdown-' + gallery.id" class="dropdown-content show" style="bottom: 39px; margin-left: 30px" @click.stop>
                                        <ul>
                                            <li @click="goToEditGallery(gallery.galleries_code)">
                                                <i class="fas fa-edit"></i> Edit Gallery
                                            </li>
                                            <li @click="showDeleteConfirm(gallery)">
                                                <i class="fas fa-trash-alt"></i> Delete Gallery
                                            </li>

                                        </ul>
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
import { Modal } from 'ant-design-vue';
import { notification } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import Layout from '../Layout.vue';
import { useGalleryStore } from '../../stores/galleryStore.js';
import Sidebar from './components/Sidebar.vue';
import '@assets/css/account.css';
import { h } from 'vue';
import axios from 'axios';
import getUrlList from "../../provider.js";

export default {
    name: 'MyGallery',
    components: {
        Layout,
        Sidebar,
        Modal,
    },
    data() {
        return {
            activeDropdown: null,
            galleryToDelete: null, // Lưu trữ gallery cần xóa
        };
    },
    computed: {
        galleries() {
            const store = useGalleryStore();
            return store.galleries; // Lấy danh sách galleries từ store
        }
    },
    mounted() {
        const store = useGalleryStore();
        store.fetchGalleries(); // Fetch dữ liệu khi component được mount
    },
    methods: {
        goToGalleryDetails(galleries_code) {
            this.$router.push(`/galleryDetails/${galleries_code}`);
        },
        goToAddGallery() {
            this.$router.push('/addGallery');
        },
        goToEditGallery(galleries_code) {
            this.$router.push(`/editGallery/${galleries_code}`);
        },
        toggleDropdown(id) {
            this.activeDropdown = this.activeDropdown === id ? null : id;
        },
        showDeleteConfirm(gallery) {
            this.galleryToDelete = gallery;
            Modal.confirm({
                title: 'Are you sure delete this gallery?',
                icon: h(ExclamationCircleOutlined),
                content: 'Once deleted, the photos in the gallery will be deleted, this action cannot be undone',
                onOk: this.deleteGallery,
                onCancel() {},
            });
        },
        deleteGallery() {
            console.log('Gallery to delete:', this.galleryToDelete); // Xem đối tượng gallery

            if (this.galleryToDelete && this.galleryToDelete.galleries_code) {
                const url = getUrlList().deleteGallery; // Lấy URL từ getUrlList()
                const galleriesCode = this.galleryToDelete.galleries_code;

                console.log('Deleting gallery with code:', galleriesCode); // Kiểm tra giá trị galleries_code

                axios
                    .delete(`${url}/${galleriesCode}`, {
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`
                        }
                    })
                    .then(response => {
                        console.log('Gallery deleted successfully:', response);
                        this.galleryToDelete = null;

                        // Cập nhật lại danh sách galleries trong store bằng cách loại bỏ gallery đã xóa
                        const store = useGalleryStore();
                        store.galleries = store.galleries.filter(gallery => gallery.galleries_code !== galleriesCode);
                        // Hiển thị thông báo thành công
                        notification.success({
                            message: 'Success',
                            description: 'Gallery deleted successfully!',
                        });
                    })
                    .catch(error => {
                        console.log('Error deleting gallery:', error);
                        this.galleryToDelete = null;
                    });
            } else {
                console.log('Gallery code is missing or invalid.');
            }
        }


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
.create-gallery-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: #f5f5f5;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
}
.icon-container {
    font-size: 50px; /* Đặt kích thước cho biểu tượng */
    color: #007bff; /* Màu sắc cho biểu tượng */
    margin-bottom: 10px;
}
.create-gallery-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}
.create-gallery-button:hover {
    background-color: #0056b3;
}
.gallery-images.empty {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: rgb(215, 216, 219);
    height: 220px; /* Đặt chiều cao để giữ không gian */
}

.gallery-images.empty i {
    font-size: 50px; /* Kích thước biểu tượng */
    color: #b0b0b0; /* Màu sắc cho biểu tượng */
}

.gallery-images.empty p {
    margin: 0;
    color: #b0b0b0; /* Màu sắc cho văn bản */
}
.featured-galleries {
    width: 100%;
}
.galleries-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 1rem;
}
.gallery-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    box-sizing: border-box;
    width: 100%;
    padding: 15px;
    background-color: #f5f5f5;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-top: 20px;
}
.gallery-card .gallery-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.gallery-info h4 {
    font-size: 1rem;
    margin: 0;
}
.gallery-images {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px;
    flex-grow: 1;
}
.gallery-images img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
}
.gallery-images.single-image {
    display: flex;
    height: 200px;
    top: 50px;
    width: 200%;
    border-radius: 8px;
    overflow: hidden;
    background-color: #f5f5f5;
}

.gallery-images.single-image img {
    height: 100%;
    width: auto;
    object-fit: cover;
    border-radius: 8px;
}

.image-count {
    max-width: 100%;
    width: fit-content;
    height: fit-content;
    display: flex;
    color: white;
    background-color: rgb(69, 69, 124);
    border-radius: 4px;
    align-items: center;
    padding: 4px 8px;
    margin-left: 4px;
    word-break: break-word;
}
.image-count svg {
    font-size: 20px;
    margin-right: 5px;
}
.image-count span {
    color: whitesmoke;
}
.gallery-footer {
    padding: 10px;
    display: flex;
    align-items: center;
}
.gallery-create {
    padding: 10px;
    display: flex;
    align-items: center;
}
.gallery-footer h4{
    margin-top: 10px;
}
.user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
}
.footer-buttons {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 8px;
}
.btn-favorite,
.btn-options {
    background: none;
    border: none;
    color: gray;
    cursor: pointer;
    font-size: 20px;
    margin-left: 10px;
}
.btn-options.active i {
    color: whitesmoke;
    background-color: #1890ff;
    border-radius: 50%;
    padding: 5px;
}
.create-gallery-button:focus,
.btn-options:focus {
    outline: none;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    padding: 5px 10px;
    z-index: 1;
}

/* Hiển thị dropdown khi được kích hoạt */
.dropdown-content.show {
    display: block;
}

.dropdown-content ul {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    margin: 0;

}

.dropdown-content li {
    padding: 15px 15px 15px 25px;
    display: flex;
    align-items: center;
    color: #222222;
    white-space: nowrap;
}

.dropdown-content li:hover {
    color: whitesmoke; /* Màu chữ khi hover */
    background-color: #1890ff; /* Màu nền khi hover */
}

.dropdown-content li i {
    margin-right: 8px;
}

.dropdown-content li:hover i {
    color: whitesmoke;
    background-color: #1890ff;
}
</style>
