<footer>
	<?php get_footer("main"); ?>
</footer>

<?php wp_footer(); ?>

<?php
//CHECK TO SEE IF WE'RE IN THE MAGENTO OR WORDPRESS SIDE, CHANGE NAMESPACE
if (strpos($_SERVER['PHP_SELF'], '/store/') !== false){
    //do nothing
} else { ?>
    <script type="text/javascript">
        jQuery( document ).ready(function() {
            //if you hover over main menu item with a child, assign class to magic menu
            jQuery("li.main-menu-item.menu-item-depth-0.menu-item-has-children").hoverIntent({ 
        		over: function() {
        			if( jQuery('#magic_sub_menu').hasClass('disappear') || !jQuery('#magic_sub_menu').hasClass('appear') ) {
                        jQuery('#magic_sub_menu').addClass("appear");
                        jQuery('li.main-menu-item.menu-item-depth-0.menu-item-has-children ul').css('z-index', '0');
                        var $me = jQuery(this);
            			$me.addClass("appear");
            			$me.children('li.main-menu-item.menu-item-depth-0 > ul.sub-menu.menu-depth-1').addClass('appear');
                        $me.children('li.sub-menu-item').removeClass("disappear");
            			$me.children('li.sub-menu-item').addClass("appear");
                        $me.children('ul').css('z-index', '10');
            			console.log('appear');
        			}
                },
        		out: function() {
        			jQuery('#magic_sub_menu').removeClass('appear');
                    var $me = jQuery(this);
        			$me.removeClass('appear');
        			$me.children('li.main-menu-item.menu-item-depth-0 > ul.sub-menu.menu-depth-1').removeClass('appear');
        			$me.children('li.sub-menu-item').addClass('appear');
                    $me.children('ul').css('z-index', '10');
        			console.log('disappear');
        		},
                timeout: 175
            });
            jQuery("li.main-menu-item.menu-item-depth-0.menu-item-has-children ul").hoverIntent({ 
                over: function() {
                    
                },
                out: function() {

                }
            });

            function checkMenu() {
                if( 
                  !jQuery('#magic_sub_menu').is(':hover') &&
                  !jQuery('li.main-menu-item.menu-item-depth-0.menu-item-has-children').filter(function() { return $me.is(':hover') }).length > 0
                ) {
                    jQuery('#magic_sub_menu').removeClass('appear');
                    jQuery('#magic_sub_menu').addClass('disappear');
                    jQuery('li.menu-item-has-children').removeClass('appear');
                    console.log('make background disappear');
                }
            }
        });
    </script>
<?php } ?>
<!-- google analytics -->
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-70430530-1']);
    _gaq.push(['_trackPageview']);
    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
<!-- BEGIN PHP Live! HTML Code -->
<span style="color: #0000FF; text-decoration: underline; cursor: pointer;position: fixed; bottom: -3px; right: 0; z-index: 100000;" id="phplive_btn_1496173713" onclick="phplive_launch_chat_0(0)"></span>
<script type="text/javascript">

(function() {
var phplive_e_1496173713 = document.createElement("script") ;
phplive_e_1496173713.type = "text/javascript" ;
phplive_e_1496173713.async = true ;
phplive_e_1496173713.src = "https://t2.phplivesupport.com/lashbrook/js/phplive_v2.js.php?v=0|1496173713|2|" ;
document.getElementById("phplive_btn_1496173713").appendChild( phplive_e_1496173713 ) ;
})() ;

</script>
<!-- END PHP Live! HTML Code -->
</body>
</html>
