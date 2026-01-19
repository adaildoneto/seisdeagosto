(function($){
  $(function(){
    var $sliders = $('.weather-forecast-slider');
    if (!$sliders.length || typeof $.fn.slick !== 'function') return;
    $sliders.each(function(){
      var $el = $(this);
      if ($el.hasClass('slick-initialized')) return;
      $el.slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 4000,
        arrows: false,
        dots: true,
        adaptiveHeight: true,
        accessibility: false
      });
    });
  });
})(jQuery);
