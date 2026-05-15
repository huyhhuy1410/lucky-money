<?php

/**
 * Plugin Name: Bốc Lì Xì
 * Description: Plugin tích hợp module bốc bao lì xì lấy lộc năm mới.
 * Version:     1.0.0
 * Author:      Huy Vo
 * Text Domain: lucky-money
 */
if (!defined('ABSPATH')) {
    exit();
}
define('LUCKY_MONEY_PATH', plugin_dir_path(__FILE__)); // Path to the plugin directory
define('LUCKY_MONEY_URL', plugins_url('', __FILE__));
define('LUCKY_MONEY_DIR', __DIR__);                   // Current directory of this file
define('LUCKY_MONEY_LIMIT', get_option('lm_option_limit_lucky_money', 1));
define('LUCKY_MONEY_CONDITION', get_option('lm_option_condition', 'email'));
define('LUCKY_MONEY_TIME_LIMIT', get_option('lm_option_time_limit', 24));
define('LUCKY_MONEY_TIME_UNIT', get_option('lm_option_time_unit', 'hours'));
add_filter('theme_page_templates', 'lucky_money_register_templates');
function lucky_money_register_templates($templates)
{
    $lucky_money_templates = array(
        'public/template/boclixi.index.php' => 'Bốc Lì Xì',
    );
    return array_merge($templates, $lucky_money_templates);
}
add_filter('template_include', 'lucky_money_load_lucky_money_template');
function lucky_money_load_lucky_money_template($template)
{
    $page_template = get_page_template_slug();
    if (strpos($page_template, 'boclixi.index.php') !== false) {
        $new_template = LUCKY_MONEY_PATH . '/' . $page_template;
        if (file_exists($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_action('wp_enqueue_scripts', 'lucky_money_enqueue_styles_scripts');
function lucky_money_enqueue_styles_scripts()
{
    if (is_page()) {
        $page_template = get_page_template_slug();

        if (strpos($page_template, 'boclixi.index.php') !== false) {
            // Enqueue CSS
            wp_enqueue_style('lucky_money-style', LUCKY_MONEY_URL . '/public/css/style.css', array(), rand());
            wp_enqueue_style('lucky_money-toast', LUCKY_MONEY_URL . '/public/css/lucky-money-toast.css', array(), rand());
            wp_enqueue_style('lucky_money-backdoor', LUCKY_MONEY_URL . '/public/css/backdoor.css', array(), rand());
            // Enqueue JS
            wp_enqueue_script('lucky_money-jquery', LUCKY_MONEY_URL . '/public/assets/library/jquery/jquery.js', array(), rand(), true);
            wp_enqueue_script('lucky_money-jquery-migrate', LUCKY_MONEY_URL . '/public/assets/library/jquery/jquery-migrate.js', array(), rand(), true);
            wp_enqueue_script('lucky_money-smoothscroll', LUCKY_MONEY_URL . '/public/assets/library/smoothscroll/SmoothScroll.min.js', array(), rand(), true);
            wp_enqueue_script('lucky_money-gsap', LUCKY_MONEY_URL . '/public/assets/library/gsap/gsap.min.js', array(), rand(), true);
            wp_enqueue_script('lucky_money-scrolltrigger', LUCKY_MONEY_URL . '/public/assets/library/gsap/ScrollTrigger.min.js', array(), rand(), true);
            wp_enqueue_script('lucky_money-trimline', LUCKY_MONEY_URL . '/public/assets/library/trimline/jquery.trimLines.js', array(), rand(), true);
            wp_enqueue_script('lucky_money-main', LUCKY_MONEY_URL . '/public/js/main.js', array(), rand(), true);
            wp_localize_script(
                'lucky_money-main',
                'lucky_money_ajax_url',
                [
                    'ajaxURL'   => admin_url('admin-ajax.php'),
                    'siteURL'   => get_site_url(),
                    'ajaxNonce' => wp_create_nonce('lucky-money-ajax-security'),
                    'lucky_money_program' => get_the_ID(),
                    'lucky_money_success' => __('Chúc mừng', 'lucky-money'),
                    'lucky_money_success_prefix' => __('Bạn đã trúng 1', 'lucky-money'),
                    'lucky_money_failed' => __('Cảm ơn đã tham gia', 'lucky-money'),
                    'lucky_money_failed_prefix' => __('Chúc bạn may mắn lần sau', 'lucky-money'),
                    'lucky_money_dir_url' => LUCKY_MONEY_URL . '/public',
                ]
            );
        }
    }
}
add_filter('script_loader_tag', 'lucky_money_add_module_to_scripts', 10, 3);
function lucky_money_add_module_to_scripts($tag, $handle, $src)
{
    if (in_array($handle, array('lucky_money-main'))) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}

// Global variable to hold footer HTML
global $boclixi_footer_html;
$boclixi_footer_html = '';
global $boclixi_shortcode_rendered;
$boclixi_shortcode_rendered = false;
function lm_render_lucky_money_field_template_shortcode($atts)
{
    global $boclixi_footer_html, $boclixi_shortcode_rendered;
    // Prevent duplicate rendering
    if ($boclixi_shortcode_rendered) {
        return ''; // Return nothing if already processed
    }
    $atts = shortcode_atts([
        'id' => 0, // Page ID
    ], $atts);
    $shortcode_id = intval($atts['id']);
    // Verify the page ID
    if (!$shortcode_id) {
        return;
    }
    // Verify if the page uses the Bốc Lì Xì template
    $page_template = get_page_template_slug($shortcode_id);
    if (strpos($page_template, 'boclixi.index.php') == false) {
        return;
    }
    // Verify if the current page uses the Bốc Lì Xì template
    $page_template = get_page_template_slug();
    if (strpos($page_template, 'boclixi.index.php') !== false) {
        return;
    }
    $end_time = get_post_meta($shortcode_id, '_lucky_money_field_end_time', true);
    $current_date = new DateTime();
    if ($end_time) {
        $end_date = verifyDate($end_time);
        if ($current_date > $end_date)
            return;
    }
    wp_enqueue_style('lucky_money-style', LUCKY_MONEY_URL . '/public/css/style.css', array(), rand());
    wp_enqueue_style('lucky_money-toast', LUCKY_MONEY_URL . '/public/css/lucky-money-toast.css', array(), rand());
    wp_enqueue_style('lucky_money-backdoor', LUCKY_MONEY_URL . '/public/css/backdoor.css', array(), rand());
    // Enqueue JS
    wp_enqueue_script('lucky_money-jquery', LUCKY_MONEY_URL . '/public/assets/library/jquery/jquery.js', array(), rand(), true);
    wp_enqueue_script('lucky_money-jquery-migrate', LUCKY_MONEY_URL . '/public/assets/library/jquery/jquery-migrate.js', array(), rand(), true);
    wp_enqueue_script('lucky_money-smoothscroll', LUCKY_MONEY_URL . '/public/assets/library/smoothscroll/SmoothScroll.min.js', array(), rand(), true);
    wp_enqueue_script('lucky_money-gsap', LUCKY_MONEY_URL . '/public/assets/library/gsap/gsap.min.js', array(), rand(), true);
    wp_enqueue_script('lucky_money-scrolltrigger', LUCKY_MONEY_URL . '/public/assets/library/gsap/ScrollTrigger.min.js', array(), rand(), true);
    wp_enqueue_script('lucky_money-trimline', LUCKY_MONEY_URL . '/public/assets/library/trimline/jquery.trimLines.js', array(), rand(), true);
    wp_enqueue_script('lucky_money-main', LUCKY_MONEY_URL . '/public/js/main.js', array(), rand(), true);
    wp_localize_script(
        'lucky_money-main',
        'lucky_money_ajax_url',
        [
            'ajaxURL'   => admin_url('admin-ajax.php'),
            'siteURL'   => get_site_url(),
            'ajaxNonce' => wp_create_nonce('lucky-money-ajax-security'),
            'lucky_money_program' => $shortcode_id,
            'lucky_money_success' => __('Chúc mừng', 'lucky-money'),
            'lucky_money_success_prefix' => __('Bạn đã trúng 1', 'lucky-money'),
            'lucky_money_failed' => __('Cảm ơn đã tham gia', 'lucky-money'),
            'lucky_money_failed_prefix' => __('Chúc bạn may mắn lần sau', 'lucky-money'),
            'lucky_money_dir_url' => LUCKY_MONEY_URL . '/public',
        ]
    );
    // Fetch the content of the partial template
    ob_start();
    $shortcode_id = $atts['id']; // Pass the ID to the partial
    include 'public/partials/boclixi-shortcode.php';
    $content = ob_get_clean();
    // Append content to global variable
    $boclixi_footer_html .= $content;
    // Set the flag to true
    $boclixi_shortcode_rendered = true;
    // Return an empty string to avoid inline rendering
    return;
}
add_shortcode('boclixi_page', 'lm_render_lucky_money_field_template_shortcode');
function lm_output_lucky_money_field_footer_html()
{
    global $boclixi_footer_html;
    // Print the HTML in the footer
    if (!empty($boclixi_footer_html)) {
        echo $boclixi_footer_html;
    }
}
add_action('wp_footer', 'lm_output_lucky_money_field_footer_html');

require_once(LUCKY_MONEY_DIR . '/includes/prizes.class.php');
require_once(LUCKY_MONEY_DIR . '/includes/programs.class.php');
require_once(LUCKY_MONEY_DIR . '/includes/results.class.php');
require_once(LUCKY_MONEY_DIR . '/includes/emails.class.php');
require_once(LUCKY_MONEY_DIR . '/admin/lucky_money-admin.php');
require_once(LUCKY_MONEY_DIR . '/admin/lucky_money-functions.php');
require_once(LUCKY_MONEY_DIR . '/admin/lucky_money-ajax.php');
require_once(LUCKY_MONEY_DIR . '/admin/lucky_money-hooks.php');
