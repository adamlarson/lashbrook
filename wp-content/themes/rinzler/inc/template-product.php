<?php

// Template Name: Single Product
// Not sure if this a Magento Page.
// No ACF Fields are pulled onto this page

get_header();

?>

<section class="product">
    <div class="wrapper">
        <div class="product-images">
            <img class="main-img" src="/wp-content/uploads/2016/02/main-gallery.png" />
            <div class="gallery">
                <div class="gallery-icon">
                    <img src="/wp-content/uploads/2016/02/gallery-item.png" />
                </div>
                <div class="gallery-icon">
                    <img src="/wp-content/uploads/2016/02/gallery-item.png" />
                </div>
                <div class="gallery-icon">
                    <img src="/wp-content/uploads/2016/02/gallery-item.png" />
                </div>
                <div class="gallery-icon">
                    <img src="/wp-content/uploads/2016/02/gallery-item.png" />
                </div>
            </div>
        </div>
        <div class="product-details">
            <div class="product-info">
                <h4 class="finish">CC9F17/METEORITEPOLISH</h4>
                <h1>customizable Meteorite ring</h1>
                <p>This is subtext describing the product list here. Maybe calling out some features or dimensions.</p>
            </div>
            <div class="product-customize">
                <h2>Customize your ring</h2>
                <div class="product-custom-option">
                    <h6>Base</br>Metal</h6>
                </div>
                <div class="product-custom-option">
                    <h6>Ring</br>Inlay</h6>
                </div>
                <div class="product-custom-option">
                    <h6>Ring</br>Finish</h6>
                </div>
                <div class="product-custom-option">
                    <h6>Ring</br>Size</h6>
                </div>
                <div class="product-custom-wide">
                </div>
            </div>
            <div class="product-links">
                <div class="product-pricing">
                    <h2>$1,0000,0000</h2>
                    <p>add to wishlist</p>
                    <div class="links">
                        <button class="gold">BUY NOW</button>
                        <button class="gold">FIND IN STORE</button>
                    </div>
                </div>
                <div class="product-buttons">
                    <a href="">
                        <i class="fa fa-facebook"></i>
                    </a>
                    <a href="">
                        <i class="fa fa-twitter"></i>
                    </a>
                    <a href="">
                        <i class="fa fa-pinterest"></i>
                    </a>
                    <a href="">
                        <i class="fa fa-instagram"></i>
                    </a>
                    <a href="">
                        <i class="fa fa-print" onClick="window.print();return false"></i>
                    </a>
                    <a href="">
                        <i class="fa fa-envelope"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZ41wUpMzn7lw7EGBxX_j6hjuQ6WZfDLA"></script>
<script>
    function initialize() {
        var mapOptions = {
            zoom: 16,
            scrollwheel: false,
            center: new google.maps.LatLng(40.234185, -111.663201),
            styles: [{"featureType":"water","elementType":"all","stylers":[{"hue":"#7fc8ed"},{"saturation":55},{"lightness":-6},{"visibility":"on"}]},{"featureType":"water","elementType":"labels","stylers":[{"hue":"#7fc8ed"},{"saturation":55},{"lightness":-6},{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"hue":"#83cead"},{"saturation":1},{"lightness":-15},{"visibility":"on"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"hue":"#f3f4f4"},{"saturation":-84},{"lightness":59},{"visibility":"on"}]},{"featureType":"landscape","elementType":"labels","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"on"}]},{"featureType":"road","elementType":"labels","stylers":[{"hue":"#bbbbbb"},{"saturation":-100},{"lightness":26},{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"hue":"#ffcc00"},{"saturation":100},{"lightness":-35},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"hue":"#ffcc00"},{"saturation":100},{"lightness":-22},{"visibility":"on"}]},{"featureType":"poi.school","elementType":"all","stylers":[{"hue":"#d7e4e4"},{"saturation":-60},{"lightness":23},{"visibility":"on"}]}]
        }


        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        var image = '';
                                              
        new google.maps.Marker({
            animation: google.maps.Animation.DROP,
            position: new google.maps.LatLng(40.234185, -111.663201),
            map: map,
            icon: image
        });                                                            
    }

    google.maps.event.addDomListener(window, 'load', initialize);

</script>

<section class="map" id="map-canvas">
</section>

<section class="product-info">
    <a class="anchor-tag" id="product-info" >
    </a>
	<div class="wrapper">
		<img src="/wp-content/uploads/2016/02/meteoriteBkrd.png" class="product-image" />
		<div class="product-description">
			<img class="icon" src="/wp-content/uploads/2016/02/meteroiteIcon.png">
			<h2>Meteorite</h2>

			<p>Lashbrook uses only authentic Gibeon Meteorite, from the West African country of Namibia. Gibeon Meteorite is found over an area 171 miles long and 62 miles wide. It is classified as a fine octahedrite, where the metal shows crystalline structure normally found only in gemstones. This visible crystal structure, or Widmanstatten pattern, was caused by the extremely slow cooling of the material at the core of the asteroid in space. As one of the most inert of the larger meteorites found on Earth, it is less prone to oxidation and has a better Widmanstatten pattern due to the presence of a small amount of nickel.</p>

			<p>For most people, the meteorite should remain inert and rust-free during the course of normal wear. Because of this natural rust inhibition, Gibeon is one of the most popular types of Meteorite used in jewelry. With ever-increasing demand, the availability of raw material continues to decrease thus increasing its rarity and price.</p>

			<p>Each piece of Meteorite cut for a ring has its own unique characteristics. The lines and pattern will vary between pieces, as will the presence of random inclusions. These inclusions are typically seen as dark spots or tiny holes on the surface of the Meteorite and are often rare traces of metal that represent combinations of materials that, in some cases, do not occur naturally on Earth. It is these rare inclusions that geologists often use to identify authenticity of a meteorite and through which they can identify its origin. Due to this natural variation in the Meteorite and because of its increasing rarity, you should anticipate that each piece of Meteorite and each ring will be slightly unique.</p>

			 <p>Lashbrook produces styles incorporating Meteorite in contemporary metals, precious metals and combinations of the two. Meteorite can be inlaid in the center of a ring or off-center, in either a round or square band. It can be also be made with a sleeve or inlaid on the very edge of a ring. Despite its natural protection against rust, Meteorite is primarily composed of iron, so oxidation is always a possibility. Exposure to strong oxidizing agents such as chlorine, bleach or salt can change the pH of the metal and increase chances of rusting. These chemicals, found in some household cleaners, pools, hot tubs, and salt water, should be avoided.</p>

            <a href="#product-info">
                <i class="fa fa-angle-up"></i>
            </a>
		</div>
	</div>
</section>

<section class="banner" style="background-image: url('/wp-content/uploads/2016/02/banner.png')">
    <div class="wrapper">
        <img class="banner-item" src="/wp-content/uploads/2016/02/warranty.png" />
        <img class="banner-item" src="/wp-content/uploads/2016/02/sizing.png" />
        <img class="banner-item" src="/wp-content/uploads/2016/02/madeinUSA.png" />
        <img class="banner-item" src="/wp-content/uploads/2016/02/hand-crafter.png" />
        <img class="banner-item" src="/wp-content/uploads/2016/02/award.png" />
    </div>
</section>

<?php

get_footer(); 

?>