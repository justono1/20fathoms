(function($) {

  $(window).scroll(function(e) {
    var t = $(this).scrollTop();
  
    var navUpdated = false;
    if(t > 1 && navUpdated === false) {
      $('.site-header').addClass('is-scrolled');
      navUpdated = true;
    }
  
    if(t <= 0) {
      $('.site-header').removeClass('is-scrolled');
      navUpdated = false;
    }
  });

  $(window).resize(function() {
    var w = $(this).outerWidth() + 15;
    if(w > 599) {
      $(".menu").attr('style', '');
    }
  });

  $(document).ready(function() {
    $(".mobile-nav-trigger").on("click", function() {
      $(".menu").fadeToggle();
    });
  });
  
})(jQuery);
