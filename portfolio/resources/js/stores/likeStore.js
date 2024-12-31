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
                this.likedPhotos = response.data.data.map(like => like.photo_image_id);
            } catch (error) {
                console.error('Failed to fetch liked photos:', error.response.data);
                throw error;
            }
        },
        async likePhoto(photoImageId, photoUserId) {
            try {
                const response = await axios.post(
                    getUrlList().likePhoto,
                    {
                        photo_image_id: photoImageId,
                        photo_user_id: photoUserId,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    }
                );
                this.likedPhotos.push(photoImageId);
                return response.data;
            } catch (error) {
                console.error('Failed to like photo:', error.response?.data || error.message);
                throw error;
            }
        },
        async unlikePhoto(photoImageId) {
            try {
                const response = await axios.post(getUrlList().unlikePhoto, { photo_image_id: photoImageId }, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                this.likedPhotos = this.likedPhotos.filter(id => id !== photoImageId);
                return response.data;
            } catch (error) {
                console.error('Failed to unlike photo:', error.response.data);
                throw error;
            }
        }
    }
});
