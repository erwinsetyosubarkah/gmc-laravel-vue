// script.js

window.initMap = function() {};

// 1. SCROLL TO TOP (Berjalan langsung)
(function ($) {
  'use strict';
  $(window).on('scroll', function () {
    if ($(window).scrollTop() > 70) {
        $('.backtop').addClass('reveal');
    } else {
        $('.backtop').removeClass('reveal');
    }
  });
})(jQuery);

// 2. DAFTARKAN FUNGSI KE WINDOW AGAR BISA DIPANGGIL SETELAH IMPORT
window.initNovenaPlugins = function() {
  (function ($) {
    'use strict';

    // Reset Slick Slider agar tidak double inisialisasi pada v-for
    if ($('.portfolio-single-slider').hasClass('slick-initialized')) $('.portfolio-single-slider').slick('unslick');
    if ($('.clients-logo').hasClass('slick-initialized')) $('.clients-logo').slick('unslick');
    if ($('.testimonial-wrap').hasClass('slick-initialized')) $('.testimonial-wrap').slick('unslick');
    if ($('.testimonial-wrap-2').hasClass('slick-initialized')) $('.testimonial-wrap-2').slick('unslick');

    // Slick Slider Inisialisasi
    $('.portfolio-single-slider').slick({ infinite: true, arrows: false, autoplay: true, autoplaySpeed: 2000 });

    $('.clients-logo').slick({
      infinite: true, arrows: false, autoplay: true, slidesToShow: 6, slidesToScroll: 6, autoplaySpeed: 6000,
      responsive: [
        { breakpoint: 1024, settings: { slidesToShow: 6, slidesToScroll: 6, infinite: true, dots: true } },
        { breakpoint: 900, settings: { slidesToShow: 4, slidesToScroll: 4 } },
        { breakpoint: 600, settings: { slidesToShow: 4, slidesToScroll: 4 } },
        { breakpoint: 480, settings: { slidesToShow: 2, slidesToScroll: 2 } }
      ]
    });

    $('.testimonial-wrap').slick({
      slidesToShow: 1, slidesToScroll: 1, infinite: true, dots: true, arrows: false, autoplay: true, vertical: true, verticalSwiping: true, autoplaySpeed: 6000,
      responsive: [
        { breakpoint: 1024, settings: { slidesToShow: 1, slidesToScroll: 1, infinite: true, dots: true } },
        { breakpoint: 480, settings: { slidesToShow: 1, slidesToScroll: 1 } }
      ]
    });

    $('.testimonial-wrap-2').slick({
      slidesToShow: 2, slidesToScroll: 2, infinite: true, dots: true, arrows: false, autoplay: true, autoplaySpeed: 6000,
      responsive: [
        { breakpoint: 1024, settings: { slidesToShow: 2, slidesToScroll: 2, infinite: true, dots: true } },
        { breakpoint: 480, settings: { slidesToShow: 1, slidesToScroll: 1 } }
      ]
    });

    // Google Maps
    var google_map_canvas = $('#map-canvas');
    if (google_map_canvas.length && typeof google !== 'undefined') {
      var mapOptions = { zoom: 13, center: new google.maps.LatLng(50.97797382271958, -114.107718560791) };
      new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    }

    // Counter Up
    $('.counter-stat span').counterUp({ delay: 10, time: 1000 });

    // Shuffle.js Filter
    var Shuffle = window.Shuffle;
    var containerShuffle = document.querySelector('.shuffle-wrapper');
    if (containerShuffle) {
      var myShuffle = new Shuffle(containerShuffle, { itemSelector: '.shuffle-item', buffer: 1 });
      $('input[name="shuffle-filter"]').off('change').on('change', function (evt) {
        var input = evt.currentTarget;
        if (input.checked) { myShuffle.filter(input.value); }
      });
    }

    // Vanilla JS membaca elemen v-for global
    const listElemen = document.querySelectorAll('.shuffle-item, .testimonial-wrap, .counter-stat');

  })(jQuery);
};
