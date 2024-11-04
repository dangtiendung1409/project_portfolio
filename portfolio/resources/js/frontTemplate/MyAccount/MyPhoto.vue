
<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="my-photo-page">
                <div class="content-layout">
                    <Sidebar />
                    <main>
                        <header class="header">
                            <div class="header-content">
                                <span>My Photos</span>
                                <p class="header-subtitle">
                                    <span>0 photo</span>
                                </p>
                            </div>
                            <div class="tabs">
                                <span class="tab-item active">All</span>
                                <span class="tab-item">Favorites</span>
                                <span class="tab-item">Shared</span>
                            </div>
                        </header>
                        <div class="sort-select">
                            <div class="show-options">
                                <label for="show-select">Show:</label>
                                <select id="show-select">
                                    <option value="all">All</option>
                                    <option value="favorites">Favorites</option>
                                </select>
                            </div>
                            <div class="sort-options">
                                <label for="sort-select">Sort by:</label>
                                <select id="sort-select">
                                    <option value="date">Date</option>
                                    <option value="name">Name</option>
                                    <option value="size">Size</option>
                                </select>
                            </div>
                        </div>
                        <div class="trial-info">
                            <p>Enjoy unlimited uploads with a 30-day free trial!</p>
                            <p>You have 21 uploads remaining and your trial ends on Oct 03, 2024.</p>
                            <button class="trial-button">Start your trial</button>
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
import '@assets/css/account.css';
export default {
    name: 'MyPhoto',
    components: {
        Layout,
        Sidebar
    },
}
</script>

<style scoped>
main {
    flex: 1;
    padding-left: 0;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
</style>
