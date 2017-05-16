<?php

// Template Name: About Us

$hero_image = get_field('hero_image');
$hero_image_url = $hero_image["url"];

get_header();

?>

<section class="hero image-medium" style="background-image:url('<?php echo $hero_image_url; ?>');">
</section>

<?php if( have_rows('content_modules') ):

    while ( have_rows('content_modules') ) : the_row();

    	$image = get_sub_field('image');
    	$image_url = $image['url'];

		?>
		<a name="<?php the_sub_field('page_anchor_name'); ?>"></a>
		<section class="wysiwyg two-col">
			<div class="wrapper">
				<h1><?php the_sub_field('title'); ?></h1>
				<?php 
					$content_box_1 = get_sub_field('content_box_1');
					$content_box_2 = get_sub_field('content_box_2');
					$style = (empty($content_box_2))? "width:auto;" : "";
				?>
				<p style="<?php echo $style; ?>"><?php echo $content_box_1 ?></p>
				<?php if(!empty($content_box_2)): ?>
					<p><?php echo $content_box_2; ?></p>
				<?php endif; ?>
			</div>
		</section>
		<?php $style = (strtolower(get_sub_field('title')) == "award winners")? "width:95%" :""; ?>
		<?php $style2 = (strtolower(get_sub_field('title')) == "award winners")? "width:100%" :""; ?>
		<section class="hero no-background" style="<?php echo $style; ?>">
			
			<div class="wrapper" style="<?php echo $style2; ?>">
				<?php $image_link = get_sub_field('image_link');
				if(!$image_link): ?>
					<img class="image" src="<?php echo $image_url; ?>" />
				<?php else: ?>
					<a href="<?php echo $iamge_link; ?>" class="hero-link">
						<img class="image" src="<?php echo $image_url; ?>" />
					</a>
				<?php endif; ?>
			</div>
		</section>

        <?php 

    endwhile;

endif;

if( have_rows('page_links') ): ?>

	<section class="thumbs">
		<div class="wrapper">

		<?php while ( have_rows('page_links') ) : the_row(); 
	    	$thumb_image   = get_sub_field('thumb_image');
	    	$thumb_url     = $thumb_image['url'];
	    	$external_link = get_sub_field('external_link');
	    	$url = get_sub_field('url');
	    	$page_link = get_sub_field('page_link');
				/*?>
				<a href="<?php if(!$external_link) { echo $page_link; } else { echo $url; } ?>" class="button" style="background-image: url('<?php echo $thumb_url ?>')" <?php if($external_link) { ?> target="_blank" rel="no-rel"<?php } ?>>
					<h4><?php the_sub_field('title'); ?></h4>
				</a>
				*/?>
				<a href="<?php echo (!$external_link)? $page_link : $url; ?>" class="button" <?php if($external_link): ?> target="_blank" rel="no-rel"<?php endif; ?>><?php the_sub_field('title'); ?>
				</a>
	        <?php 
	    endwhile;
		?>
		</div>
	</section>

<?php endif;

get_footer(); 

?>