<?php /* Template Name: Unique Page */ get_header(); ?>

  <section id="unique" class="wysiwyg">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 header">
          <img src="<?php echo get_template_directory_uri(); ?>/images/icon-unique.png" alt="Unique Icon" />
          <h1><?php the_title(); ?></h1>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12 materials">
          <div class="overlay"></div>
          <div class="row">

          <?php $i = 0; ?>
          <?php while(have_rows('material_blocks')) : the_row(); ?>
            <?php $i++;
            $identifier = strtolower(str_replace(" ", "_", get_sub_field('title'))); ?>
            <a name="<?php echo $identifier; ?>"></a>
            <div class="col col-sm-6 <?php echo ($i % 2 == 0) ? 'second' : '' ; ?>">
              <div class="u-block" style="background: url(<?php the_sub_field('background_image'); ?>) no-repeat center center; background-size: cover;">
                <span class="btn-close"></span>
                <div class="content">
                  <img class="icon" src="<?php the_sub_field('icon'); ?>" alt="Block Icon" />
                  <h2><?php the_sub_field('title'); ?></h2>
                  <p class="short">
                    <?php the_sub_field('short_description'); ?>
                  </p>
                  <div class="row long-descriptions">
                      <div class="col-sm-12">
                          <p class="long">
                              <?php the_sub_field('left_description'); ?>
                              <a class="button" href="<?php echo the_sub_field('link_to_store'); ?>"><?php echo the_sub_field('link_to_store_text'); ?></a>
                          </p>
                      </div>
                  </div>
                  <button id="button" class="<?php echo $identifier; ?>"><?php the_sub_field('button_label') ?></button>
                  <button class="mobile-close">Close</button>
                  <a class="bottom-banner" href="<?php echo the_sub_field('overlay_bottom_banner_link'); ?>"><img src="<?php echo the_sub_field('overlay_bottom_banner'); ?>"></a>
                </div>
              </div>
            </div>

            <?php if($i % 2 == 0) echo '</div><div class="row">'; ?>

          <?php endwhile; ?>

          </div>

        </div>
      </div>
    </div>
  </section>

<?php get_footer(); ?>
