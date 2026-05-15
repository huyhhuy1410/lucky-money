<?php 
 if (!defined('ABSPATH')) {
    exit();
} 
 ?>
<div class="mnw">
    <div class="wrap" id="Lucky_MoneyPrizeAdmin">
        <h1><?php _e('Sự kiện', 'lucky-money'); ?></h1>
        <?php
        $lucky_money_program_templates = [];
        $lucky_money_pages = get_pages();
        foreach ($lucky_money_pages as $page) {
            $template = get_page_template_slug($page->ID);
            if (strpos($template, 'boclixi.index.php') !== false) {
                $lucky_money_program_templates[$page->ID] = $page->post_title;
            }
        }
        ?>

        <?php $lucky_money_program_option = get_option('lm_option_program'); ?>
        <select name="lm_program_id" class="lmProgramJs">
            <option value=""><?php echo __('Vui lòng chọn chương trình', 'lucky-money'); ?></option>
            <?php 
            if (is_array( $lucky_money_program_templates ) && !empty( $lucky_money_program_templates ) ) {
                foreach ( $lucky_money_program_templates as $lucky_money_program_id => $lucky_money_program_name ) { ?>
                    <option value="<?php echo $lucky_money_program_id; ?>"
                        <?php selected( $lucky_money_program_option, $lucky_money_program_id ); ?>>
                        <?php echo sprintf(__('%s', 'lucky-money'), $lucky_money_program_name); ?>
                    </option>
            <?php }
            } ?>
        </select>

        <div class="mnw-pro">
            <?php if( !empty( $lucky_money_program_id ) && !empty( $page_exists = get_post( $lucky_money_program_option ) ) ){ ?>
                <div class="mnw-pro-tab">
                    <div class="mnw-bottom-list">
                        <div class="mnw-bottom mnw-prizes open">
                            <div class="mnw-pro-panels">
                                <div class="mnw-pro-panel">
                                    <div class="wrapper">
                                        <?php lucky_money_program_setting_view() ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>