<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 */
global $wp;
$current_url = home_url(add_query_arg(array(),$wp->request));
$lastSegment = basename(parse_url($current_url, PHP_URL_PATH));
get_header();
?>


<style>
    .header h1 {
        color: #FFF;
        padding: 0 30px;
        font-size: 2.8em;
        line-height: 1.3em;
        margin: 0 0 25px;
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;
    }
    .content_container {
        max-width:1170px;
        margin: 40px auto;
    }
    .blog-index h3 {margin-bottom:30px;}
    a.category-link {
        background-size: cover;
        background: #000000 no-repeat center center;
        position: relative;
        display: block;
        width: 100%;
        height: 160px;
        text-align: center;
        font-size: 1.5em;
        font-family: "Idlewild SSm A", "Idlewild SSm B", 'Open Sans Condensed', 'Helvetica Neue';
        color: white;
        padding-top: 15px;
        box-sizing: border-box;
        margin-bottom: 25px;
    }

    .category-link .icon {
        display: block;
        width: 20px;
        margin: 0 auto 15px;
        position: relative;
        z-index: 2;
    }

    .category-link .example {
        width: 100px;
        position: absolute;
        top: 90px;
        left: 0;
        z-index: 2;
        right: 0;
        margin-left: auto;
        margin-right: auto;
    }

    .category-link span {
        position: relative;
        z-index: 3;
        transition: text-shadow 0.5s;
    }
    .category-link .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #003053;
        opacity: 0.75;
        transition: opacity 0.5s;
        z-index: 1;
    }
    .category-link:hover .overlay {
        opacity: 0;
    }
</style>

    <section class="hero image-medium" style="background-image:url('<?php bloginfo('url'); ?>/wp-content/uploads/2016/02/searhc_image.jpg');background-repeat:no-repeat; background-position:top;">
        <div class="header">
            <h1>
                404: No result for "<?php echo $lastSegment;?>"
            </h1>
        </div>
    </section>


<section class="blog">
    <div class="wrapper">
        <div class="blog-index">
            <h3>
                We're sorry, the page you attempted to view either does not exist or has been moved!
            </h3>

            <div class="search-module">
                <form action="/" method="get">
                    <i class="fa fa-search"></i>
                    <input placeholder="Try to find it via search" type="text" name="s" id="search" class="search" value="<?php the_search_query(); ?>" />
                    <button class="gold" type="submit">SEARCH</button>
                </form>
            </div>

            <div id="category-links">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <h3>
                            View rings by category
                        </h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/classics" class="category-link " style="background-image:url(/store/pub/media/images/category-images/classics/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/classics/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/classics/demo-ring.png">
                            <span>CLASSICS</span>
                        </a>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/meteorite" class="category-link " style="background-image:url(/store/pub/media/images/category-images/meteorite/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/meteorite/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/meteorite/demo-ring.png">
                            <span>METEORITE</span>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/damascus-steel" class="category-link " style="background-image:url(/store/pub/media/images/category-images/damascus/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/damascus/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/damascus/demo-ring.png">
                            <span>DAMASCUS</span>
                        </a>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/hardwood" class="category-link " style="background-image:url(/store/pub/media/images/category-images/hardwood/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/hardwood/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/hardwood/demo-ring.png">
                            <span>HARDWOOD</span>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/elysium" class="category-link " style="background-image:url(/store/pub/media/images/category-images/elysium/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/elysium/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/elysium/demo-ring.png">
                            <span>ELYSIUM</span>
                        </a>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/cobalt-chrome" class="category-link " style="background-image:url(/store/pub/media/images/category-images/cobalt-chrome/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/cobalt-chrome/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/cobalt-chrome/demo-ring.png">
                            <span>COBALT CHROME</span>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/zirconium" class="category-link " style="background-image:url(/store/pub/media/images/category-images/zirconium/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/zirconium/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/zirconium/demo-ring.png">
                            <span>ZIRCONIUM</span>
                        </a>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/precious" class="category-link " style="background-image:url(/store/pub/media/images/category-images/precious/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/precious/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/precious/demo-ring.png">
                            <span>PRECIOUS</span>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/titanium" class="category-link " style="background-image:url(/store/pub/media/images/category-images/titanium/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/titanium/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/titanium/demo-ring.png">
                            <span>TITANIUM</span>
                        </a>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/outdoor" class="category-link " style="background-image:url(/store/pub/media/images/category-images/outdoor/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/outdoor/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/outdoor/demo-ring.png">
                            <span>OUTDOOR</span>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/lifestyle" class="category-link " style="background-image:url(/store/pub/media/images/category-images/lifestyle/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/lifestyle/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/lifestyle/demo-ring.png">
                            <span>LIFESTYLE</span>
                        </a>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/carbon-fiber" class="category-link " style="background-image:url(/store/pub/media/images/category-images/carbon-fiber/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/carbon-fiber/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/carbon-fiber/demo-ring.png">
                            <span>CARBON FIBER</span>
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/mokume-gane" class="category-link " style="background-image:url(/store/pub/media/images/category-images/mokume-gane/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/mokume-gane/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/mokume-gane/demo-ring.png">
                            <span>MOKUME GANE</span>
                        </a>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <a href="/store/tungsten-ceramic" class="category-link " style="background-image:url(/store/pub/media/images/category-images/tungsten-ceramic/link.jpg);">
                            <div class="overlay"></div>
                            <img class="icon" src="/store/pub/media/images/category-images/tungsten-ceramic/icon.png">
                            <img class="example" src="/store/pub/media/images/category-images/tungsten-ceramic/demo-ring.png">
                            <span>TUNGSTEN CERAMIC</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php get_sidebar(); ?>
    </div>
</section>






<?php get_footer(); ?>
