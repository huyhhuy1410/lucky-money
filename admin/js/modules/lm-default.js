import { ajaxPostData, Noti, scrollToID } from "./lm-global.js";

export default function LMDefaultModule() {
    const timerAlert = 3000;

    jQuery(document).on('change', '.lm_field', function (e) {
        e.preventDefault();
        var $this = jQuery(this);
        var parent = $this.closest('.lm-row');
        var color = $this.closest(".mnw-color-item");

        if( color.length ){
            var color_value = $this.val();
            var color_sqr = color.find(".mnw-color-label");
            color_sqr.css("background", color_value);
        }

        var updation_button = parent.find('.lm_button.update');
        if( updation_button.length && updation_button.hasClass('disabled') ){
            updation_button.removeClass('disabled')
        }
    });

    jQuery(document).on('click', '.lm-pagination-ajax a.page-numbers', function (e) {
        e.preventDefault();
        var $this = jQuery(this);
        var form = $this.closest('form');
        var pagination = $this.closest('.lm-pagination-ajax');
        var pagedText = $this.text();
        var paged = pagedText.match(/\d+/);
        if ( !paged ) {
            if ( !$this.hasClass('next') ) {
                var pagedCurrentText = parseInt( pagination.find('.page-numbers.current').text() );
                var paged = pagedCurrentText - 1;
            } else {
                var pagedCurrentText = parseInt(pagination.find('.page-numbers.current').text());
                var paged = pagedCurrentText + 1;
            }
        } else {
            paged = paged[0];
        }

        var processing = form;
        var formData   = new FormData( form[0] );
        formData.append('paged', paged);
        formData.append('action', 'lm_ajax_result_pagination_ajax');
        ajaxPostData( formData, processing )
            .then( function (result) {
                
                processing.removeClass('loading');
                if ( result.success ) {
                    if( result.data.html != undefined ){
                        processing.find('.lmr-data-list').html( result.data.html );
                    }

                    if( result.data.max_pages != undefined && result.data.max_pages ){
                        processing.find('.lm-ct-act-btn.export').attr( 'data-result_pages', result.data.max_pages );
                    }
                }else{
                    Noti({
                        text: result.data.message,
                        title: result.data.title,
                        icon: "danger",
                        timer: timerAlert,
                    });
                }

            }).catch( (error) => {

                processing.removeClass('loading');
                Noti({
                    text: error.message,
                    title: lm_ajax_url.lm_title,
                    icon: "danger",
                    timer: timerAlert,
                });

            });
    });

    jQuery(document).on('change', '.lm_result_filter_js', function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        var form  = $this.closest('form');
        var processing = form;
        var formData   = new FormData( form[0] );
        formData.append('paged', 1);
        formData.append('filter', 1);
        formData.append('action', 'lm_ajax_result_pagination_ajax');
        ajaxPostData( formData, processing )
            .then( function (result) {
                
                processing.removeClass('loading');
                if ( result.success ) {
                    if( result.data.html != undefined ){
                        processing.find('.lmr-data-list').html( result.data.html );
                    }

                    if( result.data.max_pages != undefined && result.data.max_pages ){
                        processing.find('.lm-ct-act-btn.export').attr( 'data-result_pages', result.data.max_pages );
                    }
                }else{
                    Noti({
                        text: result.data.message,
                        title: result.data.title,
                        icon: "danger",
                        timer: timerAlert,
                    });
                }

            }).catch( (error) => {

                processing.removeClass('loading');
                Noti({
                    text: error.message,
                    title: lm_ajax_url.lm_title,
                    icon: "danger",
                    timer: timerAlert,
                });

            });
    });

    jQuery(document).on('change', '.lm_result_page_js', function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        var form  = $this.closest('form');
        var processing = form;
        var formData   = new FormData( form[0] );
        formData.append( 'paged', $this.val() );
        formData.append('action', 'lm_ajax_result_pagination_ajax');
        ajaxPostData( formData, processing )
            .then( function (result) {
                
                processing.removeClass('loading');
                if ( result.success ) {
                    if( result.data.html != undefined ){
                        processing.find('.lmr-data-list').html( result.data.html );
                    }

                    if( result.data.max_pages != undefined && result.data.max_pages ){
                        processing.find('.lm-ct-act-btn.export').attr( 'data-result_pages', result.data.max_pages );
                    }
                }else{
                    Noti({
                        text: result.data.message,
                        title: result.data.title,
                        icon: "danger",
                        timer: timerAlert,
                    });
                }

            }).catch( (error) => {

                processing.removeClass('loading');
                Noti({
                    text: error.message,
                    title: lm_ajax_url.lm_title,
                    icon: "danger",
                    timer: timerAlert,
                });

            });
    });

    jQuery(document).on('submit', '#frmProgramResultData', function(e) {
        e.preventDefault();
        var $this = jQuery(this);
        var form  = $this;
        var processing = form;
        var formData   = new FormData( form[0] );
        formData.append('paged', 1);
        formData.append('filter', 1);
        formData.append('action', 'lm_ajax_result_pagination_ajax');
        ajaxPostData( formData, processing )
            .then( function (result) {
                processing.removeClass('loading');
                if ( result.success ) {
                    if( result.data.html != undefined ){
                        processing.find('.lmr-data-list').html( result.data.html );
                    }

                    if( result.data.max_pages != undefined && result.data.max_pages ){
                        processing.find('.lm-ct-act-btn.export').attr( 'data-result_pages', result.data.max_pages );
                    }
                }else{
                    Noti({
                        text: result.data.message,
                        title: result.data.title,
                        icon: "danger",
                        timer: timerAlert,
                    });
                }

            }).catch( (error) => {

                processing.removeClass('loading');
                Noti({
                    text: error.message,
                    title: lm_ajax_url.lm_title,
                    icon: "danger",
                    timer: timerAlert,
                });

            });
    });

    jQuery(document).on('click', '.lm-email-image-js', function (e) {
        e.preventDefault();
        var $this = jQuery(this);
        var $parent = $this.closest( '.lm-email-image' );

        var file_frame = (
            wp.media.frames.file_frame = wp.media({
                title: lm_ajax_url.lm_media_title,
                button: {
                    text: lm_ajax_url.lm_media,
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            })
        );

        file_frame.on("select", function () {
            var attachment = file_frame.state().get("selection").first().toJSON();
            $parent.find('input').val( attachment.url )
        });

        file_frame.open();
    });

    jQuery(document).on('click', '.resultReceivedJs', function (e) {
        e.preventDefault();
        var $this = jQuery(this);
        var processing  = $this.closest('.program_result_row');
        var result_id   = $this.val();
        var result_flag = $this.prop('checked');

        jQuery.confirm({
            animationBounce: 1.5,
            title: lm_ajax_url.lm_danger,
            icon: 'fa fa-warning',
            closeIcon: false,
            content: result_flag ? lm_ajax_url.lm_received : lm_ajax_url.lm_refunded,
            type: 'red',
            buttons: {
                tryAgain: {
                    text: lm_ajax_url.lm_yes,
                    btnClass: 'red',
                    action: function () {
                        var formData    = new FormData();
                        formData.append('action', 'lm_ajax_program_result_received');
                        formData.append('result_id', result_id);
                        formData.append('result_flag', result_flag ? 1 : 0 );
                        ajaxPostData( formData, processing )
                        .then( function (result) {
                            
                            processing.removeClass('loading');
                            if ( result.success ) {
                                if( result.data.html != undefined ){
                                    processing.html( result.data.html );
                                }
                            }else{
                                Noti({
                                    text: result.data.message,
                                    title: result.data.title,
                                    icon: "danger",
                                    timer: timerAlert,
                                });
                            }

                        }).catch( (error) => {

                            processing.removeClass('loading');
                            Noti({
                                text: error.message,
                                title: lm_ajax_url.lm_title,
                                icon: "danger",
                                timer: timerAlert,
                            });

                        });
                    }
                },
                close: {
                    text: lm_ajax_url.lm_no,
                }
            },
        });
    });

    jQuery(document).on('click', '.lm-ct-act-btn.export', async function (e) {
        e.preventDefault();
        var $this = jQuery(this);
        var form = $this.closest('form');

        var parent = $this.closest( '.lm-ct-actions' );
        var program_id = $this.data('program_id');
        var total_pages = $this.data('result_pages');
    
        var loadingArea = jQuery('.lm-ct-action-export');
        loadingArea.show();
        var loadingProgress = loadingArea.find('.export-loading-progress');
    
        // Reset loading progress
        loadingProgress.css('width', '0%');
    
        var currentPage = 1;
        var allData = [];
    
        async function exportData(page, form) {
            try {
                var formData = new FormData( form[0] );
                console.log( formData );
                
                formData.append('action', 'lm_ajax_result_report_ajax');
                formData.append('page', page);
                formData.append('program_id', program_id);

                const response = await jQuery.ajax({
                    url: lm_ajax_url.ajaxURL,
                    type: 'POST',
                    data: Object.fromEntries(formData),
                });
    
                // Push the returned data into the allData array
                allData.push(...response.data);  
                // Spread operator to flatten the array
    
                // Update the progress bar
                var progress = Math.ceil((page / total_pages) * 100);
                loadingProgress.css('width', progress + '%');
                loadingArea.find('.export-loading-perc').html( progress + '%' );
    
                // If not the last page, continue fetching the next page
                if (page < total_pages) {
                    exportData(page + 1, form);
                } else {
                    finalizeExport(allData);
                }
            } catch (error) {
                console.error('Error fetching data:', error);
                jQuery('.export-loading-bar').hide();
            }
        }
    
        async function finalizeExport(data) {
            // Import the SheetJS library
            const XLSX = await import("https://cdn.sheetjs.com/xlsx-0.19.2/package/xlsx.mjs");
    
            // Custom headers from the localized script
            const customHeaders = lm_ajax_url.lm_export_headers;
    
            // Original keys in the data
            const originalKeys = [
                "id", "email", "phone", "fullname", "received", "received_note", 
                "received_at", "status", "created_at", "updated_at",
                "prize_id", "prize_type", "prize_value", "prize_name", 
                "prize_description", "prize_thumbnail",
                "page_id", "page_name"
            ];
    
            // Map original keys to custom headers
            const mappedData = data.map(item => {
                let newItem = {};
                originalKeys.forEach((key, index) => {
                    newItem[customHeaders[index]] = item[key];
                });
                return newItem;
            });
    
            // Convert the mapped data to a worksheet
            var worksheet = XLSX.utils.json_to_sheet(mappedData);
    
            // Create a new workbook and append the worksheet
            var workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Results");
    
            // Generate a Blob representing the Excel file
            var wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
            var blob = new Blob([wbout], { type: 'application/octet-stream' });
    
            // Create a download link and trigger it
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'results.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
    
            jQuery('.lm-ct-actions').removeClass('loading');
            jQuery('.lm-ct-action-export').hide();
        }
    
        if ( !parent.hasClass('loading') ) {
            parent.addClass('loading');
            exportData(currentPage, form);
        }
    });
}