<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="py-1 bg-black">
                <div class="container">
                    <div class="row no-gutters d-flex align-items-start align-items-center px-md-0">
                        <div class="col-lg-12 d-block">
                            <div class="row d-flex">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END nav -->
            <div class="login-container">
                <div class="login-box">
                    <h2>Sign In</h2>
                    <form @submit.prevent="handleLogin">
                        <div v-if="errorMessage" style="color: red; margin-bottom: 15px;">
                            {{ errorMessage }}
                        </div>

                        <div class="input-group">
                            <label for="email">Email:</label>
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" v-model="email" required autofocus>
                        </div>

                        <div class="input-group">
                            <label for="password">Password:</label>
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="password" v-model="password" required>
                        </div>

                        <div style="margin-top:20px;" class="remember-password">
                            <label><input type="checkbox">Remember Me</label>
                            <a href="#">Forget Password</a>
                        </div>
                        <button type="submit" class="btn btn-primary py-3 px-5">Login</button>
                    </form>

                    <div class="create-account">
                        <router-link :to="'/register'" class="create-account">
                            <p>Create A New Account? <span style="color:#007BFF;">Sign Up</span></p>
                        </router-link>
                    </div>
                </div>
            </div>

        </template>
    </Layout>
</template>
<script>
import axios from 'axios';
import Layout from './Layout.vue'
import getUrlList from "../provider.js";
import { notification } from 'ant-design-vue';
export default {
    name: 'Login',
    components: {
        Layout,
    },
    data() {
        return {
            email: '',
            password: '',
            errorMessage: '',
        };
    },
    mounted() {
        const successMessage = localStorage.getItem("successMessage");
        if (successMessage) {
            notification.success({
                message: "Success",
                description: successMessage,
                placement: "topRight", // Hiển thị góc trên bên phải
                duration: 3, // Thời gian hiển thị (3 giây)
            });
            localStorage.removeItem("successMessage");
        }
    },
    methods: {
        async handleLogin() {
            try {
                const response = await axios.post(getUrlList().login, {
                    email: this.email,
                    password: this.password,
                });

                // Lưu token vào localStorage
                localStorage.setItem('token', response.data.token);
                localStorage.setItem('refresh_token', response.data.refresh_token);

                // Điều hướng tới route được trả về
                window.location.href = response.data.route;
            } catch (error) {
                console.error(error);
                this.errorMessage = error.response?.data?.error || 'Login failed, please try again.';
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
    text-align: left;
} .input-group input {
      width: 100%;
      padding: 8px;
      padding-left: 30px;
      border: 1px solid #ccc;
      border-radius: 3px;
  }

.input-group .input-icon {
    position: absolute;
    left: 8px;
    top: 60%;
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
