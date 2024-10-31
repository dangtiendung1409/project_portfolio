<template>
    <div id="portfolio-grid" class="row no-gutter" data-aos="fade-up" data-aos-delay="200">
        <div v-for="item in photos" :key="item.id" class="item web col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <a href="work-single.html" class="item-wrap fancybox">
                <div class="work-info">
                    <h3>{{ item.photo.title }}</h3>
                    <span>{{ item.photo.category.category_name }}</span>
                </div>
                <img v-lazy="`${item.image_url}`" class="img-fluid rounded-image" alt="">
            </a>
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
    }
};
</script>
