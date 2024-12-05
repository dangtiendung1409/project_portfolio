<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="site-section">
                <div>
                    <div class="row">
                        <!-- Main Image Section -->
                        <div class="col-md-9" data-aos="fade-up">
                            <img :src="photoDetail.image_url" alt="Image" class="img-fluid photo-img" />
                        </div>

                        <!-- Info Section on the Right -->
                        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                            <div class="info-container">
                                <!-- Header with Icons in a Separate Div -->
                                <div class="icon-wrapper">
                                    <button class="btn icon-btn">
                                        <i class="fa-regular fa-heart"></i>
                                    </button>
                                    <button class="btn icon-btn">
                                        <i class="fa-solid fa-share-nodes"></i>
                                    </button>
                                    <button class="btn icon-btn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button class="btn icon-btn">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>
                                </div>
                                <!-- Details Section -->
                                <div class="details-wrapper">
                                    <h3 class="h3">{{ photoDetail.photo.title }}</h3>
                                    <p class="text-muted">{{ photoDetail.photo.description }}</p>
                                    <ul class="list-unstyled">
                                        <li><strong>Country:</strong> {{ photoDetail.photo.country || "NOR" }}</li>
                                        <li><strong>Date:</strong> 20 February 2019</li>
                                        <li><strong>Likes:</strong> 400</li>
                                        <li><strong>Views:</strong> 27.5K</li>
                                    </ul>
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
import axios from "axios";
import Layout from "./Layout.vue";
import getUrlList from "../provider.js";

export default {
    name: "PhotoDetail",
    components: {
        Layout,
    },
    data() {
        return {
            photoDetail: {
                photo: {
                    title: "",
                    description: "",
                    country: "NOR", // Quốc gia mặc định
                },
                image_url: "",
            },
        };
    },
    methods: {
        async fetchPhotoDetail(token) {
            try {
                const response = await axios.get(`${getUrlList().getPhotoDetail}/${token}`);
                this.photoDetail = response.data.data;
            } catch (error) {
                console.error("Error fetching photo details:", error);
            }
        },
    },
    created() {
        const token = this.$route.params.token;
        this.fetchPhotoDetail(token);
    },
};
</script>
<style scoped>
.photo-img {
  width: 100%;
  height: 630px;
    padding-left: 30px;
  object-fit: cover; /* Ensure image covers the area */
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.info-container {
    word-break: break-word;
    width: 325px;
    height: 100%;
    padding: 24px;
    -webkit-box-align: center;
    align-items: center;
    background: rgb(247, 248, 250);
    transition-property: width;
    transition-duration: 0.2s;
    transition-timing-function: ease;
    position: relative;
}

.icon-wrapper {
    display: flex;
    justify-content: space-around; /* Spread icons evenly */
    margin-bottom: 20px;
    background-color: #ffffff; /* Background for the icons */
    padding: 24px 22px;
    border-radius: 5px;

    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.details-wrapper {
    background-color: #ffffff; /* Background color for the details section */
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.icon-btn {
    border: none; /* Remove button borders */
    background: none; /* Remove background */
    cursor: pointer;
    font-size: 20px; /* Icon size */
}

 .text-muted {
  color: #6c757d;
}
.h3 {
      margin: 10px 0;
}
</style>
