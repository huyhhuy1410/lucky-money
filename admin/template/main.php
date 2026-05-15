<?php
if (!defined('ABSPATH')) {
    exit();
}
$flagHidden = true;

$current_userId = get_current_user_id();

$current_userData = get_userdata($current_userId);

$lm_options = lm_prize_options();

$lm_options = apply_filters('lm_lucky_money_prize_options', $lm_options);

global $lucky_moneyPrize;
$prizes = $lucky_moneyPrize->get_data(); ?>

<div class="wrap" id="Lucky_MoneyPrizeAdmin">
    <div class="mnw">
        <div class="mnw-container">
            <div class="mnw-wrap">
                <div class="mnw-top">
                    <div class="heads mnw-nmb">
                        <div class="heads-tt">
                            <p class="tt"><?php echo sprintf(
                                                __('Xin chào, %s', 'lucky-money'),
                                                $current_userData->display_name
                                            ); ?></p>
                            <p class="des">
                                <?php echo __('Kiểm tra danh sách các phần thưởng và cài đặt', 'lucky-money'); ?>
                            </p>
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
                <div class="mnw-bottom-list">
                    <div class="mnw-bottom mnw-added">
                        <form id="frmLucky_MoneyPrizeCreation" class="is-loading-row">
                            <div class="mnw-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th><?php _e('Kiểu', 'lucky-money'); ?></th>
                                            <th><?php _e('Nhãn', 'lucky-money'); ?></th>
                                            <th><?php _e('Mô tả', 'lucky-money'); ?></th>
                                            <?php if (class_exists('Woocommerce')) { ?>
                                                <th><?php _e('Giá trị', 'lucky-money'); ?></th>
                                            <?php } ?>
                                            <th><?php _e('Hành động', 'lucky-money'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="lm-row lm-creation">
                                            <td data-label="<?php _e('Kiểu', 'lucky-money'); ?>">
                                                <div class="mnw-ip">
                                                    <?php
                                                    if (is_array($lm_options) && !empty($lm_options)) { ?>
                                                        <select class="lm_type_js" name="lm_type" required>
                                                            <option value=""><?php echo __('Chọn kiểu phần thưởng', 'lucky-money'); ?></option>
                                                            <?php foreach ($lm_options as $value => $name) { ?>
                                                                <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                            <td data-label="<?php _e('Nhãn', 'lucky-money'); ?>">
                                                <div class="mnw-ip">
                                                    <input type="text" name="lm_name" required
                                                        placeholder="<?php echo __('Nhập nhãn *', 'lucky-money'); ?>">
                                                </div>
                                            </td>
                                            <td data-label="<?php _e('Mô tả', 'lucky-money'); ?>">
                                                <div class="mnw-ip">
                                                    <input type="text" name="lm_description"
                                                        placeholder="<?php echo __('Nhập mô tả', 'lucky-money'); ?>">
                                                </div>
                                            </td>
                                            <?php if (class_exists('Woocommerce')) { ?>
                                                <td data-label="<?php _e('Giá trị', 'lucky-money'); ?>">
                                                    <div class="mnw-coupon-setting">
                                                        <div class="mnw-select">
                                                            <div class="mnw-ip">
                                                                <select class="lm_coupon_field lm_coupon_option" name="lm_type_option" required disabled>
                                                                    <option value=""><?php echo __('Chọn kiểu khuyến mãi', 'lucky-money'); ?></option>
                                                                    <option value="default"><?php echo __('Cố định', 'lucky-money'); ?></option>
                                                                    <option value="percent"><?php echo __('Phần trăm', 'lucky-money'); ?></option>
                                                                </select>
                                                            </div>
                                                            <div class="mnw-ip">
                                                                <input type="number" class="lm_coupon_field lm_coupon_value" required disabled
                                                                    name="lm_type_value" placeholder="<?php echo __('Nhập giá trị', 'lucky-money'); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="mnw-coupon-no-setting">
                                                            ...
                                                        </div>
                                                    </div>
                                                </td>
                                            <?php } ?>
                                            <td data-label="<?php _e('Hành động', 'lucky-money'); ?>">
                                                <button type="submit" class="mnw-btn second center lm_button add">
                                                    <div class="txt">
                                                        <?php echo __('Thêm mới', 'lucky-money'); ?>
                                                    </div>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="mnw-bottom mnw-prizes open">
                        <form id="frmLucky_MoneyPrizeData" class="is-loading-row">
                            <div class="heads">
                                <div class="heads-tt">
                                    <p class="tt">
                                        <?php echo __('Phần thưởng', 'lucky-money'); ?>
                                    </p>
                                </div>
                                <?php if (!$flagHidden) { ?>
                                    <div class="mnw-filter">
                                        <div class="mnw-filter-flex">
                                            <div class="mnw-ip">
                                                <div class="mnw-srch">
                                                    <button class="mnw-srch-btn">
                                                        <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/ic-srch.svg" alt="">
                                                    </button>
                                                    <input type="text" placeholder="Tìm kiếm">
                                                </div>
                                            </div>
                                            <div class="mnw-ip">
                                                <div class="mnw-date">
                                                    <input type="date">
                                                </div>
                                            </div>
                                            <div class="mnw-ip">
                                                <a href="" class="mnw-btn">
                                                    <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/ic-download.svg" alt="">
                                                    <span class="txt">Tải xuống</span>
                                                </a>
                                            </div>
                                            <div class="mnw-ip">
                                                <a href="" class="mnw-btn pri">
                                                    <span class="txt">Tìm kiếm</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="mnw-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th><?php _e('ID', 'lucky-money'); ?></th>
                                            <th><?php _e('Kiểu', 'lucky-money'); ?></th>
                                            <th><?php _e('Nhãn', 'lucky-money'); ?></th>
                                            <th><?php _e('Mô tả', 'lucky-money'); ?></th>
                                            <?php if (class_exists('Woocommerce')) { ?>
                                                <th><?php _e('Giá trị', 'lucky-money'); ?></th>
                                            <?php } ?>
                                            <th><?php _e('Hành động', 'lucky-money'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($prizes)) {
                                            foreach ($prizes as $prize_key => $prize): ?>
                                                <tr class="lm-row prize_row is-loading-row">
                                                    <td data-label="<?php _e('ID', 'lucky-money'); ?>">
                                                        <?php echo esc_html($prize['id']); ?>
                                                    </td>
                                                    <td data-label="<?php _e('Kiểu', 'lucky-money'); ?>">
                                                        <?php
                                                        if (is_array($lm_options) && !empty($lm_options)) { ?>
                                                            <div class="mnw-ip">
                                                                <select class="lm_field lm_type_js" name="lm_data[<?php echo $prize['id']; ?>][type]">
                                                                    <?php foreach ($lm_options as $value => $name) { ?>
                                                                        <option value="<?php echo $value; ?>" <?php selected($prize['type'], $value) ?>>
                                                                            <?php echo $name; ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                    <td data-label="<?php _e('Nhãn', 'lucky-money'); ?>">
                                                        <div class="mnw-ip">
                                                            <input class="lm_field" type="text" name="lm_data[<?php echo $prize['id']; ?>][name]" value="<?php echo $prize['name']; ?>"
                                                                placeholder="<?php echo __('Nhập nhãn *', 'lucky-money'); ?>">
                                                        </div>
                                                    </td>
                                                    <td data-label="<?php _e('Mô tả', 'lucky-money'); ?>">
                                                        <div class="mnw-ip">
                                                            <input class="lm_field" type="text" name="lm_data[<?php echo $prize['id']; ?>][description]"
                                                                value="<?php echo $prize['description']; ?>" placeholder="<?php echo __('Nhập mô tả', 'lucky-money'); ?>">
                                                        </div>
                                                    </td>
                                                    <?php
                                                    if (class_exists('Woocommerce')) {
                                                    ?>
                                                        <td data-label="<?php _e('Giá trị', 'lucky-money'); ?>">
                                                            <?php
                                                            if (in_array($prize['type'], ['coupon'])) { ?>
                                                                <div class="mnw-coupon-setting active">
                                                                    <div class="mnw-select">
                                                                        <div class="mnw-ip">
                                                                            <select class="lm_field lm_coupon_field lm_coupon_option active" name="lm_data[<?php echo $prize['id']; ?>][type_option]" required>
                                                                                <option value=""><?php echo __('Chọn kiểu khuyến mãi', 'lucky-money'); ?></option>
                                                                                <option value="default" <?php selected($prize['type_option'], 'default'); ?>>
                                                                                    <?php echo __('Cố định', 'lucky-money'); ?>
                                                                                </option>
                                                                                <option value="percent" <?php selected($prize['type_option'], 'percent'); ?>>
                                                                                    <?php echo __('Phần trăm', 'lucky-money'); ?>
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mnw-ip">
                                                                            <input type="number" class="lm_field lm_coupon_field lm_coupon_value active" required
                                                                                name="lm_data[<?php echo $prize['id']; ?>][type_value]" value="<?php echo $prize['type_value']; ?>"
                                                                                placeholder="<?php echo __('Nhập giá trị', 'lucky-money'); ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mnw-coupon-no-setting">
                                                                        ...
                                                                    </div>
                                                                </div>
                                                            <?php } else { ?>
                                                                <div class="mnw-coupon-setting">
                                                                    <div class="mnw-select">
                                                                        <div class="mnw-ip">
                                                                            <select class="lm_field lm_coupon_field lm_coupon_option" name="lm_data[<?php echo $prize['id']; ?>][type_option]" required disabled>
                                                                                <option value=""><?php echo __('Chọn kiểu khuyến mãi', 'lucky-money'); ?></option>
                                                                                <option value="default">
                                                                                    <?php echo __('Cố định', 'lucky-money'); ?>
                                                                                </option>
                                                                                <option value="percent">
                                                                                    <?php echo __('Phần trăm', 'lucky-money'); ?>
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mnw-ip">
                                                                            <input type="number" class="lm_field lm_coupon_field lm_coupon_value" required disabled
                                                                                name="lm_data[<?php echo $prize['id']; ?>][type_value]"
                                                                                value="<?php echo $prize['type_value']; ?>"
                                                                                placeholder="<?php echo __('Nhập giá trị', 'lucky-money'); ?>">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mnw-coupon-no-setting">
                                                                        ...
                                                                    </div>
                                                                </div>
                                                            <?php }
                                                            ?>
                                                        </td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <td data-label="<?php _e('Hành động', 'lucky-money'); ?>">
                                                        <div class="mnw-table-action">
                                                            <div class="mnw-cursor mnw-table-action-btn mnw-edit lm_button update lmPrizeUpdation disabled"
                                                                data-prize_key="<?php echo $prize['id']; ?>">
                                                                <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/mnw-ic-pen.svg" alt="">
                                                            </div>
                                                            <div class="mnw-cursor mnw-table-action-btn mnw-del lm_button lmPrizeDeletion"
                                                                data-prize_key="<?php echo $prize['id']; ?>">
                                                                <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/mnw-ic-trash.svg" alt="">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        } else { ?>
                                            <tr>
                                                <td colspan="<?php echo class_exists('Woocommerce') ? '6' : '5'; ?>">
                                                    <?php echo __('Chưa có phần thưởng nào ở đây!!!', 'lucky-money'); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>