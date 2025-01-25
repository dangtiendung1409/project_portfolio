import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider';
import { notification } from 'ant-design-vue';

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
        },
        async addPhotoToGallery(gallery_id, photo_id) {
            this.loading = true;
            this.error = null;

            // Lấy token từ localStorage hoặc nơi lưu trữ khác
            const token = localStorage.getItem('token');

            try {
                const response = await axios.post(getUrlList().addPhotoToGallery, {
                    gallery_id,
                    photo_id
                }, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                // Cập nhật gallery với ảnh mới
                const updatedGallery = response.data.gallery;
                const index = this.galleries.findIndex(g => g.id === updatedGallery.id);
                if (index !== -1) {
                    this.galleries[index] = updatedGallery;
                } else {
                    this.galleries.push(updatedGallery);
                }

                // Hiển thị thông báo thành công
                notification.success({
                    message: 'Success',
                    description: response.data.message,
                });
            } catch (error) {
                this.error = error.response ? error.response.data.message : error.message;
            } finally {
                this.loading = false;
            }
        }
    }
});
