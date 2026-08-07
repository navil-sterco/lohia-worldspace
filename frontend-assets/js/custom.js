(function ($) {
  "use strict";

  // =========================
  // AOS INIT
  // =========================
  AOS.init({
    duration: 800,
    easing: "ease-out",
    once: false,
    isMobile:false,
  });

  // =========================
  // HEADER STICKY (FIXED STABILITY)
  // =========================
  $(document).ready(function () {

    let lastScrollTop = 0;
    const header = $('.main_header');

    let hideSections = window.innerWidth < 768
      ? ['.mobilehide_header']
      : ['.hide_header'];

    $(window).on('scroll', function () {

      const scrollTop = $(this).scrollTop();
      let insideAnySection = false;

      hideSections.forEach((selector) => {
        const section = $(selector);
        if (!section.length) return;

        const top = section.offset().top;
        const bottom = top + section.outerHeight();

        if (scrollTop >= top - 100 && scrollTop <= bottom) {
          insideAnySection = true;
        }
      });

      if (scrollTop > 50) {
        header.addClass('sticky');
      } else {
        header.removeClass('sticky');
      }

      if (insideAnySection) {
        header.css('transform', 'translateY(-100%)');
      }
      else if (scrollTop > lastScrollTop && scrollTop > 50) {
        header.css('transform', 'translateY(-100%)');
      }
      else {
        header.css('transform', 'translateY(0)');
      }

      if (scrollTop <= 0) {
        header.css('transform', 'translateY(0)');
        header.removeClass('sticky');
      }

      lastScrollTop = scrollTop;
    });

  });


  // =========================
  // HAMBURGER MENU START
  // =========================

function hamburgerToggle(btn, action) {
    const header = document.querySelector(".hamb_header");

    btn.addEventListener("click", () => {
        header.classList[action]("active"); // action = "add" or "remove"
    });
}

hamburgerToggle(document.querySelector(".hamb_btn"), "add");
hamburgerToggle(document.querySelector(".hamb_close"), "remove");


  // =========================
  // Menu Dropdown MENU START
  // =========================
function dropdownToggle(menuSelector, btnSelector, itemSelector) {
    const dropdowns = document.querySelectorAll(menuSelector);

    dropdowns.forEach((dropdown) => {
        const toggleBtn = dropdown.querySelector(btnSelector);

        if (!toggleBtn) return;

        // Toggle dropdown
        toggleBtn.addEventListener("click", (e) => {
            e.stopPropagation();

            // Close other dropdowns
            dropdowns.forEach((item) => {
                if (item !== dropdown) {
                    item.classList.remove("active");
                }
            });

            // Toggle current
            dropdown.classList.toggle("active");
        });

        // Close when item is clicked
        dropdown.addEventListener("click", (e) => {
            if (e.target.closest(itemSelector)) {
                dropdown.classList.remove("active");
            }
        });
    });

    // Close on outside click
    document.addEventListener("click", (e) => {
        dropdowns.forEach((dropdown) => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove("active");
            }
        });
    });
}

dropdownToggle(".dropdown_menu", ".dropdown_toggle", ".dropdown_item a");

  // =========================
  // COUNTER ANIMATION (FIXED SUFFIX)
  // =========================
  const counters = document.querySelectorAll('.counter');

  const animateCount = (counter) => {

    const target = +counter.getAttribute('data-target');
    const speed = 200;
    const increment = target / speed;

    let count = 0;

    const suffixEl = counter.querySelector('sup, span.suffix');
    const suffixHTML = suffixEl ? suffixEl.outerHTML : "";

    const update = () => {
      count += increment;

      if (count < target) {
        counter.innerHTML = `${Math.ceil(count)}${suffixHTML}`;
        requestAnimationFrame(update);
      } else {
        counter.innerHTML = `${target}${suffixHTML}`;
      }
    };

    counter.innerHTML = `0${suffixHTML}`;
    update();
  };

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCount(entry.target);
        obs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.6 });

  counters.forEach(c => observer.observe(c));

  // =========================
  // REVEAL ON SCROLL (FIXED)
  // =========================
(function () {

    function revealOnScroll() {

        if (window.innerWidth < 992) return;

        const items = [
            { selector: '.reveal-left', cls: 'revealed-left' },
            { selector: '.reveal-right', cls: 'revealed-right' },
            { selector: '.reveal-top', cls: 'revealed-top' },
            { selector: '.reveal-bottom', cls: 'revealed-bottom' }
        ];

        items.forEach(item => {

            document.querySelectorAll(item.selector).forEach((el, index) => {

                if (el.classList.contains(item.cls)) return;

                const rect = el.getBoundingClientRect();

                if (rect.top < window.innerHeight - 150) {

                    setTimeout(() => {
                        el.classList.add(item.cls);
                    }, index * 250); // 250ms gap

                }

            });

        });

    }

    window.addEventListener('load', revealOnScroll);
    window.addEventListener('scroll', revealOnScroll);
    window.addEventListener('resize', revealOnScroll);

})();

  // =========================
  // GALLERY
  // =========================
$(document).ready(function () {
    $('.gallery').each(function () {
        const id = $(this).data('albumid');
        $(this)
            .find('.gallery_item')
            .attr('rel', 'gallery' + id);
    });
    $('.gallery_item').rbox();
});


// Mobile Menu

function togglePanel(targetId, activeBtn) {
    const $panel = $('.mobmenu_panel > div[data-id="' + targetId + '"]');
    const isActive = $panel.hasClass("show");

    // Hide all panels
    $(".mobmenu_panel > div").removeClass("show");

    // Remove active from all buttons
    $(".mobmenu_nav button").removeClass("active");

    // Show selected panel if not already active
    if (!isActive) {
        $panel.addClass("show");
        $(activeBtn).addClass("active");
    }
}

$(".mobmenu_nav button").on("click", function () {
    const targetId = $(this).data("target");
    togglePanel(targetId, this);
});

// Mobile Menu Tab End
  $(".mobile_menu li a").click(function (e) {
    var same = $(this).hasClass("active");
    var siblings = $(this).parent(".menu_item").parent().children();
    siblings.find("a.active + .sub_menu").slideUp();
    siblings.find("a").removeClass("active");
    if ($(this).next().hasClass("sub_menu") && !same) {
      e.preventDefault();
      $(this).addClass("active");
      $(this).next(".sub_menu").slideDown();
    }
  });

  
// Common Single image slider
$(document).ready(function () {
    new Swiper('.image_slider', {
        loop: true,
        slidesPerView: 1,
        spaceBetween: 0,
        speed: 1800,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        grabCursor: true,
        watchOverflow: true,
        observer: true,
        observeParents: true,

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        }
    });
});


const ProdcrSwiper = new Swiper('.prodcr_swiper', {
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
const nwsrelatedSwiper = new Swiper('.nwsrelate_swiper', {
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
        nextEl: ".nws-next",
        prevEl: ".nws-prev",
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
            slidesPerView: 2,
            spaceBetween: 10,
        },
        1200: {
            slidesPerView: 2,
            spaceBetween: 15,
        },
        1900: {
            slidesPerView: 2,
            spaceBetween: 20,
        }
    }
});
const GallerySwiper = new Swiper('.prj_gallery', {
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
        nextEl: ".glry-next",
        prevEl: ".glry-prev",
    },

    breakpoints: {
        768: {
            slidesPerView: 1,
            spaceBetween: 0,
        },
        768: {
            slidesPerView: 1,
            spaceBetween: 0,
        },
        992: {
            slidesPerView: 2,
            spaceBetween: 0,
        },
        1200: {
            slidesPerView: 2,
            spaceBetween: 0,
        },
        1900: {
            slidesPerView: 1.68,
            spaceBetween: 0,
        }
    }
});

const SpeakSwipertwo = new Swiper('.speak_swiper', {
    loop: true,
    effect: 'fade',
    fadeEffect: {
        crossFade: true,
    },

    // autoplay: {
    //     delay: 2500,
    //     disableOnInteraction: false,
    //     pauseOnMouseEnter: true,
    // },

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



// Custom Tab Area
document.addEventListener('DOMContentLoaded', function () {
  const tabs = document.querySelectorAll('.tab-link');
  const tabPanes = document.querySelectorAll('.tab-pane');

  tabs.forEach(tab => {
    tab.addEventListener('click', function () {
      if (tab.classList.contains('disabled')) return;

      tabs.forEach(t => t.classList.remove('active'));
      tabPanes.forEach(pane => pane.classList.remove('fade', 'active'));

      tab.classList.add('active');
      const targetPane = document.querySelector(tab.getAttribute('data-target'));

      if (targetPane) {
        targetPane.classList.add('fade', 'active');
      }
    });
  });
});

// Tab To Accordion Js Start
jQuery(function ($) {

    function getContentWrap($tabsWrap) {
        return $tabsWrap.next('.accordion-tabcontent');
    }

    function initAccordion($tabsWrap) {

        const $contentWrap = getContentWrap($tabsWrap);

        let $activeTab = $tabsWrap.find('.tab-button.active');

        if (!$activeTab.length) {
            $activeTab = $tabsWrap.find('.tab-button').first().addClass('active');
        }

        const id = $activeTab.data('actab-id');

        $contentWrap.find('.accordiontab-item').removeClass('active');

        const $target = $contentWrap.find(
            '.accordiontab-item[data-actab-id="' + id + '"]'
        );

        $target.addClass('active');
    }

    /* ===== INIT ALL ACCORDIONS ===== */
    $('.accordion-tabs').each(function () {
        initAccordion($(this));
    });

    /* ===== TAB CLICK ===== */
    $(document).on('click', '.tab-button', function () {

        const $btn = $(this);
        const $tabsWrap = $btn.closest('.accordion-tabs');
        const $contentWrap = getContentWrap($tabsWrap);

        const id = $btn.data('actab-id');

        $tabsWrap.find('.tab-button').removeClass('active');
        $btn.addClass('active');

        $contentWrap.find('.accordiontab-item').removeClass('active');

        const $target = $contentWrap.find(
            '.accordiontab-item[data-actab-id="' + id + '"]'
        );

        $target.addClass('active');

        $target.find('.accordion-tabs').each(function () {
            initAccordion($(this));
        });

    });

    /* ===== ACCORDION LABEL CLICK ===== */
    $(document).on('click', '.accordiontab_btn', function () {

        $(this)
            .closest('.accordiontab-item')
            .toggleClass('active');

    });

});
// Tab To Accordion Js End
// Accordion Js Start

jQuery(function ($) {
  $(".accordions").each(function () {
    var $acc = $(this);
    $acc.find(".accordions-collapse").hide();
    $acc.find(".accordions-button.active").each(function () {
      $(this).add($(this).next(".accordions-collapse")).addClass("active");
      $(this).next(".accordions-collapse").show();
    });
    $acc.find(".accordions-collapse.active").each(function () {
      var $btn = $(this).prev(".accordions-button");
      if ($btn.length && !$btn.hasClass("active")) $btn.addClass("active");
      $(this).show();
    });
  });
  $(document).on("click", ".accordions-button", function (e) {
    e.preventDefault();

    var $btn = $(this);
    var $panel = $btn.next(".accordions-collapse");
    var $wrapper = $btn.closest(".accordions");
    $wrapper.find(".accordions-collapse").stop(true, true);
    if ($wrapper.hasClass("single_accordions")) {
      if (!$btn.hasClass("active")) {
        $wrapper.find(".accordions-button").removeClass("active");
        $wrapper.find(".accordions-collapse").removeClass("active").slideUp();
        $btn.addClass("active");
        $panel.addClass("active").slideDown();
      } else {
        $btn.removeClass("active");
        $panel.removeClass("active").slideUp();
      }
    } else {
      if ($btn.hasClass("active")) {
        $btn.removeClass("active");
        $panel.removeClass("active").slideUp();
      } else {
        $btn.addClass("active");
        $panel.addClass("active").slideDown();
      }
    }
  });

});


document.addEventListener('DOMContentLoaded', function () {

    // Show selected file name
    document.addEventListener('change', function (e) {
        if (e.target.matches('.upload_file input[type="file"]')) {
            showFileName(e.target);
        }
    });

    // Click on text to open file dialog
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('file-name')) {
            const input = e.target.closest('.upload_file')?.querySelector('input[type="file"]');
            if (input) input.click();
        }
    });

    function showFileName(input) {
        const wrapper = input.closest('.upload_file');
        if (!wrapper) return;

        const fileName = wrapper.querySelector('.file-name');

        if (input.files && input.files.length) {
            fileName.textContent = input.files[0].name.replace(
                /^(.{4,10}).*(\.[^.]*)$/,
                '$1$2'
            );
        } else {
            fileName.textContent = 'Upload Registration Certificate (copy)';
        }
    }

    // Function to clear the file input
    window.clearUploadFile = function (selector) {
        const input = document.querySelector(selector);
        if (!input) return;

        input.value = '';

        const wrapper = input.closest('.upload_file');
        if (!wrapper) return;

        wrapper.querySelector('.file-name').textContent =
            'Upload Registration Certificate (copy)';
    };

});
})(jQuery);
