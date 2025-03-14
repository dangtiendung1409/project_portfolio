<template>
    <Layout>
        <template v-slot:content>
            <div class="blog-container">
                <!-- Full-Screen Image Section -->
                <section class="full-screen-image">
                    <img src="/images/covers/covers_0.jpeg" alt="Full Screen" class="image" />
                    <div class="image-container">
                        <h2>Mastering Light and Perspective in Urban Architecture</h2>
                        <p>Published by MyPortfolio Blog - 7 days ago</p>
                        <p>Architectural photography goes beyond capturing buildings...</p>
                    </div>
                </section>

                <!-- Featured Article -->
                <section class="featured" v-for="blog in latestBlogs" :key="blog.id">
                    <img :src="blog.cover_image" alt="Featured Article" class="featured-image" />
                    <div class="featured-content">
                        <h2>{{ blog.title }}</h2>
                        <p>Published by MyPortfolio Blog - {{ timeAgo(blog.created_at) }}</p>
                        <div v-html="blog.content"></div>
                        <button class="read-more" @click="goToBlog(blog.slug)">Keep reading</button>
                    </div>
                </section>

                <!-- Latest Posts -->
                <section class="latest-posts">
                    <h2>Latest Posts</h2>
                    <div class="posts-grid">
                        <div class="post" v-for="blog in olderBlogs" :key="blog.id" @click="goToBlog(blog.slug)">
                            <img :src="blog.cover_image" alt="Post Image" class="post-image" />
                            <h3>{{ blog.title }}</h3>
                            <p>MyPortfolio Blog - {{ timeAgo(blog.created_at) }}</p>
                        </div>
                    </div>
                </section>
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
    name: "Blog",
    components: {
        Layout,
    },
    data() {
        return {
            featuredBlog: {},  // Blog mới nhất cho Featured section
            olderBlogs: [],    // Các blog cũ hơn cho Latest Posts
        };
    },
    mounted() {
        this.fetchLatestBlogs();  // Lấy 5 blog gần nhất
        this.fetchOlderBlogs();   // Lấy blog cũ hơn
    },
    methods: {
        async fetchLatestBlogs() {
            try {
                const response = await axios.get(getUrlList().getLatestBlogs);
                if (response.data.status === 'success' && response.data.blogs.length > 0) {
                    this.latestBlogs = response.data.blogs; // Lưu toàn bộ 5 blog từ API
                } else {
                    throw new Error('No latest blogs found');
                }
            } catch (error) {
                console.error('Error fetching latest blogs:', error);
                notification.error({
                    message: 'Error',
                    description: 'Failed to load latest blogs.',
                });
            }
        },
        async fetchOlderBlogs() {
            try {
                const response = await axios.get(getUrlList().getOlderBlogs);
                if (response.data.status === 'success') {
                    this.olderBlogs = response.data.blogs; // Lấy danh sách blog cũ
                } else {
                    throw new Error('No older blogs found');
                }
            } catch (error) {
                console.error('Error fetching older blogs:', error);
                notification.error({
                    message: 'Error',
                    description: 'Failed to load older blogs.',
                });
            }
        },
        timeAgo(date) {
            const now = new Date();
            const past = new Date(date);
            const diffTime = Math.abs(now - past);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays === 0 ? "Today" : `${diffDays} days ago`;
        },
        goToBlog(slug) {
            this.$router.push({name: 'BlogDetails', params: {slug}});
        },
    },
};
</script>

<style scoped>
.blog-container {
    max-width: 2200px;
    margin: auto;
}

.full-screen-image {
    position: relative;
    height: 500px; /* Chiều cao của ảnh lớn */
    overflow: hidden;
}

.image {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Giữ tỷ lệ và lấp đầy không gian */
}

.image-container {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    background-color: rgba(0, 0, 0, 0.5); /* Nền mờ cho chữ */
    text-align: center;
}

.featured {
    display: flex;
    align-items: center;
    padding: 20px;
}

.featured-image {
    width: 500px; /* Chiều rộng cố định */
    height: 300px; /* Chiều cao cố định */
    object-fit: cover; /* Giữ tỷ lệ ảnh */
}

.featured-content {
    padding: 20px;
}

.read-more {
    background: #007bff; /* Màu xanh */
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    border-radius: 5px;
    transition: background 0.3s;
}

.read-more:hover {
    background: #0056b3; /* Màu xanh đậm khi hover */
}

.latest-posts {
    padding: 20px;
}

.posts-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.post {
    background: #f3f3f3;
    padding: 10px;
}

.post-image {
    width: 100%; /* Chiếm toàn bộ chiều rộng */
    height: 150px; /* Chiều cao cố định */
    object-fit: cover; /* Giữ tỷ lệ ảnh */
}
</style>
