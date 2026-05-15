<?php
if (!defined('ABSPATH')) {
    exit();
}
// $data_prize = isset($args['data_prize']) &&  !empty($args['data_prize'])  ? (array)$args['data_prize'] : array();
// $wheel_program_type = $data_prize['wheel_program_type'];
// $cookie_result_id_root = $data_prize['cookie_result_id_root'];
// $cookie_result = $data_prize['cookie_result'];
// $winning_prize_id = $data_prize['winning_prize_id'];
// $cookie_result_id = $data_prize['cookie_result_id'];
$wheel_program_type = $lucky_money_program_type;

if ($wheel_program_type == 'default') { ?>
    <div class="popup " data-popup-id="form" id="lm_form">
        <div class="popup-overlay"></div>
        <div class="popup-main lixi-popup lixi-form">
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
                            <p class="form-text"><?php echo __('Thông tin nhận quà', 'lucky-money'); ?></p>
                            <div class="mnw-noti mnw-noti-success d-none" style="display: none">
                                <div class="mnw-noti-image ">
                                    <img src="<?php echo LUCKY_MONEY_URL ?>/public/assets/images/surprise-gift-box.png" loading="lazy" alt="">
                                </div>

                                <div class="mnw-noti-title">
                                    <?php echo __('Nhận thưởng thành công<br>
                        Vui lòng kiểm tra email', 'lucky-money'); ?>
                                </div>
                                <div class="mnw-noti-description">

                                </div>
                            </div>

                            <?php
                            $UserDisplay = $UserEmail = false;
                            if (is_user_logged_in()) {
                                $CurrentUser = wp_get_current_user();
                                $UserDisplay = $CurrentUser->display_name;
                                $UserEmail   = $CurrentUser->user_email;
                            } ?>
                            <form id="formLucky_MoneyUserInformation">
                                <?php
                                if ($winning_prize_id) { ?>
                                    <input type="hidden" name="lm_prize_id"
                                        value="<?php echo $winning_prize_id ?>">
                                <?php
                                }
                                ?>
                                <div class="lixi-form-box">
                                    <div class="form-list row">
                                        <div class="form-ip col <?php echo $UserDisplay !== false ? 'lucky-money-readonly' : ''; ?>">
                                            <span class="text"><?php echo __('Họ và tên của bạn', 'lucky-money'); ?> <span class="red">*</span>
                                            </span>
                                            <input type="text" class="lucky-money-req-field" name="lm_user_fullname"
                                                value="<?php echo $UserDisplay; ?>" placeholder="<?php echo __('Nhập họ và tên của bạn', 'lucky-money') ?>"
                                                required <?php echo $UserDisplay !== false ? 'readonly' : ''; ?>>
                                        </div>
                                        <div class="form-ip col">
                                            <span class="text"><?php echo __('Số điện thoại', 'lucky-money'); ?> <span class="red">*</span>
                                            </span>
                                            <input type="tel" class="lucky-money-req-field" name="lm_user_phone" required pattern="^[+]?[0-9]{9,12}$"
                                                placeholder="<?php echo __('Nhập số điện thoại của bạn', 'lucky-money') ?>">
                                        </div>
                                        <div class="form-ip col <?php echo $UserEmail !== false ? 'lucky-money-readonly' : ''; ?>">
                                            <span class="text"><?php echo __('Email', 'lucky-money'); ?> <span class="red">*</span>
                                            </span>
                                            <input type="email" class="lucky-money-req-field" name="lm_user_email"
                                                value="<?php echo $UserEmail; ?>" placeholder="<?php echo __('Nhập email của bạn', 'lucky-money') ?>"
                                                required <?php echo $UserEmail !== false ? 'readonly' : ''; ?>>
                                        </div>

                                        <div class="form-ip col">
                                            <button class="lixi-form-btn sendBtn clSound is-loading-coin" id="spinButton" type="submit">
                                                <span></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

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
<?php } else { ?>

    <div class="popup <?php echo !$popup && !empty($cookie_result_id_root) ? 'open' : ''; ?>" data-popup-id="form">
        <div class="popup-overlay"></div>
        <div class="popup-main lixi-popup lixi-form">
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
                            <p class="form-text"><?php echo __('Thông tin nhận quà', 'lucky-money'); ?></p>
                            <?php if (!empty($cookie_result)) { ?>

                                <?php
                                $UserDisplay = $UserEmail = false;
                                if (is_user_logged_in()) {
                                    $CurrentUser = wp_get_current_user();
                                    $UserDisplay = $CurrentUser->display_name;
                                    $UserEmail   = $CurrentUser->user_email;
                                } ?>
                                <form id="formLucky_MoneyUserInformation2">
                                    <div class="lixi-form-box">
                                        <div class="form-list row">
                                            <div class="form-ip col <?php echo $UserDisplay !== false ? 'lucky-money-readonly' : ''; ?>">
                                                <span class="text"><?php echo __('Họ và tên của bạn', 'lucky-money'); ?> <span class="red">*</span>
                                                </span>
                                                <input type="text" class="lucky-money-req-field" name="lm_user_fullname"
                                                    value="<?php echo $UserDisplay; ?>" placeholder="<?php echo __('Nhập họ và tên của bạn', 'lucky-money') ?>"
                                                    required <?php echo $UserDisplay !== false ? 'readonly' : ''; ?>>
                                            </div>
                                            <div class="form-ip col">
                                                <span class="text"><?php echo __('Số điện thoại', 'lucky-money'); ?> <span class="red">*</span>
                                                </span>
                                                <input type="tel" class="lucky-money-req-field" name="lm_user_phone" required
                                                    placeholder="<?php echo __('Nhập số điện thoại của bạn', 'lucky-money') ?>">
                                            </div>
                                            <div class="form-ip col <?php echo $UserEmail !== false ? 'lucky-money-readonly' : ''; ?>">
                                                <span class="text"><?php echo __('Email', 'lucky-money'); ?> <span class="red">*</span>
                                                </span>
                                                <input type="email" class="lucky-money-req-field" name="lm_user_email"
                                                    value="<?php echo $UserEmail; ?>" placeholder="<?php echo __('Nhập email của bạn', 'lucky-money') ?>"
                                                    required <?php echo $UserEmail !== false ? 'readonly' : ''; ?>>
                                            </div>
                                            <input type="hidden" name="lm_result_id"
                                                value="<?php echo $cookie_result_id_root ? $cookie_result_id_root : ''; ?>">
                                            <div class="form-ip col">
                                                <button class="lixi-form-btn sendBtn clSound is-loading-coin" id="spinButton" type="submit">
                                                    <span></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php
                            } else {
                            ?>
                                <?php if ($cookie_result_id_root == 'none') { ?>
                                    <div class="mnw-noti mnw-noti-sad">
                                        <div class="mnw-noti-image">
                                            <img src="<?php echo LUCKY_MONEY_URL; ?>/public/assets/images/sad-popup.png" loading="lazy" alt="" />
                                        </div>
                                        <div class="mnw-noti-title">
                                            <?php echo __('Chúc bạn may mắn lần sau', 'lucky-money'); ?>
                                        </div>
                                        <div class="mnw-noti-description">
                                            <?php echo sprintf(
                                                __('Cảm ơn bạn đã dành thời gian
                                <br>tham gia sự kiện của %s.', 'lucky-money'),
                                                get_bloginfo('name')
                                            ); ?>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="mnw-noti mnw-noti-success">
                                        <div class="mnw-noti-image">
                                            <img src="<?php echo LUCKY_MONEY_URL; ?>/public/assets/images/surprise-gift-box.png" loading="lazy" alt="" />
                                        </div>
                                        <div class="mnw-noti-description">
                                            <?php echo sprintf(
                                                __('Cảm ơn bạn đã dành thời gian
                                <br>tham gia sự kiện của %s.', 'lucky-money'),
                                                get_bloginfo('name')
                                            ); ?>
                                        </div>

                                    </div>
                                <?php } ?>
                            <?php
                            }
                            ?>
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
<?php
}
?>