import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider.js';

export const useCommentStore = defineStore('commentStore', {
    state: () => ({
        comments: [], // Danh sách bình luận đã tải
        currentPage: 1, // Trang hiện tại
        lastPage: 1, // Trang cuối cùng
        total: 0, // Tổng số bình luận
        loading: false, // Trạng thái đang tải
    }),
    actions: {
        async fetchComments(token, page = 1) {
            if (this.loading) return;
            this.loading = true;

            try {
                const response = await axios.get(`${getUrlList().getCommentsByPhotoToken}/${token}`, {
                    params: {
                        page: page,
                        per_page: 3,
                    },
                });

                const { data, current_page, last_page, total } = response.data;

                // Nối hoặc thay thế bình luận
                this.comments = page === 1 ? data : [...this.comments, ...data];
                this.currentPage = current_page;
                this.lastPage = last_page;
                this.total = total;
            } catch (error) {
                console.error("Error fetching comments:", error);
            } finally {
                this.loading = false;
            }
        },
        async postComment(photoToken, commentText) {
            try {
                const tokenFromLocalStorage = localStorage.getItem("token");
                const response = await axios.post(getUrlList().postComment, {
                    photo_token: photoToken,
                    comment_text: commentText,
                }, {
                    headers: {
                        Authorization: `Bearer ${tokenFromLocalStorage}`,
                    },
                });

                // Tải lại bình luận từ trang 1 để cập nhật
                await this.fetchComments(photoToken, 1);

                return response.data.comment;
            } catch (error) {
                console.error("Error posting comment:", error);
            }
        },
        async deleteComment(commentId, photoToken) {
            try {
                const tokenFromLocalStorage = localStorage.getItem("token");
                await axios.delete(`${getUrlList().deleteComment(commentId)}`, {
                    headers: {
                        Authorization: `Bearer ${tokenFromLocalStorage}`,
                    },
                });

                // Tải lại bình luận từ trang 1
                await this.fetchComments(photoToken, 1);
            } catch (error) {
                console.error("Error deleting comment:", error);
            }
        },
    },
});
