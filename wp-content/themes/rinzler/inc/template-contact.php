<?php /* Template Name: Contact Us */ get_header(); ?>

  <section id="addresses" class="wysiwyg">
    <div class="container">
      <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
          <h1 class="headline"><?php the_field('page_title'); ?></h1>
        </div>
      </div>
      <div class="row">
        <?php while(have_rows('addresses')) : the_row(); ?>

          <div class="col col-sm-4">
            <?php the_sub_field('address'); ?>
          </div>

        <?php endwhile; ?>
      </div>
    </div>
  </section>

  <div class="section-break"></div>

  <section id="contact-form" class="wysiwyg">
    <div class="container">
      <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
          <h1 class="headline"><?php the_field('form_title'); ?></h1>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-10 col-sm-offset-1">
          <?php the_field('form_shortcode'); ?>
        </div>
      </div>
    </div>
  </section>

<?php get_footer(); ?>
