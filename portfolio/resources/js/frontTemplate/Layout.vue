<template>
    <nav class="navbar">
        <div class="navbar-left">
            <a class="navbar-brand" href="index.html">MyPortfolio</a>
            <div class="nav-links">
                <a href="#" class="nav-link" style="color: #007bff">Discover</a>
                <a href="#" class="nav-link">About us</a>
                <a href="#" class="nav-link">Blog</a>
                <a href="#" class="nav-link">Category</a>
            </div>
        </div>
        <div class="navbar-right">
            <div class="search-bar">
                <input type="text" placeholder="Search powered by AI">
                <i class="fas fa-search search-icon"></i>
            </div>
            <div class="icon-container">
                <i class="fa-regular fa-user" style="font-size: 24px;"></i>
                <i class="fa-regular fa-envelope" style="font-size: 24px;"></i>
                <i class="fa-regular fa-bell" style="font-size: 24px;"></i>
            </div>
            <button class="upload-button">
                <i class="fa-solid fa-arrow-up"></i> Upload
            </button>
            <button class="hamburger" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars"></i>
            </button>
            <button class="close-menu" aria-label="Close menu" style="display: none;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </nav>

    <main>
        <slot name="content">

        </slot>
    </main>
    <footer class="footer" role="contentinfo">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <p class="mb-1">&copy; Copyright MyPortfolio. All Rights Reserved</p>
                    <div class="credits">
                        <!--
                          All the links in the footer should remain intact.
                          You can delete the links only if you purchased the pro version.
                          Licensing information: https://bootstrapmade.com/license/
                          Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=MyPortfolio
                        -->
                        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                    </div>
                </div>
                <div class="col-sm-6 social text-md-right">
                    <a href="#"><span class="icofont-twitter"></span></a>
                    <a href="#"><span class="icofont-facebook"></span></a>
                    <a href="#"><span class="icofont-dribbble"></span></a>
                    <a href="#"><span class="icofont-behance"></span></a>
                </div>
            </div>
        </div>
    </footer>
    <div id="scripts"></div>
</template>
<script>
import axios from 'axios';
import getUrlList from "../provider.js";
export default {
    name: 'Layout',

    mounted(){
        var src = ['/front_assets/vendor/jquery/jquery.min.js','/front_assets/vendor/jquery/jquery-migrate.min.js',
            '/front_assets/vendor/bootstrap/js/bootstrap.min.js', '/front_assets/vendor/easing/easing.min.js',
            '/front_assets/vendor/php-email-form/validate.js', '/front_assets/vendor/isotope/isotope.pkgd.min.js',
            '/front_assets/vendor/aos/aos.js', '/front_assets/vendor/owlcarousel/owl.carousel.min.js',
            '/front_assets/js/main.js'
        ];
        for (var i=0; i<src.length;i++){
            const script = document.createElement('script');
            script.src = src[i];
            script.async =false;
            document.getElementById('scripts').appendChild(script);
        }
    },
    methods:{

    }
}
</script>
