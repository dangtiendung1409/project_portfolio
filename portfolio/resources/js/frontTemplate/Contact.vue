<template>
    <Layout>
        <template v-slot:content>
            <div class="site-section">
                <div class="container">
                    <div class="row mb-5 align-items-end">
                        <div class="col-md-6" data-aos="fade-up">
                            <h2>Contact</h2>
                            <p class="mb-0">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam necessitatibus incidunt ut
                                officiis explicabo inventore.
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Form Liên Hệ -->
                        <div class="col-md-6 mb-5 mb-md-0" data-aos="fade-up">
                            <form @submit.prevent="sendMessage">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="name">Name</label>
                                        <input v-model="contactForm.name" type="text" class="form-control" id="name" required />
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="email">Email</label>
                                        <input v-model="contactForm.email" type="email" class="form-control" id="email" required />
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="subject">Subject</label>
                                        <input v-model="contactForm.subject" type="text" class="form-control" id="subject" required />
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="message">Message</label>
                                        <textarea v-model="contactForm.message" class="form-control" id="message" rows="5" required></textarea>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <input type="submit" class="readmore d-block w-100" value="Send Message" />
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Thông tin liên hệ -->
                        <div class="col-md-4 ml-auto order-2" data-aos="fade-up">
                            <!-- Bản đồ -->
                            <div class="map-container">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.132612701002!2d105.7833101!3d21.027379200000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab4d3c4db5a5%3A0xdbd331a7a71119d5!2zOCBUw7RuIFRo4bqldCBUaHV54bq_dCwgTeG7uSDEkMOsbmgsIE5hbSBU4burIExpw6ptLCBIw6AgTuG7mWk!5e0!3m2!1svi!2s!4v1741779889105!5m2!1svi!2s" width="450" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>

                            <!-- Thông tin liên hệ -->
                            <div class="contact-box">
                                <div class="contact-item">
                                    <div class="contact-title">📍 Address</div>
                                    <div class="contact-text">
                                        So 8 ton that thuyet , nam tu liem , ha noi
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-title">📞 Phone</div>
                                    <div class="contact-text">+1 232 3235 324</div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-title">📧 Email</div>
                                    <div class="contact-text">MyPortfolio@gmail.com</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Layout>
</template>

<script>
import axios from 'axios';
import { notification } from 'ant-design-vue';
import Layout from './Layout.vue';
import getUrlList from '../provider.js';

export default {
    name: "Contact",
    components: {
        Layout,
    },
    data() {
        return {
            contactForm: {
                name: '',
                email: '',
                subject: '',
                message: ''
            }
        };
    },
    methods: {
        async sendMessage() {
            try {
                await axios.post(getUrlList().sendContact, this.contactForm);
                notification.success({
                    message: "Success",
                    description: "Your message has been sent successfully!",
                });

                this.contactForm = { name: '', email: '', subject: '', message: '' }; // Reset form
            } catch (error) {
                notification.error({
                    message: "Error",
                    description: "An error occurred, please try again!",
                });
                console.error("Error while sending contact message:", error);
            }
        }
    }
};
</script>

<style scoped>
/* Giao diện tổng thể của khối thông tin liên hệ */
.contact-box {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-top: 15px;
}

/* Bản đồ */
.map-container {
    margin-bottom: 20px;
    margin-top: -150px; /* Di chuyển bản đồ lên một chút */
}

/* Từng mục thông tin liên hệ */
.contact-item {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

/* Tiêu đề từng mục (Address, Phone, Email) */
.contact-title {
    font-weight: bold;
    font-size: 18px;
    color: #333;
    margin-bottom: 5px;
}

/* Nội dung của từng mục */
.contact-text {
    font-size: 16px;
    color: #555;
}
</style>
