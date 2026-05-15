<?php
if (!defined('ABSPATH')) {
    exit();
}
add_action('admin_enqueue_scripts', 'lm_register_styles_scripts');
function lm_register_styles_scripts()
{
    $screen = get_current_screen();
    // var_dump($screen->id);
    if (
        $screen &&
        in_array(
            $screen->id,
            [
                'toplevel_page_lucky_money',
                'boc-li-xi_page_lucky_money_program',
                'boc-li-xi_page_lucky_money_program_statistics',
                'boc-li-xi_page_lucky_money_program_setting',
                'boc-li-xi_page_lucky_money_program_setting_field',
                'boc-li-xi_page_lucky_money_option',
                'boc-li-xi_page_lucky_money_email',
                'boc-li-xi_page_lucky_money_template'
            ]
        )
    ) {
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'jquery-ui',
            'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js',
            array('jquery'),
            '1.13.2',
            true
        );
        wp_enqueue_style(
            'jquery-ui-styles',
            'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css',
            array(),
            '1.13.2'
        );
        wp_enqueue_style(
            'lm-admin-select2-styles',
            LUCKY_MONEY_URL . '/admin/js/select2/select2.min.css',
            array(),
            false
        );
        wp_enqueue_script(
            'lm-admin-select2-scripts',
            LUCKY_MONEY_URL . '/admin/js/select2/select2.min.js',
            array(),
            false,
            true
        );
        wp_enqueue_script(
            'lm-admin-smoothscroll-scripts',
            LUCKY_MONEY_URL . '/admin/js/smoothscroll/SmoothScroll.min.js',
            array(),
            false,
            true
        );
        wp_enqueue_script(
            'lm-admin-loader-scripts',
            LUCKY_MONEY_URL . '/admin/js/charts/loader.js',
            array(),
            rand(),
            true
        );
        wp_enqueue_style(
            'lm-admin-styles',
            LUCKY_MONEY_URL . '/admin/css/lucky_money-main.css',
            array(),
            rand()
        );
        wp_enqueue_script(
            'lm-admin-scripts',
            LUCKY_MONEY_URL . '/admin/js/lucky_money-main.js',
            array(),
            rand(),
            true
        );
        if (! wp_script_is('lucky-money-jquery-confirm', 'enqueued')) {
            wp_enqueue_script(
                'lucky-money-jquery-confirm',
                LUCKY_MONEY_URL . '/admin/js/jquery-confirm.min.js',
                array(),
                false,
                true
            );
        }
        wp_localize_script(
            'lm-admin-scripts',
            'lm_ajax_url',
            [
                'ajaxURL'   => admin_url('admin-ajax.php'),
                'siteURL'   => get_site_url(),
                'ajaxNonce' => wp_create_nonce('lucky-money-ajax-security'),
                'lm_thumb'  => LUCKY_MONEY_URL . '/admin/images/default-thumbnail.png',
                'lm_title'  => __('Thông báo', 'lucky-money'),
                'lm_danger' => __('Cảnh báo', 'lucky-money'),
                'lm_delete' => __('Bạn có xoá?', 'lucky-money'),
                'lm_received' => __('Bạn hãy xác thực nhận phần thưởng ?', 'lucky-money'),
                'lm_refunded' => __('Bạn hãy xác thực hoàn trả/thao tác khôi phục ?', 'lucky-money'),
                'lm_yes'    => __('Đồng ý', 'lucky-money'),
                'lm_no'     => __('Quay lại', 'lucky-money'),
                'lm_media'  => __('Lựa chọn', 'lucky-money'),
                'lm_media_title'    => __('Thư viện ảnh', 'lucky-money'),
                'lm_choosen_prize'  => __('Chọn phần thưởng', 'lucky-money'),
                'lm_export_headers' => [
                    "ID",
                    "Email",
                    "Phone",
                    "Full Name",
                    "Received",
                    "Received Note",
                    "Received At",
                    "Status",
                    "Created At",
                    "Updated At",
                    "Prize ID",
                    "Prize Type",
                    "Prize Value",
                    "Prize Name",
                    "Prize Description",
                    "Prize Thumbnail",
                    "Page ID",
                    "Page Name",
                ]
            ]
        );
    }
}
add_filter('script_loader_tag', 'lm_add_module_to_my_scripts', 10, 3);
function lm_add_module_to_my_scripts($tag, $handle, $src)
{
    if (in_array($handle, array('lm-admin-scripts'))) {
        $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}
// add_filter( 'lm_lucky_money_prize_options', 'lm_prize_options_customizer', 10 );
function lm_prize_options_customizer($lm_options)
{
    if (!class_exists('Woocommerce')) {
        $lm_options['none']     = __('Chúc bạn may mắn lần sau', 'lucky-money');
    } else {
        $lm_options['product']  = __('Phần thưởng', 'lucky-money');
    }
    return $lm_options;
}

// function lm_check_shortcode_in_content($content) {
//     global $boclixi_shortcode_rendered;
//     if ($boclixi_shortcode_rendered) {
//         return $content; // Prevent duplicate processing
//     }
//     if (has_shortcode($content, 'boclixi_page')) {
//         $boclixi_shortcode_rendered = true; // Mark as processed
//     }
//     return $content;
// }
// add_filter('the_content', 'lm_check_shortcode_in_content');
// add_filter('term_description', 'do_shortcode');
// Hook to add metaboxes
add_action('add_meta_boxes', 'boclixi_add_metaboxes');
function boclixi_add_metaboxes()
{
    // Get the current page template
    global $post;
    $template = get_post_meta($post->ID, '_wp_page_template', true);
    // Check if the template matches 'boclixi.index.php'
    if (strpos($template, 'boclixi.index.php') !== false) {
        remove_post_type_support('page', 'editor');



        // Add metaboxes
        add_meta_box(
            'boclixi_shortcode_metabox',
            __('[Bốc lì xì] Shortcode', 'lucky-money'),
            'boclixi_shortcode_metabox_callback',
            'page',
            'normal',
            'high'
        );
        add_meta_box(
            'boclixi_event_end_time_metabox',
            __('Ngày kết thúc [Bốc Lì Xì]', 'lucky-money'),
            'boclixi_event_end_time_metabox_callback',
            'page',
            'normal',
            'high'
        );
        add_meta_box(
            'boclixi_banner_metabox',
            __('Nội dung banner [Bốc Lì Xì]', 'lucky-money'),
            'boclixi_banner_metabox_callback',
            'page',
            'normal',
            'high'
        );
        add_meta_box(
            'boclixi_rules_metabox',
            __('Thể lệ [Bốc Lì Xì]', 'lucky-money'),
            'boclixi_rules_metabox_callback',
            'page',
            'normal',
            'high'
        );
        // Metabox for "Tiêu đề form"
        // add_meta_box(
        //     'boclixi_form_title_metabox',
        //     __('Tiêu đề form', 'textdomain'),
        //     'boclixi_form_title_metabox_callback',
        //     'page',
        //     'normal',
        //     'high'
        // );

        // Metabox for "Nội dung màn hình trò chơi"
        add_meta_box(
            'boclixi_game_screen_metabox',
            __('Nội dung trò chơi [Bốc Lì Xì]', 'textdomain'),
            'boclixi_game_screen_metabox_callback',
            'page',
            'normal',
            'high'
        );
    }
}
// Callback for the datepicker metabox
function boclixi_event_end_time_metabox_callback($post)
{
    $end_time = get_post_meta($post->ID, '_lucky_money_field_end_time', true);
?>
    <p><label for="lucky_money_field_end_time"><strong><?php _e('Chọn ngày kết thúc sự kiện', 'textdomain'); ?></strong></label></p>
    <input type="text" name="lucky_money_field_end_time" id="lucky_money_field_end_time" value="<?php echo esc_attr($end_time); ?>" class="widefat datepicker">
    <p><strong><?php echo __('*Lưu ý: Thay đổi thành ngày trước đó để kết thúc sự kiện sớm hơn', 'lucky-money') ?></strong></p>

<?php
}
// Callback for Shortcode Metabox
function boclixi_shortcode_metabox_callback($post)
{
    echo '<p><strong>' . __('*Lưu ý: Chỉ gán duy nhất 1 shortcode Bốc Lì Xì vào nội dung của trang hoặc bài viết. Không áp dụng cho nội dung của chuyên mục hay danh mục', 'lucky-money') . '</strong></p>';
    echo '<code>[boclixi_page id="' . esc_attr($post->ID) . '"]</code>';
}
// Callback for Banner Content Metabox
function boclixi_banner_metabox_callback($post)
{
    // Fetch existing values
    $year = get_post_meta($post->ID, '_lucky_money_field_year', true);
    $title = get_post_meta($post->ID, '_lucky_money_field_title', true);
    $subtitle = get_post_meta($post->ID, '_lucky_money_field_subtitle', true);
    $content = get_post_meta($post->ID, '_lucky_money_field_content', true);
    // Render fields
?>
    <p><label for="lucky_money_field_year"><strong><?php _e('Năm', 'lucky-money'); ?></strong></label></p>
    <input type="text" id="lucky_money_field_year" name="lucky_money_field_year" value="<?php echo esc_attr($year); ?>" class="widefat" />
    <p><label for="lucky_money_field_title"><strong><?php _e('Tiêu đề', 'lucky-money'); ?></strong></label></p>
    <input type="text" id="lucky_money_field_title" name="lucky_money_field_title" value="<?php echo esc_attr($title); ?>" class="widefat" />
    <p><label for="lucky_money_field_subtitle"><strong><?php _e('Tiêu đề phụ', 'lucky-money'); ?></strong></label></p>
    <input type="text" id="lucky_money_field_subtitle" name="lucky_money_field_subtitle" value="<?php echo esc_attr($subtitle); ?>" class="widefat" />
    <p><label for="lucky_money_field_content"><strong><?php _e('Nội dung', 'lucky-money'); ?></strong></label></p>
    <textarea id="lucky_money_field_content" name="lucky_money_field_content" rows="4" class="widefat"><?php echo esc_textarea($content); ?></textarea>
<?php
}
// Callback for Rules Content Metabox
function boclixi_rules_metabox_callback($post)
{
    $rules_content = get_post_meta($post->ID, '_lucky_money_field_rules_content', true);
    wp_editor($rules_content, 'lucky_money_field_rules_content', [
        'textarea_name' => 'lucky_money_field_rules_content',
    ]);
}
// Callback for "Tiêu đề form"
function boclixi_form_title_metabox_callback($post)
{
    $form_title = get_post_meta($post->ID, '_lucky_money_form_title', true);
?>
    <label for="lucky_money_form_title"><?php _e('Tiêu đề form:', 'textdomain'); ?></label>
    <input type="text" name="lucky_money_form_title" id="lucky_money_form_title" value="<?php echo esc_attr($form_title); ?>" class="widefat">
<?php
}

// Callback for "Nội dung màn hình trò chơi"
function boclixi_game_screen_metabox_callback($post)
{
    $game_screen_title = get_post_meta($post->ID, '_lucky_money_field_game_title', true);
    $game_screen_description = get_post_meta($post->ID, '_lucky_money_field_game_description', true);
?>
    <p><label for="lucky_money_field_game_title"><strong><?php _e('Tiêu đề', 'textdomain'); ?></strong></label></p>
    <input type="text" name="lucky_money_field_game_title" id="lucky_money_field_game_title" value="<?php echo esc_attr($game_screen_title); ?>" class="widefat">

    <p><label for="lucky_money_field_game_description"><strong><?php _e('Mô tả', 'textdomain'); ?></strong></label></p>
    <input type="text" name="lucky_money_field_game_description" id="lucky_money_field_game_description" value="<?php echo esc_attr($game_screen_description); ?>" class="widefat">
<?php
}
// Save Metabox Data
add_action('save_post', 'boclixi_save_metabox_data');
function boclixi_save_metabox_data($post_id)
{
    // Verify nonce
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    // Save Banner Content Fields
    if (isset($_POST['lucky_money_field_year'])) {
        update_post_meta($post_id, '_lucky_money_field_year', sanitize_text_field($_POST['lucky_money_field_year']));
    }
    if (isset($_POST['lucky_money_field_title'])) {
        update_post_meta($post_id, '_lucky_money_field_title', sanitize_text_field($_POST['lucky_money_field_title']));
    }
    if (isset($_POST['lucky_money_field_subtitle'])) {
        update_post_meta($post_id, '_lucky_money_field_subtitle', sanitize_text_field($_POST['lucky_money_field_subtitle']));
    }
    if (isset($_POST['lucky_money_field_content'])) {
        update_post_meta($post_id, '_lucky_money_field_content', sanitize_textarea_field($_POST['lucky_money_field_content']));
    }
    // Save Rules Content Field
    if (isset($_POST['lucky_money_field_rules_content'])) {
        update_post_meta($post_id, '_lucky_money_field_rules_content', $_POST['lucky_money_field_rules_content']);
    }
    // Save "Tiêu đề form"
    //  if (isset($_POST['lucky_money_form_title'])) {
    //     update_post_meta($post_id, '_lucky_money_form_title', sanitize_text_field($_POST['lucky_money_form_title']));
    // }

    // Save "Nội dung màn hình trò chơi"
    if (isset($_POST['lucky_money_field_game_title'])) {
        update_post_meta($post_id, '_lucky_money_field_game_title', sanitize_text_field($_POST['lucky_money_field_game_title']));
    }

    if (isset($_POST['lucky_money_field_game_description'])) {
        update_post_meta($post_id, '_lucky_money_field_game_description', sanitize_textarea_field($_POST['lucky_money_field_game_description']));
    }
    if (isset($_POST['lucky_money_field_end_time'])) {
        update_post_meta($post_id, '_lucky_money_field_end_time', sanitize_text_field($_POST['lucky_money_field_end_time']));
    }
}


// Enqueue Scripts and Styles for Datepicker
add_action('admin_enqueue_scripts', 'boclixi_enqueue_datepicker_assets');
function boclixi_enqueue_datepicker_assets($hook)
{
    global $post;

    if ($hook === 'post.php' || $hook === 'post-new.php') {

        $template = get_post_meta($post->ID, '_wp_page_template', true);
        if (strpos($template, 'boclixi.index.php') !== false) {
            // Enqueue jQuery UI datepicker
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-ui-datepicker-style', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
            wp_enqueue_style(
                'lm-admin-alert-styles',
                LUCKY_MONEY_URL . '/admin/css/lucky_money-alert.css',
                array(),
                false
            );
            // wp_enqueue_script(
            //     'lm-admin-global-scripts',
            //     LUCKY_MONEY_URL . '/admin/js/modules/lm-global.js',
            //     array(),
            //     false,
            //     true
            // );
            // Script for initializing the datepicker
            add_action('admin_footer', 'lucky_money_boclixi_js');
        }
    }
}
function lucky_money_boclixi_js()
{
?>
    <script type="module">
        import {
            Noti
        } from "<?php echo LUCKY_MONEY_URL ?>/admin/js/modules/lm-global.js";
        jQuery(document).ready(function($) {
            $('.datepicker').datepicker({
                minDate: '-1D',
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true,
                onClose: function(dateText, inst) {
                    const regex = /^(?:\d{2}-\d{2}-\d{4}|)$/;
                    if (!regex.test(dateText)) {
                        Noti({
                            text: '<?php echo __('Thời gian sự kiện chưa hợp lệ', 'lucky-money') ?>',
                            title: '<?php echo __('Thông báo', 'lucky-money') ?>',

                            icon: 'danger',
                            timer: '3000',
                        });
                        $(this).val(''); // Clear invalid input
                    }
                }
            });
        });
    </script>
<?php
}
