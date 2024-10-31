<template>
    <div class="following-content">
        <h3>You’re not following anyone yet</h3>
        <p>Start following photographers and stay updated with their latest photos here.</p>
        <div class="following-users">
            <div v-for="item in follows" :key="item.id" class="user-card">
                <div class="card">
                    <div class="background-images">
                        <!-- Duyệt qua các ảnh của user -->
                        <img v-for="(photoImage, index) in item.images"
                             :key="index"
                             :src="photoImage.image_url"
                             class="bg-img" />
                    </div>


                    <img :src="item.profile_picture" alt="User Image" class="profile-img" />
                    <h4>{{ item.username }}</h4>
                    <button class="btn-follow">Follow</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        follows: {
            type: Array,
            required: true
        }
    }
};
</script>
