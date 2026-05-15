<?php

/**
 * Template name: Bốc Lì Xì
 * @author : Huy Vo
 */
if (!defined('ABSPATH')) {
    exit();
}
get_header();
while (have_posts()):
    the_post();
    global $post;
    $end_time = get_post_meta($post->ID, '_lucky_money_field_end_time', true);
    $current_date = new DateTime();
    // global $wheelProgram, $wheelResult, $post;
    // $wheel_program_type = get_option('mw_option_program_type_' . $post->ID, 'default');
    $cookie_result_id_root = $cookie_result_id = $cookie_result = false;
    $winning_prize = $winning_prize_id = false;
    $programLucky_MoneyId = $post->ID;
    $popup = false;
?>
    <main class="main page-lixi">
        <?php
        $programLucky_MoneyFlag = apply_filters(
            'lm_program_lucky_money_validation_for_user',
            true,
            $programLucky_MoneyId
        );
        if ($end_time) {
            $end_date = verifyDate($end_time);
            if ($current_date > $end_date)
                include LUCKY_MONEY_DIR . '/public/partials/maintenance-popup.php';
            else {
                if ($programLucky_MoneyFlag) {
                    global $lucky_moneyProgram, $lucky_moneyResult;
                    $lucky_money_program_type = get_option('lm_option_program_type_' . $post->ID, 'default');

                    $lucky_money_prizes = $lucky_moneyProgram->get_lucky_money($post->ID);
                    $winning_prize = lm_find_winning_prize($lucky_money_prizes);
                    if ($winning_prize) {
                        $winning_prize_id = function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')
                            ? lm_encode_prize_id($winning_prize['prize_id'])
                            : $winning_prize['prize_id'];
                    }
                    if ($lucky_money_program_type == 'default') {
                        $cookie_result_id_root = '';
                        $cookie_result = '';
                    } else {
                        $cookie_result_id = isset($_COOKIE['lucky_money_' . $post->ID]) && !empty($_COOKIE['lucky_money_' . $post->ID])
                            ? esc_attr($_COOKIE['lucky_money_' . $post->ID])
                            : false;
                        if (!empty($cookie_result_id)) {
                            $cookie_result_id_root = $cookie_result_id;
                            // mã hoá
                            $cookie_result_id = !empty($cookie_result_id) && !in_array($cookie_result_id, ['none', 'done']) && function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')
                                ? lm_decode_prize_id($cookie_result_id)
                                : $cookie_result_id;
                            // phiếu kết quả
                            $cookie_result = !empty($cookie_result_id) && !in_array($cookie_result_id, ['none', 'done'])
                                ? $lucky_moneyResult->get_result_by_id($cookie_result_id)
                                : false;
                        }
                    }
                    include LUCKY_MONEY_DIR . '/public/partials/intro-popup.php';
                    include LUCKY_MONEY_DIR . '/public/partials/form-popup.php';
                    include LUCKY_MONEY_DIR . '/public/partials/guide-popup.php';
                    include LUCKY_MONEY_DIR . '/public/partials/game-popup.php';
                    include LUCKY_MONEY_DIR . '/public/partials/result-popup.php';
                } else {
                    include LUCKY_MONEY_DIR . '/public/partials/maintenance-popup.php';
                }
            }
        } else {


            if ($programLucky_MoneyFlag) {
                global $lucky_moneyProgram, $lucky_moneyResult, $post;
                $lucky_money_program_type = get_option('lm_option_program_type_' . $post->ID, 'default');

                $lucky_money_prizes = $lucky_moneyProgram->get_lucky_money($post->ID);
                $winning_prize = lm_find_winning_prize($lucky_money_prizes);
                if ($winning_prize) {
                    $winning_prize_id = function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')
                        ? lm_encode_prize_id($winning_prize['prize_id'])
                        : $winning_prize['prize_id'];
                }
                if ($lucky_money_program_type == 'default') {
                    $cookie_result_id_root = '';
                    $cookie_result = '';
                } else {
                    $cookie_result_id = isset($_COOKIE['lucky_money_' . $post->ID]) && !empty($_COOKIE['lucky_money_' . $post->ID])
                        ? esc_attr($_COOKIE['lucky_money_' . $post->ID])
                        : false;
                    if (!empty($cookie_result_id)) {
                        $cookie_result_id_root = $cookie_result_id;
                        // mã hoá
                        $cookie_result_id = !empty($cookie_result_id) && !in_array($cookie_result_id, ['none', 'done']) && function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')
                            ? lm_decode_prize_id($cookie_result_id)
                            : $cookie_result_id;
                        // phiếu kết quả
                        $cookie_result = !empty($cookie_result_id) && !in_array($cookie_result_id, ['none', 'done'])
                            ? $lucky_moneyResult->get_result_by_id($cookie_result_id)
                            : false;
                    }
                }
                include LUCKY_MONEY_DIR . '/public/partials/intro-popup.php';
                include LUCKY_MONEY_DIR . '/public/partials/form-popup.php';
                include LUCKY_MONEY_DIR . '/public/partials/guide-popup.php';
                include LUCKY_MONEY_DIR . '/public/partials/game-popup.php';
                include LUCKY_MONEY_DIR . '/public/partials/result-popup.php';
            } else {
                include LUCKY_MONEY_DIR . '/public/partials/maintenance-popup.php';
            }
        } ?>
    </main>
<?php
endwhile;
get_footer();
