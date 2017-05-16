jQuery(document).ready(function($) {

    function ekr_toogleMobileNav(){
      var  mnav = jQuery('header nav');

        if (mnav.is(':visible')) {
            mnav.fadeOut(225);
            mbutton.removeClass('active');
        }
        else {
            mnav.fadeIn(225);
            mbutton.addClass('active');
        }
    }

    // awards banner
    var awardsBanner = jQuery('#awards_banner');
    if(awardsBanner.length > 0){
      awardsBanner.on('click',function(e){
        window.location.href = this.getAttribute('data-link');
      });
    }

    // Mobile-Nav

    var mbutton = jQuery('.mobileToggle');
    var nClose = jQuery('nav .overlay-close');

    nClose.click( function() {
      ekr_toogleMobileNav();
    });
    mbutton.click( function() {
      ekr_toogleMobileNav();
    });

    //Header Scroll

    jQuery(window).scroll(function () {
      jQuery('header').toggleClass("dark-header", (jQuery(window).scrollTop() > 50));
      if( jQuery(window).scrollTop() > 50 ){
        jQuery('li.main-menu-item.menu-item-depth-0.menu-item-has-children:after').css("display","none");
      } else {
        jQuery('li.main-menu-item.menu-item-depth-0.menu-item-has-children:after').css("display","block");
      }
      
    	/* if( jQuery('#magic_sub_menu').hasClass('appear') ) {
    		jQuery('#magic_sub_menu').removeClass('appear');
    		jQuery('#magic_sub_menu').addClass('disappear');
    		jQuery('li.menu-item-has-children').removeClass('appear');
    		jQuery('li.sub-menu-item').removeClass('appear');
        jQuery('li.sub-menu-item').addClass('disappear');
    	} */
    });

    //Sidebar Toggle

    var siderToggle = jQuery('#sidebar-toggle'),
        sidebar = jQuery('aside');

    siderToggle.click( function() {

        if (sidebar.hasClass('active')) {
            sidebar.removeClass('active');
        }
        else {
            sidebar.addClass('active');
        }
    });

    //Search Trigger

    var searchTrigger = jQuery('.search-trigger'),
        modal = jQuery('.modal');

    searchTrigger.click( function() {

        if (modal.is(':visible')) {
            modal.fadeOut(225);
        }
        else {
            modal.fadeIn(225);
            jQuery( "#search" ).focus();
        }
    });

    jQuery(document).on('keyup',function(evt) {
        if (evt.keyCode == 27) {
           modal.fadeOut(225);
        }
    });

    //Animate to anchor tag

    jQuery('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
          var target = jQuery(this.hash);
          target = target.length ? target : jQuery('[name=' + this.hash.slice(1) +']');
          if (target.length) {
            jQuery('html, body').animate({
              scrollTop: target.offset().top
            }, 1000);
            setTimeout(focus, 1000);
            return false;
          }
        }
    });

    // Owl Carousel
    if ($.fn.owlCarousel) {
      jQuery('.owl-carousel').owlCarousel({
          autoplay: true,
          autoPlaySpeed: 5000,
          autoPlayTimeout: 5000,
          loop:true,
          margin:0,
          nav:true,
          navText:["<i class='fa fa-angle-left'>","<i class='fa fa-angle-right'>"],
          items:1,
          dots: true,
          touchDrag : false,
          mouseDrag : false
      });
    }


    // Unique page scripts

    jQuery(".u-block #button").click(function(){
      var parent = jQuery(this).parent().parent();
      parent.addClass('expand');
      parent.find('p.short').fadeOut(100);
      parent.find('.long-descriptions').fadeIn(1000);
      jQuery(this).fadeOut(100);
      jQuery('.overlay').fadeIn(300);
    });

    jQuery(".u-block .btn-close").click(function(){
      var parent = jQuery(this).parent();

      parent.removeClass('expand');
      parent.find('p.short').fadeIn(1000);
      parent.find('.long-descriptions').fadeOut(100);
      parent.find('#button').fadeIn(1000);
      jQuery('.overlay').fadeOut(300);
    });

    jQuery(".overlay").click(function(){
	    var block = jQuery('.u-block');
		block.removeClass('expand');
		block.find('p.short').fadeIn(1000);
		block.find('.long-descriptions').fadeOut(100);
		block.find('#button').fadeIn(1000);
		jQuery(this).fadeOut(300);
    });

    jQuery(".mobile-close").click(function(){
	    var block = jQuery('.u-block');
		block.removeClass('expand');
		block.find('p.short').fadeIn(1000);
		block.find('.long-descriptions').fadeOut(100);
		block.find('#button').fadeIn(1000);
		jQuery(".overlay").fadeOut(300);
    });

    var url = window.location.href;
    if(url.indexOf('unique-material') > 0 && url.indexOf('#') > 0){
        var parts = url.split("#");
        var section = parts[1];
        $(".u-block #button." + section).trigger("click");

    }

});
