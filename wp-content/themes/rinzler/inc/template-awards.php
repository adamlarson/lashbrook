<?php
    // Template Name: Header Vanilla-Content Footer
    $hero_image = get_field('hero_image');
    get_header();
?>
<style>

</style>
<?php if ( have_posts() ) :  while ( have_posts() ) : the_post(); ?>
    <section class="hero image-medium" style="background-image:url('<?php echo $hero_image; ?>');"></section>

    <section class="content_container">
        <div class="container-fluid">
            <?php the_content(); ?>

            <section class="ring-collection">
                <img class="left-fader" src="/store/pub/media/images/rings/fader.png" />
                <img class="right-fader" src="/store/pub/media/images/rings/fader.png" />
                <div class="left-arrow" style=""><i class="fa fa-angle-left"></i></div>
                <div class="right-arrow" style=""><i class="fa fa-angle-right"></i></div>
                <div class="rings-holder" id="ring-holder">
                    <?php if( have_rows('award_rings') ): ?>
                        <?php while( have_rows('award_rings') ): the_row();
                                $i++;
                                ?>
                                <img class="ring ring<?= $i ?>" src="<?php the_sub_field('award_ring'); ?>" alt="<?php the_sub_field("award_title") ?>" data-link="<?php the_sub_field("award_store_url") ?>" data-award="<?php the_sub_field("award_logo") ?>" />
                            <?php endwhile; ?>
                    <?php endif; ?>
                    
                    <div class="ring-data">
                        <div class="ring-name">
                            RING NAME HERE
                        </div>
                        <a href="" class="ring-link">View <i class="fa fa-caret-right"></i></a>
                    </div>
                </div>
            </section>
            <script src="/wp-content/themes/rinzler/js/ringcollection.js"></script>
            <script>

                new RingCollection(document.querySelector(".ring-collection"));

            </script>

            <?php if( have_rows('award_rings') ): ?>
                <?php while( have_rows('award_rings') ): the_row();?>
                    <div class="row">
                        <div class="col-xs-12">
                            <img src="<?php the_sub_field('image'); ?>" alt="" class="img-responsive" />
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>

<?php endwhile; endif; ?>

<?php get_footer(); ?>