<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="<?php echo strip_tags(get_the_excerpt()); ?>">
	<title><?php echo ucwords(get_the_title());?></title>
    <?php if (get_permalink()) { ?>
        <meta property="og:url" content="<?php the_permalink(); ?>" />
    <?php } ?>
    <?php if (get_the_title()) { ?>
        <meta property="og:title" content="<?php the_title(); ?>" />
    <?php } ?>
    <?php if (get_field('description')) { ?>
        <meta property="og:description" content="<?php the_field('description'); ?>" />
    <?php } ?>
    <?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
    <?php if ((get_field('logo')) || $thumb ) { ?>
        <meta property="og:image" content="<?php if($thumb) { echo $thumb[0]; } else { the_field('logo'); } ?>"/>
    <?php } ?>

    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_template_directory_uri(); ?>/favicons/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo get_template_directory_uri(); ?>/favicons/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_template_directory_uri(); ?>/favicons/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/favicons/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo get_template_directory_uri(); ?>/favicons/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_template_directory_uri(); ?>/favicons/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo get_template_directory_uri(); ?>/favicons/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_template_directory_uri(); ?>/favicons/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/favicons/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/favicons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/favicons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="<?php echo get_template_directory_uri(); ?>/favicons/manifest.json">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicons/favicon.ico">
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="https://cloud.typography.com/7179114/6939372/css/fonts.css" />
    <meta name="apple-mobile-web-app-title" content="Lashbrook">
    <meta name="application-name" content="Lashbrook">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/favicons/mstile-144x144.png">
    <meta name="msapplication-config" content="<?php echo get_template_directory_uri(); ?>/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <?php do_action('wp_head'); ?>
    <link rel="stylesheet" type="text/css" title="Custom navigation css file" href="<?php echo get_bloginfo('url'); ?>/wp-content/themes/rinzler/css/wp_navigation.css ">
</head>

<body <?php body_class(); ?>>

	<section class="modal">
		<div class="search-module">
			<form action="/" method="get">
				<i class="fa fa-search"></i>
				<input placeholder="Search..." type="text" name="s" id="search" class="search" value="<?php the_search_query(); ?>" />
				<button class="gold" type="submit">SEARCH</button>
			</form>
			<div class="close search-trigger">
				<span class="btn-close"></span>
			</div>
		</div>
	</section>

<?php get_header("main"); ?>
