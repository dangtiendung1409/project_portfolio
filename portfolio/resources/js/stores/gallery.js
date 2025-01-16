import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider';

export const useGalleryStore = defineStore('gallery', {
    state: () => ({
        galleries: [],
        loading: false,
        error: null
    }),

    actions: {
        async fetchGalleries() {
            this.loading = true;
            this.error = null;

            // Lấy token từ localStorage hoặc nơi lưu trữ khác
            const token = localStorage.getItem('token');

            try {
                const response = await axios.get(getUrlList().getGallery, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.galleries = response.data.data;
            } catch (error) {
                this.error = error.response ? error.response.data.message : error.message;
            } finally {
                this.loading = false;
            }
        }
    }
});
