<?php
if (!defined('ABSPATH')) {
    exit();
}
function lm_prize_options()
{
    if (!class_exists('Woocommerce')) {
        return [
            'none'      => __('Không trúng', 'lucky-money'),
            'custom'    => __('Tuỳ chỉnh', 'lucky-money'),
        ];
    } else {
        return [
            'none'      => __('Không trúng', 'lucky-money'),
            'custom'    => __('Tuỳ chỉnh', 'lucky-money'),
            'coupon'    => __('Mã ưu đãi', 'lucky-money'),
        ];
    }
}

function lm_replace_tel($hotline = '')
{
    if (empty($hotline)) {
        return;
    }
    $string   = preg_replace('/\s+/', '', $hotline);
    $stringaz = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $string);
    $tel = 'tel:' . $stringaz;
    return $tel;
}

function lm_pagination_links($current_page, $max_pages = 1)
{
    if ($max_pages <= 1) {
        return;
    }

    // Get the current URL
    $base_url = admin_url('admin.php?page=lucky_money_program');

    // Generate the pagination links
    echo '<div class="lm-pagination lm-pagination-ajax">';
    echo paginate_links([
        'base'      => add_query_arg('lm-paged', '%#%', $base_url),
        'format'    => '',
        'current'   => max(1, $current_page),
        'total'     => $max_pages,
        'prev_text' => '‹‹',
        'next_text' => '››',
        'type'      => 'list',
        'end_size'  => 3,
        'mid_size'  => 3,
    ]);
    echo '</div>';
}

function lm_encode_prize_id($prize_id)
{
    $key = AUTH_KEY;
    $hash = substr(hash_hmac('md5', $prize_id, $key), 0, 16);
    $data = $prize_id . ':' . $hash;
    return base64_encode($data);
}

function lm_decode_prize_id($encoded_string)
{
    $key = AUTH_KEY;
    $decoded = base64_decode($encoded_string);
    if ($decoded === false) {
        return false;
    }

    list($prize_id, $hash) = explode(':', $decoded);

    $valid_hash = substr(hash_hmac('md5', $prize_id, $key), 0, 16);
    if (hash_equals($valid_hash, $hash)) {
        return (int)$prize_id;
    }

    return false;
}

function lm_find_winning_prize($lucky_money_prizes, $lm_prize_id = false)
{
    if (!$lucky_money_prizes) {
        return false;
    }

    if (!$lm_prize_id) {
        $filtered_prizes = array_values(array_filter($lucky_money_prizes, function ($prize) {
            return ($prize['prize_quantity'] === null || $prize['prize_quantity'] > 0) && $prize['prize_percent'] > 0;
        }));

        if (empty($filtered_prizes)) {
            return false;
        }

        $total_weight = array_reduce($filtered_prizes, function ($carry, $prize) {
            return $carry + $prize['prize_percent'];
        }, 0);

        $random_number = mt_rand(0, $total_weight * 1000000) / 1000000;
        $cumulative_weight = 0;

        foreach ($filtered_prizes as $prize) {
            $cumulative_weight += $prize['prize_percent'];
            if ($random_number <= $cumulative_weight) {
                return $prize;
            }
        }
    } else {
        if (function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')) {
            $lm_prize_id = lm_decode_prize_id($lm_prize_id);
        }

        $filtered_prizes = array_values(
            array_filter($lucky_money_prizes, function ($prize) use ($lm_prize_id) {
                return $prize['prize_id'] == $lm_prize_id;
            })
        );

        if (!empty($filtered_prizes)) {
            if (($filtered_prizes[0]['prize_quantity'] === null || $filtered_prizes[0]['prize_quantity'] > 0) && $filtered_prizes[0]['prize_percent'] > 0) {
                return $filtered_prizes[0];
            } else {
                return lm_find_winning_prize($lucky_money_prizes);
            }
        }
    }

    return false;
}

function lm_create_unique_coupon_code()
{
    $prefix = 'LUCKY-MONEY-';

    do {
        $random_chars = strtoupper(wp_generate_password(6, false, false));
        $coupon_code = $prefix . $random_chars;

        $coupon_exists = wc_get_coupon_id_by_code($coupon_code);
    } while ($coupon_exists);

    return $coupon_code;
}
add_action('wp_footer', 'lucky_money_filter_front_footer');
function lucky_money_filter_front_footer()
{
    echo '<div id="lucky-money-toast"></div>';
}
function lm_get_message_success($text = '')
{
    $message = '';
    $message .= '<div class="toast__icon"><span class="dashicons dashicons-yes"></span></div>';
    $message .= '<div class="toast__body">';
    $message .= '<h3 class="toast__title">' . __('Thông báo!', 'lucky-money') . '</h3>';
    $message .= '<p class="toast__msg">' . $text . '</p>';
    $message .= '</div>';
    $message .= '<div class="toast__close"><span class="dashicons dashicons-no"></span></div>';
    $message .= '<div class="progress"></div>';

    return $message;
}

function lm_get_message_error($text = '')
{
    $message = '';
    $message .= '<div class="toast__icon"><span class="dashicons dashicons-no"></span></div>';
    $message .= '<div class="toast__body">';
    $message .= '<h3 class="toast__title">' . __('Thông báo!', 'lucky-money') . '</h3>';
    $message .= '<p class="toast__msg">' . $text . '</p>';
    $message .= '</div>';
    $message .= '<div class="toast__close"><span class="dashicons dashicons-no"></span></div>';
    $message .= '<div class="progress"></div>';

    return $message;
}

function verifyDate($date)
{
    return (DateTime::createFromFormat('dd-mm-yy', $date) !== false);
}