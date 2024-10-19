<template>
    <Layout>
        <template v-slot:content="slotProps">
            <div class="site-section site-portfolio">
                <div class="container">
                    <TabBar :activeItem="activeItem" :setActive="setActive" />
                    <ForYou v-if="activeItem === 'forYou'" :photos="photos" />
                    <Following v-else-if="activeItem === 'following'" :users="followingUsers" />
                    <Explore v-else-if="activeItem === 'explore'" :users="followingUsers"/>
                </div>
            </div>

            <div class="site-section">
                <div class="container">
                    <div class="row justify-content-center text-center mb-4">
                        <div class="col-5">
                            <h3 class="h3 heading">My Clients</h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 col-sm-4 col-md-2">
                            <a href="#" class="client-logo"><img src="/front_assets/img/logo-adobe.png" alt="Image" class="img-fluid"></a>
                        </div>
                        <div class="col-4 col-sm-4 col-md-2">
                            <a href="#" class="client-logo"><img src="/front_assets/img/logo-uber.png" alt="Image" class="img-fluid"></a>
                        </div>
                        <div class="col-4 col-sm-4 col-md-2">
                            <a href="#" class="client-logo"><img src="/front_assets/img/logo-apple.png" alt="Image" class="img-fluid"></a>
                        </div>
                        <div class="col-4 col-sm-4 col-md-2">
                            <a href="#" class="client-logo"><img src="/front_assets/img/logo-netflix.png" alt="Image" class="img-fluid"></a>
                        </div>
                        <div class="col-4 col-sm-4 col-md-2">
                            <a href="#" class="client-logo"><img src="/front_assets/img/logo-nike.png" alt="Image" class="img-fluid"></a>
                        </div>
                        <div class="col-4 col-sm-4 col-md-2">
                            <a href="#" class="client-logo"><img src="/front_assets/img/logo-google.png" alt="Image" class="img-fluid"></a>
                        </div>

                    </div>
                </div>
            </div>


            <div class="site-section">
                <div class="container">
                    <div class="row justify-content-center text-center mb-4">
                        <div class="col-5">
                            <h3 class="h3 heading">My Services</h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                            <span class="la la-cube la-3x mb-4"></span>
                            <h4 class="h4 mb-2">Web Design</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
                            <ul class="list-unstyled list-line">
                                <li>Lorem ipsum dolor sit amet consectetur adipisicing</li>
                                <li>Non pariatur nisi</li>
                                <li>Magnam soluta quod</li>
                                <li>Lorem ipsum dolor</li>
                                <li>Cumque quae aliquam</li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                            <span class="la la-mobile la-3x mb-4"></span>
                            <h4 class="h4 mb-2">Mobile Applications</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
                            <ul class="list-unstyled list-line">
                                <li>Lorem ipsum dolor sit amet consectetur adipisicing</li>
                                <li>Non pariatur nisi</li>
                                <li>Magnam soluta quod</li>
                                <li>Lorem ipsum dolor</li>
                                <li>Cumque quae aliquam</li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                            <span class="la la-image la-3x mb-4"></span>
                            <h4 class="h4 mb-2">Graphic Design</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
                            <ul class="list-unstyled list-line">
                                <li>Lorem ipsum dolor sit amet consectetur adipisicing</li>
                                <li>Non pariatur nisi</li>
                                <li>Magnam soluta quod</li>
                                <li>Lorem ipsum dolor</li>
                                <li>Cumque quae aliquam</li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                            <span class="la la-search la-3x mb-4"></span>
                            <h4 class="h4 mb-2">SEO</h4>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit explicabo inventore.</p>
                            <ul class="list-unstyled list-line">
                                <li>Lorem ipsum dolor sit amet consectetur adipisicing</li>
                                <li>Non pariatur nisi</li>
                                <li>Magnam soluta quod</li>
                                <li>Lorem ipsum dolor</li>
                                <li>Cumque quae aliquam</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="site-section pt-0">
                <div class="container">

                    <div class="owl-carousel testimonial-carousel">

                        <div class="testimonial-wrap">
                            <div class="testimonial">
                                <img src="/front_assets/img/person_1.jpg" alt="Image" class="img-fluid">
                                <blockquote>
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam necessitatibus incidunt ut officiis
                                        explicabo inventore.</p>
                                </blockquote>
                                <p>&mdash; Jean Hicks</p>
                            </div>
                        </div>

                        <div class="testimonial-wrap">
                            <div class="testimonial">
                                <img src="/front_assets/img/person_2.jpg" alt="Image" class="img-fluid">
                                <blockquote>
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam necessitatibus incidunt ut officiis
                                        explicabo inventore.</p>
                                </blockquote>
                                <p>&mdash; Chris Stanworth</p>
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
import Layout from './Layout.vue'
import TabBar from './components/TabBar.vue';
import ForYou from './components/ForYou.vue';
import Following from './components/Following.vue';
import Explore from './components/Explore.vue';
import getUrlList from "../provider.js";
export default {
    name: 'Index',
    components : {
        Layout,
        TabBar,
        ForYou,
        Following,
        Explore,
    },
    data() {
        return {
            activeItem: 'forYou',
            photos: [],
            followingUsers: [
                {
                    id: 1, name: 'Paul Boomsma',
                    image: '/front_assets/img/user1.jpeg',
                    backgroundImages: ['/front_assets/img/img_1.jpg', '/front_assets/img/img_2.jpg', '/front_assets/img/img_3.jpg', '/front_assets/img/img_4.jpg']
                },
                {
                    id: 2, name: 'Dario Maschietti',
                    image: '/front_assets/img/user2.jpeg',
                    backgroundImages: ['/front_assets/img/img_1.jpg', '/front_assets/img/img_2.jpg', '/front_assets/img/img_3.jpg', '/front_assets/img/img_4.jpg']
                },
                {
                    id: 3, name: 'Instagram User',
                    image: '/front_assets/img/user3.jpeg',
                    backgroundImages: ['/front_assets/img/img_1.jpg', '/front_assets/img/img_2.jpg', '/front_assets/img/img_3.jpg', '/front_assets/img/img_4.jpg']
                },
                {
                    id: 4, name: 'Łukasz Spychała',
                    image: '/front_assets/img/user4.jpeg',
                    backgroundImages: ['/front_assets/img/img_1.jpg', '/front_assets/img/img_2.jpg', '/front_assets/img/img_3.jpg', '/front_assets/img/img_4.jpg']
                },
                {
                    id: 5, name: 'ADRIAN RAGONA',
                    image: '/front_assets/img/user5.jpeg',
                    backgroundImages: ['/front_assets/img/img_1.jpg', '/front_assets/img/img_2.jpg', '/front_assets/img/img_3.jpg', '/front_assets/img/img_4.jpg']
                },
                {
                    id: 6, name: 'ADRIAN RAGONA',
                    image: '/front_assets/img/user5.jpeg',
                    backgroundImages: ['/front_assets/img/img_1.jpg', '/front_assets/img/img_2.jpg', '/front_assets/img/img_3.jpg', '/front_assets/img/img_4.jpg']
                },
                {
                    id: 7, name: 'ADRIAN RAGONA',
                    image: '/front_assets/img/user5.jpeg',
                    backgroundImages: ['/front_assets/img/img_1.jpg', '/front_assets/img/img_2.jpg', '/front_assets/img/img_3.jpg', '/front_assets/img/img_4.jpg']
                },
                {
                    id: 8, name: 'ADRIAN RAGONA',
                    image: '/front_assets/img/user5.jpeg',
                    backgroundImages: ['/front_assets/img/img_1.jpg', '/front_assets/img/img_2.jpg', '/front_assets/img/img_3.jpg', '/front_assets/img/img_4.jpg']
                },
            ],
        }
    },
    watch: {
        activeItem(newItem) {
            if (newItem === 'forYou') {
                this.getPhoto();
            }
        }
    },
    mounted() {
        if (this.activeItem === 'forYou') {
            this.getPhoto();
        }
    },
    methods: {
        setActive(item) {
            this.activeItem = item;
        },
        async getPhoto() {
            try {
                const response = await axios.get(getUrlList().getPhotoData);
                this.photos = response.data;
                console.log(this.photos);
            } catch (error) {
                console.error(error);
            }
        }
    }
}
</script>
