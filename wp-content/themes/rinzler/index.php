<?php

get_header();

?>

<section class="hero image-large overlay" style="background-image: url('http://lashbrook.dev/wp-content/uploads/2016/02/article.png')">
	<div class="wrapper">
		<strong></strong>
		<h1>This is a featured</br>article title</h1>
		<button>Read More</button>
	</div>
</section>

<section class="blog">
	<div class="wrapper">
		<div class="blog-index">
			<?php 
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post(); 
					$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
					$excerpt = get_the_excerpt();
					?>
						<div class="post">
							<div class="date">
								<span class="date-month"><?php the_time('M') ?></span>
                                <span class="date-day"><?php the_time('d') ?></span>
                                <span class="date-year"><?php the_time('Y') ?></span>
							</div>
							<div class="content">
								<?php if ($image) { ?>
									<a href="<?php the_permalink();?>">
										<img src="<?php echo $image[0]; ?>" />
									</a>
								<?php } ?>
								<p class="list-title">Category:</p> <?php echo get_the_category_list(); ?>
								<a href="<?php the_permalink();?>">
									<h2><?php the_title(); ?></h2>
								</a>
								<p><?php echo $excerpt ?></p> 
								<div class="hot-links">
									<a href="<?php the_permalink();?>">
										<button>
											READ MORE
										</button>
									</a>
									<div class="social-icons">
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
								</div>
							</div>
						</div>
					<?php 
				} 
			} 
			?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</section>

<?php

get_footer(); 

?>