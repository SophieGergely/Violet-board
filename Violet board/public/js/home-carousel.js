document.addEventListener("DOMContentLoaded", function () {
    const CARD = 220;
    const GAP  = 20;

    document.querySelectorAll('.carousel-section').forEach((carouselSection) => {
        const wrapper    = carouselSection.querySelector('.carousel-content');
        const view       = carouselSection.querySelector('.carousel-view');
        const cards      = wrapper.querySelectorAll('.product-card');
        const leftArrow  = carouselSection.querySelector('.arrow.left');
        const rightArrow = carouselSection.querySelector('.arrow.right');

        let currentIndex = 0;

        function calcVisible(availableWidth) {
            return Math.max(1, Math.floor((availableWidth + GAP) / (CARD + GAP)));
        }

        function updateCarousel() {
            view.style.width = '';

            const sectionStyle  = window.getComputedStyle(carouselSection);
            const sectionWidth  = carouselSection.clientWidth
                                - parseFloat(sectionStyle.paddingLeft)
                                - parseFloat(sectionStyle.paddingRight);

            const visible = calcVisible(sectionWidth);
            const viewW   = visible * (CARD + GAP) - GAP;

            view.style.width = Math.min(viewW, sectionWidth) + 'px';

            const maxIndex = Math.max(0, cards.length - visible);
            currentIndex   = Math.max(0, Math.min(currentIndex, maxIndex));
            wrapper.style.transform = `translateX(${-currentIndex * (CARD + GAP)}px)`;
        }

        leftArrow.addEventListener('click',  () => { currentIndex--; updateCarousel(); });
        rightArrow.addEventListener('click', () => { currentIndex++; updateCarousel(); });
        window.addEventListener('resize',    () => { currentIndex = 0; updateCarousel(); });

        requestAnimationFrame(updateCarousel);
    });
});
