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
});


