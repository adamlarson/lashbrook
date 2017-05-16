<?php

// remove p tags from all the images
function filter_ptags_on_images($content){
    return preg_replace('/<p>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s', '\1', $content);
}
add_filter('the_content', 'filter_ptags_on_images');

// Scripts

function custom_style() { 
    wp_register_style('my-style',get_bloginfo( 'stylesheet_directory' ) . '/css/style.css',false,0.1);
    wp_enqueue_style( 'my-style' );
    wp_enqueue_script('owl-carousel', get_stylesheet_directory_uri() . '/js/owl.carousel.js', array( 'jquery' ));
} add_action( 'wp_enqueue_scripts', 'custom_style' ); 

//StyleSheet

wp_enqueue_style( 'stylesheet', get_stylesheet_uri() );
wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), '4.5' );
wp_enqueue_style( 'owl-carousel', get_stylesheet_directory_uri() . '/css/owl.carousel.css', array(), '4.0.4' );
wp_enqueue_style( 'ring-collection', get_stylesheet_directory_uri() . '/css/ring_collection.css', array(), '4.0.4' );

function shift_scripts()  {
    wp_enqueue_script('jquery','/wp-includes/js/jquery/jquery.js','','',true);
    wp_enqueue_script('script', get_template_directory_uri(). '/js/script.js', array( 'jquery' ),'',true);
    wp_enqueue_script('jquery-hoverintent', get_template_directory_uri(). '/js/jquery.hoverIntent.js', array( 'jquery' ),'',true);
} add_action( 'wp_enqueue_scripts', 'shift_scripts' ); 

// WP Menu(s)

register_nav_menus(array( 
    'main_menu'  => 'Main Menu'
));

show_admin_bar( false );

// Options Page

if( function_exists('acf_add_options_page') ) {
    acf_add_options_sub_page(array(
        'page_title' 	=> 'General Settings',
    ));
}

// Featured Image Support

add_theme_support( 'post-thumbnails' );

// Adds description to Post Thumbnails

add_filter('admin_post_thumbnail_html', 'add_featured_image_instruction');
function add_featured_image_instruction($content)
{
    return $content .= '<p>Thumbnail Dimensions <br />Min-Width: 400px x Min-Height: 400px</p>';
}

// Enables the Excerpt meta box in Page edit screen

function wpcodex_add_excerpt_support_for_pages() {
    add_post_type_support( 'page', 'excerpt' );
}
add_action( 'init', 'wpcodex_add_excerpt_support_for_pages' );

//WP Login Styles

function my_login_logo() { ?>
    <style type="text/css">
        .login h1 a {
            background-image: url('<?php the_field('wp_login_logo','options'); ?>');
        }
        .login form {
            background-color: rgba(0, 68, 116, 0.95);
            border-radius:8px;
        }
        #login label {
            color: #ffffff !important;
        }
        .login #nav a,
        .login #backtoblog a {
            color: #dec984;
        }
        #particles-js{
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            background-color: #003053;
            background-position: 50% 50%;
            background-repeat: no-repeat;
            z-index: 0;
        }
        h1,form,p {
            position: relative;
            z-index: 1;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

//WP Login Particles

function ekr_particles() { ?>
    <div id="particles-js"></div>
<?php }
add_action( 'login_message', 'ekr_particles' );

function ekr_particles_script() { ?>
    <script src="<?php echo get_template_directory_uri(); ?>/js/particles.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/js/app.js"></script>
    <?php }
add_action( 'login_footer', 'ekr_particles_script' );

// EKR admin Logo

function ekr_admin() { ?>
    <img 
        src="<?php echo get_template_directory_uri(); ?>/images/admin-logo.png" 
        style="bottom:20px; left:20px; width: 120px;z-index: 500;position: relative;"
    />
<?php }
add_action( 'admin_footer_text', 'ekr_admin' );

// Excerpt trailing dots

function new_excerpt_more( $more ) {
    return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

//Lashbrook Color Scheme

function additional_admin_color_schemes() {
    //Get the theme directory
    $theme_dir = get_template_directory_uri();
 
    //Ocean
    wp_admin_css_color( 'lashbrook', __( 'Lashbrook' ),
        $theme_dir . '/admin-colors/_admin.css',
        array( '#003053', '#003053', '#668398', '#dec984' )
    );
}
add_action('admin_init', 'additional_admin_color_schemes');


/**
 * Custom walker class.
 */
class Lashbrook_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Starts the list before the elements are added.
     *
     * Adds classes to the unordered list sub-menus.
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        // Depth-dependent classes.
        $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
        $display_depth = ( $depth + 1); // because it counts the first submenu as 0
        $classes = array(
            'sub-menu',
            ( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
            ( $display_depth >=2 ? 'sub-sub-menu' : '' ),
            'menu-depth-' . $display_depth
        );
        $class_names = implode( ' ', $classes );

        // Build HTML for output.
        $output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n";
    }

    /**
     * Start the element output.
     *
     * Adds main/sub-classes to the list items and links.
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     * @param int    $id     Current item ID.
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $wp_query;
        $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

        // Depth-dependent classes.
        $depth_classes = array(
            ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
            ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
            ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
            'menu-item-depth-' . $depth
        );
        $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

        // Passed classes.
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

        // Build HTML.
        $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

        // Link attributes.
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        $attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';

        // Build HTML output and pass through the proper filter.
        $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
            $args->before,
            $attributes,
            $args->link_before,
            apply_filters( 'the_title', $item->title, $item->ID ),
            $args->link_after,
            $args->after
        );
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}

?>
