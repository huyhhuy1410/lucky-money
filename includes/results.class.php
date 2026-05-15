<?php
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('Lucky_MoneyResult')) :

    class Lucky_MoneyResult
    {

        private $table = 'lucky_money_result';

        function __construct()
        {
            $this->setup();
            $this->init_hooks();
        }

        private function init_hooks()
        {

            if (class_exists('Woocommerce')) {

                add_action('woocommerce_order_status_changed', [$this, 'lm_update_received_status_on_order_status_change'], 10, 4);
            }

            add_filter('lm_import_winning_data', [$this, 'lm_import_winning_data_for_lucky_money'], 10, 2);

            add_filter('lm_user_validation_for_lucky_money', [$this, 'lm_lucky_money_user_validation_for_lucky_money'], 10, 3);

            add_filter('lm_program_lucky_money_validation_for_user', [$this, 'lm_lucky_money_program_lucky_money_validation_for_user'], 10, 2);

            add_action('wp_ajax_lm_ajax_result_pagination_ajax', [$this, 'lm_ajax_result_pagination_ajax']); // login

            add_action('wp_ajax_lm_ajax_result_report_ajax', [$this, 'lm_ajax_result_report_ajax']); // login

            add_action('wp_ajax_lm_ajax_program_result_received', [$this, 'lm_ajax_program_result_received']); // login

            add_action('wp_ajax_lm_ajax_program_prize_result_updation', [$this, 'lm_ajax_program_prize_result_updation']); // login
            add_action('wp_ajax_nopriv_lm_ajax_program_prize_result_updation', [$this, 'lm_ajax_program_prize_result_updation']); // no-login

            // add_action('wp_ajax_lm_ajax_program_prize_mail_send', [$this, 'lm_ajax_program_prize_mail_send']); // login
            // add_action('wp_ajax_nopriv_lm_ajax_program_prize_mail_send', [$this, 'lm_ajax_program_prize_mail_send']); // no-login
        }

        public function lm_import_winning_data_for_lucky_money($flag, $data)
        {
            if (empty($data)) {
                return false;
            }

            $flag = $this->import_data($data);
            return $flag !== false ? $flag : false;
        }

        public function lm_lucky_money_user_validation_for_lucky_money($validation, $lm_page_id, $lm_user)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . 'lucky_money_result';

            $lm_limit = LUCKY_MONEY_LIMIT;
            $lm_condition = LUCKY_MONEY_CONDITION;
            $lm_time_limit = LUCKY_MONEY_TIME_LIMIT;
            $lm_time_unit = LUCKY_MONEY_TIME_UNIT;

            $conditions = [];
            if ($lm_condition === 'email' || $lm_condition === 'both') {
                $conditions[] = $wpdb->prepare("email = %s", $lm_user['email']);
            }
            if ($lm_condition === 'phone' || $lm_condition === 'both') {
                $conditions[] = $wpdb->prepare("phone = %s", $lm_user['phone']);
            }

            if (empty($conditions)) {
                return $validation;
            }

            $logical_operator = $lm_condition === 'both' ? ' OR ' : ' AND ';
            $where_clause = implode($logical_operator, $conditions);
            $count_query = "SELECT COUNT(*) FROM $table_name WHERE ($where_clause) AND page_id = %d";
            $spin_count = $wpdb->get_var($wpdb->prepare($count_query, $lm_page_id));

            if ($lm_limit && $spin_count >= $lm_limit) {
                return false;
            }

            $recent_spin_query = "SELECT created_at FROM $table_name WHERE ($where_clause) AND page_id = %d ORDER BY created_at DESC LIMIT 1";
            $recent_spin_time = $wpdb->get_var(
                $wpdb->prepare($recent_spin_query, $lm_page_id)
            );

            if ($recent_spin_time) {
                $time_limit_seconds = 0;
                switch ($lm_time_unit) {
                    case 'days':
                        $time_limit_seconds = $lm_time_limit * DAY_IN_SECONDS;
                        break;
                    case 'hours':
                        $time_limit_seconds = $lm_time_limit * HOUR_IN_SECONDS;
                        break;
                    case 'minutes':
                        $time_limit_seconds = $lm_time_limit * MINUTE_IN_SECONDS;
                        break;
                }
                $next_spin_time = strtotime($recent_spin_time) + $time_limit_seconds;
                if (current_time('timestamp') < $next_spin_time) {
                    return false;
                }
            }

            return true;
        }

        public function lm_lucky_money_program_lucky_money_validation_for_user($validation, $lm_page_id)
        {
            global $lucky_moneyProgram;
            $lucky_moneyProgramData = $lucky_moneyProgram->get_lucky_money($lm_page_id);
            if (!$lucky_moneyProgramData) {
                return false;
            }
            return true;
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
                prize_value TEXT NULL,
                email TEXT NULL,
                phone TEXT NULL,
                fullname TEXT NULL,
                received TINYINT(1) NOT NULL,
                received_note TEXT NULL,
                received_at datetime NULL,
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
            $result = $wpdb->insert($table_name, array(
                'page_id'       => $data['page_id'],
                'prize_id'      => $data['prize_id'],
                'prize_thumbnail' => $data['prize_thumbnail'] ? esc_attr($data['prize_thumbnail']) : null,
                'prize_value'   => $data['prize_value'] ? esc_attr($data['prize_value']) : null,
                'email'         => $data['email'],
                'phone'         => $data['phone'],
                'fullname'      => $data['fullname'],
                'status'        => 1,
                'received'      => 0,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ));

            if ($result !== false) {
                return $wpdb->insert_id;
            }

            return false;
        }

        public function get_data_count($page_id = null)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            $is_woocommerce = class_exists('WooCommerce');

            $cte = "WITH filtered_results AS (
            SELECT result.id
            FROM $table_name result
            INNER JOIN $prize_table prize ON result.prize_id = prize.id
            WHERE result.status = 1";

            if (!$is_woocommerce) {
                $cte .= " AND prize.type != 'coupon'";
            }

            if ($page_id) {
                $cte .= $wpdb->prepare(" AND result.page_id = %d", $page_id);
            }

            $cte .= ")";

            $query = "$cte
            SELECT COUNT(*) 
            FROM filtered_results";

            $count = $wpdb->get_var($query);

            return $count !== null ? intval($count) : 0;
        }

        public function get_data($page_id = null, $page = 1, $per_page = 5, $filters = [], $keyword = false)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            $is_woocommerce = class_exists('WooCommerce');

            // Build the base SQL query
            $base_query = "SELECT result.*, 
                result.page_id AS page_name, 
                prize.type AS prize_type, 
                prize.name AS prize_name, 
                prize.description AS prize_description
            FROM $table_name result
            INNER JOIN $prize_table prize ON result.prize_id = prize.id
            WHERE result.status = 1";

            if ($page_id) {
                $base_query .= $wpdb->prepare(" AND result.page_id = %d", $page_id);
            }

            if (!empty($keyword)) {
                $like_keyword = '%' . $wpdb->esc_like($keyword) . '%';
                $base_query .= $wpdb->prepare(
                    " AND (result.fullname LIKE %s OR result.email LIKE %s OR result.phone LIKE %s)",
                    $like_keyword,
                    $like_keyword,
                    $like_keyword
                );
            }

            foreach ($filters as $column => $value) {
                if ($column == 'result.received' && !$value) {
                    $base_query .= $wpdb->prepare(" AND prize.type != %s AND $column = %s", 'none', $value);
                } else {
                    $base_query .= $wpdb->prepare(" AND $column = %s", $value);
                }
            }

            // Exclude coupons if WooCommerce is not active
            if (!$is_woocommerce) {
                $base_query .= $wpdb->prepare(" AND prize.type != %s", 'coupon');
            }

            $total_query = "SELECT COUNT(*) FROM ($base_query) AS subquery";
            $total = $wpdb->get_var($total_query);

            $max_pages = ($per_page == -1) ? 1 : ceil($total / $per_page);

            $offset = ($page - 1) * $per_page;

            if ($per_page == -1) {
                $per_page = $total;
                $offset = 0;
            }

            $final_query = $base_query . "
            ORDER BY result.created_at DESC
            LIMIT %d OFFSET %d";

            $results = $wpdb->get_results(
                $wpdb->prepare(
                    $final_query,
                    $per_page,
                    $offset
                ),
                ARRAY_A
            );

            return [
                'results'      => $results,
                'page'         => $page,
                'max_pages'    => $max_pages,
                'found_posts'  => $total,
            ];
        }

        public function get_data_prize($search, $page_id)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            $is_woocommerce = class_exists('WooCommerce');

            if ($is_woocommerce) {
                // If WooCommerce is active, join with the prize table and get distinct prize data
                if ($search) {
                    $search = '%' . $wpdb->esc_like($search) . '%';
                    $query = $wpdb->prepare(
                        "SELECT DISTINCT result.prize_id, prize.*
                        FROM $table_name result
                        INNER JOIN $prize_table prize ON result.prize_id = prize.id
                        WHERE prize.status = 1
                        AND (prize.name LIKE %s OR prize.description LIKE %s OR prize.id LIKE %s)
                        AND result.page_id = %d",
                        $search,
                        $search,
                        $search,
                        $page_id
                    );
                } else {
                    $query = $wpdb->prepare(
                        "SELECT DISTINCT result.prize_id, prize.*
                        FROM $table_name result
                        INNER JOIN $prize_table prize ON result.prize_id = prize.id
                        WHERE prize.status = 1
                        AND result.page_id = %d",
                        $page_id
                    );
                }
            } else {
                // If WooCommerce is not active, still join with the prize table and get distinct prize data
                if ($search) {
                    $search = '%' . $wpdb->esc_like($search) . '%';
                    $query = $wpdb->prepare(
                        "SELECT DISTINCT result.prize_id, prize.*
                        FROM $table_name result
                        INNER JOIN $prize_table prize ON result.prize_id = prize.id
                        WHERE prize.status = 1
                        AND result.page_id = %d
                        AND (prize.name LIKE %s OR prize.description LIKE %s OR prize.id LIKE %s)",
                        $page_id,
                        $search,
                        $search,
                        $search
                    );
                } else {
                    $query = $wpdb->prepare(
                        "SELECT DISTINCT result.prize_id, prize.*
                        FROM $table_name result
                        INNER JOIN $prize_table prize ON result.prize_id = prize.id
                        WHERE prize.status = 1
                        AND result.page_id = %d",
                        $page_id
                    );
                }
            }

            // Fetch and return the results
            $results = $wpdb->get_results($query, ARRAY_A);
            return $results;
        }

        public function get_chart_data($page_id = null)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            $program_table = $wpdb->base_prefix . 'lucky_money_program';
            $is_woocommerce = class_exists('WooCommerce');

            if (empty($page_id)) {

                if (!$is_woocommerce) {
                    $query = $wpdb->prepare(
                        "SELECT 
                            result.result_prize,
                            result.result_name,
                            result.result_quantity,
                            prog.prize_background AS result_background,
                            prog.prize_color AS result_color
                        FROM (
                            SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE pz.type != %s AND rs.status = 1
                            GROUP BY rs.prize_id
                        ) AS result
                        INNER JOIN $program_table prog
                        ON prog.page_id = result.result_page 
                        AND prog.prize_id = result.result_prize",
                        'coupon'
                    );
                } else {
                    $query = $wpdb->prepare(
                        "SELECT 
                            result.result_prize,
                            result.result_name,
                            result.result_quantity,
                            prog.prize_background AS result_background,
                            prog.prize_color AS result_color
                        FROM (
                            SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE rs.status = 1
                            GROUP BY rs.prize_id
                        ) AS result
                        INNER JOIN $program_table prog
                        ON prog.page_id = result.result_page 
                        AND prog.prize_id = result.result_prize",
                    );
                }
            } else {
                if (!$is_woocommerce) {
                    $query = $wpdb->prepare(
                        "SELECT 
                            result.result_prize,
                            result.result_name,
                            result.result_quantity,
                            prog.prize_background AS result_background,
                            prog.prize_color AS result_color
                        FROM (
                            SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE rs.page_id = %d
                                AND pz.type != %s 
                                AND rs.status = 1
                            GROUP BY rs.prize_id
                        ) AS result
                        INNER JOIN $program_table prog
                        ON prog.page_id = result.result_page 
                        AND prog.prize_id = result.result_prize",
                        $page_id,
                        'coupon'
                    );
                } else {
                    $query = $wpdb->prepare(
                        "SELECT 
                            result.result_prize,
                            result.result_name,
                            result.result_quantity,
                            prog.prize_background AS result_background,
                            prog.prize_color AS result_color
                        FROM (
                            SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE rs.page_id = %d 
                              AND rs.status = 1
                            GROUP BY rs.prize_id
                        ) AS result
                        INNER JOIN $program_table prog
                        ON prog.page_id = result.result_page 
                        AND prog.prize_id = result.result_prize",
                        $page_id
                    );
                }
            }

            $results = $wpdb->get_results($query, ARRAY_A);

            $results_data = [];
            if ($results !== false) {
                foreach ($results as $key => $result) {
                    $results_data[] = [
                        'id' => $result['result_prize'],
                        'name' => $result['result_name'],
                        'quantity' => $result['result_quantity'] ? intval($result['result_quantity']) : 0,
                        'colorBg' => $result['result_background'],
                        'colorText' => $result['result_color']
                    ];
                }

                return $results_data;
            }

            return false;
        }

        public function get_overview_data($page_id = null, $type = true)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            $program_table = $wpdb->base_prefix . 'lucky_money_program';
            $is_woocommerce = class_exists('WooCommerce');

            if (empty($page_id)) {

                if (!$type) {
                    $query = $wpdb->prepare(
                        "SELECT 
                            rs.prize_id AS result_prize, 
                            rs.page_id AS result_page, 
                            pz.name AS result_name, 
                            COUNT(rs.prize_id) AS result_quantity
                        FROM $table_name rs
                        INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                        WHERE rs.status = 1
                            AND pz.type   = %s
                        GROUP BY rs.prize_id",
                        'none'
                    );
                } else {
                    if (!$is_woocommerce) {
                        $query = $wpdb->prepare(
                            "SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE rs.status = 1
                                AND pz.type != %s
                                AND pz.type != %s
                            GROUP BY rs.prize_id",
                            'none',
                            'coupon'
                        );
                    } else {
                        $query = $wpdb->prepare(
                            "SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE rs.status = 1
                                AND pz.type != %s
                            GROUP BY rs.prize_id",
                            'none'
                        );
                    }
                }
            } else {

                if (!$type) {
                    $query = $wpdb->prepare(
                        "SELECT 
                            rs.prize_id AS result_prize, 
                            rs.page_id AS result_page, 
                            pz.name AS result_name, 
                            COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE rs.page_id = %d 
                            AND rs.status = 1
                            AND pz.type   = %s
                            GROUP BY rs.prize_id",
                        $page_id,
                        'none'
                    );
                } else {
                    if (!$is_woocommerce) {
                        $query = $wpdb->prepare(
                            "SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                                FROM $table_name rs
                                INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                                WHERE rs.page_id = %d 
                                AND rs.status = 1
                                AND pz.type   != %s
                                AND pz.type   != %s
                                GROUP BY rs.prize_id",
                            $page_id,
                            'none',
                            'coupon'
                        );
                    } else {
                        $query = $wpdb->prepare(
                            "SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                                FROM $table_name rs
                                INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                                WHERE rs.page_id = %d 
                                AND rs.status = 1
                                AND pz.type   != %s
                                GROUP BY rs.prize_id",
                            $page_id,
                            'none'
                        );
                    }
                }
            }

            $results = $wpdb->get_results($query, ARRAY_A);
            if ($results !== false) {
                return array_reduce($results, function ($carry, $item) {
                    return $carry + (int)$item['result_quantity'];
                }, 0);
            }
            return false;
        }

        public function get_result_data($page_id = null)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            $program_table = $wpdb->base_prefix . 'lucky_money_program';
            $is_woocommerce = class_exists('WooCommerce');

            if (empty($page_id)) {
                if (!$is_woocommerce) {
                    $query = $wpdb->prepare(
                        "SELECT 
                            result.result_prize,
                            result.result_name,
                            result.result_quantity,
                            prog.prize_background AS result_background,
                            prog.prize_color AS result_color
                        FROM (
                            SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE pz.type != %s AND rs.status = 1
                            GROUP BY rs.prize_id
                        ) AS result
                        INNER JOIN $program_table prog
                        ON prog.page_id = result.result_page 
                        AND prog.prize_id = result.result_prize",
                        'coupon'
                    );
                } else {
                    $query = $wpdb->prepare(
                        "SELECT 
                            result.result_prize,
                            result.result_name,
                            result.result_quantity,
                            prog.prize_background AS result_background,
                            prog.prize_color AS result_color
                        FROM (
                            SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE rs.status = 1
                            GROUP BY rs.prize_id
                        ) AS result
                        INNER JOIN $program_table prog
                        ON prog.page_id = result.result_page 
                        AND prog.prize_id = result.result_prize",
                    );
                }
            } else {
                if (!$is_woocommerce) {
                    $query = $wpdb->prepare(
                        "SELECT 
                            result.result_prize,
                            result.result_name,
                            result.result_quantity,
                            prog.prize_background AS result_background,
                            prog.prize_color AS result_color
                        FROM (
                            SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE rs.page_id = %d 
                                AND pz.type  != %s
                                AND rs.status = 1
                            GROUP BY rs.prize_id
                        ) AS result
                        INNER JOIN $program_table prog
                        ON prog.page_id = result.result_page 
                        AND prog.prize_id = result.result_prize",
                        $page_id,
                        'coupon'
                    );
                } else {
                    $query = $wpdb->prepare(
                        "SELECT 
                            result.result_prize,
                            result.result_name,
                            result.result_quantity,
                            prog.prize_background AS result_background,
                            prog.prize_color AS result_color
                        FROM (
                            SELECT 
                                rs.prize_id AS result_prize, 
                                rs.page_id AS result_page, 
                                pz.name AS result_name, 
                                COUNT(rs.prize_id) AS result_quantity
                            FROM $table_name rs
                            INNER JOIN $prize_table pz ON rs.prize_id = pz.id
                            WHERE rs.page_id = %d 
                              AND rs.status = 1
                            GROUP BY rs.prize_id
                        ) AS result
                        INNER JOIN $program_table prog
                        ON prog.page_id = result.result_page 
                        AND prog.prize_id = result.result_prize",
                        $page_id
                    );
                }
            }


            $results = $wpdb->get_results($query, ARRAY_A);
            return $results !== false ? $results : false;
        }

        public function get_email_information_data($page_id = null)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            $is_woocommerce = class_exists('WooCommerce');

            if (!$is_woocommerce) {
                if (empty($page_id)) {
                    $query = $wpdb->prepare(
                        "SELECT 
                            COUNT(DISTINCT rs.email) AS email_count
                            FROM $table_name rs
                            INNER JOIN $prize_table pz
                            WHERE pz.id = rs.prize_id 
                            AND pz.type != %s
                            AND rs.status = 1",
                        'coupon'
                    );
                } else {
                    $query = $wpdb->prepare(
                        "SELECT 
                            COUNT(DISTINCT rs.email) AS email_count
                            FROM $table_name rs
                            INNER JOIN $prize_table pz
                            WHERE pz.id = rs.prize_id 
                            AND rs.page_id = %d
                            AND pz.type != %s
                            AND rs.status = 1",
                        $page_id,
                        'coupon'
                    );
                }
            } else {
                if (empty($page_id)) {
                    $query = "SELECT 
                        COUNT(DISTINCT rs.email) AS email_count
                        FROM $table_name rs
                        WHERE rs.status = 1";
                } else {
                    $query = $wpdb->prepare(
                        "SELECT 
                            COUNT(DISTINCT rs.email) AS email_count
                            FROM $table_name rs
                            WHERE 
                            rs.page_id = %d 
                            AND rs.status = 1",
                        $page_id
                    );
                }
            }


            $email_count = $wpdb->get_var($query);
            return $email_count !== false ? (int)$email_count : false;
        }

        public function get_phone_information_data($page_id = null)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            $is_woocommerce = class_exists('WooCommerce');

            if (!$is_woocommerce) {
                if (empty($page_id)) {
                    $query = $wpdb->prepare(
                        "SELECT 
                            COUNT(DISTINCT rs.phone) AS phone_count
                            FROM $table_name rs
                            INNER JOIN $prize_table pz
                            WHERE pz.id = rs.prize_id 
                            AND pz.type != %s
                            AND rs.status = 1",
                        'coupon'
                    );
                } else {
                    $query = $wpdb->prepare(
                        "SELECT 
                            COUNT(DISTINCT rs.phone) AS phone_count
                            FROM $table_name rs
                            INNER JOIN $prize_table pz
                            WHERE pz.id = rs.prize_id 
                            AND rs.page_id = %d
                            AND pz.type != %s
                            AND rs.status = 1",
                        $page_id,
                        'coupon'
                    );
                }
            } else {
                if (empty($page_id)) {
                    $query = "SELECT 
                        COUNT(DISTINCT rs.phone) AS phone_count
                        FROM $table_name rs
                        WHERE rs.status = 1";
                } else {
                    $query = $wpdb->prepare(
                        "SELECT 
                            COUNT(DISTINCT rs.phone) AS phone_count
                            FROM $table_name rs
                            WHERE 
                            rs.page_id = %d 
                            AND rs.status = 1",
                        $page_id
                    );
                }
            }


            $phone_count = $wpdb->get_var($query);
            return $phone_count !== false ? (int)$phone_count : false;
        }

        public function get_result_by_id($id)
        {
            global $wpdb;
            $id = intval($id);

            $table_name = $wpdb->base_prefix . $this->table;
            $prize_table = $wpdb->base_prefix . 'lucky_money_prize';
            $program_table = $wpdb->base_prefix . 'lucky_money_program';

            $query = $wpdb->prepare(
                "SELECT result.*, 
                prize.type AS prize_type, 
                prize.name AS prize_name, 
                prize.description AS prize_description, 
                result.prize_thumbnail AS prize_thumbnail
                FROM $table_name result
                INNER JOIN $prize_table prize ON result.prize_id = prize.id
                WHERE result.id = %d AND result.status = 1",
                $id
            );

            $result = $wpdb->get_row($query, ARRAY_A);
            if ($result) {
                return $result;
            } else {
                return false;
            }
        }

        public function get_id_by_prize_value($prize_value)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $query = $wpdb->prepare("SELECT id 
                FROM $table_name
                WHERE prize_value = %s
                LIMIT 1
            ", $prize_value);
            return $wpdb->get_var($query);
        }

        public function update_received_status($id, $received, $received_note = null)
        {
            global $wpdb;

            $id = intval($id);

            $received_at = current_time('mysql');

            $data = [
                'received'     => $received ? 1 : 0,
                'received_at'  => $received_at,
            ];

            if (!empty($received_note)) {
                $data['received_note'] = $received_note;
            }

            $updated = $wpdb->update(
                $wpdb->base_prefix . $this->table,
                $data,
                ['id' => $id],
                array_merge(
                    ['%d', '%s'],
                    !empty($received_note) ? ['%s'] : []
                ),
                ['%d']
            );

            if ($updated !== false) {
                return true;
            } else {
                return false;
            }
        }

        public function update_data_dynamic($data, $where)
        {
            global $wpdb;

            $table_name = $wpdb->base_prefix . $this->table;

            if (empty($data) || empty($where)) {
                return false;
            }

            $update_pairs = [];
            $update_values = [];

            foreach ($data as $column => $value) {
                $update_pairs[] = "`$column` = %s";
                $update_values[] = $value;
            }

            $where_pairs = [];
            $where_values = [];

            foreach ($where as $column => $value) {
                $where_pairs[] = "`$column` = %s";
                $where_values[] = $value;
            }

            $query = sprintf(
                "UPDATE `%s` SET %s WHERE %s",
                esc_sql($table_name),
                implode(', ', $update_pairs),
                implode(' AND ', $where_pairs)
            );

            $values = array_merge($update_values, $where_values);

            $updated = $wpdb->query($wpdb->prepare($query, $values));

            return $updated !== false ? $updated : false;
        }

        public function lm_ajax_result_pagination_ajax()
        {

            $form = $_POST;
            $lm_program_paged = !empty(@$form['paged']) ? $form['paged'] : 1;
            $lm_program_id    = !empty(@$form['lm_program_id']) ? $form['lm_program_id'] : false;

            if (!$lm_program_id) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }

            $lm_result_filters = [];
            $lm_result_prize    = !empty(@$form['lm_result_prize']) ? $form['lm_result_prize'] : false;
            $lm_result_keyword  = !empty(@$form['lm_result_keyword']) ? $form['lm_result_keyword'] : false;
            $lm_result_received = !empty(@$form['lm_result_received']) ? $form['lm_result_received'] : false;

            if (!empty($lm_result_prize)) {
                $lm_result_filters['result.prize_id'] = $lm_result_prize;
            }

            if (!empty($lm_result_received)) {
                if ($lm_result_received == 'received') {
                    $lm_result_filters['result.received'] = 1;
                } else if ($lm_result_received == 'none-received') {
                    $lm_result_filters['result.received'] = 0;
                }
            }

            ob_start();
            $lucky_moneyResultData = $this->get_data($lm_program_id, $lm_program_paged, 5, $lm_result_filters, $lm_result_keyword);
?>
            <?php if (is_array(@$lucky_moneyResultData['results']) && !empty(@$lucky_moneyResultData['results'])) { ?>
                <div class="mnw-table">
                    <table>
                        <thead>
                            <tr>
                                <th><?php echo __('ID', 'lucky-money'); ?></th>
                                <th><?php echo __('Họ tên', 'lucky-money'); ?></th>
                                <th><?php echo __('Phần thưởng', 'lucky-money'); ?></th>
                                <th><?php echo __('Liên hệ', 'lucky-money'); ?></th>
                                <th><?php echo __('Hành động', 'lucky-money'); ?></th>
                                <th><?php echo __('Tham gia', 'lucky-money'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lucky_moneyResultData['results'] as $result_key => $result) { ?>

                                <tr class="lm-table-row lm-row program_result_row is-loading-row">
                                    <?php include LUCKY_MONEY_DIR . '/admin/partials/result-row.php'; ?>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="mnw-pagi">
                    <p class="mnw-txt">
                        <?php echo sprintf(
                            __('Kết quả từ %s đến trang %s', 'lucky-money'),
                            $lucky_moneyResultData['page'],
                            $lucky_moneyResultData['max_pages']
                        ); ?>
                    </p>
                    <div class="mnw-pagi-right">
                        <?php lm_pagination_links($lucky_moneyResultData['page'], $lucky_moneyResultData['max_pages']); ?>
                        <div class="mnw-ip">
                            <select name="lm_result_page" class="lm_result_page_js">
                                <?php for ($i = 1; $i <= $lucky_moneyResultData['max_pages']; $i++) : ?>
                                    <option value="<?php echo $i; ?>" <?php selected($i, $lucky_moneyResultData['page']); ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <span class="txt"><?php echo __(' / Trang', 'lucky-money'); ?></span>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="lm-empty-message">
                    <?php echo __('Không có dữ liệu phù hợp', 'lucky-money'); ?>
                </div>
            <?php } ?>
<?php
            wp_send_json_success(
                [
                    'title'         => __('Thông báo!', 'lucky-money'),
                    'message'       => __('Đã gọi thành công', 'lucky-money'),
                    'html'          => ob_get_clean(),
                    'max_pages'     => is_array(@$lucky_moneyResultData['results']) && !empty(@$lucky_moneyResultData['results']) ? $lucky_moneyResultData['max_pages'] : 0
                ]
            );
            wp_die();
        }

        public function lm_ajax_result_report_ajax()
        {
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : 0;

            if (empty($page) || empty($program_id)) {
                wp_send_json_error(false);
            }

            $lm_result_filters = [];
            $lm_result_prize    = !empty(@$_POST['lm_result_prize']) ? $_POST['lm_result_prize'] : false;
            $lm_result_keyword  = !empty(@$_POST['lm_result_keyword']) ? $_POST['lm_result_keyword'] : false;
            $lm_result_received = !empty(@$_POST['lm_result_received']) ? $_POST['lm_result_received'] : false;

            if (!empty($lm_result_prize)) {
                $lm_result_filters['result.prize_id'] = $lm_result_prize;
            }

            if (!empty($lm_result_received)) {
                if ($lm_result_received == 'received') {
                    $lm_result_filters['result.received'] = 1;
                } else if ($lm_result_received == 'none-received') {
                    $lm_result_filters['result.received'] = 0;
                }
            }

            $data = $this->get_data($program_id, $page, 5, $lm_result_filters, $lm_result_keyword);
            if ($data && !empty(@$data['results'])) {
                $results = $data['results'];
                foreach ($results as $index => $result) {

                    $fieldsToDecode = ['email', 'phone', 'fullname', 'page_name', 'prize_name', 'prize_description'];
                    foreach ($fieldsToDecode as $field) {
                        if (!empty($result[$field])) {
                            $results[$index][$field] = html_entity_decode(
                                $result[$field],
                                ENT_QUOTES | ENT_HTML5,
                                'UTF-8'
                            );
                        }
                    }

                    if (!empty($result['page_name'])) {
                        $results[$index]['page_name'] = get_the_title($result['page_name']);
                    }

                    if (!empty($result['prize_thumbnail'])) {
                        $results[$index]['prize_thumbnail'] = wp_get_attachment_image_url($result['prize_thumbnail'], 'full');
                    }
                }
                wp_send_json_success($results);
            } else {
                wp_send_json_error(false);
            }
        }

        public function lm_ajax_program_result_received()
        {
            $result_id = isset($_POST['result_id']) ? intval($_POST['result_id']) : false;
            $result_flag = isset($_POST['result_flag']) ? boolval($_POST['result_flag']) : false;
            if (!$result_id) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }

            $updationResult = $this->update_received_status($result_id, $result_flag ? true : false);
            if (!$updationResult) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }

            $result = $this->get_result_by_id($result_id);
            if (!$result) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }

            ob_start();

            include LUCKY_MONEY_DIR . '/admin/partials/result-row.php';

            wp_send_json_success(
                [
                    'title'         => __('Thông báo!', 'lucky-money'),
                    'message'       => __('Đã gọi thành công', 'lucky-money'),
                    'html'          => ob_get_clean()
                ]
            );
            wp_die();
        }

        public function lm_update_received_status_on_order_status_change($order_id, $old_status, $new_status, $order)
        {
            $coupon_codes = $order->get_coupon_codes();
            if (is_array($coupon_codes) && !empty($coupon_codes)) {
                foreach ($coupon_codes as $coupon_code) {
                    $result_id = $this->get_id_by_prize_value($coupon_code);
                    if ($result_id) {
                        $received_status = !in_array($new_status, ['failed', 'cancelled']) ? 1 : 0;
                        $received_note = !in_array($new_status, ['failed', 'cancelled']) ? sprintf(
                            __('Được sử dụng ở đơn hàng #%s<br>%s', 'lucky-money'),
                            $order_id,
                            current_time('mysql')
                        ) : '';
                        $this->update_received_status($result_id, $received_status, $received_note);
                    }
                }
            }
        }

        public function lm_ajax_program_prize_result_updation()
        {
            $page_id = !empty(@$_POST['page_id']) ? esc_attr($_POST['page_id']) : false;
            if (isset($page_id) && !empty($page_id)) {

                $lm_user_fullname = !empty(@$_POST['lm_user_fullname']) ? esc_attr($_POST['lm_user_fullname']) : false;
                $lm_user_phone = !empty(@$_POST['lm_user_phone']) ? esc_attr($_POST['lm_user_phone']) : false;
                $lm_user_email = !empty(@$_POST['lm_user_email']) ? esc_attr($_POST['lm_user_email']) : false;
                $lm_user_data = [
                    'email'     =>  $lm_user_email,
                    'phone'     =>  $lm_user_phone,
                    'fullname'  =>  $lm_user_fullname,
                ];
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

                $lm_result_id = !empty(@$_POST['lm_result_id'])
                    ? esc_attr($_POST['lm_result_id'])
                    : false;

                $lm_result_id = !empty($lm_result_id) && function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')
                    ? lm_decode_prize_id($lm_result_id)
                    : $lm_result_id;

                $lm_result = $lm_result_id
                    ? $this->get_result_by_id($lm_result_id)
                    : false;

                if ($lm_result) {

                    global $lucky_moneyProgram;
                    $lucky_money_prizes = $lucky_moneyProgram->get_lucky_money($page_id);
                    $lm_prize_id = function_exists('lm_encode_prize_id') && function_exists('lm_decode_prize_id')
                        ? lm_encode_prize_id($lm_result['prize_id'])
                        : $lm_result['prize_id'];

                    $winning_prize = lm_find_winning_prize($lucky_money_prizes, $lm_prize_id);
                    if (!$winning_prize) {
                        wp_send_json_error([
                            'title'   => __('Thông báo', 'lucky-money'),
                            'message' => lm_get_message_error(__('Không tìm thấy phần thưởng hợp lệ', 'lucky-money')),
                        ]);
                    }

                    if ($winning_prize['prize_type'] === 'coupon' && class_exists('WooCommerce')) {
                        $winning_value = $lm_result['prize_value'];

                        $winning_coupon = new WC_Coupon($winning_value);
                        if ($winning_coupon->get_id()) {
                            $winning_coupon->set_email_restrictions([$lm_user_email]);
                            $winning_coupon->save();
                        } else {
                            wp_send_json_error([
                                'title'   => __('Thông báo', 'lucky-money'),
                                'message' => lm_get_message_error(__('Không tìm thấy phần thưởng hợp lệ', 'lucky-money')),
                            ]);
                        }
                    }

                    // Gửi mail ở đây
                    do_action('lm_send_winner_email', [
                        'prize'         => $winning_prize,
                        'prize_id'      => $winning_prize['prize_id'],
                        'prize_thumbnail' => $winning_prize['prize_thumbnail'],
                        'prize_value'   => $lm_result['prize_value'],
                        'prize_type'    => $winning_prize['prize_type'],
                        'email'     =>  $lm_user_email,
                        'phone'     =>  $lm_user_phone,
                        'fullname'  =>  $lm_user_fullname,
                        'page_id'   =>  $page_id,
                        'program_name' => $page_id ? get_the_title($page_id) : null,
                    ]);

                    $lm_user_data['updated_at'] = current_time('mysql');
                    $lmr_flag = $this->update_data_dynamic($lm_user_data, [
                        'id' => $lm_result['id']
                    ]);

                    setcookie('lucky_money_' . $page_id, 'done', 0, '/');
                    wp_send_json_success([
                        'title'   => __('Thông báo', 'lucky-money'),
                        'message' => __('Nhận thưởng thành công<br>
                        Vui lòng kiểm tra email', 'lucky-money'),
                    ]);
                } else {

                    wp_send_json_error([
                        'title'   => __('Thông báo', 'lucky-money'),
                        'message' => lm_get_message_error(__('Không tìm thấy phần thưởng hợp lệ', 'lucky-money')),
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
    }

    global $lucky_moneyResult;
    $lucky_moneyResult = new Lucky_MoneyResult();

endif;
