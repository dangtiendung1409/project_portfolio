import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider.js';

export const useCommentStore = defineStore('commentStore', {
    state: () => ({
        comments: [],
    }),
    actions: {
        async fetchComments(token) {
            try {
                // Không cần header Authorization vì API này không yêu cầu xác thực
                const response = await axios.get(`${getUrlList().getCommentsByPhotoToken}/${token}`);
                this.comments = response.data.data;
            } catch (error) {
                console.error("Error fetching comments:", error);
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
                        Authorization: `Bearer ${tokenFromLocalStorage}`
                    }
                });

                // Gọi lại fetchComments để cập nhật danh sách comments
                await this.fetchComments(photoToken);

                return response.data.comment; // Trả về comment để xử lý thêm nếu cần
            } catch (error) {
                console.error("Error posting comment:", error);
            }
        },
    },
});
