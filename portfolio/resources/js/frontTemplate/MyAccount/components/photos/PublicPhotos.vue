<template>
    <div class="galleries-grid">
        <div class="photo-card create-gallery-card">
            <div class="icon-container">
                <i class="fa-regular fa-image"></i>
            </div>
            <div class="gallery-info">
                <h4>Upload your photos</h4>
            </div>
            <div class="gallery-create">
                <button @click="$emit('createPhoto')" class="create-gallery-button">Upload</button>
            </div>
        </div>
        <div v-for="photo in photos" :key="photo.id" class="photo-card">
            <router-link :to="{ name: 'PhotoDetail', params: { token: photo.photo_token } }">
                <img :src="photo.image_url" :alt="photo.title" />
            </router-link>
            <div class="gallery-info">
                <h4>{{ truncateTitle(photo.title) }}</h4>
                <span>{{ photo.privacy_status == '0' ? 'Public' : 'Private' }}</span>
            </div>
            <button class="ellipsis-icon"
                    @click.stop="$emit('toggleDropdown','dropdown-' + photo.id)"
                    :class="{'active': activeDropdown === 'dropdown-' + photo.id}"
            >
                <i class="fa-solid fa-ellipsis"></i>
            </button>
            <div v-if="activeDropdown === 'dropdown-' + photo.id" class="dropdown-content show"  @click.stop>
                <ul>
                    <li @click="$emit('editPhoto',photo.id)">
                        <i class="fa-solid fa-pencil"></i> Edit
                    </li>
                    <li @click="$emit('addGallery',photo.id)">
                        <i class="fa-solid fa-plus"></i> Add to Gallery
                    </li>
                    <li @click="downloadImage(photo.image_url, photo.title)">
                        <i class="fa-solid fa-download"></i> Download
                    </li>
                    <li @click="$emit('deletePhoto',photo)">
                        <i class="fa-solid fa-trash-can"></i> Remove
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PublicPhotos',
    props: {
        photos: {
            type: Array,
            required: true
        },
        activeDropdown: {
            type: String,
            default: null
        },
        downloadImage: {
            type: Function,
            required: true
        }
    },
    methods: {
        truncateTitle(title) {
            const maxLength = 20; // Adjust the max length as needed
            if (!title) {
                return 'Untitled';
            }
            if (title.length > maxLength) {
                return title.substring(0, maxLength) + '...';
            }
            return title;
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
.trial-info {
    margin-top: 30px;
    padding: 290px;
    text-align: center;
    background-color: #fff;
    flex-shrink: 0;
    border-radius: 8px; /* Thêm bo góc nếu cần */

}

.trial-button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.trial-button:hover {
    background-color: #0056b3;
}
.photo-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    background-color: #f5f5f5;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: visible;
    width: 100%;
    transition: transform 0.3s, box-shadow 0.3s;
}

.photo-card img {
    width: 100%;
    height: 300px; /* Đặt chiều cao cố định cho ảnh */
    object-fit: cover; /* Đảm bảo ảnh không bị méo */
    border-radius: 12px 12px 0 0; /* Bo góc cho ảnh */
}
.gallery-info {
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.gallery-info h4 {
    font-size: 1rem;
    margin: 0;
}
.gallery-info span {
    font-size: 0.9rem;
    color: #777; /* Màu sắc cho trạng thái công khai */
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
    font-size: 50px;
    color: #007bff;
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
.featured-galleries {
    overflow-y: auto;
    width: 100%;
}
.galleries-grid {
    margin-top: 30px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 1rem;
}
.ellipsis-icon {
    position: absolute;
    bottom: 60px;
    min-width: auto;
    min-height: auto;
    right: 10px;
    background-color: #ffffff;
    border-radius: 50%;
    padding: 7px 13px;
    border: 0;
    box-shadow: rgba(0, 0, 0, 0.08) 0px 2px 6px;
}
.ellipsis-icon.active {
    background-color: #2986f7; /* Màu nền xanh */
}

.ellipsis-icon.active i {
    color: #ffffff; /* Màu icon trắng */
}
.ellipsis-icon i {
    color: #007bff; /* Màu của biểu tượng */
    font-size: 16px; /* Kích thước biểu tượng */
}
.dropdown-content {
    margin-left: 60px;
    margin-top: 300px;
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
    z-index: 1000;
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
