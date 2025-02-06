
import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider.js';

export const useUserStore = defineStore('user', {
    state: () => ({
        user: {
            username: '',
            email: '',
            bio: '',
            profile_picture: null,
            cover_photo: null, // Thêm ảnh bìa
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
        async updateUserProfile(userData, profileFile, coverFile) {
            const formData = new FormData();
            formData.append('username', userData.username);
            formData.append('email', userData.email);
            formData.append('bio', userData.bio);

            if (profileFile) {
                formData.append('profile_picture', profileFile);
            }
            if (coverFile) {
                formData.append('cover_photo', coverFile);
            }

            try {
                const response = await axios.post(getUrlList().updateProfile, formData, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`,
                        'Content-Type': 'multipart/form-data',
                    },
                });

                this.user = response.data.user;
                localStorage.setItem('successMessage', 'Profile updated successfully!');
            } catch (error) {
                console.error('Failed to update profile:', error.response?.data || error);
                throw error;
            }
        },
    },
});

