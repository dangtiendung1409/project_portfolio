
<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <header class="header">
                            <div class="header-content">
                                <span>Profile Information</span>
                            </div>
                        </header>
                        <div class="trial-info">
                            <form>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" v-model="user.username"  />
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" v-model="user.email"  />
                                </div>
                                <div class="form-group">
                                    <label for="bio">Bio</label>
                                    <textarea id="bio" v-model="user.bio" ></textarea>
                                </div>
                                <button type="button" class="edit-button" @click="enableEditing">Save Profile</button>
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
    name: 'MyAccount',
    components: {
        Layout,
        Sidebar
    },
    data() {
        return {
            user: {
                username: 'Dang Tien Dung',
                email: 'dung@example.com',
                bio: 'Lorem ipsum dolor sit amet.'
            }
        };
    },
    methods: {
        enableEditing() {
            // Logic để chuyển sang chế độ chỉnh sửa nếu cần
            console.log('Editing enabled');
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

input[type="text"],
input[type="email"],
textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

textarea {
    height: 100px; /* Chiều cao cho textarea */
}

.edit-button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.edit-button:hover {
    background-color: #0056b3;
}
</style>
