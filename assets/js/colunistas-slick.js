(function($){
  function initColunistasSlick(){
    var $grid = $('.colunistas-grid');
    if(!$grid.length) return;

    var viewport = window.innerWidth || document.documentElement.clientWidth;
    if (viewport < 768) {
      $grid.each(function(){
        var $g = $(this);
        if (!$g.hasClass('slick-initialized')) {
          // Move direct child columns into slick
          $g.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            mobileFirst: true,
            dots: true,
            arrows: true,
            infinite: false,
            adaptiveHeight: true,
            responsive: [
              {
                breakpoint: 768,
                settings: 'unslick'
              }
            ]
          });
        }
      });
    } else {
      // Unslick on desktop to restore grid
      $grid.filter('.slick-initialized').slick('unslick');
    }
  }

  $(document).ready(function(){
    initColunistasSlick();
    $(window).on('resize', function(){
      // Debounce resize
      clearTimeout(window.__colunistasResizeTimer);
      window.__colunistasResizeTimer = setTimeout(initColunistasSlick, 150);
    });

    // Touch activation for hover effect on mobile
    $(document).on('touchstart', '.colunista-card', function(){
      var $c = $(this);
      $c.addClass('touch-active');
      var t = $c.data('touchTimer');
      if (t) { clearTimeout(t); }
      $c.data('touchTimer', setTimeout(function(){
        $c.removeClass('touch-active');
      }, 1000));
    });
  });
})(jQuery);
