<?php

if (!defined('ABSPATH')) {
    exit();
}

// $data_prize = isset($args['data_prize']) &&  !empty($args['data_prize'])  ? (array)$args['data_prize'] : array();
// $wheel_program_type = $data_prize['wheel_program_type'];
$wheel_program_type = $lucky_money_program_type;

$datapopup = $wheel_program_type == 'default' ? 'form' : 'game';
?>
<div class="popup" data-popup-id="guide">
    <div class="popup-overlay"></div>
    <div class="popup-main lixi-popup lixi-guide">
        <div class="popup-main-wrapper">
            <div class="bg">
                <img src="<?php echo LUCKY_MONEY_URL ?>/public/assets/images/bg-popup.jpg" alt="" />
            </div>
            <div class="boxDrop">
                <div class="boxDropFrame"></div>
            </div>
            <div class="decorTop">
                <div class="decorTop-in">
                    <div class="decorTop-it hoamai">
                        <div class="decorTop-it-in">
                            <img src="<?php echo LUCKY_MONEY_URL ?>/public/assets/images/hoamai.png" alt="" />
                        </div>
                    </div>
                    <div class="decorTop-it longden">
                        <div class="decorTop-it-in">
                            <img src="<?php echo LUCKY_MONEY_URL ?>/public/assets/images/longden.png" alt="" />
                        </div>
                    </div>
                    <div class="decorTop-it nut disabled">
                        <div class="decorTop-it-in">
                            <div class="decorTop-btn dtl popup-open clSound" data-popup="guide"></div>
                            <div class="decorTop-btn bd gameStart popup-open clSound" data-popup="<?php echo $datapopup ?>"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="popup-over">
                <div class="popup-wrapper">
                    <div class="lixi-popup-content">
                        <div class="lixi-popup-frame">
                            <div class="frame frameTop">
                                <img src="<?php echo LUCKY_MONEY_URL ?>/public/assets/images/frameTop.svg" alt="" />
                            </div>
                            <div class="frame frameMid"></div>
                            <div class="frame frameBot">
                                <img src="<?php echo LUCKY_MONEY_URL ?>/public/assets/images/frameTop.svg" alt="" />
                            </div>
                        </div>
                        <p class="guide-text"><?php echo __('Thể lệ chương trình', 'lucky-money'); ?></p>
                        <div class="guide-content lucky-money-content">
                            <?php
                            $content = get_post_meta($post->ID, '_lucky_money_field_rules_content', true);
                            echo apply_filters('the_content', $content);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if ($popup) {
        ?>
            <div class="popup-close">
                <i class="fas fa-times icon"></i>
            </div>
        <?php
        }
        ?>
    </div>
</div>