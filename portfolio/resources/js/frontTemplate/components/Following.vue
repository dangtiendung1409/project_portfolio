<template>
    <div class="following-content">
        <h3>You’re not following anyone yet</h3>
        <p>Start following photographers and stay updated with their latest photos here.</p>

        <div class="following-users">
            <div v-for="user in users" :key="user.id" class="user-card">
                <div class="card">
                    <div :class="[
                        'background-images',
                        user.photos.length === 1 ? 'one-image' : '',
                        user.photos.length === 2 ? 'two-images' : '',
                        user.photos.length === 4 ? 'four-images' : '',
                        user.photos.length === 0 ? 'no-image' : ''
                    ]">
                        <template v-if="user.photos.length > 0">
                            <img v-for="bg in user.photos.slice(0, 4)" :key="bg.id" :src="bg.image_url" class="bg-img" />
                        </template>
                        <div v-else class="content-unavailable">
                            <i class="fa-regular fa-images"></i>
                            <p>Content Unavailable</p>
                        </div>
                    </div>

                    <router-link :to="{ name: 'MyProfile', params: { username: user.username } }">
                        <img :src="user.profile_picture" alt="User Image" class="profile-img" />
                    </router-link>
                    <h4>{{ user.name }}</h4>
                    <button class="btn-follow" @click="toggleFollow(user)">
                        {{ user.following ? 'Unfollow' : 'Follow' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from '@/stores/authStore';
import { useFollowStore } from '@/stores/followStore';
import { Modal,notification } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { h } from 'vue';
export default {
    props: {
        users: Array,
    },
    async mounted() {
        const followStore = useFollowStore();
        await followStore.fetchFollowingList();
        this.updateFollowingState();
    },
    methods: {
        async checkLogin() {
            const authStore = useAuthStore();
            await authStore.checkLoginStatus();
            if (!authStore.isLoggedIn) {
                this.$router.push({ name: 'Login' });
                return false;
            }
            return true;
        },
        updateFollowingState() {
            const followStore = useFollowStore();
            this.users.forEach(user => {
                user.following = followStore.followingList.includes(user.id);
            });
        },
        async toggleFollow(user) {
            if (!await this.checkLogin()) return;

            const followStore = useFollowStore();
            const username = user.username; // Lấy username để hiển thị trong thông báo

            if (user.following) {
                Modal.confirm({
                    title: 'Are you sure you want to unfollow this user?',
                    icon: h(ExclamationCircleOutlined),
                    content: 'This will unfollow the photographer. You will no longer see their content in your For You feed.',
                    onOk: async () => {
                        try {
                            await followStore.unfollowUser(user.id);
                            user.following = false;
                            notification.success({
                                message: 'Success',
                                description: `You have unfollowed ${username}.`,
                                placement: 'topRight',
                                duration: 3,
                            });
                        } catch (error) {
                            console.error('Error unfollowing user:', error);
                            notification.error({
                                message: 'Error',
                                description: `Failed to unfollow ${username}.`,
                                placement: 'topRight',
                                duration: 3,
                            });
                        }
                    },
                    onCancel() {
                        // Không làm gì nếu hủy
                    },
                });
            } else {
                try {
                    await followStore.followUser(user.id);
                    user.following = true;
                    notification.success({
                        message: 'Success',
                        description: `You are now following ${username}.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                } catch (error) {
                    console.error('Error following user:', error);
                    notification.error({
                        message: 'Error',
                        description: `Failed to follow ${username}.`,
                        placement: 'topRight',
                        duration: 3,
                    });
                }
            }
        },
    },
    watch: {
        users: {
            handler() {
                this.updateFollowingState();
            },
            deep: true,
            immediate: true
        }
    }
};
</script>
