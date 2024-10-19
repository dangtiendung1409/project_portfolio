<template>
    <div id="explore-grid" class="d-flex flex-wrap" data-aos="fade-up" data-aos-delay="200">
        <div class="photo-header">
            <h2>Popular Photos</h2>
            <p>New uploads with the highest Pulse rating</p>
        </div>
        <div class="item-explore col-3 mb-4"> <!-- Thay đổi class ở đây -->
            <a href="work-single.html" class="item-wrap fancybox">
                <div class="work-info">
                    <h3>Boxed Water</h3>
                    <span>Web</span>
                </div>
                <img class="img-thin" src="/front_assets/img/img_1.jpg" alt="Boxed Water">
            </a>
        </div>
        <div class="item-explore col-3 mb-4"> <!-- Thay đổi class ở đây -->
            <a href="work-single.html" class="item-wrap fancybox">
                <div class="work-info">
                    <h3>Build Indoo</h3>
                    <span>Photography</span>
                </div>
                <img class="img-thin" src="/front_assets/img/img_2.jpg" alt="Build Indoo">
            </a>
        </div>
        <div class="item-explore col-3 mb-4"> <!-- Thay đổi class ở đây -->
            <a href="work-single.html" class="item-wrap fancybox">
                <div class="work-info">
                    <h3>Cocooil</h3>
                    <span>Branding</span>
                </div>
                <img class="img-thin" src="/front_assets/img/img_3.jpg" alt="Cocooil">
            </a>
        </div>
        <div class="item-explore col-3 mb-4"> <!-- Thay đổi class ở đây -->
            <a href="work-single.html" class="item-wrap fancybox">
                <div class="work-info">
                    <h3>Nike Shoe</h3>
                    <span>Design</span>
                </div>
                <img class="img-thin" src="/front_assets/img/img_4.jpg" alt="Nike Shoe">
            </a>
        </div>
        <div class="item-explore col-3 mb-4"> <!-- Thay đổi class ở đây -->
            <a href="work-single.html" class="item-wrap fancybox">
                <div class="work-info">
                    <h3>Kitchen Sink</h3>
                    <span>Photography</span>
                </div>
                <img class="img-thin" src="/front_assets/img/img_5.jpg" alt="Kitchen Sink">
            </a>
        </div>
        <div class="item-explore col-3 mb-4"> <!-- Thay đổi class ở đây -->
            <a href="work-single.html" class="item-wrap fancybox">
                <div class="work-info">
                    <h3>Amazon</h3>
                    <span>Branding</span>
                </div>
                <img class="img-thin" src="/front_assets/img/img_6.jpg" alt="Amazon">
            </a>
        </div>
        <div class="item-explore col-3 mb-4"> <!-- Thay đổi class ở đây -->
            <a href="work-single.html" class="item-wrap fancybox">
                <div class="work-info">
                    <h3>Amazon</h3>
                    <span>Branding</span>
                </div>
                <img class="img-thin" src="/front_assets/img/img_6.jpg" alt="Amazon">
            </a>
        </div>
        <div class="item-explore col-3 mb-4"> <!-- Thay đổi class ở đây -->
            <a href="work-single.html" class="item-wrap fancybox">
                <div class="work-info">
                    <h3>Amazon</h3>
                    <span>Branding</span>
                </div>
                <img class="img-thin" src="/front_assets/img/img_6.jpg" alt="Amazon">
            </a>
        </div>
        <!--Top follow-->
        <div class="following-content-2">
            <div class="follow-header">
                <h2>Featured Photographers</h2>
                <p>Photographers we think you should check out</p>
            </div>
            <div class="following-users">
                <div v-for="user in users" :key="user.id" class="user-card">
                    <div class="card">
                        <div class="background-images">
                            <img v-for="bg in user.backgroundImages" :key="bg" :src="bg" class="bg-img" />
                        </div>
                        <img :src="user.image" alt="User Image" class="profile-img" />
                        <h4>{{ user.name }}</h4>
                        <button class="btn-follow">Follow</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Top Categories -->
        <div class="top-categories mb-4">
            <h2>Top Categories</h2>
            <div class="top-category-header d-flex justify-content-between align-items-center">
                <p>This week's top categories ranked by popularity</p>
                <a href="#" class="view-all" style="color: #007bff;">View All &gt;</a>
            </div>
            <div class="categories-grid">
                <div class="category-item">
                    <img src="/front_assets/img/img_1.jpg" alt="Landscapes">
                    <span>Landscapes</span>
                </div>
                <div class="category-item">
                    <img src="/front_assets/img/img_2.jpg" alt="Animals">
                    <span>Animals</span>
                </div>
                <div class="category-item">
                    <img src="/front_assets/img/img_3.jpg" alt="Nature">
                    <span>Nature</span>
                </div>
                <div class="category-item">
                    <img src="/front_assets/img/img_4.jpg" alt="Black and White">
                    <span>Black and White</span>
                </div>
                <div class="category-item">
                    <img src="/front_assets/img/img_5.jpg" alt="City and Architecture">
                    <span>City and Architecture</span>
                </div>
                <div class="category-item">
                    <img src="/front_assets/img/img_6.jpg" alt="Macro">
                    <span>Macro</span>
                </div>
                <div class="category-item">
                    <img src="/front_assets/img/img_3.jpg" alt="People">
                    <span>People</span>
                </div>
                <div class="category-item">
                    <img src="/front_assets/img/img_3.jpg" alt="Travel">
                    <span>Travel</span>
                </div>
            </div>
        </div>
        <!-- Featured Galleries -->
        <div class="featured-galleries mb-4">
            <h2>Featured Galleries</h2>
            <div class="top-gallery-header d-flex justify-content-between align-items-center">
                <p>Must-see Galleries curated by the 500px community</p>
                <a href="#" class="view-all">View All &gt;</a>
            </div>
            <div class="galleries-grid">
                <div class="gallery-card">
                    <div class="gallery-info">
                        <h4>Insan</h4>
                        <div class="image-count">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z" fill="white"></path><path d="M12.4003 5.33337C13.3337 5.33337 14.1337 4.53337 14.1337 3.60003C14.1337 2.6667 13.3337 1.8667 12.4003 1.8667C11.467 1.8667 10.667 2.6667 10.667 3.60003C10.667 4.53337 11.467 5.33337 12.4003 5.33337ZM12.4003 2.80003C12.8003 2.80003 13.2003 3.13337 13.2003 3.60003C13.2003 4.0667 12.867 4.40003 12.4003 4.40003C12.0003 4.40003 11.6003 4.0667 11.6003 3.60003C11.6003 3.13337 11.9337 2.80003 12.4003 2.80003Z" fill="white"></path>
                            </svg>
                            <span>27</span>
                        </div>
                    </div>
                    <div class="gallery-images">
                        <img src="/front_assets/img/img_1.jpg" alt="Gallery 1 Image 1">
                        <img src="/front_assets/img/img_2.jpg" alt="Gallery 1 Image 2">
                        <img src="/front_assets/img/img_3.jpg" alt="Gallery 1 Image 3">
                        <img src="/front_assets/img/img_4.jpg" alt="Gallery 1 Image 4">
                    </div>
                    <div class="gallery-footer">
                        <img class="user-avatar" src="/front_assets/img/user1.jpeg" alt="User Avatar">
                        <h4>Dung</h4>
                        <div class="footer-buttons">
                            <button class="btn-favorite"><i class="fas fa-heart"></i></button>
                            <button class="btn-options"><i class="fa-solid fa-ellipsis"></i></button>
                        </div>
                    </div>
                </div>
                <div class="gallery-card">
                    <div class="gallery-info">
                        <h4>Insan</h4>
                        <div class="image-count">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z" fill="white"></path><path d="M12.4003 5.33337C13.3337 5.33337 14.1337 4.53337 14.1337 3.60003C14.1337 2.6667 13.3337 1.8667 12.4003 1.8667C11.467 1.8667 10.667 2.6667 10.667 3.60003C10.667 4.53337 11.467 5.33337 12.4003 5.33337ZM12.4003 2.80003C12.8003 2.80003 13.2003 3.13337 13.2003 3.60003C13.2003 4.0667 12.867 4.40003 12.4003 4.40003C12.0003 4.40003 11.6003 4.0667 11.6003 3.60003C11.6003 3.13337 11.9337 2.80003 12.4003 2.80003Z" fill="white"></path>
                            </svg>
                            <span>27</span>
                        </div>
                    </div>
                    <div class="gallery-images">
                        <img src="/front_assets/img/img_1.jpg" alt="Gallery 1 Image 1">
                        <img src="/front_assets/img/img_2.jpg" alt="Gallery 1 Image 2">
                        <img src="/front_assets/img/img_3.jpg" alt="Gallery 1 Image 3">
                        <img src="/front_assets/img/img_4.jpg" alt="Gallery 1 Image 4">
                    </div>
                    <div class="gallery-footer">
                        <img class="user-avatar" src="/front_assets/img/user1.jpeg" alt="User Avatar">
                        <h4>Dung</h4>
                        <div class="footer-buttons">
                            <button class="btn-favorite"><i class="fas fa-heart"></i></button>
                            <button class="btn-options"><i class="fa-solid fa-ellipsis"></i></button>
                        </div>
                    </div>
                </div>
                <div class="gallery-card">
                    <div class="gallery-info">
                        <h4>Insan</h4>
                        <div class="image-count">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5333 0H0.466667C0.2 0 0 0.2 0 0.466667V10.2V15.5333C0 15.8 0.2 16 0.466667 16H15.5333C15.8 16 16 15.8 16 15.5333V13.4V0.466667C16 0.2 15.8 0 15.5333 0ZM15.0667 0.933333V12.2667L10.5333 7.66667C10.4667 7.6 10.3333 7.53333 10.2 7.53333C10.0667 7.53333 9.93333 7.6 9.86667 7.66667L8.53333 9L5.8 6.2C5.6 6 5.33333 6 5.13333 6.13333L0.933333 9.26667V0.933333H15.0667ZM15.0667 15.0667H0.933333V10.4667L3.8 8.33333L5.86667 10.4C5.93333 10.4667 6.06667 10.5333 6.2 10.5333C6.33333 10.5333 6.46667 10.4667 6.53333 10.4C6.73333 10.2 6.73333 9.93333 6.53333 9.73333L4.53333 7.73333L5.4 7.06667L8.26667 9.93333L9.6 11.2667C9.66667 11.3333 9.8 11.4 9.93333 11.4C10.0667 11.4 10.2 11.3333 10.2667 11.2667C10.4667 11.0667 10.4667 10.8 10.2667 10.6L9.26667 9.6L10.2667 8.6L15.1333 13.5333V15.0667H15.0667Z" fill="white"></path><path d="M12.4003 5.33337C13.3337 5.33337 14.1337 4.53337 14.1337 3.60003C14.1337 2.6667 13.3337 1.8667 12.4003 1.8667C11.467 1.8667 10.667 2.6667 10.667 3.60003C10.667 4.53337 11.467 5.33337 12.4003 5.33337ZM12.4003 2.80003C12.8003 2.80003 13.2003 3.13337 13.2003 3.60003C13.2003 4.0667 12.867 4.40003 12.4003 4.40003C12.0003 4.40003 11.6003 4.0667 11.6003 3.60003C11.6003 3.13337 11.9337 2.80003 12.4003 2.80003Z" fill="white"></path>
                            </svg>
                            <span>27</span>
                        </div>
                    </div>
                    <div class="gallery-images">
                        <img src="/front_assets/img/img_1.jpg" alt="Gallery 1 Image 1">
                        <img src="/front_assets/img/img_2.jpg" alt="Gallery 1 Image 2">
                        <img src="/front_assets/img/img_3.jpg" alt="Gallery 1 Image 3">
                        <img src="/front_assets/img/img_4.jpg" alt="Gallery 1 Image 4">
                    </div>
                    <div class="gallery-footer">
                        <img class="user-avatar" src="/front_assets/img/user1.jpeg" alt="User Avatar">
                        <h4>Dung</h4>
                        <div class="footer-buttons">
                            <button class="btn-favorite"><i class="fas fa-heart"></i></button>
                            <button class="btn-options"><i class="fa-solid fa-ellipsis"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        users: Array,
    },
};
</script>
