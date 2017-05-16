<?php

$image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ), 'full' );
$default_image = get_field('default_blog_image','options');

get_header();

?>

<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
			?>
			<section class="blog">
				<div class="wrapper">
					<div class="blog-index">
						<div class="post">
							<div class="date">
								<span class="date-month"><?php the_time('M') ?></span>
                <span class="date-day"><?php the_time('d') ?></span>
                <span class="date-year"><?php the_time('Y') ?></span>
							</div>
							<div class="share social-icons">
								<a href="http://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>&title=<?php the_title(); ?>" target="_blank">
									<i class="fa fa-facebook"></i>
								</a>
								<a href="http://twitter.com/intent/tweet?status=<?php the_title(); ?>+<?php the_permalink(); ?>" target="_blank" >
									<i class="fa fa-twitter"></i>
								</a>
								<?php if($image) { ?>
									<a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php echo $image[0]; ?>&description=<?php echo $excerpt ?>" target="_blank">
										<i class="fa fa-pinterest"></i>
									</a>
								<?php } ?>
							</div>
							<div class="content">
								<?php if ($image) { ?>
									<a href="<?php the_permalink();?>">
										<img src="<?php echo $image[0]; ?>" />
									</a>
								<?php } ?>
								<p class="list-title">Category:</p> <?php echo get_the_category_list(); ?>
								<h2><?php the_title(); ?></h2>
								<p><?php the_content(); ?></p>
							</div>
						</div>
						<?php get_sidebar(); ?>
					</div>
				</div>
			</section>
			<?php
		}
	}
?>

<?php

get_footer();

?>
