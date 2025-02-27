import { defineStore } from 'pinia';
import getUrlList from '../provider';
import axios from 'axios';

export const useBlockStore = defineStore('block', {
    state: () => ({
        blockedUsers: [], // Danh sách những người bị chặn
    }),

    actions: {
        // Lấy danh sách những người bị chặn
        async fetchBlockedUsers() {
            try {
                const response = await axios.get(getUrlList().getBlockedUsers, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });

                // Lưu danh sách ID của những người bị chặn
                this.blockedUsers = response.data.map(user => user.id);

            } catch (error) {
                console.error('Error fetching blocked users:', error);
            }
        },

        // Chặn người dùng
        async blockUser(userId) {
            try {
                await axios.post(getUrlList().blockUser, { blocked_id: userId }, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });

                this.blockedUsers.push(userId); // Thêm vào danh sách người bị chặn
            } catch (error) {
                console.error('Error blocking user:', error);
                throw error;
            }
        },

        // Bỏ chặn người dùng
        async unblockUser(userId) {
            try {
                await axios.post(getUrlList().unblockUser, { blocked_id: userId }, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });

                // Xóa user khỏi danh sách blocked
                this.blockedUsers = this.blockedUsers.filter(id => id !== userId);
            } catch (error) {
                console.error('Error unblocking user:', error);
                throw error;
            }
        }
    }
});
