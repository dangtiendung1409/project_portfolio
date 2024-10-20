import {createApp} from 'vue';
import App from './App.vue';
import router from './routes.js';
const app = createApp(App);

// Đăng ký custom directive lazy load
app.directive('lazy', {
    mounted(el, binding) {
        el.src = '/images/loading-image.gif';
        el.classList.add('lazy-loading');

        const options = {
            root: null,
            threshold: 0.5
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Khi ảnh vào khung nhìn, thay đổi src thành ảnh thật
                    el.src = binding.value;
                    el.classList.remove('lazy-loading');
                    el.classList.add('lazy-loaded');
                    observer.unobserve(el);
                }
            });
        }, options);

        observer.observe(el);
    }
});


// Sử dụng router và mount ứng dụng
app.use(router).mount("#app");
