// var url_controller = baseUrl + '/home/';
// var save_method;
// var id_use = 0;
// var performance1, performance2;

$(document).ready(function () {
  typed();
  swiper();
});

// typed js
function typed() {
  new Typed('#typed', {
    strings: ['amanah', 'transparan'],
    typeSpeed: 100,
    delaySpeed: 100,
    loop: true,
  });
}

// swiper js
function swiper() {
  var swiper = new Swiper('.slide-container', {
    slidesPerView: 4,
    spaceBetween: 20,
    sliderPerGroup: 4,
    loop: true,
    centerSlide: 'true',
    fade: 'true',
    grabCursor: 'true',
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
      dynamicBullets: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
      },
      520: {
        slidesPerView: 2,
      },
      768: {
        slidesPerView: 2,
      },
      850: {
        slidesPerView: 3,
      },
      1000: {
        slidesPerView: 4,
      },
    },
  });
}
