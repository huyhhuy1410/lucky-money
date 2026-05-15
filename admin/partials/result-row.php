<?php
if (!$result) {
    return;
} ?>

<td data-label="ID">
    <?php echo $result['id']; ?>
</td>

<td data-label="<?php echo __('Họ tên', 'lucky-money'); ?>">
    <div class="mnw-author">
        <div class="mnw-author-avatar preview-container">
            <div class="avatar preview-img">
                <?php echo get_avatar($result['email'], 'small'); ?>
            </div>
        </div>
        <span class="txt"><?php echo $result['fullname']; ?></span>
    </div>
</td>

<td data-label="Phần thưởng">
    <div class="lm-prize">
        <div class="lm-prize-wr">
            <div class="lm-prize-lf">
                <?php 
                // var_dump($result['prize_thumbnail']) 
                ?>
                <?php if ( empty( $result['prize_thumbnail'] ) ) { ?>
                    <img src="<?php echo LUCKY_MONEY_URL . '/admin/images/default-thumbnail.png';  ?>">
                <?php } else { ?>
                    <?php echo wp_get_attachment_image( $result['prize_thumbnail'], 'small' );  ?>
                <?php } ?>
            </div>
            <div class="lm-prize-rt">
                <div class="lm-prize-tt">
                    <?php echo $result['prize_name']; ?>
                </div>

                <?php 
                if ( !empty( $result['prize_value'] ) ) { ?>
                    <?php if ( class_exists('woocommerce') ) {
                        $coupon_code = $result['prize_value'];
                        $coupon = new WC_Coupon($coupon_code);
                        if ( !$coupon ) { ?>
                            <div class="lm-prize-val">
                                <span class="lm-prize-val-lab">
                                    <?php echo __('Mã ưu đãi: ', 'lucky-money'); ?>
                                </span>
                                <span class="lm-prize-val-code">
                                    <?php echo $coupon_code; ?>
                                </span>
                            </div>
                        <?php } else { ?>
                            <a href="<?php echo get_edit_post_link($coupon->id); ?>" class="lm-prize-val" target="_blank">
                                <span class="lm-prize-val-lab">
                                    <?php echo __('Mã ưu đãi: ', 'lucky-money'); ?>
                                </span>
                                <span class="lm-prize-val-code">
                                    <?php echo $coupon_code; ?>
                                </span>
                            </a>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <?php if ( !empty( $result['prize_description'] ) ) { ?>
                        <div class="lm-prize-sub">
                            <?php echo $result['prize_description']; ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</td>

<td data-label="Liên hệ">
    <div class="mnw-address">
        <a href="mailto:<?php echo $result['email']; ?>" class="mnw-address-it">
            <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/ic-email.svg" alt="">
            <span class="txt"><?php echo $result['email']; ?></span>
        </a>
        <a href="<?php echo lm_replace_tel($result['phone']); ?>" class="mnw-address-it">
            <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/ic-phone.svg" alt="">
            <span class="txt"><?php echo $result['phone']; ?></span>
        </a>
    </div>
</td>

<td data-label="Hành động">
    <?php if (!in_array($result['prize_type'], ['none'])) { ?>
        <div class="switch-infor is-loading-area">
            <?php if (!in_array($result['prize_type'], ['coupon'])) { ?>
                <label class="switch">
                    <input type="checkbox" class="resultReceivedJs" name="results[<?php echo $result['id']; ?>][received]"
                        value="<?php echo $result['id']; ?>" <?php checked($result['received'], true); ?>>
                    <span class="slider round"></span>
                </label>
            <?php } ?>
            <div class="switch-label">
                <?php if (!in_array($result['prize_type'], ['coupon'])) { ?>

                    <?php
                    if (!$result['received']) {
                        echo __('Chưa nhận', 'lucky-money');
                    } else {
                        echo sprintf(
                            __('Đã nhận %s', 'lucky-money'),
                            $result['received_at']
                        );
                    }; ?>

                <?php } else { ?>

                    <?php
                    if ($result['received']) {

                        if (!empty($result['received_note'])) {
                            $received_note = $result['received_note'];
                            $received_note = preg_replace_callback('/#(\d+)/', function ($matches) {
                                $order_id = $matches[1];
                                $edit_url = get_edit_post_link($order_id);
                                return '<a href="' . esc_url($edit_url) . '" target="_blank">#' . esc_html($order_id) . '</a>';
                            }, $received_note);
                    ?>
                            <div class="received-note-cus">
                                <?php echo $received_note; ?>
                            </div>
                        <?php } else { ?>
                            <?php
                            echo sprintf(
                                __('Đã nhận %s', 'lucky-money'),
                                $result['received_at']
                            ); ?>
                    <?php }
                    } ?>

                <?php } ?>
            </div>
        </div>
    <?php } ?>
</td>

<td data-label="Tham gia">
    <div class="mnw-date"><?php echo $result['created_at']; ?></div>
</td>