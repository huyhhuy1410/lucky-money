<?php
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('Lucky_MoneyPrize')) :

    class Lucky_MoneyPrize
    {

        private $table = 'lucky_money_prize';

        function __construct()
        {
            $this->setup();
            $this->init_hooks();
        }

        private function init_hooks()
        {
            add_action('wp_ajax_lm_ajax_prize_creation', [$this, 'lm_ajax_prize_creation']); // login
            add_action('wp_ajax_lm_ajax_prize_updation', [$this, 'lm_ajax_prize_updation']); // login
            add_action('wp_ajax_lm_ajax_prize_deletion', [$this, 'lm_ajax_prize_deletion']); // login
            add_action('wp_ajax_lm_ajax_prize_data', [$this, 'lm_ajax_prize_data']); // login
        }

        /**
         * name: tên phần thưởng
         * type: loại phần thưởng [none, custom, coupon, product]
         * */
        private function setup()
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));
            if (! $wpdb->get_var($query) == $table_name) {
                $charset_collate    = $wpdb->get_charset_collate();
                $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                type TEXT NOT NULL,
                name TEXT NOT NULL, 
                description TEXT NULL, 
                type_option TEXT NULL,
                type_value TEXT NULL,
                status TINYINT(1) NOT NULL,
                created_at datetime NOT NULL,
                updated_at datetime NOT NULL,
                PRIMARY KEY  (id)
                ) $charset_collate;";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }

        public function get_data($search = null)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $is_woocommerce = class_exists('WooCommerce');

            if (!$is_woocommerce) {
                if ($search) {
                    $search = '%' . $wpdb->esc_like($search) . '%';
                    $query = $wpdb->prepare(
                        "SELECT id, name FROM $table_name WHERE type != %s AND (name LIKE %s OR description LIKE %s OR id LIKE %s) AND status = 1",
                        'coupon',
                        $search,
                        $search,
                        $search
                    );
                } else {
                    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE type != %s AND status = 1", 'coupon');
                }
            } else {
                if ($search) {
                    $search = '%' . $wpdb->esc_like($search) . '%';
                    $query = $wpdb->prepare(
                        "SELECT id, name FROM $table_name WHERE (name LIKE %s OR description LIKE %s OR id LIKE %s) AND status = 1",
                        $search,
                        $search,
                        $search
                    );
                } else {
                    $query = "SELECT * FROM $table_name WHERE status = 1";
                }
            }

            $results = $wpdb->get_results($query, ARRAY_A);

            if ($search) {
                return $results;
            } else {
                return $results;
            }
        }

        public function get_item($id)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d AND status = 1", $id);
            $result = $wpdb->get_row($query, ARRAY_A);
            return $result;
        }

        public function import_data($data)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $current_time = current_time('mysql');
            $wpdb->insert(
                $table_name,
                array(
                    'type'        => $data['type'],
                    'name'        => $data['name'],
                    'description' => isset($data['description']) ? $data['description'] : null,
                    'type_option' => isset($data['type_option']) ? $data['type_option'] : null,
                    'type_value'  => isset($data['type_value']) ? $data['type_value'] : null,
                    'status'      => 1,
                    'created_at'  => $current_time,
                    'updated_at'  => $current_time,
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%s',
                    '%s'
                )
            );

            if ($wpdb->insert_id) {
                return $wpdb->insert_id;
            } else {
                return false; // or handle error as needed
            }
        }

        public function update_data($id, $data)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $current_time = current_time('mysql');

            $result = $wpdb->update(
                $table_name,
                array(
                    'type'        => $data['type'],
                    'name'        => $data['name'],
                    'description' => !empty(@$data['description']) ? $data['description'] : null,
                    'type_option' => !empty(@$data['type_option']) ? $data['type_option'] : null,
                    'type_value'  => !empty(@$data['type_value']) ? $data['type_value'] : null,
                    'updated_at'  => $current_time,
                ),
                array('id' => $id),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                ),
                array('%d')
            );

            return $result !== false;
        }

        public function delete_data($id)
        {
            global $wpdb;
            $table_name = $wpdb->base_prefix . $this->table;
            $program_table_name = $wpdb->base_prefix . 'lucky_money_program';
            $result_table_name = $wpdb->base_prefix . 'lucky_money_result';

            $result = $wpdb->update(
                $table_name,
                array('status' => 0),
                array('id' => $id),
                array('%d'),
                array('%d')
            );

            if ($result !== false) {

                $page_ids = $wpdb->get_col(
                    $wpdb->prepare(
                        "SELECT page_id FROM $program_table_name WHERE status = 1 AND prize_id = %d",
                        $id
                    )
                );

                if (!empty($page_ids)) {

                    $page_ids_placeholder = implode(',', array_fill(0, count($page_ids), '%d'));

                    $update_program = $wpdb->update(
                        $program_table_name,
                        array(
                            'status' => 0,
                            'updated_at' => current_time('mysql'),
                        ),
                        array(
                            'status' => 1,
                            'prize_id' => $id,
                        ),
                        array(
                            '%d',
                            '%s',
                        ),
                        array('%d', '%d')
                    );

                    $update_result = $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE $result_table_name SET status = 0, updated_at = %s WHERE status = 1 AND prize_id = %d AND page_id IN ($page_ids_placeholder)",
                            array_merge(
                                [current_time('mysql'), $id],
                                $page_ids
                            )
                        )
                    );
                }
            }

            return ($result !== false);
        }

        public function lm_ajax_prize_creation()
        {
            $form = $_POST;
            $lm_type = !empty(@$form['lm_type']) ? $form['lm_type'] : false;
            $lm_name = !empty(@$form['lm_name']) ? $form['lm_name'] : false;
            $lm_description = !empty(@$form['lm_description']) ? $form['lm_description'] : false;
            $lm_type_option = !empty(@$form['lm_type_option']) ? $form['lm_type_option'] : false;
            $lm_type_value  = !empty(@$form['lm_type_value']) ? $form['lm_type_value'] : false;

            $lm_data = [
                'type'       => $lm_type,
                'name'       => $lm_name,
                'description' => $lm_description,
                'type_option' => $lm_type_option,
                'type_value'  => $lm_type_value
            ];

            $lucky_moneyPrizeId = $this->import_data($lm_data);
            if (!$lucky_moneyPrizeId) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }

            ob_start();

            $lm_options = lm_prize_options();

            $lm_options = apply_filters('lm_lucky_money_prize_options', $lm_options);

            $lucky_moneyPrizeItem = $this->get_item($lucky_moneyPrizeId);
?>

            <tr class="lm-row prize_row is-loading-row">
                <td data-label="<?php _e('ID', 'lucky-money'); ?>">
                    <?php echo esc_html($lucky_moneyPrizeItem['id']); ?>
                </td>
                <td data-label="<?php _e('Kiểu', 'lucky-money'); ?>">
                    <?php
                    if (is_array($lm_options) && !empty($lm_options)) { ?>
                        <div class="mnw-ip">
                            <select class="lm_field lm_type_js" name="lm_data[<?php echo $lucky_moneyPrizeItem['id']; ?>][type]">
                                <?php foreach ($lm_options as $value => $name) { ?>
                                    <option value="<?php echo $value; ?>" <?php selected($lucky_moneyPrizeItem['type'], $value) ?>>
                                        <?php echo $name; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                </td>
                <td data-label="<?php _e('Nhãn', 'lucky-money'); ?>">
                    <div class="mnw-ip">
                        <input class="lm_field" type="text" name="lm_data[<?php echo $lucky_moneyPrizeItem['id']; ?>][name]" value="<?php echo $lucky_moneyPrizeItem['name']; ?>"
                            placeholder="<?php echo __('Nhập nhãn *', 'lucky-money'); ?>">
                    </div>
                </td>
                <td data-label="<?php _e('Mô tả', 'lucky-money'); ?>">
                    <div class="mnw-ip">
                        <input class="lm_field" type="text" name="lm_data[<?php echo $lucky_moneyPrizeItem['id']; ?>][description]"
                            value="<?php echo $lucky_moneyPrizeItem['description']; ?>" placeholder="<?php echo __('Nhập mô tả', 'lucky-money'); ?>">
                    </div>
                </td>
                <?php
                if (class_exists('Woocommerce')) {
                ?>
                    <td data-label="<?php _e('Giá trị', 'lucky-money'); ?>">
                        <?php

                        if (in_array($lucky_moneyPrizeItem['type'], ['coupon'])) { ?>
                            <div class="mnw-coupon-setting active">
                                <div class="mnw-select">
                                    <div class="mnw-ip">
                                        <select class="lm_field lm_coupon_field lm_coupon_option active" name="lm_data[<?php echo $lucky_moneyPrizeItem['id']; ?>][type_option]" required>
                                            <option value=""><?php echo __('Chọn kiểu khuyến mãi', 'lucky-money'); ?></option>
                                            <option value="default" <?php selected($lucky_moneyPrizeItem['type_option'], 'default'); ?>>
                                                <?php echo __('Cố định', 'lucky-money'); ?>
                                            </option>
                                            <option value="percent" <?php selected($lucky_moneyPrizeItem['type_option'], 'percent'); ?>>
                                                <?php echo __('Phần trăm', 'lucky-money'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mnw-ip">
                                        <input type="number" class="lm_field lm_coupon_field lm_coupon_value active" required
                                            name="lm_data[<?php echo $lucky_moneyPrizeItem['id']; ?>][type_value]" value="<?php echo $lucky_moneyPrizeItem['type_value']; ?>"
                                            placeholder="<?php echo __('Nhập giá trị', 'lucky-money'); ?>">
                                    </div>
                                </div>
                                <div class="mnw-coupon-no-setting">
                                    ...
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="mnw-coupon-setting">
                                <div class="mnw-select">
                                    <div class="mnw-ip">
                                        <select class="lm_field lm_coupon_field lm_coupon_option" name="lm_data[<?php echo $lucky_moneyPrizeItem['id']; ?>][type_option]" required disabled>
                                            <option value=""><?php echo __('Chọn kiểu khuyến mãi', 'lucky-money'); ?></option>
                                            <option value="default">
                                                <?php echo __('Cố định', 'lucky-money'); ?>
                                            </option>
                                            <option value="percent">
                                                <?php echo __('Phần trăm', 'lucky-money'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mnw-ip">
                                        <input type="number" class="lm_field lm_coupon_field lm_coupon_value" required disabled
                                            name="lm_data[<?php echo $lucky_moneyPrizeItem['id']; ?>][type_value]"
                                            value="<?php echo $lucky_moneyPrizeItem['type_value']; ?>"
                                            placeholder="<?php echo __('Nhập giá trị', 'lucky-money'); ?>">
                                    </div>
                                </div>
                                <div class="mnw-coupon-no-setting">
                                    ...
                                </div>
                            </div>
                        <?php }
                        ?>
                    </td>
                <?php
                }
                ?>
                <td data-label="<?php _e('Hành động', 'lucky-money'); ?>">
                    <div class="mnw-table-action">
                        <div class="mnw-cursor mnw-table-action-btn mnw-edit lm_button update lmPrizeUpdation disabled"
                            data-prize_key="<?php echo $lucky_moneyPrizeItem['id']; ?>">
                            <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/mnw-ic-pen.svg" alt="">
                        </div>
                        <div class="mnw-cursor mnw-table-action-btn mnw-del lm_button lmPrizeDeletion"
                            data-prize_key="<?php echo $lucky_moneyPrizeItem['id']; ?>">
                            <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/mnw-ic-trash.svg" alt="">
                        </div>
                    </div>
                </td>
            </tr>

<?php
            $row = ob_get_clean();
            wp_send_json_success(
                [
                    'title' => __('Thông báo!', 'lucky-money'),
                    'message' => __('Đã tạo thành công', 'lucky-money'),
                    'html'  => $row
                ]
            );
            wp_die();
        }

        public function lm_ajax_prize_updation()
        {
            $form = $_POST;

            $lm_data = !empty(@$form['lm_data']) ? $form['lm_data'] : false;
            $prize_key = !empty(@$form['prize_key']) ? $form['prize_key'] : false;

            if (empty($lm_data) || empty($prize_key)) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }

            $lm_by_prize_key = isset($lm_data[$prize_key]) ? $lm_data[$prize_key] : false;
            if (empty($lm_by_prize_key)) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra [2]', 'lucky-money'),
                    ]
                );
                wp_die();
            }

            if (
                $lm_by_prize_key['type'] == 'coupon' &&
                (empty($lm_by_prize_key['type_option']) || empty($lm_by_prize_key['type_value']))
            ) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Vui lòng nhập khuyến mãi', 'lucky-money'),
                    ]
                );
                wp_die();
            }

            $lucky_moneyPrizeUpdation = $this->update_data($prize_key, $lm_by_prize_key);
            if (!$lucky_moneyPrizeUpdation) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra [4]', 'lucky-money'),
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

        public function lm_ajax_prize_deletion()
        {
            $form = $_POST;

            $lm_data = !empty(@$form['lm_data']) ? $form['lm_data'] : false;
            $prize_key = !empty(@$form['prize_key']) ? $form['prize_key'] : false;

            if (empty($lm_data) || empty($prize_key)) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra', 'lucky-money'),
                    ]
                );
                wp_die();
            }

            $lm_by_prize_key = isset($lm_data[$prize_key]) ? $lm_data[$prize_key] : false;
            if (empty($lm_by_prize_key)) {
                wp_send_json_error(
                    [
                        'title'         => __('Thông báo!', 'lucky-money'),
                        'message'       => __('Đã có lỗi xảy ra [2]', 'lucky-money'),
                    ]
                );
                wp_die();
            }

            $lucky_moneyPrizeDeletion = $this->delete_data($prize_key);
            if (!$lucky_moneyPrizeDeletion) {
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

        public function lm_ajax_prize_data()
        {

            $search = isset($_POST['search'])
                ? sanitize_text_field($_POST['search'])
                : '';

            $options = $this->get_data($search);
            wp_send_json($options);
        }
    }

    global $lucky_moneyPrize;
    $lucky_moneyPrize = new Lucky_MoneyPrize();

endif;
