<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="category-section">
                <h1>Explore Categories</h1>
                <div class="category-grid">
                    <div class="category-card" v-for="category in categories" :key="category.id">
                        <router-link :to="{ name: 'DetailsCategory', query: { slugs: category.slug } }">
                            <img :src="category.image" alt="Category image" class="category-image"/>
                            <div class="category-overlay">
                                <div class="category-name">{{ category.category_name }}</div>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </template>
    </Layout>
</template>

<script>
import axios from 'axios';
import getUrlList from '../provider.js';
import Layout from './Layout.vue';

export default {
    name: "Category",
    components: {
        Layout,
    },
    data() {
        return {
            categories: [],
        };
    },
    mounted() {
        this.fetchCategories();
    },
    methods: {
        async fetchCategories() {
            try {
                const response = await axios.get(getUrlList().getCategories);
                this.categories = response.data;
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        },
    },
};
</script>

<style scoped>
.category-section {
    padding: 20px;
    text-align: center;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* 4 columns */
    gap: 15px;
}

.category-card {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    transition: transform 0.3s;
    height: 200px; /* Chiều cao của card */
}

.category-image {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Đảm bảo hình ảnh phủ đầy thẻ */
}

.category-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7); /* Màu nền tối */
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background 0.3s; /* Hiệu ứng chuyển đổi cho background */
}

.category-name {
    color: white;
    font-size: 20px;
    font-weight: bold;
    text-align: center;
}

/* Giảm độ tối của overlay khi hover */
.category-card:hover .category-overlay {
    background-color: rgba(0, 0, 0, 0.4);
}
.category-card:hover .category-image {
    transform: scale(1.2); /* Phóng to ảnh nhẹ */
}
</style>
