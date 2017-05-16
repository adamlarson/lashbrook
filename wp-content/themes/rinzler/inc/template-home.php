<?php

// Template Name: Home

$promo_background     = get_field('promo_background');
$promo_background_url = $promo_background["url"];

get_header();

?>

<section class="owl-carousel owl-theme">
	<?php if( have_rows('main_slider') ): ?>
		<?php while( have_rows('main_slider') ): the_row();
			$image     = get_sub_field('image');
			$image_url = $image['url'];
			?>
			<section class="hero image-large " style="background-image: url('<?php echo $image_url ?>')">
				<div class="wrapper">
					<strong><img src="<?php the_sub_field('icon'); ?>"></strong>
					<h1><?php the_sub_field('text'); ?></h1>
					<?php if( have_rows('buttons') ): ?>
						<?php while( have_rows('buttons') ): the_row();
							?>
							<a href="<?php the_sub_field('link'); ?>">
								<button>
									<?php the_sub_field('text'); ?>
								</button>
							</a>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
			</section>
		<?php endwhile; ?>
	<?php endif; ?>
</section>

<section class="ring-slider">
	<div class="icon"><img src="<?php the_field('ring_slider_icon'); ?>"></div>
	<h2><?php the_field('ring-slider-title'); ?></h2>
</section>
<section class="ring-collection">
	<img class="left-fader" src="/store/pub/media/images/rings/fader.png" />
    <img class="right-fader" src="/store/pub/media/images/rings/fader.png" />
    <div class="left-arrow" style=""><i class="fa fa-angle-left"></i></div>
    <div class="right-arrow" style=""><i class="fa fa-angle-right"></i></div>
	<div class="rings-holder" id="ring-holder">
		<?php if( have_rows('ring_slider_images') ): ?>
			<?php while( have_rows('ring_slider_images') ): the_row();
					$i++;
					?>
					<img class="ring ring<?= $i ?>" src="<?php the_sub_field('image'); ?>" alt="<?php the_sub_field("ring_name") ?>" data-link="<?php the_sub_field("store_link") ?>" />
				<?php endwhile; ?>
		<?php endif; ?>
		
		<div class="ring-data">
	        <a class="ring-name" href="">
	            RING NAME HERE
	        </a>
	        <a href="" class="ring-link">View <i class="fa fa-caret-right"></i></a>
	    </div>
	</div>
</section>
<script src="/wp-content/themes/rinzler/js/ringcollection.js"></script>
<script>

    new RingCollection(document.querySelector(".ring-collection"));

</script>
<section class="promo">
	<div class="wrapper" style="background-image:url('<?php echo $promo_background_url; ?>');">
		<div class="overlay">
			<div class="title-holder">
				<div class="metal-icon" style="background-image: url('<?php the_field('metal_icon'); ?>')"></div>
				<h2><?php the_field('promo_title'); ?></h2>
			</div>
			<div style="clear:both"></div>
			<?php the_field('promo_text'); ?>
			<a href="<?php the_field('promo_button_url'); ?>">
				<button><?php the_field('promo_button_text'); ?></button>
			</a>
		</div>

        <div class="image_wrapper">
		    <img src="<?php the_field('promo_ring_image'); ?>">
        </div>
	</div>
</section>

<!--<section class="awardwinners">-->
<!--	<div class="wrapper">-->
<!--		<div class="golden">-->
<!--			<img class="awards-icon" src="--><?php //echo get_template_directory_uri(); ?><!--/images/icon-awards.png" alt="Awards Icon" />-->
<!--			<h3>--><?php //the_field('award_title'); ?><!--</h3>-->
<!--			--><?php //the_field('award_copy'); ?>
<!--		</div>-->
<!--	</div>-->
<!--	<img class="rings" src="--><?php //the_field('award_rings'); ?><!--">-->
<!--</section>-->

<section class="awardwinners_2 container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="wrapper_2">
                <div class="golden_2" id="awards_banner" data-link="<?php the_field('awards_button_url'); ?>">
                    <img class="awards-icon" src="<?php echo get_template_directory_uri(); ?>/images/icon-awards.png" alt="Awards Icon" />

                    <h3>
                        <?php the_field('award_title'); ?>
                    </h3>

                    <?php the_field('award_copy'); ?>

                    <a href="<?php the_field('awards_button_url'); ?>">
                        <button><?php the_field('awards_button_text'); ?></button>
                    </a>


                </div>
            </div>
        </div>
    </div>
    <div class="rings_container">
        <img class="rings" src="<?php the_field('award_rings'); ?>">
    </div>
</section>
<?php

get_footer();

?>
