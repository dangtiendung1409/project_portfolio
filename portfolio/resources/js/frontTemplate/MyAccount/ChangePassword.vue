<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <header class="header">
                            <div class="header-content">
                                <span>Change Password</span>
                            </div>
                        </header>
                        <div class="trial-info">
                            <form @submit.prevent="changePassword">
                                <div class="form-group">
                                    <label for="current-password">Current Password</label>
                                    <input type="password" id="current-password" v-model="password.current" required />
                                </div>
                                <div class="form-group">
                                    <label for="new-password">New Password</label>
                                    <input type="password" id="new-password" v-model="password.new" required />
                                </div>
                                <div class="form-group">
                                    <label for="confirm-password">Confirm New Password</label>
                                    <input type="password" id="confirm-password" v-model="password.confirm" required />
                                </div>
                                <button type="submit" class="change-button">Change Password</button>
                            </form>
                        </div>
                    </main>
                </div>
            </div>
        </template>
    </Layout>
</template>

<script>
import axios from 'axios';
import Layout from '../Layout.vue';
import Sidebar from './components/Sidebar.vue';

export default {
    name: 'ChangePassword',
    components: {
        Layout,
        Sidebar
    },
    data() {
        return {
            password: {
                current: '',
                new: '',
                confirm: ''
            }
        };
    },
    methods: {
        changePassword() {
            // Logic to handle password change
            if (this.password.new !== this.password.confirm) {
                alert("New password and confirmation do not match.");
                return;
            }
            console.log('Changing password:', this.password);
            // Send request to API to change password
        }
    }
}
</script>

<style scoped>
.my-photo-page {
    padding: 20px;
    height: calc(150vh - 100px);
    display: flex;
    flex-direction: column;
}

.content-layout {
    display: flex;
    height: 100%;
    margin-top: 50px;
}

main {
    flex: 1;
    padding-left: 0;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    background-color: #ffffff;
    padding: 48px 40px 10px;
    border-radius: 8px;
    margin: 0;
}

.header-content {
    display: flex;
    flex-direction: column;
}

.header-content span {
    font-size: 24px;
    line-height: 28px;
    font-weight: bold;
    text-transform: none;
    margin: 0px;
    color: rgb(34, 34, 34);
}

.trial-info {
    margin-top: 30px;
    padding: 20px;
    text-align: left;
    background-color: #fff;
    flex-shrink: 0;
    border-radius: 8px;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.change-button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.change-button:hover {
    background-color: #0056b3;
}
</style>
