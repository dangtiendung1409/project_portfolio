// src/stores/reportStore.js
import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider.js';
import { notification } from 'ant-design-vue';

export const useReportStore = defineStore('report', {
    state: () => ({
        reports: [],
    }),

    actions: {
        // Gửi báo cáo
        async reportContent({ reporterId, violatorId, reason, photoId, commentId, galleryId }) {
            try {
                const payload = {
                    reporter_id: reporterId,
                    violator_id: violatorId,
                    report_reason: reason,
                    photo_id: photoId || null,
                    comment_id: commentId || null,
                    gallery_id: galleryId || null,
                };

                const response = await axios.post(getUrlList().reportViolation, payload, {
                    headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
                });

                // Lưu báo cáo vào state nếu cần
                this.reports.push(response.data.report);

                notification.success({
                    message: 'Reported successfully.',
                    description: response.data.message,
                    placement: 'topRight',
                    duration: 3,
                });

                return response.data;
            } catch (error) {
                console.error('Failed to send report:', error);
                const errorMessage = error.response?.data?.error || 'Unable to send report. Please try again.';
                notification.error({
                    message: 'Error',
                    description: errorMessage,
                    placement: 'topRight',
                    duration: 3,
                });
                throw error;
            }
        }
    }
});
