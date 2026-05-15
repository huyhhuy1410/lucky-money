<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="popup open" data-popup-id="maintenance">
    <div class="popup-overlay"></div>
    <div class="popup-main lixi-popup lixi-intro">
        <div class="popup-main-wrapper">
            <div class="bg">
                <img src="<?php echo LUCKY_MONEY_URL ?>/public/assets/images/bg-popup.jpg" alt="" />
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
                        <p class="wish"> <?php echo $programLucky_MoneyFlag ? __('Sự kiện đã kết thúc', 'lucky-money') :  __('Sự kiện chưa sẵn sàng', 'lucky-money'); ?></p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>