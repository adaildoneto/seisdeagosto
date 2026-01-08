$(window).scroll(function(){
  var sticky = $('#headnev'),
      scroll = $(window).scrollTop();

  if (scroll >= 400) sticky.addClass('fixed-top');
  else sticky.removeClass('fixed-top');
});

$(document).ready(function(){
  $('#slides1').slick({
        dots: true,
  		infinite: true,
 		speed: 500,
  		fade: true,
  		cssEase: 'linear',
        autoplay: true
      });
});

$(document).ready(function(){
  $('#slides2').slick({
        dots: true,
  		infinite: true,
 		speed: 700,
  		fade: true,
  		cssEase: 'linear',
        autoplay: true
      });
});

$(document).ready(function(){
$('.colunistas').slick({
  dots: true,
  infinite: true,
  speed: 300,
  arrows: true,
  autoplay: true,  
  slidesToShow: 4,
  slidesToScroll: 1,
  responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        infinite: true,
        arrows: true,
  		autoplay: true,
        dots: true
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 2,
            arrows: true,
  autoplay: true,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        arrows: true,
  		autoplay: true,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
});
});

$(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });

$(document).ready(function () {
            $('#sidebarCollapse3').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });

$(document).ready(function(){
  $('.sidecolunistas').slick({
        dots: true,
  		infinite: true,
 		speed: 700,
  		fade: true,
  		cssEase: 'linear',
        autoplay: true
      });
});

$(document).ready(function(){
	var btnDelete = document.getElementById('clear');
      var inputFocus = document.getElementById('inputFocus');
      //- btnDelete.on('click', function(e) {
      //-   e.preventDefault();
      //-   inputFocus.classList.add('isFocus')
      //- })
      //- inputFocus.addEventListener('click', function() {
      //-   this.classList.add('isFocus')
      //- })
      btnDelete.addEventListener('click', function(e)
      {
        e.preventDefault();
        inputFocus.value = ''
      })
      document.addEventListener('click', function(e)
      {
        if (document.getElementById('first').contains(e.target))
        {
          inputFocus.classList.add('isFocus')
        }
        else
        {
          // Clicked outside the box
          inputFocus.classList.remove('isFocus')
        }
      });
    });
