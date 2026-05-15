<?php

if (!$lm_program_id) {
    return;
}

if (empty($lm_program_view) || $lm_program_view == 'default') { ?>

    <?php
    global $lucky_moneyPrize;
    global $lucky_moneyProgram;
    $lm_program_prizes = $lucky_moneyProgram->get_data($lm_program_id); ?>
    <form id="frmProgramPrizeData">
        <input type="hidden" name="lm_program_id" value="<?php echo $lm_program_id; ?>">
        <div class="heads">
            <div class="heads-tt">
                <p class="tt">
                    <?php echo __('Phần thưởng', 'lucky-money'); ?>
                </p>
                <p class="des">
                    <?php echo __('Chương trình nên được nhập đủ số lượng giới hạn là 8 phần thưởng', 'lucky-money'); ?>
                </p>

            </div>
            <div class="des">
                <a type="submit" class="mnw-btn pri" href="<?php echo get_edit_post_link($lm_program_id) ?>" target="_blank">
                    <div class="txt">
                        <?php echo __('Nội dung chương trình', 'lucky-money'); ?>
                    </div>
                </a>
            </div>
        </div>
        <?php if (is_array($lm_program_prizes) && !empty($lm_program_prizes)) { ?>
            <div class="mnw-table mnw-table-has-sort">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th><?php _e('Ảnh mô tả', 'lucky-money'); ?></th>
                            <th><?php _e('Phần thưởng', 'lucky-money'); ?></th>
                            <th><?php _e('Phần trăm', 'lucky-money'); ?></th>
                            <th><?php _e('Số lượng', 'lucky-money'); ?></th>
                            <th style="display:none"><?php _e('Màu nền / Màu chữ', 'lucky-money'); ?></th>
                            <th><?php _e('Hành động', 'lucky-money'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($lm_program_prizes as $program_prize_key => $program_prize) {
                            $lucky_moneyPrizeItem = $lucky_moneyPrize->get_item($program_prize['prize_id']);
                        ?>
                            <tr class="lm-row program_prize_row is-loading-row">
                                <td data-label="<?php _e('Sắp xếp', 'lucky-money'); ?>">
                                    <div class="mnw-sort">
                                        <img src="<?php echo LUCKY_MONEY_URL . '/admin/images/mnw-ic-sort.svg';  ?>">
                                    </div>
                                    <input type="number" class="lm_field lm_sort_field"
                                        value="<?php echo $program_prize['prize_sort'] ?>"
                                        name="lm_data[<?php echo $program_prize['id']; ?>][prize_sort]"
                                        required readonly hidden>
                                </td>
                                <td data-label="<?php _e('Ảnh mô tả', 'lucky-money') ?>">
                                    <div class="lm-thumb mnw-author">
                                        <div class="mnw-author-avatar preview-container">
                                            <div class="avatar preview-img lm-thumb-js">
                                                <?php if (empty($program_prize['prize_thumbnail'])) { ?>
                                                    <img src="<?php echo LUCKY_MONEY_URL . '/admin/images/default-thumbnail.png';  ?>">
                                                <?php } else { ?>
                                                    <img src="<?php echo wp_get_attachment_image_url($program_prize['prize_thumbnail'], 'small');  ?>">
                                                <?php } ?>
                                            </div>
                                            <input type="text" class="lm_field" value="<?php echo $program_prize['prize_thumbnail'] ?>"
                                                name="lm_data[<?php echo $program_prize['id']; ?>][prize_thumbnail]" readonly hidden>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="<?php _e('Phần thưởng', 'lucky-money'); ?>">
                                    <div class="mnw-ip">
                                        <select class="lm_field lm_prize_select2"
                                            name="lm_data[<?php echo $program_prize['id']; ?>][prize_id]" required>
                                            <option value="<?php echo sprintf('%s.%s', $lucky_moneyPrizeItem['id'], $lucky_moneyPrizeItem['type']) ?>"
                                                <?php selected($lucky_moneyPrizeItem['id'], $program_prize['prize_id']); ?>>
                                                <?php echo $lucky_moneyPrizeItem['name']; ?>
                                            </option>
                                        </select>
                                    </div>
                                </td>
                                <td data-label="<?php _e('Phần trăm', 'lucky-money') ?>">
                                    <div class="mnw-ip">
                                        <input type="number" class="lm_field"
                                            value="<?php echo $program_prize['prize_percent'] ? intval($program_prize['prize_percent']) : null ?>"
                                            name="lm_data[<?php echo $program_prize['id']; ?>][prize_percent]" min="0" max="100"
                                            required>
                                    </div>
                                </td>
                                <td data-label="<?php _e('Số lượng', 'lucky-money') ?>">
                                    <div class="mnw-ip">
                                        <input type="number" class="lm_field lm_field_prize_quantity"
                                            value="<?php echo $program_prize['prize_quantity'] ? intval($program_prize['prize_quantity']) : $program_prize['prize_quantity'] ?>"
                                            name="lm_data[<?php echo $program_prize['id']; ?>][prize_quantity]"
                                            <?php echo $lucky_moneyPrizeItem['type'] == 'none' ? 'disabled' : ''; ?>>
                                    </div>
                                </td>
                                <td style="display:none" data-label="<?php _e('Màu nền / Màu chữ', 'lucky-money'); ?>">
                                    <div class="mnw-color">
                                        <div class="mnw-color-item">
                                            <label class="mnw-color-label" style="background: <?php echo $program_prize['prize_background'] ?>">
                                                <input type="color" class="lm_field colorPicker" value="<?php echo $program_prize['prize_background'] ?>"
                                                    name="lm_data[<?php echo $program_prize['id']; ?>][prize_background]" required>
                                            </label>
                                        </div>
                                        <div class="mnw-color-item">
                                            <label class="mnw-color-label" style="background: <?php echo $program_prize['prize_color'] ?>">
                                                <input type="color" class="lm_field colorPicker" value="<?php echo $program_prize['prize_color'] ?>"
                                                    name="lm_data[<?php echo $program_prize['id']; ?>][prize_color]" required>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="<?php _e('Hành động', 'lucky-money'); ?>">
                                    <div class="mnw-table-action">
                                        <button type="button" class="mnw-table-action-btn mnw-edit lm_button update lmProgramPrizeUpdation disabled"
                                            data-program_prize_key="<?php echo $program_prize['id']; ?>"
                                            title="<?php _e('Cập nhật', 'lucky-money'); ?>">
                                            <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/mnw-ic-pen.svg" alt="">
                                        </button>
                                        <button type="button" class="mnw-table-action-btn mnw-del lm_button lmProgramPrizeDeletion"
                                            data-program_prize_key="<?php echo $program_prize['id']; ?>"
                                            title="<?php _e('Xoá', 'lucky-money'); ?>">
                                            <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/mnw-ic-trash.svg" alt="">
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="lm-empty-message">
                <?php echo __('Chưa có dữ liệu', 'lucky-money'); ?>
            </div>
        <?php } ?>
    </form>

<?php } else if ($lm_program_view == 'statistics') { ?>

    <?php
    global $lucky_moneyProgram;
    $lucky_moneyProgramPrizes = $lucky_moneyProgram->get_data($lm_program_id);

    global $lucky_moneyResult;
    $lucky_moneyResultCount = $lucky_moneyResult->get_data_count($lm_program_id);
    $lucky_moneyResultData = $lucky_moneyResult->get_data($lm_program_id);
    $lucky_moneyResultChartData = $lucky_moneyResult->get_chart_data($lm_program_id);

    $lucky_moneyResultEmailData = $lucky_moneyResult->get_email_information_data($lm_program_id);
    $lucky_moneyResultPhoneData = $lucky_moneyResult->get_phone_information_data($lm_program_id);

    $lucky_moneyResultOverview  = [
        'none' => $lucky_moneyResult->get_overview_data($lm_program_id, false),
        'prize' => $lucky_moneyResult->get_overview_data($lm_program_id)
    ];

    $lucky_moneyResultOverview2 = $lucky_moneyResult->get_result_data($lm_program_id);
    ?>

    <!-- GIAODIENMOI -->
    <div class="mnw-pro">
        <div class="mnw-pro-tab">
            <div class="mnw-bottom-list">
                <div class="mnw-bottom open">
                    <div class="mnw-pro-panels">
                        <div class="mnw-pro-panel">
                            <div class="wrapper">
                                <div class="mnw-grid">
                                    <div class="mnw-grid-col">
                                        <div class="mnw-grid-item">
                                            <div class="inner">
                                                <div class="heads">
                                                    <p class="tt">
                                                        <?php echo __('Phần thưởng', 'lucky-money'); ?>
                                                    </p>
                                                    <p class="des">
                                                        <?php echo sprintf(
                                                            __('Tổng cộng %s phần thưởng', 'lucky-money'),
                                                            $lucky_moneyProgramPrizes !== false
                                                                ? count($lucky_moneyProgramPrizes)
                                                                : 0
                                                        ); ?>
                                                    </p>
                                                </div>
                                                <div class="bodies">
                                                    <?php
                                                    if (is_array($lucky_moneyResultChartData) && !empty($lucky_moneyResultChartData)) {

                                                        $chartQuantityTotal = array_reduce($lucky_moneyResultChartData, function ($carry, $item) {
                                                            return $carry + $item['quantity'];
                                                        }, 0);

                                                        foreach ($lucky_moneyResultChartData as $key => $item) {
                                                            $percent_item = $item['quantity'] > 0
                                                                ? number_format(($item['quantity'] * 100) / $chartQuantityTotal, 2)
                                                                : 0;

                                                            $lucky_moneyResultChartData[$key]['percent'] = $percent_item;
                                                        }

                                                        usort($lucky_moneyResultChartData, function ($a, $b) {
                                                            return $b['percent'] <=> $a['percent'];
                                                        });

                                                        $chartPrizeTotal = array_map(function ($item) {
                                                            return $item['id'];
                                                        }, $lucky_moneyResultChartData);
                                                    ?>
                                                        <div class="mnw-progress">
                                                            <?php foreach ($lucky_moneyResultChartData as $rcdatak => $item) {
                                                                $percent_item = $item['percent']; // Use the sorted percent
                                                            ?>
                                                                <div class="mnw-progress-it" title="<?php echo $item['quantity']; ?>">
                                                                    <div class="mnw-progress-heads">
                                                                        <p class="names"><?php echo $item['name']; ?></p>
                                                                        <p class="percents">
                                                                            <?php echo sprintf('%s', $percent_item . '%'); ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="mnw-progress-line" style="--cl-pri:<?php echo $item['colorBg']; ?>;--percent:<?php echo $percent_item; ?>%;">
                                                                    </div>
                                                                </div>
                                                            <?php } ?>

                                                            <?php
                                                            foreach ($lucky_moneyProgramPrizes as $rprogprizek => $item) {
                                                                $item_id = $item['prize_id'];
                                                                if (!in_array($item_id, $chartPrizeTotal)) { ?>
                                                                    <div class="mnw-progress-it" title="0">
                                                                        <div class="mnw-progress-heads">
                                                                            <p class="names"><?php echo $item['prize_name']; ?></p>
                                                                            <p class="percents">
                                                                                <?php echo '0%'; ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="mnw-progress-line" style="--percent:0%;"></div>
                                                                    </div>
                                                            <?php }
                                                            } ?>
                                                        </div>
                                                    <?php } else { ?>

                                                        <?php
                                                        // If no results in $lucky_moneyResultChartData, display the prizes from $lucky_moneyProgramPrizes with 0% progress
                                                        if (is_array($lucky_moneyProgramPrizes) && !empty($lucky_moneyProgramPrizes)) {
                                                            foreach ($lucky_moneyProgramPrizes as $rprogprizek => $item) { ?>
                                                                <div class="mnw-progress-it">
                                                                    <div class="mnw-progress-heads">
                                                                        <p class="names"><?php echo $item['prize_name']; ?></p>
                                                                        <p class="percents">
                                                                            <?php echo '0%'; ?>
                                                                        </p>
                                                                    </div>
                                                                    <div class="mnw-progress-line" style="--percent:0%;"></div>
                                                                </div>
                                                            <?php }
                                                        } else { ?>

                                                            <?php echo __('Không có phần thưởng nào', 'lucky-money'); ?>

                                                        <?php } ?>

                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mnw-grid-col">
                                        <div class="mnw-grid-item">
                                            <div class="inner">
                                                <div class="heads">
                                                    <p class="tt">
                                                        <?php echo __('Lượng người tham gia', 'lucky-money'); ?>
                                                    </p>
                                                    <p class="des">
                                                        <?php
                                                        echo sprintf(
                                                            __('Tổng cộng %s người', 'lucky-money'),
                                                            $lucky_moneyResultCount,
                                                        );
                                                        ?>
                                                    </p>
                                                </div>
                                                <div class="bodies">
                                                    <?php
                                                    if ($lucky_moneyResultCount > 0) {
                                                        $nonePercent = number_format(floatval($lucky_moneyResultOverview['none'] * 100 / $lucky_moneyResultCount), 2);
                                                        $prizePercent = number_format(floatval($lucky_moneyResultOverview['prize'] * 100 / $lucky_moneyResultCount), 2)
                                                    ?>
                                                        <div class="mnw-total">
                                                            <div class="mnw-circle">
                                                                <div class="mnw-circle-wrapper">
                                                                    <div class="circle" data-value="<?php echo $prizePercent;  ?>%">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            viewBox="0 0 36 36" class="progress-svg">
                                                                            <circle cx="18" cy="18" r="15.9155"
                                                                                class="progress-bar__background" />
                                                                            <circle cx="18" cy="18" r="15.9155"
                                                                                class="progress-bar__progress js-progress-bar" />
                                                                        </svg>
                                                                    </div>
                                                                    <div class="circle" data-value="<?php echo $nonePercent;  ?>%">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            viewBox="0 0 36 36" class="progress-svg">
                                                                            <circle cx="18" cy="18" r="15.9155"
                                                                                class="progress-bar__background" />
                                                                            <circle cx="18" cy="18" r="15.9155"
                                                                                class="progress-bar__progress js-progress-bar" />
                                                                        </svg>
                                                                    </div>
                                                                    <div class="mnw-circle-content">
                                                                        <p class="ctn-sub"><?php echo __('Tổng cộng', 'lucky-money'); ?></p>
                                                                        <p class="ctn-main num-of-chart"><?php echo $lucky_moneyResultCount; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mnw-tips">
                                                                <div class="mnw-tips-it blue">
                                                                    <p>
                                                                        <?php
                                                                        echo sprintf(
                                                                            __('%s Không trúng thưởng', 'lucky-money'),
                                                                            $nonePercent . '%'
                                                                        ); ?>
                                                                    </p>
                                                                </div>
                                                                <div class="mnw-tips-it l-blue">
                                                                    <p>
                                                                        <?php
                                                                        echo sprintf(
                                                                            __('%s Trúng thưởng', 'lucky-money'),
                                                                            $prizePercent . '%'
                                                                        ); ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <?php } else { ?>

                                                        <?php echo __('Chưa có người dùng nào', 'lucky-money'); ?>

                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mnw-grid-col">
                                        <?php
                                        $lucky_moneyResultInfo = 0;
                                        if ($lucky_moneyResultEmailData)
                                            $lucky_moneyResultInfo += $lucky_moneyResultEmailData;

                                        if ($lucky_moneyResultPhoneData)
                                            $lucky_moneyResultInfo += $lucky_moneyResultPhoneData;
                                        ?>
                                        <div class="mnw-grid-item">
                                            <div class="inner">
                                                <div class="heads">
                                                    <p class="tt">
                                                        <?php echo __('Thông tin thu nhập', 'lucky-money'); ?>
                                                    </p>
                                                    <p class="des">
                                                        <?php echo sprintf(
                                                            __('Tổng cộng %s dữ liệu', 'lucky-money'),
                                                            $lucky_moneyResultInfo ? $lucky_moneyResultInfo : 0
                                                        ); ?>
                                                    </p>
                                                </div>
                                                <div class="bodies">
                                                    <?php if ($lucky_moneyResultInfo > 0) {
                                                        $phonePercent = number_format(floatval($lucky_moneyResultPhoneData * 100 / $lucky_moneyResultInfo), 2);
                                                        $emailPercent = number_format(floatval($lucky_moneyResultEmailData * 100 / $lucky_moneyResultInfo), 2)
                                                    ?>
                                                        <div class="mnw-total">
                                                            <div class="mnw-circle">
                                                                <div class="mnw-circle-wrapper">
                                                                    <div class="circle" data-value="<?php echo $emailPercent; ?>%">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            viewBox="0 0 36 36" class="progress-svg">
                                                                            <circle cx="18" cy="18" r="15.9155"
                                                                                class="progress-bar__background" />
                                                                            <circle cx="18" cy="18" r="15.9155"
                                                                                class="progress-bar__progress js-progress-bar" />
                                                                        </svg>
                                                                    </div>
                                                                    <div class="circle" data-value="<?php echo $phonePercent; ?>%">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            viewBox="0 0 36 36" class="progress-svg">
                                                                            <circle cx="18" cy="18" r="15.9155"
                                                                                class="progress-bar__background" />
                                                                            <circle cx="18" cy="18" r="15.9155"
                                                                                class="progress-bar__progress js-progress-bar" />
                                                                        </svg>
                                                                    </div>
                                                                    <div class="mnw-circle-content">
                                                                        <p class="ctn-sub"><?php echo __('Tổng cộng', 'lucky-money'); ?></p>
                                                                        <p class="ctn-main num-of-chart"><?php echo $lucky_moneyResultInfo; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mnw-tips">
                                                                <div class="mnw-tips-it blue">
                                                                    <p><?php echo sprintf(
                                                                            __('%s Số điện thoại', 'lucky-money'),
                                                                            $lucky_moneyResultPhoneData
                                                                        ) ?></p>
                                                                </div>
                                                                <div class="mnw-tips-it l-blue">
                                                                    <p><?php echo sprintf(
                                                                            __('%s Email', 'lucky-money'),
                                                                            $lucky_moneyResultEmailData
                                                                        ) ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } else { ?>
                                                        <?php echo __('Chưa có dữ liệu nào', 'lucky-money'); ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mnw-client">
                                    <div class="heads">
                                        <div class="heads-tt">
                                            <p class="tt"><?php echo __('Danh sách người dùng tham gia', 'lucky-money'); ?></p>
                                        </div>
                                    </div>
                                    <?php if (is_array(@$lucky_moneyResultData['results']) && !empty(@$lucky_moneyResultData['results'])) { ?>
                                        <form id="frmProgramResultData" class="is-loading-row">
                                            <input type="hidden" name="lm_program_id" value="<?php echo $lm_program_id; ?>">
                                            <div class="lm-ct-actions mnw-filter">
                                                <div class="lm-ct-action-export" style="display:none;">
                                                    <div class="export-loading-area">
                                                        <div class="export-loading-text">
                                                            <?php echo __('Đang xuất...', 'lucky-money'); ?>
                                                            <div class="export-loading-perc">
                                                            </div>
                                                        </div>
                                                        <div class="export-loading-bar">
                                                            <div class="export-loading-progress"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mnw-filter-flex">
                                                    <div class="mnw-ip">
                                                        <div class="mnw-btn lm-ct-act-btn export"
                                                            data-result_pages="<?php echo $lucky_moneyResultData['max_pages']; ?>"
                                                            data-program_id="<?php echo $lm_program_id; ?>">
                                                            <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/ic-download.svg" alt="">
                                                            <span class="txt"><?php echo __('Tải xuống', 'lucky-money'); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="mnw-ip hasIcon">
                                                        <div class="icon">
                                                            <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/ic-gift.svg" alt="">
                                                        </div>
                                                        <select name="lm_result_received"
                                                            class="lm_select2 lm_result_filter_js">
                                                            <option value=""><?php echo __('Tình trạng nhận thưởng', 'lucky-money'); ?></option>
                                                            <option value="received"><?php echo __('Đã nhận thưởng', 'lucky-money'); ?></option>
                                                            <option value="none-received"><?php echo __('Chưa nhận thưởng', 'lucky-money'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="mnw-ip hasIcon">
                                                        <div class="icon">
                                                            <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/ic-star.svg" alt="">
                                                        </div>
                                                        <select class="lm_result_prize_select2 lm_result_filter_js" name="lm_result_prize" required>
                                                            <option value=""><?php echo __('Chọn phần thưởng', 'lucky-money'); ?></option>
                                                        </select>
                                                    </div>
                                                    <div class="mnw-ip">
                                                        <div class="mnw-srch">
                                                            <div class="mnw-srch-btn">
                                                                <img src="<?php echo LUCKY_MONEY_URL; ?>/admin/images/ic-srch.svg"
                                                                    alt="">
                                                            </div>
                                                            <input type="text" name="lm_result_keyword" class="lm_result_filter_js"
                                                                placeholder="<?php echo __('Nhập từ khoá tìm kiếm'); ?>" />
                                                        </div>
                                                    </div>


                                                    <div class="mnw-ip">
                                                        <button type="submit" class="mnw-btn pri lm-ct-act-btn lm_result_filter_click filters">
                                                            <div class="txt">
                                                                <?php echo __('Tìm kiếm', 'lucky-money'); ?>
                                                            </div>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="lmr-data-list">
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
                                                                <?php
                                                                // var_dump($result); 
                                                                ?>
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
                                            </div>
                                        </form>
                                    <?php } else { ?>
                                        <div class="lm-empty-message">
                                            <?php echo __('Chưa có dữ liệu', 'lucky-money'); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php } else if ($lm_program_view == 'setting') { ?>

    <?php
    $flagHidden = true;
    if (!$flagHidden && $lm_program_id) {
        $lucky_money_program_type = get_option('lm_option_program_type_' . $lm_program_id, 'default'); ?>
        <select name="lm_program_type" class="lmProgramTypeJs">
            <option value="default" <?php selected('default', $lucky_money_program_type); ?>>
                <?php echo __('Điền thông tin trước khi tham gia', 'lucky-money'); ?>
            </option>
            <option value="lucky_money" <?php selected('lucky_money', $lucky_money_program_type); ?>>
                <?php echo __('Tham gia trước điền thông tin sau', 'lucky-money'); ?>
            </option>
        </select>
    <?php } ?>

    <?php
    if ($lm_program_id) {
        $lucky_money_program_type = get_option('lm_option_program_type_' . $lm_program_id, 'default'); ?>
        <div class="mnw-progsetting">
            <p class="mnw-progsetting-description">
                <?php echo __('Chọn cách người dùng tương tác<br>
                với chương trình [Bốc Lì Xì].', 'lucky-money'); ?>
            </p>
            <div class="mnw-progsetting-section">
                <div class="mnw-progsetting-items">
                    <div class="mnw-progsetting-item">
                        <label class="mnw-progsetting-option" for="program-type-default">
                            <input
                                id="program-type-default"
                                class="lmProgramTypeJs"
                                type="radio"
                                name="lm_program_type"
                                value="default"
                                <?php checked('default', $lucky_money_program_type); ?>>
                            <span class="mnw-progsetting-option-text">
                                <?php echo __('Điền thông tin trước khi tham gia', 'lucky-money'); ?>
                            </span>
                        </label>
                        <p class="mnw-progsetting-description">
                            <?php echo __('Người dùng phải điền thông tin nhận thưởng<br>
                            trước khi tham gia sự kiện.', 'lucky-money'); ?>
                        </p>
                    </div>
                    <div class="mnw-progsetting-item">
                        <label class="mnw-progsetting-option" for="program-type-lucky_money">
                            <input
                                id="program-type-lucky_money"
                                class="lmProgramTypeJs"
                                type="radio"
                                name="lm_program_type"
                                value="lucky_money"
                                <?php checked('lucky_money', $lucky_money_program_type); ?>>
                            <span class="mnw-progsetting-option-text">
                                <?php echo __('Tham gia trước điền thông tin sau', 'lucky-money'); ?>
                            </span>
                        </label>
                        <p class="mnw-progsetting-description">
                            <?php echo __('Người dùng có thể tham gia sự kiện trước 
                            <br>rồi điền thông tin nhận thưởng sau.', 'lucky-money'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>


<?php } ?>