// src/stores/likeStore.js
import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider';

export const useLikeStore = defineStore('likeStore', {
    state: () => ({
        likedPhotos: [],
        likedGalleries: []
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
        async fetchLikedGalleries() {
            try {
                const response = await axios.get(getUrlList().getLikedGalleries, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`
                    }
                });
                this.likedGalleries = response.data.data.map(like => like.gallery_id);
            } catch (error) {
                console.error('Failed to fetch liked galleries:', error.response?.data || error.message);
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
        },
        async likeGallery(galleryId, galleryUserId) {
            try {
                const response = await axios.post(
                    getUrlList().likeGallery,
                    {
                        gallery_id: galleryId,
                        gallery_user_id: galleryUserId,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    }
                );
                this.likedGalleries.push(galleryId);
                return response.data;
            } catch (error) {
                console.error('Failed to like gallery:', error.response?.data || error.message);
                throw error;
            }
        },
        async unlikeGallery(galleryId) {
            try {
                const response = await axios.post(
                    getUrlList().unlikeGallery,
                    { gallery_id: galleryId },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem('token')}`
                        }
                    }
                );
                this.likedGalleries = this.likedGalleries.filter(id => id !== galleryId);
                return response.data;
            } catch (error) {
                console.error('Failed to unlike gallery:', error.response?.data || error.message);
                throw error;
            }
        }
    }
});
