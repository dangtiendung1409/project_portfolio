// src/stores/userStore.js
import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider.js';

export const useUserStore = defineStore('user', {
    state: () => ({
        user: {
            username: '',
            email: '',
            bio: '',
            profile_picture: null, // default image
        },
    }),
    actions: {
        async fetchUserData() {
            try {
                const response = await axios.get(getUrlList().getUser, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                    },
                });
                if (response.data.user) {
                    this.user = response.data.user;
                }
            } catch (error) {
                console.error('Error fetching user data:', error);
            }
        },
        async updateUserProfile(userData, file) {
            const formData = new FormData();
            formData.append('username', userData.username);
            formData.append('email', userData.email);
            formData.append('bio', userData.bio);

            if (file) {
                formData.append('profile_picture', file);
            }

            try {
                const response = await axios.post(getUrlList().updateProfile, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'multipart/form-data',
                    },
                });

                // Update user data in store after successful update
                this.user = response.data.user;

                // Optionally, notify other parts of the app (e.g., using localStorage or eventBus)
                localStorage.setItem('successMessage', 'Profile updated successfully!');
            } catch (error) {
                console.error('Failed to update profile:', error.response?.data || error);
                throw error; // Ném lỗi để xử lý ở component
            }
        },
    },
});
