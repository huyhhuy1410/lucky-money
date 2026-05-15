<?php

if (!defined('ABSPATH')) {
    exit();
}

// $data_prize = isset($args['data_prize']) &&  !empty($args['data_prize'])  ? (array)$args['data_prize'] : array();
// $wheel_program_type = $lucky_money_program_type;

?>
<div class="popup" data-popup-id="game" id="lm_game">
    <div class="popup-overlay"></div>
    <div class="popup-main lixi-popup lixi-game">
        <div class="popup-main-wrapper">


            <div class="bg">
                <img src="<?php echo LUCKY_MONEY_URL ?>/public/assets/images/bg-popup1.jpg" alt="" />
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
                </div>
            </div>
            <div class="popup-over">
                <div class="popup-wrapper">
                    <div class="lixi-game-wrap">
                        <div class="lixi-game-head">
                            <p class="tt"><?php echo get_post_meta($post->ID, '_lucky_money_field_game_title', true); ?></p>
                            <p class="des"><?php echo get_post_meta($post->ID, '_lucky_money_field_game_description', true); ?></p>
                        </div>
                        <div class="canvas-container">
                            <canvas id="gameCanvas" data-prize_id="<?php echo $winning_prize_id ?>"></canvas>
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