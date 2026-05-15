import { ajaxPostData, Noti } from "./lm-global.js";

export default function LMProgramsModule() {
    jQuery(document).ready(function () {

        const timerAlert = 3000;
        const $programJs = jQuery('.lmProgramJs');
        if ( $programJs.length ) {
            $programJs.on('change', function (e) {
                e.preventDefault();
                lmProgramInformationAjax( $programJs.val() );
            });

            if ( $programJs.val() !== '' ) {
                lmProgramInformationAjax( $programJs.val() );
            }

            jQuery(document).on('change', '.lmProgramTypeJs', function(e) {
                e.preventDefault();
                var $this = jQuery(this);
                var page_id = $programJs.val();
                lmProgramInformationAjax2( page_id, $this.val() )
            });
        }

        function lmCircles() {
            const circles = document.querySelectorAll(".circle");
            if (circles) {
                circles.forEach((circle) => {
                    const value = parseFloat(circle.getAttribute("data-value")); // Lấy giá trị từ data-value
                    const progressCircle = circle.querySelector(".js-progress-bar");

                    if (progressCircle) {
                        const radius = progressCircle.r.baseVal.value; // Bán kính của vòng tròn
                        const circumference = 2 * Math.PI * radius; // Chu vi của vòng tròn

                        progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
                        progressCircle.style.strokeDashoffset = circumference;

                        // Tính toán offset dựa trên giá trị (0-100%)
                        const offset = circumference - (value / 100) * circumference;

                        // Thêm animation
                        progressCircle.style.transition = "stroke-dashoffset 1s ease-out";
                        progressCircle.style.strokeDashoffset = offset;
                    }
                });
            }
        }

        function lmSortable() {
            jQuery(".mnw-table.mnw-table-has-sort table tbody").sortable({
                handle: ".mnw-sort",
                placeholder: "sortable-placeholder",
                update: function (event, ui) {
                    jQuery(".mnw-table.mnw-table-has-sort table tbody tr").each(function (index) {
                        jQuery(this).find("input.lm_sort_field").val(index);
                        jQuery(this).data("sort", index);

                        var lmnProgPrizeUpdate = jQuery(this).find(".lmProgramPrizeUpdation");
                        if( lmnProgPrizeUpdate.length ){
                            var program_prize_key   = lmnProgPrizeUpdate.data('program_prize_key');
                            var processing  = lmnProgPrizeUpdate.closest('.program_prize_row');
                            var formData    = new FormData( lmnProgPrizeUpdate.closest('form')[0] );
                            formData.append('action', 'lm_ajax_program_prize_updation');
                            formData.append('program_prize_key', program_prize_key);
                            ajaxPostData( formData, processing )
                            .then( function (result) {
                                console.log('sortable success ' + program_prize_key );
                                processing.removeClass('loading');
                            }).catch( (error) => {
                                console.log('sortable error ' + program_prize_key );
                                processing.removeClass('loading');
                            });
                        }
                    });
                },
            });
        }

        function lmPopup() {
            const popupClose = document.querySelectorAll(".lm-popup-close");
            const popupOverlay = document.querySelectorAll(".lm-popup-overlay");
            const body = document.getElementsByTagName("body")[0];
            const popup = document.querySelectorAll(".lm-popup");
            if (popupClose) {
                popupClose.forEach((item) => {
                    item.addEventListener("click", () => {
                        const parentPopup = item.closest(".lm-popup");
                        // console.log(parentPopup);
                        if (parentPopup && parentPopup.classList.contains("open")) {
                            parentPopup.classList.remove("open");
                            body.classList.remove("no-scroll");
                        }
                    });
                });
            }
            if (popupOverlay) {
                popupOverlay.forEach((item) => {
                    item.addEventListener("click", () => {
                        const parentPopup = item.closest(".lm-popup");
                        parentPopup.classList.remove("open");
                        body.classList.remove("no-scroll");
                    });
                });
            }

            const popupOpens = document.querySelectorAll(".lm-popup-open");
            if (popupOpens) {
                popupOpens.forEach((item) => {
                    item.addEventListener("click", (e) => {
                        e.preventDefault();
                        const idString = item.getAttribute("data-popup");
                        if (popup) {
                            popup.forEach((item) => {
                                if (item.getAttribute("data-popup-id") == idString) {
                                    item.classList.add("open");
                                    body.classList.add("no-scroll");
                                }
                            });
                        }
                    });
                }); 
            }
        }

        function lmProgramPrizeSelect2() {
            const program_id = jQuery('#frmProgramResultData').find('input[name="lm_program_id"]').val();

            jQuery('.lm_prize_select2').select2({
                ajax: {
                    url: lm_ajax_url.ajaxURL,
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            action: 'lm_ajax_prize_data',
                            search: params.term || '',
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: jQuery.map(data, function(item) {
                                return {
                                    id: item.id + '.' + item.type,
                                    text: item.name,
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
            });

            jQuery('.lm_result_prize_select2').select2({
                ajax: {
                    url: lm_ajax_url.ajaxURL,
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            action: 'lm_ajax_get_result_prizes',
                            program_id: program_id,
                            search: params.term || '',
                        };
                    },
                    processResults: function(data) {
                        var allOption = {
                            id: '',
                            text: lm_ajax_url.lm_choosen_prize
                        };
                        var results = jQuery.map(data, function(item) {
                            return {
                                id: item.prize_id,
                                text: item.name,
                            };
                        });
            
                        // Prepend the "All" option to the results
                        results.unshift(allOption);
            
                        return {
                            results: results
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
            });

            jQuery('.lm_select2').select2();
        }

        function lmProgramInformationAjax( $element ) {
            var progVisual = jQuery('#lmProgramVisual');
            var progVisualValue = progVisual.data('prog_view');

            var formData = new FormData();
            formData.append('action', 'lm_ajax_program_prize_information');
            formData.append('lm_program_id', $element);
            if( progVisualValue !== undefined ){
                formData.append('lm_program_view', progVisualValue);
            }
            
            ajaxPostData(formData, progVisual)
                .then(function (result) {
                    progVisual.removeClass('loading');
                    if (result.success) {
                        console.log('program_information success');
                        if (result.data.html != undefined && result.data.html != '') {
                            progVisual.html(result.data.html);
                            lmPopup();
                            lmCircles();
                            lmSortable();
                            lmProgramPrizeSelect2();
                        }
                    } else {
                        console.log('program_information error');
                    }
                }).catch((error) => {
                    progVisual.removeClass('loading');
                    Noti({
                        text: error.message,
                        title: lm_ajax_url.lm_title,
                        icon: "danger",
                        timer: timerAlert,
                    });
                });
        }

        function lmProgramInformationAjax2( page_id, type_value ) {
            var progVisual = jQuery('#lmProgramVisual');

            var formData = new FormData();
            formData.append('action', 'lm_ajax_program_prize_information_2');
            formData.append('lm_program_id', page_id);
            formData.append('lm_program_type', type_value);
            ajaxPostData(formData, progVisual)
            .then(function (result) {
                progVisual.removeClass('loading');
                if (result.success) {
                    console.log('program_information 2 success');
                } else {
                    console.log('program_information 2 error');
                }
            }).catch((error) => {
                Noti({
                    text: error.message,
                    title: lm_ajax_url.lm_title,
                    icon: "danger",
                    timer: timerAlert,
                });
            });
        }

        jQuery(document).on('change', '.lm_prize_select2', function(e) {
            e.preventDefault();
            var $this = jQuery(this);
            var parent = $this.closest('.lm-row');
            var prize = $this.val();
            var [id, type] = prize.split('.');
        
            if ( type === 'none' ) {
                parent.find('.lm_field_prize_quantity').prop('disabled', true);
            } else {
                parent.find('.lm_field_prize_quantity').prop('disabled', false);
            }
        });

        jQuery(document).on('submit', '#frmLucky_MoneyProgramPrizeCreation', function (e) {
            e.preventDefault();
            var open = jQuery('.lm_added_js');
            var $this = jQuery(this);
            var formData = new FormData( $this[0] );
            const prizeNum = jQuery("#frmProgramPrizeData tr.program_prize_row").length;
            formData.append('lm_program_prize_num', prizeNum);

            formData.append('action', 'lm_ajax_program_prize_creation');

            ajaxPostData(formData, $this)
                .then(function (result) {
                    $this.removeClass('loading');
                    if (result.success) {
                        console.log('program_prize_creation success');
                        $this.find('.mnw-color-label').css('background', 'unset');
                        $this.find('.lm-thumb img').attr( 'src', lm_ajax_url.lm_thumb );
                        $this[0].reset();

                        Noti({
                            text: result.data.message,
                            title: result.data.title,
                            icon: "success",
                            timer: timerAlert,
                        });

                        lmProgramInformationAjax( $programJs.val() );
                        if( open.length ){
                            open.trigger('click');
                        }
                    } else {
                        console.log('program_information error');
                        Noti({
                            text: result.data.message,
                            title: result.data.title,
                            icon: "danger",
                            timer: timerAlert,
                        });
                    }
                }).catch((error) => {
                    $this.removeClass('loading');
                    Noti({
                        text: error.message,
                        title: lm_ajax_url.lm_title,
                        icon: "danger",
                        timer: timerAlert,
                    });
                });
        });

        jQuery(document).on('click', '.lm-thumb-js', function (e) {
            e.preventDefault();
            var $this = jQuery(this);
            var $parent = $this.closest( '.lm-thumb' );

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
                $parent.find('input').val( attachment.id )
                $parent.find('.preview-img img').attr( 'src' , attachment.url )
                $parent.find('input').trigger('change');
            });

            file_frame.open();
        });

        jQuery(document).on('click', '.lmProgramPrizeUpdation', function (e) {
            e.preventDefault();
            var $this = jQuery(this);
            var program_prize_key   = $this.data('program_prize_key');
            var processing  = $this.closest('.program_prize_row');
            var formData    = new FormData( $this.closest('form')[0] );
            formData.append('action', 'lm_ajax_program_prize_updation');
            formData.append('program_prize_key', program_prize_key);
            ajaxPostData( formData, processing )
            .then( function (result) {
                
                processing.removeClass('loading');
                if ( result.success ) {
                    Noti({
                        text: result.data.message,
                        title: result.data.title,
                        icon: "success",
                        timer: timerAlert,
                    });
                    var updation_button = processing.find('.lm_button.update');
                    if( updation_button.length && !updation_button.hasClass('disabled') ){
                        updation_button.addClass('disabled')
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

        jQuery(document).on('click', '.lmProgramPrizeDeletion', function (e) {
            e.preventDefault();
            var $this = jQuery(this);
            var program_prize_key = $this.data('program_prize_key');
            var processing  = $this.closest('.program_prize_row');

            jQuery.confirm({
                animationBounce: 1.5,
                title: lm_ajax_url.lm_danger,
                icon: 'fa fa-warning',
                closeIcon: false,
                content: lm_ajax_url.lm_delete,
                type: 'red',
                buttons: {
                    tryAgain: {
                        text: lm_ajax_url.lm_yes,
                        btnClass: 'red',
                        action: function () {
                            var formData    = new FormData( $this.closest('form')[0] );
                            formData.append('action', 'lm_ajax_program_prize_deletion');
                            formData.append('program_prize_key', program_prize_key);
                            ajaxPostData( formData, processing )
                            .then( function (result) {
                                
                                processing.removeClass('loading');
                                if ( result.success ) {
                                    lmProgramInformationAjax( $programJs.val() );
                                    Noti({
                                        text: result.data.message,
                                        title: result.data.title,
                                        icon: "success",
                                        timer: timerAlert,
                                    });
                                    // setTimeout(() => {
                                    //     window.location.reload();
                                    // }, timerAlert);
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
    });
}
