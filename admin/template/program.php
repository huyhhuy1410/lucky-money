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
            if (is_array($lucky_money_program_templates) && !empty($lucky_money_program_templates)) {
                foreach ($lucky_money_program_templates as $lucky_money_program_id => $lucky_money_program_name) { ?>
                    <option value="<?php echo $lucky_money_program_id; ?>"
                        <?php selected($lucky_money_program_option, $lucky_money_program_id); ?>>
                        <?php echo sprintf(__('%s', 'lucky-money'), $lucky_money_program_name); ?>
                    </option>
            <?php }
            } ?>
        </select>

        <div class="mnw-pro">
            <?php if (!empty($lucky_money_program_id) && !empty($page_exists = get_post($lucky_money_program_option))) { ?>
                <div class="mnw-pro-tab">
                    <?php
                    $view = isset($_GET['view']) ? esc_attr($_GET['view']) : '';
                    if (empty($view) || $view == 'default') { ?>
                        <div class="mnw-top">
                            <div class="heads mnw-nmb">
                                <div class="heads-tt">
                                    <p class="tt"><?php echo sprintf(
                                                        __('Chương trình: %s', 'lucky-money'),
                                                        $page_exists->post_title
                                                    ); ?></p>
                                </div>
                                <button type="button" class="mnw-btn lm_added_js">
                                    <div class="mnw-flex mnw-visual">
                                        <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/mnw-ic-upload.svg" />
                                        <span class="txt"><?php echo __('Thêm mới', 'lucky-money'); ?></span>
                                    </div>
                                    <div class="mnw-flex mnw-hidden">
                                        <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/mnw-ic-back.svg" />
                                        <span class="txt"><?php echo __('Quay lại', 'lucky-money'); ?></span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="mnw-bottom-list">
                        <?php if (empty($view) || $view == 'default') { ?>
                            <div class="mnw-bottom mnw-added">
                                <div class="mnw-pro-panels">
                                    <div class="mnw-pro-panel">
                                        <div class="wrapper">
                                            <form id="frmLucky_MoneyProgramPrizeCreation" class="is-loading-row">
                                                <input type="hidden" name="lm_program_id" value="<?php echo $lucky_money_program_option; ?>">
                                                <div class="heads">
                                                    <div class="heads-tt">
                                                        <p class="tt">
                                                            <?php echo __('Thêm mới', 'lucky-money'); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="mnw-table">
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th style="display:none"><?php _e('Sắp xếp', 'lucky-money'); ?></th>
                                                                <th><?php _e('Ảnh mô tả', 'lucky-money'); ?></th>
                                                                <th><?php _e('Phần thưởng', 'lucky-money'); ?></th>
                                                                <th><?php _e('Phần trăm', 'lucky-money'); ?></th>
                                                                <th><?php _e('Số lượng', 'lucky-money'); ?></th>
                                                                <th style="display:none"><?php _e('Màu nền / Màu chữ', 'lucky-money'); ?></th>
                                                                <th><?php _e('Hành động', 'lucky-money'); ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr class="lm-row is-loading-row">
                                                                <td style="display:none" data-label="<?php _e('Sắp xếp', 'lucky-money'); ?>">
                                                                    <div class="mnw-ip">
                                                                        <input type="number" name="lm_program_sort" value="0" required readonly>
                                                                    </div>
                                                                </td>
                                                                <td data-label="<?php echo __('Ảnh mô tả (nếu có)', 'lucky-money'); ?>">
                                                                    <div class="lm-thumb mnw-author">
                                                                        <div class="mnw-author-avatar preview-container">
                                                                            <div class="avatar preview-img lm-thumb-js">
                                                                                <img src="<?php echo LUCKY_MONEY_URL . '/admin/images/default-thumbnail.png';  ?>">
                                                                            </div>
                                                                            <input type="text" class="lm_field" name="lm_program_thumbnail" readonly hidden>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td data-label="<?php echo __('Phần thưởng', 'lucky-money'); ?>">
                                                                    <div class="mnw-ip">
                                                                        <select class="lm_prize_select2" name="lm_program_prize" required>
                                                                            <option value=""><?php echo __('Chọn phần thưởng', 'lucky-money'); ?></option>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td data-label="<?php echo __('Phần trăm', 'lucky-money'); ?>">
                                                                    <div class="mnw-ip">
                                                                        <input type="number" name="lm_program_percent" min="0" max="100" required>
                                                                    </div>
                                                                </td>
                                                                <td data-label="<?php echo __('Số lượng (nếu có)', 'lucky-money'); ?>">
                                                                    <div class="mnw-ip">
                                                                        <input type="number" class="lm_field_prize_quantity" name="lm_program_quantity">
                                                                    </div>
                                                                </td>
                                                                <td style="display:none" data-label="<?php echo __('Màu nền / Màu chữ', 'lucky-money'); ?>">
                                                                    <div class="mnw-color">
                                                                        <div class="mnw-color-item">
                                                                            <label class="mnw-color-label">
                                                                                <input type="color" class="lm_field colorPicker" name="lm_program_background" required>
                                                                            </label>
                                                                        </div>
                                                                        <div class="mnw-color-item">
                                                                            <label class="mnw-color-label">
                                                                                <input type="color" class="lm_field colorPicker" name="lm_program_color" required>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td data-label="<?php echo __('Hành động', 'lucky-money'); ?>">
                                                                    <div class="mnw-table-action">
                                                                        <button type="submit" class="mnw-btn lm_button add lm_program_prize_js">
                                                                            <div class="txt">
                                                                                <?php echo __('Thêm mới', 'lucky-money'); ?>
                                                                            </div>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="mnw-bottom mnw-prizes open">
                            <div class="mnw-pro-panels">
                                <div class="mnw-pro-panel">
                                    <div class="wrapper">
                                        <?php
                                        switch ($view) {
                                            case 'statistics':
                                                lucky_money_program_statistics_view();
                                                break;
                                            // case 'field':
                                            //     lucky_money_program_field_view();
                                            //     break;
                                            default:
                                                lucky_money_program_default_view();
                                                break;
                                        }
                                        ?>
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