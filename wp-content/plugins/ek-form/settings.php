<?php
require_once WPCF7_PLUGIN_DIR . '/includes/functions.php';
require_once WPCF7_PLUGIN_DIR . '/includes/l10n.php';
require_once WPCF7_PLUGIN_DIR . '/includes/formatting.php';
require_once WPCF7_PLUGIN_DIR . '/includes/pipe.php';
require_once WPCF7_PLUGIN_DIR . '/includes/shortcodes.php';
require_once WPCF7_PLUGIN_DIR . '/includes/capabilities.php';
require_once WPCF7_PLUGIN_DIR . '/includes/contact-form-template.php';
require_once WPCF7_PLUGIN_DIR . '/includes/contact-form.php';
require_once WPCF7_PLUGIN_DIR . '/includes/mail.php';
require_once WPCF7_PLUGIN_DIR . '/includes/submission.php';
require_once WPCF7_PLUGIN_DIR . '/includes/upgrade.php';
require_once WPCF7_PLUGIN_DIR . '/includes/integration.php';

if (is_admin()) {
    require_once WPCF7_PLUGIN_DIR . '/admin/admin.php';
} else {
    require_once WPCF7_PLUGIN_DIR . '/includes/controller.php';
}

if (!defined('RG_CURRENT_PAGE')) {
    define('RG_CURRENT_PAGE', basename($_SERVER['PHP_SELF']));
}

class WPCF7 {

    public static function load_modules() {
        self::load_module('acceptance');
        self::load_module('akismet');
        self::load_module('checkbox');
        self::load_module('count');
        self::load_module('date');
        self::load_module('file');
        self::load_module('flamingo');
        self::load_module('jetpack');
        self::load_module('listo');
        self::load_module('number');
        self::load_module('quiz');
        self::load_module('really-simple-captcha');
        self::load_module('recaptcha');
        self::load_module('response');
        self::load_module('select');
        self::load_module('submit');
        self::load_module('text');
        self::load_module('textarea');
    }

    protected static function load_module($mod) {
        $dir = WPCF7_PLUGIN_MODULES_DIR;

        if (empty($dir) || !is_dir($dir)) {
            return false;
        }

        $file = path_join($dir, $mod . '.php');

        if (file_exists($file)) {
            include_once $file;
        }
    }

    public static function get_option($name, $default = false) {
        $option = get_option('wpcf7');

        if (false === $option) {
            return $default;
        }

        if (isset($option[$name])) {
            return $option[$name];
        } else {
            return $default;
        }
    }

    public static function update_option($name, $value) {
        $option = get_option('wpcf7');
        $option = ( false === $option ) ? array() : (array) $option;
        $option = array_merge($option, array($name => $value));
        update_option('wpcf7', $option);
    }

    /* EK Changes */

    //Action target that adds the 'Insert Form' button to the post/page edit screen
    public static function add_form_button() {

        $is_add_form_page = self::page_supports_add_form_button();

        if (!$is_add_form_page) {
            return;
        }

        $forms = new WP_Query(["post_type" => "wpcf7_contact_form", "post_limits" => -1]);

        $formsArray = ["" => "Select a Form"];
        if (isset($forms->posts) && count($forms->posts)) {
            foreach ($forms->posts as $post) {
                $formsArray[$post->ID] = $post->post_title;
            }
        } else {
            $formsArray = ["" => "No Forms To Add"];
        }
        add_thickbox();
        ?>
        <a href="#TB_inline?width=600&height=550&inlineId=ek-forms-popup" class="button ek-add-form-btn thickbox" title="Add EK Form">
            <span class="dashicons-before dashicons-clipboard" style="position: relative; top: 3px; left: -3px;"></span>
            Add EK Form
        </a>
        <div id="ek-forms-popup" style="display: none;">
            <p>
            <div>Select a form to add</div>
            <div>
                <select id="ek-form-id">
                    <?php foreach ($formsArray as $form_id => $form_name) { ?>
                        <option value="<?= $form_id; ?>"><?= $form_name; ?></option>
                    <?php } ?>
                </select>
            </div>
            <br/>
            <br/>
            <a id="ek-form-insert" class="button-primary" style="margin-right: 10px;">Insert Form</a>
            <a id="ek-form-cancel" class="button" style="color:#bbb;" href="#">Cancel</a>
        </p>
        </div>
        <script>
            jQuery(function() {
                jQuery("#ek-form-cancel").off().click(function() {
                    tb_remove();
                });

                jQuery("#ek-form-insert").off().click(function() {
                    var form_id = jQuery("#TB_ajaxContent #ek-form-id").val()
                    if (form_id == "") {
                        alert('Please select a form');
                        return;
                    }
                    var form_title = jQuery("#TB_ajaxContent #ek-form-id option:selected").text();

                    window.send_to_editor("[ek-form id=\"" + form_id + "\" title=\"" + form_title + "\"]");
                });
            });
        </script>
        <?php
    }

    public static function page_supports_add_form_button() {
        $is_post_edit_page = in_array(RG_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php'));

        $display_add_form_button = apply_filters('gform_display_add_form_button', $is_post_edit_page);

        return $display_add_form_button;
    }

}

add_action('plugins_loaded', 'wpcf7');

function wpcf7() {
    wpcf7_load_textdomain();
    WPCF7::load_modules();

    /* Shortcodes */
    add_shortcode('ek-form', 'wpcf7_contact_form_tag_func');
//	add_shortcode( 'contact-form', 'wpcf7_contact_form_tag_func' );
}

add_action('init', 'wpcf7_init');

function wpcf7_init() {
    wpcf7_get_request_uri();
    wpcf7_register_post_types();

    do_action('wpcf7_init');
}

add_action('admin_init', 'wpcf7_upgrade');

function wpcf7_upgrade() {
    $old_ver = WPCF7::get_option('version', '0');
    $new_ver = WPCF7_VERSION;

    if ($old_ver == $new_ver) {
        return;
    }

    do_action('wpcf7_upgrade', $new_ver, $old_ver);

    WPCF7::update_option('version', $new_ver);
}

/* Install and default settings */

add_action('activate_' . WPCF7_PLUGIN_BASENAME, 'wpcf7_install');

function wpcf7_install() {
    if ($opt = get_option('wpcf7'))
        return;

    wpcf7_load_textdomain();
    wpcf7_register_post_types();
    wpcf7_upgrade();

    if (get_posts(array('post_type' => 'wpcf7_contact_form')))
        return;

    $contact_form = WPCF7_ContactForm::get_template(array(
                'title' => sprintf(__('Contact form %d', 'contact-form-7'), 1)));

    $contact_form->save();
}

/* EK Custom functionality */
add_action('media_buttons', array('WPCF7', 'add_form_button'), 20);

/* END EK */