export default {
    mounted(el, binding) {
        el.src = '/images/loading-image.gif';
        el.classList.add('lazy-loading');

        const options = {
            root: null,
            threshold: 0.5,
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    el.src = binding.value;
                    el.classList.remove('lazy-loading');
                    el.classList.add('lazy-loaded');
                    observer.unobserve(el);
                }
            });
        }, options);

        observer.observe(el);
    }
};
