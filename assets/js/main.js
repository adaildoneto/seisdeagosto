
// Slick slider para menu categorias no mobile (usando divs)
(function($){
  function enableCategoriesSlick() {
    var $wrapper = $('.categories-slider-wrapper');
    if (!$wrapper.length) return;
    var $nav = $wrapper.find('.navbar-nav');
    if (!$nav.length) return;
    if ($(window).width() <= 768 && typeof $.fn.slick === 'function') {
      if (!$nav.hasClass('slick-initialized')) {
        $nav.slick({
          slidesToShow: 3,
          slidesToScroll: 1,
          arrows: false,
          dots: false,
          infinite: false,
          variableWidth: true,
          swipeToSlide: true,
          touchThreshold: 10,
          mobileFirst: true,
        });
      }
    } else if ($nav.hasClass('slick-initialized')) {
      $nav.slick('unslick');
    }
  }
  $(document).ready(enableCategoriesSlick);
  $(window).on('resize', enableCategoriesSlick);
})(jQuery);
