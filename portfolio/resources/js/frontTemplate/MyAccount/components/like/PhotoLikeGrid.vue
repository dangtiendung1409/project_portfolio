<template>
    <div class="photo-gallery">
        <div v-if="likedPhotos.length === 0" class="empty-likes">
            <h2>Add photos to your likes</h2>
            <p>Browse and like photos to curate your own collection.</p>
            <button class="add-photo-button" @click="goToAddPhoto">
                Add like photos
            </button>
        </div>
        <div v-else v-for="like in likedPhotos" :key="like.id" class="photo-item">
            <div class="photo-overlay">
                <router-link :to="{ name: 'PhotoDetail', params: { token: like.photoToken } }">
                    <img :src="like.imageUrl" alt="photo" class="photo-image" />
                </router-link>
                <div class="photo-details">
                    <img :src="like.userAvatar" alt="User Avatar" class="user-avatar" />
                    <span class="user-name2">{{ like.name }}</span>
                    <span class="icon-heart2" @click="showDeleteLikeConfirm(like)">
                        <i class="fa-solid fa-trash-can"></i>
                    </span>
                </div>
            </div>
        </div>
      </div>
</template>

<script>
export default {
    name: "PhotoLikeGrid",
    props: {
        likedPhotos: {
            type: Array,
            required: true
        }
    },
    emits: ['delete-like'],
    methods: {
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

.photo-gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.user-name2 {
    margin-left: -45px;
    max-width: 120px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    display: inline-block;
}

.photo-item {
    position: relative;
}
.photo-image {
    width: 250px;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
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
