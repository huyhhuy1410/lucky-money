<?php

if (!defined('ABSPATH')) {
    exit();
}

// $data_prize = isset($args['data_prize']) &&  !empty($args['data_prize'])  ? (array)$args['data_prize'] : array();
// $cookie_result_id_root = $data_prize['cookie_result_id_root'];
$wheel_program_type = $lucky_money_program_type;
$datapopup = $wheel_program_type == 'default' ? 'form' : 'game';
// var_dump($cookie_result_id_root);
?>
<div class="popup <?php echo empty($cookie_result_id_root) ? 'open' : ''; ?>" data-popup-id="lixi">
    <div class="popup-overlay"></div>
    <div class="popup-main lixi-popup lixi-intro">
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
                            <div class="decorTop-btn bd gameStart popup-open clSound" id="gameStart" data-popup="<?php echo $datapopup ?>"></div>
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
                        <p class="year"> <?php echo get_post_meta($post->ID, '_lucky_money_field_year', true); ?></p>
                        <p class="nameGame"> <?php echo get_post_meta($post->ID, '_lucky_money_field_title', true); ?></p>
                        <p class="wish"> <?php echo get_post_meta($post->ID, '_lucky_money_field_subtitle', true); ?></p>
                        <p class="wish-des"> <?php echo get_post_meta($post->ID, '_lucky_money_field_content', true); ?></p>
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