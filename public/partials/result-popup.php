<?php 
if (!defined('ABSPATH')) {
    exit();
} 
?>
<div class="popups" id="popup" style="display: none;">
    <div class="popups-content">
        <div class="boxDrop">
            <div class="boxDropFrame"></div>
        </div>
        <div class="lixi-popup-frame">
            <div class="frame frameTop">
                <img src="<?php echo LUCKY_MONEY_URL ?>/public/assets/images/frameTop.svg" alt="" />
            </div>
            <div class="frame frameMid"></div>
            <div class="frame frameBot">
                <img src="<?php echo LUCKY_MONEY_URL ?>/public/assets/images/frameTop.svg" alt="" />
            </div>
        </div>
        <p class="text" id="popupNoti"><?php echo __('Chúc mừng!', 'lucky-money'); ?></p>
        <div id="popupMessage"></div>
        <div class="popups-btn reciveBtn"></div>
    </div>
    <canvas id="confetti"></canvas>
</div>