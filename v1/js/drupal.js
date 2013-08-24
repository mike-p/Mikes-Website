$(function() {
	$(".drupal .drupal-modules h5 a").hover(function(){
    $(".drupal .drupal-modules h5 a").removeClass("active");
    $(this).addClass("active");

    $(".drupal .drupal-modules li div").removeClass("active");
    $(this).parent().parent().find("div").addClass("active");

    return false;
  });
  $(".drupal .drupal-modules h5 a").click(function(){
    return false;
  });
  
  if($(".home").length) {

	  $(window).scroll(function(){
		 if(!$(".homepage .nav-placeholder").length) {
		 	 $("<div class='nav-placeholder' style='height:51px; float:left; width:600px'>&nbsp;</div>").insertAfter($(".strapline"));
		 }
		
	      $(".homepage>nav").css({"zIndex":"999","position":"fixed", "top": Math.max(0,490-$(this).scrollTop())});
	  });
  }
  
  $('.homepage>nav>ul>li>a').click(function(){
      $('html, body').animate({
          scrollTop: $( $(this).attr('href') ).offset().top
      }, 500);
      return false;
  })
});
