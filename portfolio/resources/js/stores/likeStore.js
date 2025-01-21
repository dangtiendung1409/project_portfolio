// src/stores/likeStore.js
import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider';

export const useLikeStore = defineStore('likeStore', {
    state: () => ({
        likedPhotos: []
    }),
    actions: {
        async fetchLikedPhotos() {
            try {
                const response = await axios.get(getUrlList().getLikedPhotos, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                this.likedPhotos = response.data.data.map(like => like.photo_id);
            } catch (error) {
                console.error('Failed to fetch liked photos:', error.response.data);
                throw error;
            }
        },
        async likePhoto(photoId, photoUserId) {
            try {
                const response = await axios.post(
                    getUrlList().likePhoto,
                    {
                        photo_id: photoId,
                        photo_user_id: photoUserId,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    }
                );
                this.likedPhotos.push(photoId);
                return response.data;
            } catch (error) {
                console.error('Failed to like photo:', error.response?.data || error.message);
                throw error;
            }
        },
        async unlikePhoto(photoId) {
            try {
                const response = await axios.post(getUrlList().unlikePhoto, { photo_id: photoId }, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                this.likedPhotos = this.likedPhotos.filter(id => id !== photoId);
                return response.data;
            } catch (error) {
                console.error('Failed to unlike photo:', error.response.data);
                throw error;
            }
        }
    }
});
