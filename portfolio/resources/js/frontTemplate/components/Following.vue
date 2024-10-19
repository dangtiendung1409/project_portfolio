<template>
    <div class="following-content">
        <h3>You’re not following anyone yet</h3>
        <p>Start following photographers and stay updated with their latest photos here.</p>
        <div class="following-users">
            <div v-for="user in users" :key="user.id" class="user-card">
                <div class="card">
                    <div class="background-images">
                        <img v-for="bg in user.backgroundImages" :key="bg" :src="bg" class="bg-img" />
                    </div>
                    <img :src="user.image" alt="User Image" class="profile-img" />
                    <h4>{{ user.name }}</h4>
                    <button class="btn-follow">Follow</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        users: Array,
    },
};
</script>
