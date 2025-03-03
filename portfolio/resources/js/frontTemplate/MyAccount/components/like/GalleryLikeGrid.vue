<template>
    <div class="galleries-grid">
        <div v-if="likedGalleries.length === 0" class="empty-likes">
            <h2>Add galleries to your likes</h2>
            <p>Browse and like galleries to curate your own collection.</p>
            <button class="add-photo-button" @click="goToAddPhoto">
                Add like galleries
            </button>
        </div>
        <div v-else class="gallery-card" v-for="like in likedGalleries" :key="like.id" @click="goToGalleryDetails(like.galleriesCode)">
            <!-- Thông tin gallery -->
            <div class="gallery-info">
                <h4>{{ like.galleriesName }}</h4>
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
                    <span>{{ like.galleriesPhoto?.length || 0 }}</span>
                </div>
            </div>

            <!-- Hiển thị ảnh trong gallery -->
            <div class="gallery-images" :class="{ empty: like.galleriesPhoto?.length === 0 }">
                <template v-if="!like.galleriesPhoto || like.galleriesPhoto.length === 0">
                    <i class="fa-regular fa-image"></i>
                    <p>Gallery is empty</p>
                </template>
                <template v-else-if="like.galleriesPhoto.length === 1">
                    <div class="gallery-images single-image">
                        <img :src="like.galleriesPhoto[0].image_url" :alt="like.galleriesPhoto[0].title" />
                    </div>
                </template>
                <template v-else>
                    <img
                        v-for="(photo, index) in like.galleriesPhoto.slice(0, 4)"
                        :key="photo.id"
                        :src="photo.image_url"
                        :alt="photo.title"
                    />
                </template>
            </div>

            <!-- Footer của gallery -->
            <div class="gallery-footer" @click.stop>
                <img
                    class="user-avatar"
                    :src="like.userAvatar || '/images/imageUserDefault.png'"
                    alt="User Avatar"
                />
                <h4>{{ like.username || 'Người dùng không xác định' }}</h4>
                <div class="footer-buttons">
                    <button class="btn-options"  @click="showDeleteLikeConfirm(like)">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        </div>
      </div>
</template>

<script>
export default {
    name: 'GalleryLikeGrid',
    props: {
        likedGalleries: {
            type: Array,
            required: true
        }
    },
    emits: ['delete-like'],
    methods: {
        goToGalleryDetails(galleriesCode) {
            this.$router.push({
                name: 'GalleryDetailsUser',
                params: { galleries_code: galleriesCode }
            });
        },
        showDeleteLikeConfirm(like) {
            this.$emit('delete-like', like);
        },
        goToAddPhoto() {
            this.$router.push('/');
        }
    }
};
</script>

<style scoped>
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

.gallery-images.empty {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: rgb(215, 216, 219);
    height: 220px;
}

.gallery-images.empty i {
    font-size: 50px;
    color: #b0b0b0;
}

.gallery-images.empty p {
    margin: 0;
    color: #b0b0b0;
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

.gallery-footer h4 {
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

.btn-options {
    background: none;
    border: none;
    color: gray;
    cursor: pointer;
    font-size: 20px;
    margin-left: 10px;
}

.btn-options:focus {
    outline: none;
}
.empty-likes {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    width: 100%;
    height: 400px;
    color: #777;
    grid-column: 1 / -1; /* Đảm bảo empty-likes chiếm toàn bộ chiều rộng của grid */
}

.empty-likes h2 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #333;
}

.empty-likes p {
    font-size: 16px;
    color: #555;
}

.add-photo-button {
    padding: 10px 20px;
    background-color: #1890ff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}
</style>
