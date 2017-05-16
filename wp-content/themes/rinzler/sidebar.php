<aside>
	<h3 id="sidebar-toggle"><i class="fa fa-filter"></i>Search &amp; Filter Blog Posts</h3>
	<span>
		<div class="search search-trigger">
			<p>Search</p>
			<i class="fa fa-search"></i>
		</div>
		<div class="categories">
		<?php if (is_single() || is_archive()) { ?>
			<h4>Related Categories</h4>
			<?php echo get_the_category_list(); ?>
		<?php } else { ?>
			<h4>All Categories</h4>
			<ul>
	        <?php
	            $args = array(
	                'title_li'           => __( '' ),
	                'taxonomy'           => 'category',
	            );
	            wp_list_categories( $args );
	        ?>
	        </ul>
		<?php } ?>
		</div>
		<div class="archives">
			<h4>Archive</h4>
			<ul>
			<?php $args = array(
				'type'            => 'monthly',
				'limit'           => '8',
				'format'          => 'html', 
				'show_post_count' => false,
				'echo'            => 1,
				'order'           => 'DESC',
		        'post_type'     => 'post'
			);
			wp_get_archives( $args ); ?>
			</ul>
		</div>
	</span>
</aside>