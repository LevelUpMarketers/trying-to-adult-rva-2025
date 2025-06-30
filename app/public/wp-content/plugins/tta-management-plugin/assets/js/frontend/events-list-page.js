(function($){
  var headerSelector = '.site-header, .tta-header';
  var extraOffset    = 200;

  function getHeaderHeight(){
    return $(headerSelector).first().outerHeight() || 0;
  }

  $(function(){
    $('.tta-scroll-login').on('click', function(e){
      e.preventDefault();
      var target = $('#loginform');
      if(target.length){
        $('html, body').animate({
          scrollTop: target.offset().top - getHeaderHeight() - extraOffset
        }, 600);
      }
    });
  });
})(jQuery);
