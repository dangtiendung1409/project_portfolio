<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="py-1 bg-black">
                <div class="container">
                    <div class="row no-gutters d-flex align-items-start align-items-center px-md-0">
                        <div class="col-lg-12 d-block">
                            <div class="row d-flex">
                                <div class="col-md pr-4 d-flex topper align-items-center">
                                    <div class="icon mr-2 d-flex justify-content-center align-items-center"><span class="icon-phone2"></span></div>
                                    <span class="text">+ 1235 2355 98</span>
                                </div>
                                <div class="col-md pr-4 d-flex topper align-items-center">
                                    <div class="icon mr-2 d-flex justify-content-center align-items-center"><span class="icon-paper-plane"></span></div>
                                    <span class="text">youremail@email.com</span>
                                </div>
                                <div class="col-md-5 pr-4 d-flex topper align-items-center text-lg-right">
                                    <span class="text">3-5 Business days delivery &amp; Free Returns</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="login-container">
                <div class="login-box">
                    <h2>Sign Up</h2>

                    <form @submit.prevent="handleRegister">
                        <div class="input-group">
                            <label for="username">User Name:</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="username" v-model="username" required>
                            </div>
                            <span v-if="errors.username && errors.username.length" style="color:red">{{ errors.username[0] }}</span>
                        </div>

                        <div class="input-group">
                            <label for="email">Email:</label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="email" v-model="email" required>
                            </div>
                            <span v-if="errors.email && errors.email.length" style="color:red">{{ errors.email[0] }}</span>
                        </div>


                        <div class="input-group">
                            <label for="password">Password:</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="password" v-model="password" required>
                            </div>
                            <span v-if="errors.password && errors.password.length" style="color:red">{{ errors.password[0] }}</span>
                        </div>

                        <div class="input-group">
                            <label for="password_confirmation">Confirm Password:</label>
                            <div class="input-wrapper">
                                 <i class="fas fa-lock input-icon"></i>
                                 <input type="password" id="password_confirmation" v-model="password_confirmation" required>
                            </div>
                            <span v-if="errors.password_confirmation && errors.password_confirmation.length" style="color:red">{{ errors.password_confirmation[0] }}</span>
                        </div>
                        <div style="margin-top:20px;" class="remember-password">
                            <label><input type="checkbox">Remember Me</label>
                            <a href="#">Forget Password</a>
                        </div>

                        <button type="submit" class="btn btn-primary py-3 px-5">Register</button>

                        <div class="create-account">
                            <router-link :to="'/login'" class="create-account">
                                <p>Already have an account? <span style="color:#007BFF;">Sign In</span></p>
                            </router-link>
                        </div>
                    </form>
                </div>
            </div>

        </template>
    </Layout>
</template>
<script>
import axios from 'axios';
import Layout from './Layout.vue'
import getUrlList from "../provider.js";
import { notification } from "ant-design-vue";
export default {
    name: 'Login',
    components: {
        Layout,
    },
    data() {
        return {
            username: '',
            email: '',
            password: '',
            password_confirmation: '',
            errors: {
                username: [],
                email: [],
                password: [],
                password_confirmation: []
            }
        };
    },
    methods: {
        async handleRegister() {
            try {
                const response = await axios.post(getUrlList().register, {
                    username: this.username,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                });
                notification.success({
                    message: "Success",
                    description: "Registration successful! Please login to continue.",
                    placement: "topRight", // Góc trên bên phải
                    duration: 3, // Hiển thị 3 giây
                });
                this.$router.push("/login");
            } catch (error) {
                console.error('Registration failed', error);
                if (error.response && error.response.data.errors) {
                    // Lấy các lỗi từ phản hồi của server
                    this.errors = error.response.data.errors;
                } else {
                    alert('Registration failed. Please check your input.');
                }
            }
        }
    }
}
</script>
<style scoped>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    margin-top: 50px;
    margin-bottom: 70px;
}

.login-box {
    background-color: #fff;
    padding: 40px;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 450px;
    margin: 20px;
}

.login-box h2 {
    margin-bottom: 20px;
    color: #333;
}

.input-group {
    position: relative;
    margin-bottom: 15px;
    display: flex;
    flex-direction: column; /* Chuyển hướng các phần tử con theo chiều dọc */
    align-items: flex-start; /* Canh trái để label và input thẳng hàng */
}
.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-wrapper input {
    width: 100%;
    padding: 8px;
    padding-left: 30px; /* Khoảng trống cho icon */
    border: 1px solid #ccc;
    border-radius: 3px;
}

.input-wrapper .input-icon {
    position: absolute;
    left: 8px;

    transform: translateY(-50%);
    pointer-events: none;
}
.input-group label {
    margin-bottom: 5px; /* Khoảng cách giữa label và input */
}
.input-group input {
      width: 370px;
      padding: 8px;
      padding-left: 35px;
      border: 1px solid #ccc;
      border-radius: 3px;
  }
.input-group span {
    display: block;
    margin-top: 5px;
    font-size: 15px;
    color: red;
}
.input-group .input-icon {
    position: absolute;
    left: 8px;
    top: 45%;
}

.remember-password {
    font-size: 14px;
    font-weight: 500;
    margin: -15px 0 15px;
    display: flex;
    justify-content: space-between;
}

.remember-password label input {
    accent-color: #fff;
    margin-right: 3px;
}
.remember-password a {
    text-decoration: none;
}

.remember-password a:hover {
    text-decoration: underline;
}

.create-account {
    font-size: 14.5px;
    text-align: center;
    margin: 25px;
}

.create-account p a {
    color: #fff;
    font-weight: 600;
    text-decoration: none;
}

.create-account p a:hover {
    text-decoration: underline;
}
button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 15px;
    cursor: pointer;
    width: 100%;
}

button:hover {
    background-color: #0056b3;
}
</style>
