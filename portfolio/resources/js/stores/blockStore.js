import { defineStore } from 'pinia';
import getUrlList from '../provider';
import axios from 'axios';
import { notification } from 'ant-design-vue';

export const useBlockStore = defineStore('block', {
    state: () => ({
        blockedUsers: [], // Danh sách ID của những người bị chặn
        blockedUsersFullInfo: [], // Danh sách đầy đủ thông tin của những người bị chặn
    }),

    actions: {
        // Lấy danh sách những người bị chặn
        async fetchBlockedUsers() {
            const token = localStorage.getItem('token');
            if (!token) {
                notification.error({
                    message: 'Lỗi',
                    description: 'Không tìm thấy token, vui lòng đăng nhập.',
                    placement: 'topRight',
                    duration: 3,
                });
                return;
            }

            try {
                const response = await axios.get(getUrlList().getBlockedUsers, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                // Lưu danh sách ID của những người bị chặn
                this.blockedUsers = response.data.map(user => user.id) || [];

                // Lưu toàn bộ thông tin người dùng bị chặn
                this.blockedUsersFullInfo = response.data || [];

            } catch (error) {
                console.error('Error fetching blocked users:', error);
                notification.error({
                    message: 'Lỗi',
                    description: error.response?.data?.message || 'Không thể lấy danh sách người bị chặn.',
                    placement: 'topRight',
                    duration: 3,
                });
            }
        },

        // Chặn người dùng
        async blockUser(userId) {
            try {
                await axios.post(getUrlList().blockUser, { blocked_id: userId }, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });

                // Cập nhật danh sách sau khi chặn
                await this.fetchBlockedUsers();

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

                // Cập nhật cả hai danh sách: blockedUsers và blockedUsersFullInfo
                this.blockedUsers = this.blockedUsers.filter(id => id !== userId);
                this.blockedUsersFullInfo = this.blockedUsersFullInfo.filter(user => user.id !== userId);

            } catch (error) {
                console.error('Error unblocking user:', error);
                throw error;
            }
        }
    }
});
