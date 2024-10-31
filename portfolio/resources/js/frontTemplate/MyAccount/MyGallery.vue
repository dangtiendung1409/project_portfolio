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
                                <div class="gallery-card create-gallery-card">
                                    <div class="icon-container">
                                        <i class="fa-regular fa-square-plus"></i>
                                    </div>
                                    <div class="gallery-info">
                                        <h4>Curate your inspiration</h4>
                                    </div>
                                    <div class="gallery-footer">
                                        <button @click="goToAddGallery" class="create-gallery-button">Create a Gallery</button>
                                    </div>
                                </div>

                                <!-- Các gallery-card khác -->
                                <div class="gallery-card">
                                    <div class="gallery-info">
                                        <h4>Insan</h4>
                                        <div class="image-count">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z" fill="white"></path><path d="M12.4003 5.33337C13.3337 5.33337 14.1337 4.53337 14.1337 3.60003C14.1337 2.6667 13.3337 1.8667 12.4003 1.8667C11.467 1.8667 10.667 2.6667 10.667 3.60003C10.667 4.53337 11.467 5.33337 12.4003 5.33337ZM12.4003 2.80003C12.8003 2.80003 13.2003 3.13337 13.2003 3.60003C13.2003 4.0667 12.867 4.40003 12.4003 4.40003C12.0003 4.40003 11.6003 4.0667 11.6003 3.60003C11.6003 3.13337 11.9337 2.80003 12.4003 2.80003Z" fill="white"></path>
                                            </svg>
                                            <span>27</span>
                                        </div>
                                    </div>
                                    <div class="gallery-images empty">
                                        <i class="fa-regular fa-image"></i>
                                        <p>Gallery is empty</p>
                                    </div>
                                    <div class="gallery-footer">
                                        <img class="user-avatar" src="/front_assets/img/user1.jpeg" alt="User Avatar">
                                        <h4>Dung</h4>
                                        <div class="footer-buttons">
                                            <button class="btn-favorite"><i class="fa-regular fa-eye"></i></button>
                                            <button class="btn-options"><i class="fa-solid fa-ellipsis"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="gallery-card">
                                    <div class="gallery-info">
                                        <h4>Insan</h4>
                                        <div class="image-count">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z" fill="white"></path><path d="M12.4003 5.33337C13.3337 5.33337 14.1337 4.53337 14.1337 3.60003C14.1337 2.6667 13.3337 1.8667 12.4003 1.8667C11.467 1.8667 10.667 2.6667 10.667 3.60003C10.667 4.53337 11.467 5.33337 12.4003 5.33337ZM12.4003 2.80003C12.8003 2.80003 13.2003 3.13337 13.2003 3.60003C13.2003 4.0667 12.867 4.40003 12.4003 4.40003C12.0003 4.40003 11.6003 4.0667 11.6003 3.60003C11.6003 3.13337 11.9337 2.80003 12.4003 2.80003Z" fill="white"></path>
                                            </svg>
                                            <span>27</span>
                                        </div>
                                    </div>
                                    <div class="gallery-images">
                                        <img src="/front_assets/img/img_1.jpg" alt="Gallery 1 Image 1">
                                        <img src="/front_assets/img/img_2.jpg" alt="Gallery 1 Image 2">
                                        <img src="/front_assets/img/img_3.jpg" alt="Gallery 1 Image 3">
                                        <img src="/front_assets/img/img_4.jpg" alt="Gallery 1 Image 4">
                                    </div>
                                    <div class="gallery-footer">
                                        <img class="user-avatar" src="/front_assets/img/user1.jpeg" alt="User Avatar">
                                        <h4>Dung</h4>
                                        <div class="footer-buttons">
                                            <button class="btn-favorite"><i class="fa-solid fa-lock"></i></button>
                                            <button class="btn-options"><i class="fa-solid fa-ellipsis"></i></button>
                                        </div>
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
    name: 'MyGallery',
    components: {
        Layout,
        Sidebar
    },
    methods: {
        goToAddGallery() {
            this.$router.push('/addGallery');
        },
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
    height: 240px; /* Đặt chiều cao để giữ không gian */
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
}
.gallery-images img {
    width: 100%;
    height: auto;
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
</style>
