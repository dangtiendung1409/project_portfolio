import { defineStore } from 'pinia';
import getUrlList from '../provider';
import axios from 'axios';

export const useFollowStore = defineStore('follow', {
    state: () => ({
        followingList: [], // Danh sách những người đang theo dõi
    }),

    actions: {
        async fetchFollowingList() {
            try {
                const response = await axios.get(getUrlList().getFollowingList, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });

                // Lấy danh sách ID của những người đang theo dõi
                this.followingList = response.data.map(user => user.pivot.following_id);

            } catch (error) {
                console.error('Error fetching following list:', error);
            }
        },

        async followUser(userId) {
            try {
                const response = await axios.post(getUrlList().followUser, { following_id: userId }, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });
                this.followingList.push(userId); // Thêm user vào danh sách following
                return response.data;
            } catch (error) {
                console.error('Error following user:', error);
                throw error;
            }
        },

        async unfollowUser(userId) {
            try {
                await axios.post(getUrlList().unfollowUser(userId), {}, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });
                this.followingList = this.followingList.filter(id => id !== userId); // Xóa user khỏi danh sách following
            } catch (error) {
                console.error('Error unfollowing user:', error);
                throw error;
            }
        }
    }
});
