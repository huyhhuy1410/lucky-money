<?php
if (!defined('ABSPATH')) {
    exit();
}
add_action('wp_ajax_lm_ajax_get_result_prizes', 'lm_ajax_get_result_prizes'); // login
function lm_ajax_get_result_prizes() {
    $search = isset( $_POST['search'] ) 
    ? sanitize_text_field( $_POST['search'] ) 
    : '';

    $program_id = isset( $_POST['program_id'] ) 
    ? $_POST['program_id'] 
    : '';
    
    global $lucky_moneyResult;
    $options = $lucky_moneyResult->get_data_prize( $search, $program_id );
    wp_send_json($options);
}