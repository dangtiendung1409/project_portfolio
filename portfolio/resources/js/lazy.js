export default {
    mounted(el, binding) {
        el.style.filter = 'blur(20px)';
        el.style.transition = 'filter 0.3s ease';

        const options = {
            root: null,
            threshold: 0.5,
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const tempImg = new Image();
                    tempImg.src = binding.value;

                    tempImg.onload = () => {
                        el.src = binding.value;
                        el.style.filter = 'blur(0px)';
                        el.classList.add('lazy-loaded');
                        observer.unobserve(el);
                    };
                }
            });
        }, options);

        observer.observe(el);
    }
};
