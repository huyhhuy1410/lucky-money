<?php
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('Lucky_MoneyProgram')) :
    class Lucky_MoneyProgram
    {
        private $table = 'lucky_money_program';
        function __construct()
        {
            $this->setup();
            $this->init_hooks();
        }
        private function init_hooks()
        {
            add_action('wp_ajax_lm_ajax_program_prize_creation', [$this, 'lm_ajax_program_prize_creation']); // login
            add_action('wp_ajax_lm_ajax_program_prize_information', [$this, 'lm_ajax_program_prize_information']); // login
            add_action('wp_ajax_lm_ajax_program_prize_information_2', [$this, 'lm_ajax_program_prize_information_2']); // login
            add_action('wp_ajax_lm_ajax_program_prize_updation', [$this, 'lm_ajax_program_prize_updation']); // login
            add_action('wp_ajax_lm_ajax_program_prize_deletion', [$this, 'lm_ajax_program_prize_deletion']); // login
            add_action('wp_ajax_lm_ajax_program_prize_data', [$this, 'lm_ajax_program_prize_data']); // login
            add_action('wp_ajax_nopriv_lm_ajax_program_prize_data', [$this, 'lm_ajax_program_prize_data']); // no-login
            add_action('wp_ajax_lm_ajax_program_prize_win', [$this, 'lm_ajax_program_prize_win']); // login
            add_action('wp_ajax_nopriv_lm_ajax_program_prize_win', [$this, 'lm_ajax_program_prize_win']); // no-login
            add_action('wp_ajax_lm_ajax_program_prize_win_2', [$this, 'lm_ajax_program_prize_win_2']); // login
            add_action('wp_ajax_nopriv_lm_ajax_program_prize_win_2', [$this, 'lm_ajax_program_prize_win_2']); // no-login
            add_action('wp_ajax_lm_ajax_program_prize_win_3', [$this, 'lm_ajax_program_prize_win_3']); // login
            add_action('wp_ajax_nopriv_lm_ajax_program_prize_win_3', [$this, 'lm_ajax_program_prize_win_3']); // no-login
            add_action('wp_ajax_lm_ajax_program_prize_win_4', [$this, 'lm_ajax_program_prize_win_4']); // login
            add_action('wp_ajax_nopriv_lm_ajax_program_prize_win_4', [$this, 'lm_ajax_program_prize_win_4']); // no-login
        }
        private function setup()
        {
            global $wpdb;
            $table_name         = $wpdb->base_prefix . $this->table;
            $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));
            if (! $wpdb->get_var($query) == $table_name) {
                $charset_collate    = $wpdb->get_charset_collate();
                $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                page_id mediumint(9) NOT NULL,
                prize_id mediumint(9) NOT NULL,
                prize_thumbnail mediumint(9) NULL,
                prize_percent mediumint(9) NOT NULL,
                prize_quantity mediumint(9) NULL,
                prize_color TEXT NOT NULL,
                prize_background TEXT NOT NULL,
                prize_sort mediumint(9) NULL,
                status TINYINT(1) NOT NULL,
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id)
                ) $charset_collate;";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }
        public function import_data($data)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $current_time = current_time('mysql');
            $result = $wpdb->insert(
                $table_name,
                array(
                    'page_id'          => intval($data['page_id']),
                    'prize_id'         => intval($data['prize_id']),
                    'prize_thumbnail'  => !empty(@$data['prize_thumbnail']) ? intval($data['prize_thumbnail']) : null,
                    'prize_percent'    => intval($data['prize_percent']),
                    'prize_quantity'   => !empty(@$data['prize_quantity']) ? intval($data['prize_quantity']) : null,
                    'prize_color'      => $data['prize_color'],
                    'prize_background' => $data['prize_background'],
                    'prize_sort'       => 0,
                    'status'           => 1,
                    'created_at'       => $current_time,
                    'updated_at'       => $current_time,
                ),
                array(
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%d',
                    '%s',
                    '%s',
                )
            );
            if ($result) {
                return $wpdb->insert_id;
            } else {
                return false;
            }
        }
        public function get_data($page_id = null)
        {
            global $wpdb;
            $table_name  = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            // Define the column to order by
            $order_column = 'prize_sort';
            $is_woocommerce = class_exists('WooCommerce');
            if ($page_id) {
                if (!$is_woocommerce) {
                    $results = $wpdb->get_results($wpdb->prepare(
                        "SELECT lmp.*, 
                        lmpz.name AS prize_name, 
                        lmpz.description AS prize_description,
                        lmpz.type AS prize_type 
                        FROM $table_name lmp
                        INNER JOIN $prize_table lmpz ON lmp.prize_id = lmpz.id
                        WHERE lmp.page_id = %d 
                        AND lmpz.type != %s
                        AND lmp.status = 1 
                        ORDER BY lmp.$order_column ASC",
                        $page_id,
                        'coupon'
                    ), ARRAY_A);
                } else {
                    $results = $wpdb->get_results($wpdb->prepare(
                        "SELECT lmp.*,
                        lmpz.name AS prize_name, 
                        lmpz.description AS prize_description,
                        lmpz.type AS prize_type 
                        FROM $table_name lmp
                        INNER JOIN $prize_table lmpz ON lmp.prize_id = lmpz.id
                        WHERE lmp.page_id = %d 
                        AND lmp.status = 1 
                        ORDER BY lmp.$order_column ASC",
                        $page_id
                    ), ARRAY_A);
                }
            } else {
                if (!$is_woocommerce) {
                    $results = $wpdb->get_results(
                        "SELECT lmp.*,
                        lmpz.name AS prize_name, 
                        lmpz.description AS prize_description,
                        lmpz.type AS prize_type 
                        FROM $table_name lmp
                        INNER JOIN $prize_table lmpz ON lmp.prize_id = lmpz.id
                        WHERE lmpz.type != 'coupon'
                        AND lmp.status = 1 
                        ORDER BY lmp.$order_column ASC",
                        ARRAY_A
                    );
                } else {
                    $results = $wpdb->get_results(
                        "SELECT lmp.*,
                        lmpz.name AS prize_name, 
                        lmpz.description AS prize_description,
                        lmpz.type AS prize_type 
                        FROM $table_name lmp
                        INNER JOIN $prize_table lmpz ON lmp.prize_id = lmpz.id
                        WHERE lmp.status = 1 
                        ORDER BY lmp.$order_column ASC",
                        ARRAY_A
                    );
                }
            }
            return $results !== false ? $results : false;
        }
        public function get_lucky_money($page_id)
        {
            if (empty($page_id)) {
                return false;
            }
            global $wpdb;
            $program_table = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            // Define the column to order by
            $is_woocommerce = class_exists('WooCommerce');
            $order_column = 'prize_sort';
            if (!$is_woocommerce) {
                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT lmp.*, 
                            lmpz.description AS prize_description, 
                            lmpz.name AS prize_name, 
                            lmpz.type AS prize_type 
                        FROM $program_table lmp
                        INNER JOIN $prize_table lmpz ON lmp.prize_id = lmpz.id
                        WHERE lmp.page_id = %d AND lmpz.type != %s AND lmp.status = 1
                        ORDER BY lmp.$order_column ASC", // Use $order_column for ordering
                        $page_id,
                        'coupon'
                    ),
                    ARRAY_A
                );
            } else {
                $results = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT lmp.*, 
                            lmpz.description AS prize_description, 
                            lmpz.name AS prize_name, 
                            lmpz.type AS prize_type 
                        FROM $program_table lmp
                        INNER JOIN $prize_table lmpz ON lmp.prize_id = lmpz.id
                        WHERE lmp.page_id = %d AND lmp.status = 1
                        ORDER BY lmp.$order_column ASC", // Use $order_column for ordering
                        $page_id
                    ),
                    ARRAY_A
                );
            }
            if (empty($results)) {
                return false;
            }
            return $results;
        }
        public function update_data($id, $data)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $current_time = current_time('mysql');
            $update_data = array(
                'prize_thumbnail'  => !empty(@$data['prize_thumbnail']) ? intval($data['prize_thumbnail']) : null,
                'prize_percent'    => !empty(@$data['prize_percent']) ?  intval($data['prize_percent']) : null,
                'prize_quantity'   => !empty(@$data['prize_quantity']) ?  intval($data['prize_quantity']) : null,
                'prize_sort'       => !empty(@$data['prize_sort']) ?  intval($data['prize_sort']) : 0,
                'prize_background' => !empty(@$data['prize_background']) ? $data['prize_background'] : null,
                'prize_color'      => !empty(@$data['prize_color']) ? $data['prize_color'] : null,
                'updated_at'       => $current_time,
            );
            $result = $wpdb->update(
                $table_name,
                $update_data,
                array('id' => intval($id)),
                array(
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s'
                ),
                array('%d')
            );
            if ($result !== false && !empty($data['prize_thumbnail'])) {
                $result_page_id = intval($data['page_id']);
                $result_prize_combined  = intval($data['prize_id']);
                $result_prize_thumbnail = intval($data['prize_thumbnail']);
                $result_table = $wpdb->base_prefix . 'lucky_money_result';
                list($result_prize_id, $result_prize_type) = explode('.', $result_prize_combined);
                $update_result_data = array(
                    'prize_thumbnail' => $result_prize_thumbnail
                );
                $result_table = $wpdb->base_prefix . 'lucky_money_result';
                $result_updation = $wpdb->update(
                    $result_table,
                    $update_result_data,
                    array(
                        'page_id' => $result_page_id,
                        'prize_id' => $result_prize_id
                    ),
                    array('%d'),
                    array('%d', '%s')
                );
            }
            return $result !== false;
        }
        public function delete_data($id)
        {
            global $wpdb;
            $program_table_name = $wpdb->base_prefix . $this->table;
            $result_table_name = $wpdb->base_prefix . 'lucky_money_result';
            $program_data = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT page_id, prize_id FROM $program_table_name WHERE id = %d",
                    intval($id)
                ),
                ARRAY_A
            );
            if (!$program_data) {
                return false;
            }
            $result = $wpdb->update(
                $program_table_name,
                array(
                    'status' => 0,
                    'updated_at' => current_time('mysql'),
                ),
                array('id' => $id),
                array(
                    '%d',
                    '%s',
                ),
                array('%d')
            );
            if ($result !== false) {
                $update_result = $wpdb->update(
                    $result_table_name,
                    array(
                        'status' => 0,
                        'updated_at' => current_time('mysql'),
                    ),
                    array(
                        'page_id' => $program_data['page_id'],
                        'prize_id' => $program_data['prize_id'],
                    ),
                    array(
                        '%d',
                        '%s',
                    ),
                    array('%d', '%d')
                );
                return $result !== false;
            }
            return $result !== false;
        }
        public function update_data_quantity($prize_id, $page_id, $new_quantity)
        {
            global $wpdb;
            $prize_table = $wpdb->base_prefix . $this->table;
            $result = $wpdb->update(
                $prize_table,
                ['prize_quantity' => $new_quantity],
                ['prize_id' => $prize_id, 'page_id' => $page_id],
                ['%d'],
                ['%d', '%d']
            );
            return ($result !== false);
        }
        public function lm_ajax_program_prize_data()
        {
            if (isset($_POST['page_id']) && !empty($_POST['page_id'])) {
                $page_id = intval($_POST['page_id']);
                $data = $this->get_lucky_money($page_id);
                $formatted_data = [
                    'segments' => []
                ];
                // if( count( $data ) < 8 ){
                // $limitData = 0;
                // while( $limitData < 3 ){
                // foreach ( $data as $row ) {
                //     $formatted_data['segments'][] = [
                //         'id'            => $row['prize_id'],
                //         'label'         => $row['prize_name'],
                //         'description'   => $row['prize_description'],
                //         'color'         => $row['prize_color'],
                //         'background'    => $row['prize_background'],
                //         'image'         => $row['prize_thumbnail'] 
                //         ? wp_get_attachment_image_url( $row['prize_thumbnail'] , 'small' ) 
                //         : LUCKY_MONEY_URL . '/admin/images/default-thumbnail.png',
                //         'type'  => $row['prize_type'] == 'none' ? false : true,
                //     ];
                // }
                // $limitData++;
                // }
                // }else{
                foreach ($data as $row) {
                    $formatted_data['segments'][] = [
                        'id'            => $row['prize_id'],
                        'label'         => $row['prize_name'],
                        'description'   => $row['prize_description'],
                        'color'         => $row['prize_color'],
                        'background'    => $row['prize_background'],
                        'image'         => $row['prize_thumbnail']
                            ? wp_get_attachment_image_url($row['prize_thumbnail'], 'small')
                            : LUCKY_MONEY_URL . '/admin/images/default-thumbnail.png',
                        'type'  => $row['prize_type'] == 'none' ? false : true,
                    ];
                }
                // }
                wp_send_json_success($formatted_data);
            } else {
                wp_send_json_error([
                    'title'   => __('Thông báo', 'lucky-money'),
                    'message' => lm_get_message_error(__('Mã chương trình sự kiện không tồn tại hoặc rỗng', 'lucky-money')),
                ]);
            }
            wp_die();
        }
        public function lm_ajax_program_prize_win()
        {
            $page_id = !empty(@$_POST['page_id']) ? esc_attr($_POST['page_id']) : false;
            if (isset($page_id) && !empty($page_id)) {
                $lm_user_fullname   =   !empty(@$_POST['lm_user_fullname']) ? esc_attr($_POST['lm_user_fullname']) : false;
                $lm_user_phone      =   !empty(@$_POST['lm_user_phone']) ? esc_attr($_POST['lm_user_phone']) : false;
                $lm_user_email      =   !empty(@$_POST['lm_user_email']) ? esc_attr($_POST['lm_user_email']) : false;
                if (empty($lm_user_email) || empty($lm_user_fullname) || empty($lm_user_phone)) {
                    wp_send_json_error([
                        'title'   => __('Thông báo', 'lucky-money'),
                        'message' => lm_get_message_error(__('Vui lòng nhập đầy đủ thông tin', 'lucky-money')),
                    ]);
                }
                if (!is_email($lm_user_email)) {
                    wp_send_json_error([
                        'title'   => __('Thông báo', 'lucky-money'),
                        'message' => lm_get_message_error(__('Email không hợp lệ', 'lucky-money')),
                    ]);
                }
                if (!preg_match('/^[+]?[0-9]{9,12}$/', $lm_user_phone)) {
                    wp_send_json_error([
                        'title'   => __('Thông báo', 'lucky-money'),
                        'message' => lm_get_message_error(__('Số điện thoại không hợp lệ', 'lucky-money')),
                    ]);
                } 
                $lm_user_data       = [
                    'email'     =>  $lm_user_email,
                    'phone'     =>  $lm_user_phone,
                    'fullname'  =>  $lm_user_fullname,
                ];
                $lm_user_validation = apply_filters(
                    'lm_user_validation_for_lucky_money',
                    true,
                    $page_id,
                    $lm_user_data
                );
                if (!$lm_user_validation) {
                    wp_send_json_error([
                        'title'   => __('Thông báo', 'lucky-money'),
                        'message' => lm_get_message_error(__('Bạn đã hết lượt tham gia', 'lucky-money')),
                    ]);
                }
                wp_send_json_success($lm_user_data);
                // $lm_prize_id = !empty(@$_POST['lm_prize_id']) ? esc_attr($_POST['lm_prize_id']) : false;
                // $winning_prize = $winning_prize_id = $winning_value = $winning_type = null;
                // $lucky_money_prizes = $this->get_lucky_money($page_id);
                // $winning_prize = lm_find_winning_prize($lucky_money_prizes, $lm_prize_id);
                // if ($winning_prize) {
                //     $winning_prize_id = $winning_prize['id'];
                //     global $lucky_moneyPrize;
                //     $lucky_moneyPrizeItem = $lucky_moneyPrize->get_item($winning_prize_id);
                //     $winning_type  = $lucky_moneyPrizeItem['type'];
                //     if ($winning_prize['prize_quantity'] && $winning_prize['prize_quantity'] > 0) {
                //         $updated_quantity = intval($winning_prize['prize_quantity']) - 1;
                //         $update_result = $this->update_data_quantity(
                //             $winning_prize_id,
                //             $page_id,
                //             $updated_quantity
                //         );
                //         if (!$update_result) {
                //             wp_send_json_error([
                //                 'title'   => __('Thông báo', 'lucky-money'),
                //                 'message' => __('Không tìm thấy phần thưởng hợp lệ.', 'lucky-money'),
                //             ]);
                //         }
                //         $winning_prize['prize_quantity'] = $updated_quantity;
                //     }
                //     if ($lucky_moneyPrizeItem['type'] == 'coupon' && class_exists('Woocommerce')) {
                //         $couponOption = $lucky_moneyPrizeItem['type_option']; // default / percent
                //         $couponValue = $lucky_moneyPrizeItem['type_value']; // value
                //         // Create a new coupon
                //         $coupon_code = lm_create_unique_coupon_code();
                //         $coupon = new WC_Coupon();
                //         $coupon->set_code($coupon_code);
                //         if ($couponOption === 'percent') {
                //             $coupon->set_discount_type('percent'); // Percentage discount
                //             $coupon->set_amount($couponValue);
                //             $coupon->set_usage_limit(1);
                //         } else {
                //             $coupon->set_discount_type('fixed_cart'); // Fixed amount discount
                //             $coupon->set_amount($couponValue);
                //             $coupon->set_usage_limit(1);
                //         }
                //         // Optional: Các tuỳ chọn setup cho mã khuyến mãi
                //         $coupon->set_individual_use(true);
                //         $coupon->set_email_restrictions(
                //             [$lm_user_email]
                //         );
                //         // Lưa mã
                //         $coupon->save();
                //         $winning_value = $coupon_code;
                //     }
                //     $winning_data = [
                //         'prize'         => $winning_prize,
                //         'prize_id'      => $winning_prize_id,
                //         'prize_thumbnail' => $winning_prize['prize_thumbnail'],
                //         'prize_value'   => $winning_value,
                //         'prize_type'    => $winning_type,
                //         'email'     =>  $lm_user_email,
                //         'phone'     =>  $lm_user_phone,
                //         'fullname'  =>  $lm_user_fullname,
                //         'page_id'   =>  $page_id,
                //         'program_name' => $page_id ? get_the_title($page_id) : null,
                //     ];
                // Lưu trữ dữ liệu
                // $lmr_flag = apply_filters('lm_import_winning_data', true, $winning_data);
                // if (!$lmr_flag) {
                //     wp_send_json_error([
                //         'title'   => __('Thông báo', 'lucky-money'),
                //         'message' => __('Đã xảy ra lỗi trong quá trình quay thưởng', 'lucky-money'),
                //     ]);
                // }
                // // Gửi mail ở đây
                // // do_action('lm_send_winner_email', $winning_data);
                // $the_next_prize = lm_find_winning_prize($lucky_money_prizes);
                // if (!empty($the_next_prize)) {
                //     $winning_data['prize_encode_next'] = function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')
                //         ? lm_encode_prize_id($the_next_prize['id'])
                //         : $the_next_prize['id'];
                // }
                // wp_send_json_success($winning_data);
                // } else {
                //     wp_send_json_error([
                //         'title'   => __('Thông báo', 'lucky-money'),
                //         'message' => __('Không tìm thấy phần thưởng hợp lệ.', 'lucky-money'),
                //     ]);
                // }
            } else {
                wp_send_json_error([
                    'title'   => __('Thông báo', 'lucky-money'),
                    'message' => lm_get_message_error(__('Mã chương trình sự kiện không tồn tại hoặc rỗng', 'lucky-money')),
                ]);
            }
            wp_die();
        }
        public function lm_ajax_program_prize_win_2()
        {
            $page_id = !empty(@$_POST['page_id']) ? esc_attr($_POST['page_id']) : false;
            $prize_id = !empty(@$_POST['prize_id']) ? esc_attr($_POST['prize_id']) : false;
            if (!$page_id) {
                wp_send_json_error([
                    'title'   => __('Thông báo', 'lucky-money'),
                    'message' => lm_get_message_error(__('Mã chương trình sự kiện không tồn tại hoặc rỗng', 'lucky-money')),
                ]);
            }
            $winning_prize = $winning_prize_id = $winning_value = $winning_type = null;
            $lucky_money_prizes = $this->get_lucky_money($page_id);
            $winning_prize = lm_find_winning_prize($lucky_money_prizes, $prize_id);
            if ($winning_prize) {
                $winning_prize_id = $winning_prize['prize_id'];
                global $lucky_moneyPrize;
                $lucky_moneyPrizeItem = $lucky_moneyPrize->get_item($winning_prize_id);
                $winning_type = $lucky_moneyPrizeItem['type'];
                $winning_prize_quantity = $lucky_moneyPrizeItem['prize_quantity'];
                if ($winning_type != 'none') {
                    if ($winning_prize_quantity && $winning_prize_quantity > 0) {
                        $updated_quantity = intval($winning_prize_quantity) - 1;
                        $update_result = $this->update_data_quantity(
                            $winning_prize_id,
                            $page_id,
                            $updated_quantity
                        );
                        if (!$update_result) {
                            wp_send_json_error([
                                'title'   => __('Thông báo', 'lucky-money'),
                                'message' => lm_get_message_error(__('Không tìm thấy phần thưởng hợp lệ.', 'lucky-money')),
                            ]);
                        }
                        $winning_prize['prize_quantity'] = $updated_quantity;
                    }
                    if ($winning_type == 'coupon' && class_exists('Woocommerce')) {
                        $couponOption = $lucky_moneyPrizeItem['type_option']; // default / percent
                        $couponValue = $lucky_moneyPrizeItem['type_value']; // value
                        // Create a new coupon
                        $coupon_code = lm_create_unique_coupon_code();
                        $coupon = new WC_Coupon();
                        $coupon->set_code($coupon_code);
                        if ($couponOption === 'percent') {
                            $coupon->set_discount_type('percent'); // Percentage discount
                            $coupon->set_amount($couponValue);
                            $coupon->set_usage_limit(1);
                        } else {
                            $coupon->set_discount_type('fixed_cart'); // Fixed amount discount
                            $coupon->set_amount($couponValue);
                            $coupon->set_usage_limit(1);
                        }
                        // Optional: Các tuỳ chọn setup cho mã khuyến mãi
                        $coupon->set_individual_use(true);
                        // $coupon->set_email_restrictions(
                        //     [ $lm_user_email ]
                        // );
                        // Lưa mã
                        $coupon->save();
                        $winning_value = $coupon_code;
                    }
                }
                $winning_data = [
                    'prize'         => $winning_prize,
                    'prize_id'      => $winning_prize_id,
                    'prize_thumbnail' => $winning_prize['prize_thumbnail'],
                    'prize_value'   => $winning_value,
                    'prize_type'    => $winning_type,
                    'email'     =>  null,
                    'phone'     =>  null,
                    'fullname'  =>  null,
                    'page_id'   =>  $page_id,
                    'program_name' => $page_id ? get_the_title($page_id) : null,
                ];
                // Lưu trữ dữ liệu
                $lmr_flag = apply_filters('lm_import_winning_data', true, $winning_data);
                if (!$lmr_flag) {
                    wp_send_json_error([
                        'title'   => __('Thông báo', 'lucky-money'),
                        'message' => lm_get_message_error(__('Đã xảy ra lỗi trong quá trình tham gia', 'lucky-money')),
                    ]);
                }
                $winning_data['result_id'] = function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')
                    ? lm_encode_prize_id($lmr_flag)
                    : $lmr_flag;
                if ($winning_type != 'none') {
                    setcookie('lucky_money_' . $page_id, $winning_data['result_id'], time() + (86400 * 15), '/');
                } else {
                    setcookie('lucky_money_' . $page_id, 'none', time() + (86400 * 15), '/');
                }
                $the_next_prize = lm_find_winning_prize($lucky_money_prizes);
                if (!empty($the_next_prize)) {
                    $winning_data['prize_encode_next'] = function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')
                        ? lm_encode_prize_id($the_next_prize['id'])
                        : $the_next_prize['id'];
                }
                wp_send_json_success($winning_data);
            } else {
                wp_send_json_error([
                    'title'   => __('Thông báo', 'lucky-money'),
                    'message' => lm_get_message_error(__('Không tìm thấy phần thưởng hợp lệ.', 'lucky-money')),
                ]);
            }
        }
        public function lm_ajax_program_prize_win_3()
        {
            $page_id = !empty(@$_POST['page_id']) ? esc_attr($_POST['page_id']) : false;
            $prize_id = !empty(@$_POST['prize_id']) ? esc_attr($_POST['prize_id']) : false;
            if (!$page_id) {
                wp_send_json_error([
                    'title'   => __('Thông báo', 'lucky-money'),
                    'message' => lm_get_message_error(__('Mã chương trình sự kiện không tồn tại hoặc rỗng', 'lucky-money')),
                ]);
            }
            $winning_prize = $winning_prize_id = null;
            $lucky_money_prizes = $this->get_lucky_money($page_id);
            $winning_prize = lm_find_winning_prize($lucky_money_prizes, $prize_id);
            if ($winning_prize) {
                $winning_prize_id = $winning_prize['prize_id'];
                $winning_data = [
                    'prize'         => $winning_prize,
                    'prize_id'      => $winning_prize_id,
                    'prize_thumbnail' => $winning_prize['prize_thumbnail'],
                ];
                // Lưu trữ dữ liệu
                wp_send_json_success($winning_data);
            } else {
                wp_send_json_error([
                    'title'   => __('Thông báo', 'lucky-money'),
                    'message' => lm_get_message_error(__('Không tìm thấy phần thưởng hợp lệ.', 'lucky-money')),
                ]);
            }
        }
        public function lm_ajax_program_prize_win_4()
        {

            $page_id = !empty(@$_POST['page_id']) ? esc_attr($_POST['page_id']) : false;
            $user_data = !empty(@$_POST['user_data']) ? stripslashes($_POST['user_data']) : false;
            $user_arr = $user_data ? json_decode($user_data, true) : false;
            $lm_user_email = $lm_user_fullname = $lm_user_phone = null;
            if ($user_arr) {
                $lm_user_email = $user_arr['email'];
                $lm_user_fullname = $user_arr['fullname'];
                $lm_user_phone = $user_arr['phone'];
            }
            if (empty($lm_user_email) || empty($lm_user_fullname) || empty($lm_user_phone)) {
                wp_send_json_error([
                    'title'   => __('Thông báo', 'lucky-money'),
                    'message' => lm_get_message_error(__('Vui lòng nhập đầy đủ thông tin', 'lucky-money')),
                ]);
            }
            if (!is_email($lm_user_email)) {
                wp_send_json_error([
                    'title'   => __('Thông báo', 'lucky-money'),
                    'message' => lm_get_message_error(__('Email không hợp lệ', 'lucky-money')),
                ]);
            }
            if (!preg_match('/^[+]?[0-9]{9,12}$/', $lm_user_phone)) {
                wp_send_json_error([
                    'title'   => __('Thông báo', 'lucky-money'),
                    'message' => lm_get_message_error(__('Số điện thoại không hợp lệ', 'lucky-money')),
                ]);
            } 
            if (isset($page_id) && !empty($page_id)) {
                // $raw_data = !empty($_POST['data']) ? stripslashes($_POST['data']) : false;
                // $winning_data = $raw_data ? json_decode($raw_data, true) : false;
                $lm_prize_id = !empty(@$_POST['lm_prize_id']) ? esc_attr($_POST['lm_prize_id']) : false;
                $winning_prize = $winning_prize_id = $winning_value = $winning_type = null;
                $lucky_money_prizes = $this->get_lucky_money($page_id);
                $winning_prize = lm_find_winning_prize($lucky_money_prizes, $lm_prize_id);
                if ($winning_prize) {
                    $winning_prize_id = $winning_prize['prize_id'];
                    global $lucky_moneyPrize;
                    $lucky_moneyPrizeItem = $lucky_moneyPrize->get_item($winning_prize_id);
                    $winning_type  = $lucky_moneyPrizeItem['type'];
                    if ($winning_prize['prize_quantity'] && $winning_prize['prize_quantity'] > 0) {
                        $updated_quantity = intval($winning_prize['prize_quantity']) - 1;
                        $update_result = $this->update_data_quantity(
                            $winning_prize_id,
                            $page_id,
                            $updated_quantity
                        );
                        if (!$update_result) {
                            wp_send_json_error([
                                'title'   => __('Thông báo', 'lucky-money'),
                                'message' => lm_get_message_error(__('Không tìm thấy phần thưởng hợp lệ.', 'lucky-money')),
                            ]);
                        }
                        $winning_prize['prize_quantity'] = $updated_quantity;
                    }
                    if ($lucky_moneyPrizeItem['type'] == 'coupon' && class_exists('Woocommerce')) {
                        $couponOption = $lucky_moneyPrizeItem['type_option']; // default / percent
                        $couponValue = $lucky_moneyPrizeItem['type_value']; // value
                        // Create a new coupon
                        $coupon_code = lm_create_unique_coupon_code();
                        $coupon = new WC_Coupon();
                        $coupon->set_code($coupon_code);
                        if ($couponOption === 'percent') {
                            $coupon->set_discount_type('percent'); // Percentage discount
                            $coupon->set_amount($couponValue);
                            $coupon->set_usage_limit(1);
                        } else {
                            $coupon->set_discount_type('fixed_cart'); // Fixed amount discount
                            $coupon->set_amount($couponValue);
                            $coupon->set_usage_limit(1);
                        }
                        // Optional: Các tuỳ chọn setup cho mã khuyến mãi
                        $coupon->set_individual_use(true);
                        $coupon->set_email_restrictions(
                            [$lm_user_email]
                        );
                        // Lưa mã
                        $coupon->save();
                        $winning_value = $coupon_code;
                    }
                    $winning_data = [
                        'prize'         => $winning_prize,
                        'prize_id'      => $winning_prize_id,
                        'prize_thumbnail' => $winning_prize['prize_thumbnail'],
                        'prize_value'   => $winning_value,
                        'prize_type'    => $winning_type,
                        'email'     =>  $lm_user_email,
                        'phone'     =>  $lm_user_phone,
                        'fullname'  =>  $lm_user_fullname,
                        'page_id'   =>  $page_id,
                        'program_name' => $page_id ? get_the_title($page_id) : null,
                    ];
                    // // Lưu trữ dữ liệu
                    $lmr_flag = apply_filters('lm_import_winning_data', true, $winning_data);
                    if (!$lmr_flag) {
                        wp_send_json_error([
                            'title'   => __('Thông báo', 'lucky-money'),
                            'message' => lm_get_message_error(__('Đã xảy ra lỗi trong quá trình tham gia', 'lucky-money')),
                        ]);
                    }
                    // Gửi mail ở đây
                    do_action('lm_send_winner_email', $winning_data);
                    $the_next_prize = lm_find_winning_prize($lucky_money_prizes);
                    if (!empty($the_next_prize)) {
                        $winning_data['prize_encode_next'] = function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')
                            ? lm_encode_prize_id($the_next_prize['id'])
                            : $the_next_prize['id'];
                    }

                    wp_send_json_success($winning_data);
                } else {
                    wp_send_json_error([
                        'title'   => __('Thông báo', 'lucky-money'),
                        'message' => lm_get_message_error(__('Không tìm thấy phần thưởng hợp lệ.', 'lucky-money')),
                    ]);
                }
            } else {
                wp_send_json_error([
                    'title'   => __('Thông báo', 'lucky-money'),
                    'message' => lm_get_message_error(__('Mã chương trình sự kiện không tồn tại hoặc rỗng', 'lucky-money')),
                ]);
            }

            wp_die();
        }
        public function lm_ajax_program_prize_creation()
        {
            $form = $_POST;
            $lm_program_prize_num = !empty(@$form['lm_program_prize_num']) ? $form['lm_program_prize_num'] : 0;
            $lm_program_id = !empty(@$form['lm_program_id']) ? $form['lm_program_id'] : null;
            $lm_program_thumbnail   =   !empty(@$form['lm_program_thumbnail']) ? $form['lm_program_thumbnail'] : null;
            $lm_program_prize       =   !empty(@$form['lm_program_prize']) ? $form['lm_program_prize'] : null;
            $lm_program_percent     =   !empty(@$form['lm_program_percent']) ? $form['lm_program_percent'] : null;
            $lm_program_quantity    =   !empty(@$form['lm_program_quantity']) ? $form['lm_program_quantity'] : null;
            $lm_program_background  =   !empty(@$form['lm_program_background']) ? $form['lm_program_background'] : null;
            $lm_program_color       =   !empty(@$form['lm_program_color']) ? $form['lm_program_color'] : null;
            if ($lm_program_prize_num >= 8) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Chương trình đang vượt quá 8 giải thưởng', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            if (empty($lm_program_id)) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            list($lm_program_prize_id, $prize_type) = explode('.', $lm_program_prize) + [null, null]; // Handle cases where explode might return less than 2 values
            $lm_program_prize_data = [
                'page_id'           =>  $lm_program_id,
                'prize_id'          =>  $lm_program_prize_id,
                'prize_thumbnail'   =>  $lm_program_thumbnail,
                'prize_quantity'    =>  $lm_program_quantity,
                'prize_percent'     =>  $lm_program_percent,
                'prize_background'  =>  $lm_program_background,
                'prize_color'       =>  $lm_program_color,
            ];
            $lmProgramPrizeCreationId = $this->import_data($lm_program_prize_data);
            if (!$lmProgramPrizeCreationId) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            wp_send_json_success(
                [
                    'title'         => __('Thông báo!', 'lucky-money'),
                    'message'       => __('Đã tạo thành công', 'lucky-money'),
                ]
            );
            wp_die();
        }
        public function lm_ajax_program_prize_information()
        {
            $form = $_POST;
            $lm_program_id = !empty(@$form['lm_program_id']) ? intval($form['lm_program_id']) : false;
            $lm_program_view = !empty(@$form['lm_program_view']) ? esc_attr($form['lm_program_view']) : false;
            if ($lm_program_id) {
                update_option('lm_option_program', $lm_program_id);
            } else {
                $lm_program_id = get_option('lm_option_program');
            }
            if (!$lm_program_id) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            ob_start(); ?>
            <?php
            include LUCKY_MONEY_DIR . '/admin/partials/program-default.php';
            ?>
            <?php
            wp_send_json_success(
                [
                    'title'         => __('Thông báo!', 'lucky-money'),
                    'message'       => __('Đã tạo thành công', 'lucky-money'),
                    'program_id'    => $lm_program_id,
                    'program_title' => get_the_title($lm_program_id),
                    'html'          => ob_get_clean()
                ]
            );
            wp_die();
        }
        public function lm_ajax_program_prize_information_2()
        {
            $form = $_POST;
            $lm_program_id = !empty(@$form['lm_program_id']) ? intval($form['lm_program_id']) : false;
            $lm_program_type = !empty(@$form['lm_program_type']) ? esc_attr($form['lm_program_type']) : false;
            if (!$lm_program_id || !$lm_program_type) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            update_option('lm_option_program_type_' . $lm_program_id, $lm_program_type);
            wp_send_json_success(
                [
                    'title'         => __('Thông báo!', 'lucky-money'),
                    'message'       => __('Đã tạo thành công', 'lucky-money')
                ]
            );
            wp_die();
        }
        public function lm_ajax_program_prize_updation()
        {
            $form = $_POST;
            $lm_data = !empty(@$form['lm_data']) ? $form['lm_data'] : false;
            $lm_program_id = !empty(@$form['lm_program_id']) ? $form['lm_program_id'] : false;
            $program_prize_key = !empty(@$form['program_prize_key']) ? $form['program_prize_key'] : false;
            if (empty($lm_data) || empty($program_prize_key) || empty($lm_program_id)) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            $lm_by_program_prize_key = !empty(@$lm_data[$program_prize_key]) ? $lm_data[$program_prize_key] : false;
            if (empty($lm_by_program_prize_key)) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra [2]', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            if ($lm_by_program_prize_key['prize_percent'] == null) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Vui lòng nhập phần trăm', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            $lm_by_program_prize_key['page_id'] = $lm_program_id;
            $lucky_moneyProgramPrizeUpdation = $this->update_data($program_prize_key, $lm_by_program_prize_key);
            if (!$lucky_moneyProgramPrizeUpdation) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra [3]', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            wp_send_json_success(
                [
                    'title'         => __('Thông báo!', 'lucky-money'),
                    'message'       => __('Đã cập nhật thành công', 'lucky-money'),
                ]
            );
            wp_die();
        }
        public function lm_ajax_program_prize_deletion()
        {
            $form = $_POST;
            $lm_data = !empty(@$form['lm_data']) ? $form['lm_data'] : false;
            $lm_program_id = !empty(@$form['lm_program_id']) ? $form['lm_program_id'] : false;
            $program_prize_key = !empty(@$form['program_prize_key']) ? $form['program_prize_key'] : false;
            if (empty($lm_data) || empty($program_prize_key)) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            $lm_by_program_prize_key = isset($lm_data[$program_prize_key]) ? $lm_data[$program_prize_key] : false;
            if (empty($lm_by_program_prize_key)) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra [2]', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            $lucky_moneyProgramPrizeDeletion = $this->delete_data($program_prize_key);
            if (!$lucky_moneyProgramPrizeDeletion) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra [3]', 'lucky-money'),
                    ]
                );
                wp_die();
            }
            wp_send_json_success(
                [
                    'title'         => __('Thông báo!', 'lucky-money'),
                    'message'       => __('Đã xoá thành công', 'lucky-money'),
                ]
            );
            wp_die();
        }
    }
    global $lucky_moneyProgram;
    $lucky_moneyProgram = new Lucky_MoneyProgram();
endif;
