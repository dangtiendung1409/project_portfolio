<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <header class="header">
                            <div class="header-content">
                                <span>Privacy</span>
                            </div>
                        </header>
                        <div class="trial-info">
                            <h3>Blocked Users</h3>
                            <p class="description">
                                You will not see photos, galleries, or comments from these blocked users.
                                Users will not know they have been blocked.
                            </p>
                            <ul class="blocked-users-list">
                                <li v-for="user in blockedUsersList" :key="user.id" class="user-item">
                                    <img :src="user.profile_picture ? `http://127.0.0.1:8000${user.profile_picture}` : '/default-avatar.jpg'" :alt="user.username" class="user-photo" />
                                    <div class="user-details">
                                        <span class="user-name">{{ user.name }}</span>
                                        <span class="user-name">@{{ user.username }}</span>
                                    </div>
                                    <button @click="confirmUnblockUser(user)" class="unblock-button">Unblock</button>
                                </li>
                            </ul>
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
import { useBlockStore } from '@/stores/blockStore.js';
import { Modal, notification } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { h, computed } from 'vue';

export default {
    name: 'ListUserBlock',
    components: {
        Layout,
        Sidebar
    },
    data() {
        return {
            blockStore: useBlockStore(),
        };
    },
    computed: {
        blockedUsersList() {
            return this.blockStore.blockedUsersFullInfo;
        }
    },
    async mounted() {
        await this.blockStore.fetchBlockedUsers();
    },
    methods: {
        confirmUnblockUser(user) {
            Modal.confirm({
                title: 'Are you sure you want to block this user?',
                icon: h(ExclamationCircleOutlined),
                content: `You will unblock ${user.username}. They will be able to interact with you and view your content.`,
                okText: 'Yes',
                cancelText: 'No',
                onOk: () => this.unblockUser(user),
            });
        },
        async unblockUser(user) {
            try {
                await this.blockStore.unblockUser(user.id);
                notification.success({
                    message: 'Success',
                    description: `User ${user.username} has been unblocked.`,
                    placement: 'topRight',
                    duration: 3,
                });
                // Gọi lại fetchBlockedUsers để đảm bảo dữ liệu được đồng bộ với backend
                await this.blockStore.fetchBlockedUsers();
            } catch (error) {
                notification.error({
                    message: 'Error',
                    description: 'Unable to unblock user.',
                    placement: 'topRight',
                    duration: 3,
                });
            }
        }
    }
};
</script>

<style scoped>
.my-photo-page {
    padding: 20px;
    height: calc(150vh - 100px);
    display: flex;
    flex-direction: column;
}

.content-layout {
    display: flex;
    height: 100%;
    margin-top: 50px;
}

main {
    flex: 1;
    padding-left: 0;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    background-color: #ffffff;
    padding: 48px 40px 10px;
    border-radius: 8px;
    margin: 0;
}
.description {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 15px;
    line-height: 1.5;
}

.header-content {
    display: flex;
    flex-direction: column;
}

.header-content span {
    font-size: 24px;
    line-height: 28px;
    font-weight: bold;
    color: rgb(34, 34, 34);
}

.trial-info {
    margin-top: 30px;
    width: 50%;
    max-width: 600px;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
}

.blocked-users-list {
    list-style: none;
    padding: 0;
}

.user-item {
    display: flex;
    align-items: center;
    justify-content: space-between; /* Đẩy nút về phía bên phải */
    padding: 10px 0;
    border-bottom: 1px solid #eaeaea;
    width: 100%;
}

.user-photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 15px;
}



.user-name {
    font-weight: bold;
}

.username {
    color: #888;
}

.unblock-button {
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
    margin-left: 200px;
    background-color: rgb(255, 255, 255);
    border-color: rgb(8, 112, 209);
    color: rgb(8, 112, 209);
}
.unblock-button:hover {
    background-color: #f0f0f0;
}
</style>
