// Init Hero Widget Carousel
jQuery( ".carousel-item" ).first().addClass( "active" );

$(document).ready(function(){
	$( ".location_name" ).addClass( "d-inline" );
  	$( ".time_symbol" ).addClass( "d-inline" );
  	$( ".climacon" ).addClass( "d-inline" );
  	$( ".time_temperature" ).addClass( "d-inline" );
  	$( ".short_condition" ).addClass( "d-inline" );
  	$( ".today" ).addClass( "d-inline" );
  	$( ".day" ).addClass( "d-inline" );

	// Destaque Home Mobile - Autoplay slider only on mobile
	if ($(window).width() <= 768) {
		if ($('.destaques-home-mobile-slider').length && !$('.destaques-home-mobile-slider').hasClass('slick-initialized')) {
			$('.destaques-home-mobile-slider').slick({
				autoplay: true,
				autoplaySpeed: 3000,
				dots: true,
				arrows: false,
				infinite: true,
				speed: 500,
				slidesToShow: 1,
				slidesToScroll: 1,
				pauseOnHover: true,
				pauseOnFocus: true,
				accessibility: false
			});
		}
	}

	// Categories slider - Mobile only (swipe navigation, no arrows/dots)
	function initCategoriesSlider() {
		var $catSlider = $('.categories-slider-wrapper .navbar-nav');
		var isMobile = $(window).width() <= 767;
		
		if ($catSlider.length) {
			if (isMobile && !$catSlider.hasClass('slick-initialized')) {
				$catSlider.slick({
					arrows: false,
					dots: false,
					infinite: false,
					speed: 300,
					slidesToShow: 3,
					slidesToScroll: 1,
					variableWidth: true,
					swipeToSlide: true,
					touchThreshold: 10,
					accessibility: false,
					mobileFirst: true,
					centerMode: false,
					responsive: [
						{
							breakpoint: 480,
							settings: {
								slidesToShow: 2
							}
						}
					]
				});
			} else if (!isMobile && $catSlider.hasClass('slick-initialized')) {
				// Destroy slider on desktop
				$catSlider.slick('unslick');
			}
		}
	}
	
	// Init on load
	initCategoriesSlider();
	
	// Re-init on resize (with debounce)
	var resizeTimer;
	$(window).on('resize', function() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function() {
			initCategoriesSlider();
		}, 250);
	});
});


