import { defineStore } from 'pinia';
import axios from 'axios';
import jwt_decode from 'jwt-decode';
import getUrlList from '../provider';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        isLoggedIn: false,
    }),
    actions: {
        async checkLoginStatus() {
            const token = localStorage.getItem('token');
            if (token) {
                try {
                    const decodedToken = jwt_decode(token);
                    const currentTime = Date.now() / 1000; // Thời gian hiện tại tính bằng giây
                    const expTime = decodedToken.exp; // Thời gian hết hạn của token

                    // Kiểm tra token đã hết hạn chưa
                    if (expTime > currentTime) {
                        this.isLoggedIn = true;
                        const remainingTime = expTime - currentTime; // Thời gian còn lại trước khi token hết hạn

                        // Gọi refreshToken trước khi token hết hạn 1 phút
                        setTimeout(async () => {
                            await this.refreshToken();
                        }, (remainingTime - 60) * 1000);
                    } else {
                        this.isLoggedIn = false;
                        localStorage.removeItem('token');
                    }
                } catch (error) {
                    console.error('Token decode error:', error);
                    this.isLoggedIn = false;
                    localStorage.removeItem('token');
                }
            } else {
                this.isLoggedIn = false;
            }
        },
        async refreshToken() {
            try {
                const refreshToken = localStorage.getItem('refresh_token');
                if (!refreshToken) {
                    // Nếu không có refresh_token thì yêu cầu đăng nhập lại
                    alert('Token does not exist');
                    return;
                }
                const response = await axios.post(getUrlList().refreshToken, {}, {
                    headers: { Authorization: `Bearer ${refreshToken}` },
                });

                const newToken = response.data.token;
                localStorage.setItem('token', newToken);
                this.checkLoginStatus(); // Kiểm tra lại trạng thái đăng nhập
            } catch (error) {
                console.error('Failed to refresh token:', error.response?.data || error.message);
                this.handleLogout();
            }
        },
        async handleLogout() {
            try {
                const token = localStorage.getItem('token');
                if (token) {
                    await axios.post(getUrlList().logout, {}, {
                        headers: { Authorization: `Bearer ${token}` }
                    });
                }
                localStorage.removeItem('token');
                localStorage.removeItem('refresh_token');
                this.isLoggedIn = false;
                window.location.href = '/login';
            } catch (error) {
                console.error('Logout failed:', error);
            }
        }
    }
});
