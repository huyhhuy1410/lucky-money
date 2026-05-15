<?php

/**
 * Partial: Bốc Lì Xì Content
 * This file renders the content for the Bốc Lì Xì shortcode.
 */

if (!defined('ABSPATH')) {
    exit();
}

// Make sure $shortcode_id is set and valid
if (empty($shortcode_id)) {
    return;
}

// Set up the global $post variable for the page
global $post;
$post = get_post($shortcode_id);
setup_postdata($post);

// Example variables to interact with data
$programLucky_MoneyId = $shortcode_id;
$cookie_result_id_root = $cookie_result_id = $cookie_result = false;
$winning_prize = $winning_prize_id = false;
$popup = true;
// Fetch program validation
$programLucky_MoneyFlag = apply_filters(
    'lm_program_lucky_money_validation_for_user',
    true,
    $programLucky_MoneyId
);
?>
<div class="page-lixi">
    <?php
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

        // Include partials for rendering
        include LUCKY_MONEY_DIR . '/public/partials/intro-popup.php';
        include LUCKY_MONEY_DIR . '/public/partials/form-popup.php';
        include LUCKY_MONEY_DIR . '/public/partials/guide-popup.php';
        include LUCKY_MONEY_DIR . '/public/partials/game-popup.php';
        include LUCKY_MONEY_DIR . '/public/partials/result-popup.php';
    } else {
        // Maintenance popup if the program is invalid
        include LUCKY_MONEY_DIR . '/public/partials/maintenance-popup.php';
    }
    ?>
</div>
<?php
// Reset global post data
wp_reset_postdata();
