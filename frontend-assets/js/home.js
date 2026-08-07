(function ($) {
  "use strict";
  
// Hero Single slider
const heroSwiper = new Swiper('.hero_swiper', {
        loop: true,
        slidesPerView: 1,
        spaceBetween: 0,
        speed: 1800,
        // autoplay: {
        //     delay: 2500,
        //     disableOnInteraction: false,
        //     pauseOnMouseEnter: true,
        // },
        grabCursor: true,
        watchOverflow: true,
        observer: true,
        observeParents: true,
        navigation: {
            nextEl: ".herobnr-next",
            prevEl: ".herobnr-prev",
        },
});

const LandcrSwiper = new Swiper('.landcr_swiper', {
    loop: true,
    slidesPerView: 1,
    spaceBetween: 0,
    speed: 1800,

    // autoplay: {
    //     delay: 2500,
    //     disableOnInteraction: false,
    //     pauseOnMouseEnter: true,
    // },

    grabCursor: true,
    watchOverflow: true,
    observer: true,
    observeParents: true,

    navigation: {
        nextEl: ".landcr-next",
        prevEl: ".landcr-prev",
    },

    breakpoints: {
        576: {
            slidesPerView: 1,
            spaceBetween: 5,
        },
        768: {
            slidesPerView: 1,
            spaceBetween: 5,
        },
        992: {
            slidesPerView: 1,
            spaceBetween: 0,
        },
        1200: {
            slidesPerView: 1.8,
            spaceBetween: 0,
        },
        1900: {
            slidesPerView: 1.8,
            spaceBetween: 0,
        }
    }
});


const BsnsSwiper = new Swiper('.bsns_swiper', {
    loop: false,
    // autoplay: {
    //     delay: 2500,
    //     disableOnInteraction: false,
    //     pauseOnMouseEnter: true,
    // },
    slidesPerView: 1,
    spaceBetween: 0,
    speed: 1800,
    grabCursor: true,
    watchOverflow: true,
    observer: true,
    observeParents: true,

    navigation: {
        nextEl: ".bsns-next",
        prevEl: ".bsns-prev",
    },

    breakpoints: {
        768: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        992: {
            slidesPerView: 3,
            spaceBetween: 0,
        },
        1200: {
            slidesPerView: 6,
            spaceBetween: 0,
        },
        1900: {
            slidesPerView: 6,
            spaceBetween: 0,
        }
    }
});

const SpeakSwiper = new Swiper('.speak_swiper', {
    loop: true,
    effect: 'fade',
    fadeEffect: {
        crossFade: true,
    },

    autoplay: {
        delay: 2500,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
    },

    speed: 1800,
    grabCursor: true,
    watchOverflow: true,
    observer: true,
    observeParents: true,

    navigation: {
        nextEl: '.speak-next',
        prevEl: '.speak-prev',
    },
});


const SocialSwiper = new Swiper('.socwal_swiper', {
    loop: true,
    // autoplay: {
    //     delay: 2500,
    //     disableOnInteraction: false,
    //     pauseOnMouseEnter: true,
    // },
    slidesPerView: 1,
    spaceBetween: 0,
    speed: 1800,
    grabCursor: true,
    watchOverflow: true,
    observer: true,
    observeParents: true,

    navigation: {
        nextEl: ".swiper_nav>.socwal-next",
        prevEl: ".swiper_nav>.socwal-prev",
    },

    breakpoints: {
        768: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        768: {
            slidesPerView: 1,
            spaceBetween: 10,
        },
        992: {
            slidesPerView: 3,
            spaceBetween: 0,
        },
        1200: {
            slidesPerView: 3.6,
            spaceBetween: 0,
        },
        1900: {
            slidesPerView: 3.6,
            spaceBetween: 0,
        }
    }
});



})(jQuery);