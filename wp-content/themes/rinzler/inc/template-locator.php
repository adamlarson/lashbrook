<?php /* Template Name: Locator Page */ get_header(); ?>

  <section id="locator" class="wysiwyg">
    <div class="container">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <img src="<?php echo get_template_directory_uri(); ?>/images/icon-location.png" alt="Location Icon" />
          <h1><?php the_field('page_title'); ?></h1>
          <div class="button-set">
            <button id="near-me-btn">FIND NEAR ME</button>
            <span id="near-me-or">or</span>
            <div class="find-me">
              <input id="address-search" type="text" name="zipcode" value="" placeholder="Enter a zip or address">
              <input id="address-search-mag" type="submit" name="submit" value="">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section id="map">
    <div id="storelocations" class="storelocations"></div>
    <div id="store-popup" class="store-popup off">
        <i class="fa fa-close"></i>
        <div class="store-info">
            <h6 class="store-name">
                Store Name
            </h6>
            <div class="store-logo"></div>
            <p class="store-address">
                Address
            </p>
            <p class="store-phone">
                999-999-9999
            </p>
            <p class="store-web">
                <a href="#" target="_blank">#</a>
            </p>
            <p class="store-hours">
                Sometime - sometime
            </p>
        </div>
        <!--img class="store-logo" src="/store/pub/static/frontend/ekr/lashbrook/en_US/images/loader-2.gif" /-->
        <div class="store-collections">
            <h6>
                collections on display
            </h6>
            <div class="store-collections-list">

            </div>
        </div>
    </div>
    <image class="tail off" src="/store/pub/media/images/word-bubble-tail.png" />
    <script src="/wp-content/themes/rinzler/js/map.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?callback=initMap"></script>
  </section>
<?php get_footer(); ?>
