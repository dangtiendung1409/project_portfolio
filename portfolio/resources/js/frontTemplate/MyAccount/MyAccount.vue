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
                            <div class="form-container">
                                <form>
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" id="username" v-model="tempUser.username" />
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" id="email" v-model="tempUser.email" />
                                    </div>
                                    <div class="form-group">
                                        <label for="bio">Bio</label>
                                        <textarea id="bio" v-model="tempUser.bio"></textarea>
                                    </div>
                                    <button type="button" class="edit-button" @click="saveProfile">Save Profile</button>
                                </form>
                            </div>
                            <div class="upload-container">
                                <div class="photo-circle">
                                    <img :src="newProfilePhoto || user.profile_picture || '/images/imageUserDefault.png'" class="profile-photo" alt="Profile Photo" />
                                </div>
                                <input type="file" ref="fileInput" @change="handleFileUpload" style="display: none;" />
                                <button class="upload-button" @click="triggerFileInput">Upload Photo</button>
                                <p class="file-restrictions">Maximum file size: 1 MB<br />Formats: .JPEG, .PNG</p>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </template>
    </Layout>
</template>

<script>
import { useUserStore } from '@/stores/userStore';
import Layout from '../Layout.vue';
import Sidebar from './components/Sidebar.vue';
import { notification } from 'ant-design-vue';

export default {
    name: 'MyAccount',
    components: {
        Layout,
        Sidebar
    },
    data() {
        return {
            tempUser: {
                username: '',
                email: '',
                bio: '',
            },
            newProfilePhoto: '', // Ảnh đã tải lên tạm thời
            uploadedPhoto: '', // Ảnh đã tải lên
            errors: {} // Để lưu trữ lỗi từ server
        };
    },
    computed: {
        user() {
            const store = useUserStore();
            return store.user;
        },
    },
    created() {
        this.fetchUserData();
    },
    methods: {
        async fetchUserData() {
            const store = useUserStore();
            await store.fetchUserData();
            this.tempUser = { ...store.user }; // Sao chép thông tin từ user vào tempUser
        },
        async saveProfile() {
            const store = useUserStore();
            const file = this.$refs.fileInput.files[0];
            try {
                await store.updateUserProfile(this.tempUser, file);

                // Display success message
                notification.success({
                    message: 'Success',
                    description: 'Profile updated successfully!',
                    placement: 'topRight',
                    duration: 3,
                });

                // Reset newProfilePhoto after saving
                this.newProfilePhoto = '';
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    // Xử lý lỗi từ server
                    this.errors = error.response.data.errors;

                    // Hiển thị thông báo lỗi
                    for (const key in this.errors) {
                        if (this.errors.hasOwnProperty(key)) {
                            notification.error({
                                message: 'Error',
                                description: this.errors[key].join(', '),
                                placement: 'topRight',
                                duration: 5,
                            });
                        }
                    }
                } else {
                    // Xử lý lỗi không mong muốn
                    notification.error({
                        message: 'Error',
                        description: 'An unexpected error occurred.',
                        placement: 'topRight',
                        duration: 5,
                    });
                }
            }
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.newProfilePhoto = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
    },
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
    color: rgb(34, 34, 34);
}

.trial-info {
    margin-top: 30px;
    display: flex; /* Thay đổi để tạo layout hai cột */
    padding: 20px;
    text-align: left;
    background-color: #fff;
    flex-shrink: 0;
    border-radius: 8px;
}

.form-container {
    flex: 1; /* Chiếm không gian bên trái */
    margin-right: 20px; /* Khoảng cách giữa hai cột */
}

.upload-container {
    width: 300px; /* Chiều rộng cố định cho phần tải ảnh */
    display: flex;
    flex-direction: column;
    align-items: center;
}

.photo-circle {
    width: 100px; /* Đường kính vòng tròn */
    height: 100px; /* Đường kính vòng tròn */
    border-radius: 50%; /* Tạo hình tròn */
    overflow: hidden; /* Ẩn các phần ngoài vòng tròn */
    border: 2px solid #ccc;
    display: flex;
    justify-content: center;
    align-items: center;
}

.profile-photo {

    width: 100%; /* Đảm bảo ảnh vừa khít */
    height: auto; /* Giữ tỷ lệ ảnh */
}

.default-photo {
    width: 100%; /* Đảm bảo không ảnh sẽ khít */
    height: auto; /* Giữ tỷ lệ ảnh */
    text-align: center;
    line-height: 100px; /* Căn giữa nội dung */
    color: #aaa;
}

.upload-button {
    margin-top: 25px;
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.upload-button:hover {
    background-color: #0056b3;
}

.file-restrictions {
    margin-top: 25px;
    font-size: 14px;
    color: #555;
    text-align: center;
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
    height: 100px;
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
