<?php
// Template Name: Order Authorization
get_header();

?>

<section class="blog">
	<div class="wrapper m_order-eauth">
		<?php 
		if ( have_posts() ):
			while ( have_posts() ):
				the_post(); ?>
					<div class="content">
						<h1><?php the_title(); ?></h1>
						<?php the_content(); ?>
					</div>
				<?php 
			endwhile;
		endif;?>
	</div>
</section>

<?php

get_footer(); 

?>
