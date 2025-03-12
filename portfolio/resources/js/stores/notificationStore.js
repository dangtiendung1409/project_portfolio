import { defineStore } from 'pinia';
import axios from 'axios';
import getUrlList from '../provider.js';
import { formatDistanceToNow } from 'date-fns';

export const useNotificationStore = defineStore('notificationStore', {
    state: () => ({
        notifications: [],
        unreadCount: 0,
    }),
    actions: {
        async fetchNotifications() {
            try {
                const token = localStorage.getItem('token');
                if (!token) return;

                const response = await axios.get(getUrlList().getUserNotifications, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                console.log('API response:', response.data);

                this.notifications = response.data.map(notification => ({
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

                this.unreadCount = this.notifications.filter(notification => !notification.read).length;
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
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

                // Update state
                this.markAsRead(notificationId);
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        },
    },
});
