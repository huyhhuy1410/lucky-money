<?php
if (!defined('ABSPATH')) {
    exit();
}
add_action('admin_menu', 'lucky_money_admin_menu');
function lucky_money_admin_menu()
{
    // Add a top-level Lucky Money
    add_menu_page(
        'Bốc Lì Xì',
        'Bốc Lì Xì',
        'manage_options',
        'lucky_money',
        'lucky_money_main_page',
        'dashicons-tickets-alt',
        6
    );

    // Add a submenu to the Lucky Money menu
    add_submenu_page(
        'lucky_money',
        'Chương trình',
        'Chương trình',
        'manage_options',
        'lucky_money_program',
        'lucky_money_program_page'
    );

    add_submenu_page(
        'lucky_money',
        'Thống kê',
        'Thống kê',
        'manage_options',
        'lucky_money_program_statistics',
        'lucky_money_program_statistics_page'
    );

    add_submenu_page(
        'lucky_money',
        'Tuỳ chọn',
        'Tuỳ chọn',
        'manage_options',
        'lucky_money_program_setting',
        'lucky_money_program_setting_page'
    );
    // add_submenu_page(
    //     'lucky_money',
    //     'Giao diện',
    //     'Giao diện',
    //     'manage_options',
    //     'lucky_money_program_setting_field',
    //     'lucky_money_program_setting_field_page'
    // );

    add_submenu_page(
        'lucky_money',
        'Cài đặt',
        'Cài đặt',
        'manage_options',
        'lucky_money_option',
        'lucky_money_option_page'
    );



    add_submenu_page(
        'lucky_money',
        'Email',
        'Email',
        'manage_options',
        'lucky_money_email',
        'lucky_money_email_page'
    );

    // add_submenu_page(
    //     'lucky_money',
    //     'Template',
    //     'Template',
    //     'manage_options',
    //     'lucky_money_template',
    //     'lucky_money_template_page'
    // );
}

function lucky_money_main_page()
{
    include LUCKY_MONEY_DIR . '/admin/template/main.php';
}

function lucky_money_template_page()
{
    include LUCKY_MONEY_DIR . '/admin/template/frontend.php';
}

// Submenu: Lucky Money Options
function lucky_money_option_page()
{
?>
    <div class="wrap" id="Lucky_MoneyPrizeAdminOptions">
        <h1><?php _e('Cài đặt', 'lucky-money'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('lucky_money_option_group');
            do_settings_sections('lucky_money_option');
            submit_button();
            ?>
        </form>
    </div>
<?php
}


// Điều kiện: 
add_action('admin_init', 'lucky_money_register_settings');
function lucky_money_register_settings()
{

    // register_setting('lucky_money_option_group', 'lm_option_limit_lucky_money');
    register_setting('lucky_money_option_group', 'lm_option_condition');
    register_setting('lucky_money_option_group', 'lm_option_time_limit');
    register_setting('lucky_money_option_group', 'lm_option_time_unit');

    add_settings_section(
        'lucky_money_setting_section',
        __('Cài đặt', 'lucky-money'),
        null,
        'lucky_money_option'
    );

    // add_settings_field(
    //     'lm_option_limit_lucky_money',
    //     __('Số lượt quay thưởng', 'lucky-money'),
    //     'lucky_money_option_limit_lucky_money_callback',
    //     'lucky_money_option',
    //     'lucky_money_setting_section'
    // );

    add_settings_field(
        'lm_option_condition',
        __('Điều kiện hạn chế phần thưởng', 'lucky-money'),
        'lucky_money_option_condition_callback',
        'lucky_money_option',
        'lucky_money_setting_section'
    );

    add_settings_field(
        'lm_option_time_limit',
        __('Thời gian giới hạn', 'lucky-money'),
        'lucky_money_option_time_limit_callback',
        'lucky_money_option',
        'lucky_money_setting_section'
    );

    add_settings_field(
        'lm_option_time_unit',
        __('Đơn vị thời gian', 'lucky-money'),
        'lucky_money_option_time_unit_callback',
        'lucky_money_option',
        'lucky_money_setting_section'
    );
}

function lucky_money_option_limit_lucky_money_callback()
{
    $option_limit_lucky_money = get_option('lm_option_limit_lucky_money', 1); // Default value 1
?>
    <input type="number" name="lm_option_limit_lucky_money" value="<?php echo esc_attr($option_limit_lucky_money); ?>" />
<?php
}

function lucky_money_option_condition_callback()
{
    $option_condition = get_option('lm_option_condition', 'email'); // Default value 'email'
?>
    <select name="lm_option_condition">
        <option value="email" <?php selected($option_condition, 'email'); ?>>
            <?php _e('Email', 'lucky-money'); ?>
        </option>
        <option value="phone" <?php selected($option_condition, 'phone'); ?>>
            <?php _e('Số điện thoại', 'lucky-money'); ?>
        </option>
        <option value="both" <?php selected($option_condition, 'both'); ?>>
            <?php _e('Cả Email & Số điện thoại', 'lucky-money'); ?>
        </option>
    </select>
<?php
}

function lucky_money_option_time_limit_callback()
{
    $option_time_limit = get_option('lm_option_time_limit', 24); // Default value 24 
?>
    <input type="number" name="lm_option_time_limit" value="<?php echo esc_attr($option_time_limit); ?>" />
<?php
}

function lucky_money_option_time_unit_callback()
{
    $option_time_unit = get_option('lm_option_time_unit', 'hours'); // Default value 'hours' 
?>
    <select name="lm_option_time_unit">
        <option value="days" <?php selected($option_time_unit, 'days'); ?>><?php _e('Ngày', 'lucky-money'); ?></option>
        <option value="hours" <?php selected($option_time_unit, 'hours'); ?>><?php _e('Giờ', 'lucky-money'); ?></option>
        <option value="minutes" <?php selected($option_time_unit, 'minutes'); ?>><?php _e('Phút', 'lucky-money'); ?></option>
    </select>
<?php
}

function lucky_money_program_page()
{
    include LUCKY_MONEY_DIR . '/admin/template/program.php';
}
// function lucky_money_program_setting_field_page()
// {
//     include LUCKY_MONEY_DIR . '/admin/template/program-field.php';
// }

function lucky_money_program_statistics_page()
{
    include LUCKY_MONEY_DIR . '/admin/template/program-statistics.php';
}

function lucky_money_program_setting_page()
{
    include LUCKY_MONEY_DIR . '/admin/template/program-setting.php';
}

function lucky_money_program_view()
{
    return [
        'default'    => __('Chương trình', 'lucky-money'),
        'statistics' => __('Thống kê', 'lucky-money'),
        // 'field' => __('Nội dung', 'lucky-money'),
    ];
}

function lucky_money_program_default_view()
{ ?>
    <div id="lmProgramVisual" class="is-loading-group"></div>
<?php
}


function lucky_money_program_statistics_view()
{ ?>
    <div id="lmProgramVisual" class="is-loading-group" data-prog_view="statistics"></div>
<?php }

function lucky_money_program_setting_view()
{ ?>
    <div id="lmProgramVisual" class="is-loading-group" data-prog_view="setting"></div>
<?php }

// Admin page for email settings
function lucky_money_email_page()
{
?>
    <div class="wrap" id="Lucky_MoneyPrizeAdminEmail">
        <h1><?php echo __('Mẫu email trúng giải'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('lucky_money_email_options');
            do_settings_sections('lucky_money_email');
            submit_button();
            ?>
        </form>
    </div>
<?php
}

// Register email settings
add_action('admin_init', 'lucky_money_register_email_settings');
function lucky_money_register_email_settings()
{
    register_setting('lucky_money_email_options', 'lucky_money_email_enable');
    register_setting('lucky_money_email_options', 'lucky_money_email_title');
    register_setting('lucky_money_email_options', 'lucky_money_email_template');
    register_setting('lucky_money_email_options', 'lucky_money_email_failed_template');
    register_setting('lucky_money_email_options', 'lucky_money_email_header_image');
    register_setting('lucky_money_email_options', 'lucky_money_email_footer_image');

    add_settings_section(
        'lucky_money_email_section',
        'Email Template',
        null,
        'lucky_money_email'
    );

    add_settings_field(
        'lucky_money_email_enable_field',
        'Email [Bật/Tắt]',
        'lucky_money_email_enable_field_callback',
        'lucky_money_email',
        'lucky_money_email_section'
    );

    add_settings_field(
        'lucky_money_email_title_field',
        'Email Tiêu đề',
        'lucky_money_email_title_field_callback',
        'lucky_money_email',
        'lucky_money_email_section'
    );

    add_settings_field(
        'lucky_money_email_template_field',
        'Nội dung [Có thưởng]',
        'lucky_money_email_template_field_callback',
        'lucky_money_email',
        'lucky_money_email_section'
    );

    add_settings_field(
        'lucky_money_email_template_failed_field',
        'Nội dung [Không trúng]',
        'lucky_money_email_template_failed_field_callback',
        'lucky_money_email',
        'lucky_money_email_section'
    );

    add_settings_field(
        'lucky_money_email_header_image_field',
        'Email Header Image',
        'lucky_money_email_header_image_field_callback',
        'lucky_money_email',
        'lucky_money_email_section'
    );

    add_settings_field(
        'lucky_money_email_footer_image_field',
        'Email Footer Image',
        'lucky_money_email_footer_image_field_callback',
        'lucky_money_email',
        'lucky_money_email_section'
    );
}

// Callback functions for each field
function lucky_money_email_title_field_callback()
{
    $title = get_option('lucky_money_email_title', '');
?>
    <input type="text" id="lucky_money_email_title" name="lucky_money_email_title" value="<?php echo esc_attr($title); ?>" class="regular-text">
    <p class="description"><?php echo __('Sử dụng các cụm từ thay thế:<br>{program_name}: Tên sự kiện<br>', 'lucky-money'); ?></p>
<?php
}

function lucky_money_email_enable_field_callback()
{
    $enable = get_option('lucky_money_email_enable', '');
?>
    <input type="checkbox" id="lucky_money_email_enable"
        name="lucky_money_email_enable"
        class="regular-checked" <?php checked($enable, 'on') ?>>
    <p class="description"><?php echo __('Tắt mở chức năng gửi email khi người dùng trúng thưởng', 'lucky-money'); ?></p>
<?php
}

function lucky_money_email_template_field_callback()
{
    $content = get_option('lucky_money_email_template', '');
    wp_editor($content, 'lucky_money_email_template', array(
        'textarea_name' => 'lucky_money_email_template',
        'editor_class'  => 'large-text',
        'textarea_rows' => 10
    ));
?>
    <p class="description">
        <?php echo __('Sử dụng các cụm từ thay thế:<br>{program_name}: Tên sự kiện<br>{prize_name}: Tên phần thưởng<br>{prize_value}: Giá trị phần thưởng<br>{prize_description}: Giá trị phần thưởng<br>{prize_owner}: Tên người trúng thưởng', 'lucky-money'); ?>
    </p>
<?php
}


function lucky_money_email_template_failed_field_callback()
{
    $content = get_option('lucky_money_email_failed_template', '');
    wp_editor($content, 'lucky_money_email_failed_template', array(
        'textarea_name' => 'lucky_money_email_failed_template',
        'editor_class'  => 'large-text',
        'textarea_rows' => 10
    ));
?>
    <p class="description">
        <?php echo __('Sử dụng các cụm từ thay thế:<br>{program_name}: Tên sự kiện<br>{prize_name}: Tên phần thưởng<br>{prize_value}: Giá trị phần thưởng<br>{prize_description}: Giá trị phần thưởng<br>{prize_owner}: Tên người trúng thưởng', 'lucky-money'); ?>
    </p>
<?php
}

function lucky_money_email_header_image_field_callback()
{
    $header_image = esc_url(get_option('lucky_money_email_header_image', ''));
?>
    <div class="lm-email-image">
        <input type="text" id="lucky_money_email_header_image" name="lucky_money_email_header_image" value="<?php echo $header_image; ?>" class="regular-text">
        <button type="button" class="button-secondary lm-email-image-js">
            <?php echo __('Thêm ảnh', 'lucky-money'); ?>
        </button>
        <p class="description"><?php echo __('URL của hình ảnh hiển thị ở đầu email.', 'lucky-money'); ?></p>
    </div>
<?php
}

function lucky_money_email_footer_image_field_callback()
{
    $footer_image = esc_url(get_option('lucky_money_email_footer_image', ''));
?>
    <div class="lm-email-image">
        <input type="text" id="lucky_money_email_footer_image" name="lucky_money_email_footer_image" value="<?php echo $footer_image; ?>" class="regular-text">
        <button type="button" class="button-secondary lm-email-image-js">
            <?php echo __('Thêm ảnh', 'lucky-money'); ?>
        </button>
        <p class="description"><?php echo __('URL của hình ảnh hiển thị ở cuối email.', 'lucky-money'); ?></p>
    </div>
<?php
}
