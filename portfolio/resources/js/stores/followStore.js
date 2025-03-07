import { defineStore } from 'pinia';
import getUrlList from '../provider';
import axios from 'axios';

export const useFollowStore = defineStore('follow', {
    state: () => ({
        followingList: [],  // Danh sách ID user mà mình đang follow
        followersList: [],  // Danh sách ID user nào đang follow mình
        userFollowingList: [], // Danh sách object user mà user đang follow
        userFollowersList: []  // Danh sách object user đang theo dõi user
    }),

    actions: {
        async fetchFollowingList() {
            try {
                const response = await axios.get(getUrlList().getFollowingList, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });
                this.followingList = response.data.map(user => user.pivot.following_id);
            } catch (error) {
                console.error('Error fetching following list:', error);
            }
        },

        async fetchFollowersList() {
            try {
                const response = await axios.get(getUrlList().getFollowersList, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });
                this.followersList = response.data.map(user => user.id);
            } catch (error) {
                console.error('Error fetching followers list:', error);
            }
        },

        async fetchUserFollowingList(username) {
            try {
                const response = await axios.get(getUrlList().getFollowingUser(username));
                if (Array.isArray(response.data)) {
                    // Lưu toàn bộ object user
                    this.userFollowingList = response.data.map(user => ({
                        id: user.id,
                        username: user.username,
                        name: user.name,
                        profile_picture: user.profile_picture,
                        followers_count: user.followers_count || 0 // Giả sử API có followers_count, nếu không thì mặc định 0
                    }));
                } else {
                    console.warn(`Expected an array but got:`, response.data);
                    this.userFollowingList = [];
                }
            } catch (error) {
                console.error(`Error fetching following list for ${username}:`, error);
                this.userFollowingList = [];
            }
        },

        async fetchUserFollowersList(username) {
            try {
                const response = await axios.get(getUrlList().getFollowersUser(username));
                if (Array.isArray(response.data)) {
                    // Lưu toàn bộ object user
                    this.userFollowersList = response.data.map(user => ({
                        id: user.id,
                        username: user.username,
                        name: user.name,
                        profile_picture: user.profile_picture,
                        followers_count: user.followers_count || 0 // Giả sử API có followers_count, nếu không thì mặc định 0
                    }));
                } else {
                    console.warn(`Expected an array but got:`, response.data);
                    this.userFollowersList = [];
                }
            } catch (error) {
                console.error(`Error fetching followers list for ${username}:`, error);
                this.userFollowersList = [];
            }
        },

        async followUser(userId, targetUsername) {
            try {
                const response = await axios.post(getUrlList().followUser,
                    { following_id: userId },
                    { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
                );
                this.followingList.push(userId);
                // Cập nhật danh sách followers của user được follow
                if (targetUsername) {
                    await this.fetchUserFollowersList(targetUsername);
                }
                return response.data;
            } catch (error) {
                console.error('Error following user:', error);
                throw error;
            }
        },

        async unfollowUser(userId, targetUsername) {
            try {
                await axios.post(getUrlList().unfollowUser(userId), {}, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });
                this.followingList = this.followingList.filter(id => id !== userId);
                // Cập nhật danh sách followers của user được unfollow
                if (targetUsername) {
                    await this.fetchUserFollowersList(targetUsername);
                }
            } catch (error) {
                console.error('Error unfollowing user:', error);
                throw error;
            }
        }
    }
});
