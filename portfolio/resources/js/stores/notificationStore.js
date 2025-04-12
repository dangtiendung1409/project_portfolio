import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider.js';
import { formatDistanceToNow } from 'date-fns';

export const useNotificationStore = defineStore('notificationStore', {
    state: () => ({
        notifications: [], // Danh sách thông báo đã tải
        unreadCount: 0,
        currentPage: 1, // Trang hiện tại
        lastPage: 1, // Trang cuối cùng
        loading: false, // Trạng thái đang tải
    }),
    actions: {
        async fetchNotifications(page = 1) {
            if (this.loading) return;
            this.loading = true;

            try {
                const token = localStorage.getItem('token');
                if (!token) return;

                const response = await axios.get(getUrlList().getUserNotifications, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                    params: {
                        page: page,
                        per_page: 7,
                    },
                });

                const { data, current_page, last_page } = response.data;

                // Định dạng dữ liệu thô từ API
                const formattedNotifications = data.map(notification => ({
                    id: notification.id,
                    image: notification.user?.profile_picture || '/images/imageUserDefault.png',
                    message: notification.content,
                    time: formatDistanceToNow(new Date(notification.notification_date), {
                        addSuffix: true,
                        includeSeconds: true,
                    }).replace('about ', ''),
                    read: notification.is_read === 1,
                    type: notification.type,
                    photoToken: notification.photo ? notification.photo.photo_token : null,
                    galleryId: notification.gallery ? notification.gallery.id : null,
                    galleriesCode: notification.gallery ? notification.gallery.galleries_code : null,
                }));

                // Nối hoặc thay thế thông báo
                this.notifications = page === 1
                    ? formattedNotifications
                    : [...this.notifications, ...formattedNotifications];

                this.currentPage = current_page;
                this.lastPage = last_page;
                this.unreadCount = this.notifications.filter(notification => !notification.read).length;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
            } finally {
                this.loading = false;
            }
        },
        markAsRead(notificationId) {
            const notification = this.notifications.find(n => n.id === notificationId);
            if (notification) {
                notification.read = true;
                this.unreadCount = this.notifications.filter(n => !n.read).length;
            }
        },
        async markNotificationAsRead(notificationId) {
            try {
                const token = localStorage.getItem('token');
                if (!token) return;

                await axios.post(getUrlList().markNotificationAsRead, { notification_id: notificationId }, {
                    headers: { Authorization: `Bearer ${token}` },
                });

                this.markAsRead(notificationId);
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },
    },
});
