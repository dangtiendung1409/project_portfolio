
<template>
    <div class="collapse navbar-collapse custom-navmenu" id="main-navbar">
        <div class="container py-2 py-md-5">
            <div class="row align-items-start">
                <div class="col-md-2">
                    <ul class="custom-menu">
                        <li class="active"><a href="index.html">Home</a></li>
                        <li><a href="about.html">About Me</a></li>
                        <li><a href="services.html">Services</a></li>
                        <li><a href="works.html">Works</a></li>
                        <li><a href="contact.html">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-6 d-none d-md-block  mr-auto">
                    <div class="tweet d-flex">
                        <span class="icofont-twitter text-white mt-2 mr-3"></span>
                        <div>
                            <p><em>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam necessitatibus incidunt ut
                                officiis explicabo inventore. <br> <a href="#">t.co/v82jsk</a></em></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 d-none d-md-block">
                    <h3>Hire Me</h3>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam necessitatibus incidunt ut officiis
                        explicabo inventore. <br> <a href="#">myemail@gmail.com</a></p>
                </div>
            </div>

        </div>
    </div>

    <nav class="navbar navbar-light custom-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.html">MyPortfolio.</a>

            <a href="#" class="burger" data-toggle="collapse" data-target="#main-navbar">
                <span></span>
            </a>

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
}
</script>
