<template>
    <div id="portfolio-grid" class="row no-gutter" data-aos="fade-up" data-aos-delay="200">
        <div v-for="item in photos" :key="item.id" class="item web col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="item-wrap fancybox">
                <router-link :to="{ name: 'PhotoDetail', params: { token: item.photo_token } }">
                    <img v-lazy="`${item.image_url}`" class="img-fluid rounded-image" alt="">
                </router-link>
                <div class="work-info">
                    <h3>{{ item.photo.title }}</h3>
                    <span>{{ item.photo.category.category_name }}</span>
                    <div class="user-info2">
                        <img class="user-image2" :src="item.photo.user.profile_picture || '/images/userDefault.png'" style="width: 30px; height: 30px">
                        <span class="user-name2">{{ item.photo.user.username }}</span>
                        <span class="icon-heart2"><i class="fas fa-heart"></i></span>
                        <span class="icon-dots2">
                           <i :class="['fas', 'fa-ellipsis-h', { 'active': activeDropdown === 'dotsDropdown-' + item.id }]" @click="toggleDropdown('dotsDropdown-' + item.id)"></i>
                       </span>
                    </div>
                </div>
            </div>
            <div v-if="activeDropdown === 'dotsDropdown-' + item.id" class="dropdown-content show" style="right: 25px">
                <ul>
                    <li><i class="fa-regular fa-square-plus"></i> Add to Gallery</li>
                    <li><i class="fas fa-user-slash"></i> Block User</li>
                    <li><i class="fas fa-user-plus"></i> Follow User</li>
                    <li><i class="fas fa-flag"></i> Report This Photo</li>
                </ul>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    props: {
        photos: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            activeDropdown: null // Biến lưu trữ dropdown đang mở
        };
    },
    methods: {
        toggleDropdown(id) {
            if (this.activeDropdown === id) {
                this.activeDropdown = null;
            } else {
                this.activeDropdown = id;
            }
        }
    }
};
</script>

<style scoped>
.item .item-wrap {
    display: block;
    position: relative;
}
.user-info2 {
    display: flex;
    align-items: center;
    flex-direction: row;
    margin-bottom: -70px;
    margin-left: 10px;
}

.user-image2 {
    border-radius: 50%;
    margin-right: 10px;
}

.user-name2 {
    font-size: 18px;
    color: #fff;
    margin-right: auto;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
}

.icon-heart2, .icon-dots2 {
    flex-grow: 0;
    margin-left: 10px;
    font-size: 18px;
    margin-right: 10px;
    position: relative;
}

.icon-heart2:hover {
    color: #ff5a5f;
}

.icon-dots2 {
    position: relative;
    display: inline-block;
}

.dropdown-content ul {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    margin: 0;
}

.dropdown-content li {
    padding: 15px 15px 15px 25px;
    display: flex;
    align-items: center;
    color: #222222;
    white-space: nowrap;
    z-index: 1000;
}

.dropdown-content li:hover {
    color: whitesmoke; /* Màu chữ khi hover */
    background-color: #1890ff; /* Màu nền khi hover */
}

.dropdown-content li i {
    margin-right: 8px;
}

.dropdown-content li:hover i {
    color: whitesmoke;
    background-color: #1890ff;
}

/* Thêm CSS cho trạng thái active */
.icon-dots2 .fa-ellipsis-h.active {
    color: whitesmoke;
    background-color: #1890ff;
    border-radius: 50%;
    padding: 5px;
}
</style>
