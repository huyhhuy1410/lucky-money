import { ajaxPostData, Noti } from "./lm-global.js";

export default function LMPrizesModule() {
    const timerAlert = 3000;
    jQuery(document).on('click', '.lmPrizeUpdation', function (e) {
        e.preventDefault();
        var $this       = jQuery(this);
        var prize_key   = $this.data('prize_key');
        var processing  = $this.closest('.prize_row');
        var formData    = new FormData( $this.closest('form')[0] );
        formData.append('action', 'lm_ajax_prize_updation');
        formData.append('prize_key', prize_key);
        ajaxPostData( formData, processing )
        .then( function (result) {
            
            processing.removeClass('loading');
            if ( result.success ) {
                var updation_button = processing.find('.lm_button.update');
                if( updation_button.length && !updation_button.hasClass('disabled') ){
                    updation_button.addClass('disabled')
                }

                Noti({
                    text: result.data.message,
                    title: result.data.title,
                    icon: "success",
                    timer: timerAlert,
                });
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

    jQuery(document).on('click', '.lmPrizeDeletion', function (e) {
        e.preventDefault();
        var $this       = jQuery(this);
        var prize_key   = $this.data('prize_key');
        var processing  = $this.closest('.prize_row');
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
                        formData.append('action', 'lm_ajax_prize_deletion');
                        formData.append('prize_key', prize_key);
                        ajaxPostData( formData, processing )
                        .then( function (result) {
                            if ( result.success ) {
                                processing.remove();
                                Noti({
                                    text: result.data.message,
                                    title: result.data.title,
                                    icon: "success",
                                    timer: timerAlert,
                                });
                            }else{
                                processing.removeClass('loading');
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
    
    jQuery(document).on('submit', '#frmLucky_MoneyPrizeCreation', function (e) {
        e.preventDefault();
        var $this       = jQuery(this);
        var processing  = $this;

        var added = jQuery('.mnw-bottom.mnw-added');
        var prizes = jQuery('.mnw-bottom.mnw-prizes');

        var formData    = new FormData( $this[0] );
        formData.append('action', 'lm_ajax_prize_creation');
        ajaxPostData( formData, processing )
        .then( function (result) {
            
            processing.removeClass('loading');
            if ( result.success ) {
                // window.location.reload();
                $this.find('.mnw-coupon-setting').removeClass('active');
                $this[0].reset();

                if( result.data.html != undefined ){
                    prizes.find('table tbody').append( result.data.html );
                }

                Noti({
                    text: result.data.message,
                    title: result.data.title,
                    icon: "success",
                    timer: timerAlert,
                });
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

    jQuery(document).on('change', '.lm_type_js', function (e) {
        e.preventDefault();
        var $this = jQuery(this);
        var value = $this.val();
        var parent = $this.closest( '.lm-row' );
    
        if (value == 'coupon') {
            parent.find('.mnw-coupon-setting').addClass('active');
            parent.find('.lm_coupon_field').val('');
            parent.find('.lm_coupon_field').prop('disabled', false);
        } else {
            parent.find('.mnw-coupon-setting').removeClass('active');
            parent.find('.lm_coupon_field').val('');
            parent.find('.lm_coupon_field').prop('disabled', true);
        }
    });
    
    jQuery(document).on('change', '.lm_coupon_option', function () {
        var $this = jQuery(this);
        var selectedOption = $this.val();
        var parent = $this.closest( '.lm-row' );
        var couponValueField = parent.find('.lm_coupon_value');

        if (selectedOption === 'percent') {
            // Set min and max for percentage
            couponValueField.val(1);
            couponValueField.prop('min', 1);
            couponValueField.prop('max', 99);
        } else {
            // Reset min and max for other options
            couponValueField.val('');
            couponValueField.prop('min', 0);
            couponValueField.prop('max', null);
        }
    });

    jQuery(document).on('click', '.lm_added_js', function (e) {
        e.preventDefault();
        var $this = jQuery(this);
        var added = jQuery('.mnw-bottom.mnw-added');
        var prizes = jQuery('.mnw-bottom.mnw-prizes');

        if( !$this.hasClass('open') ){
            $this.addClass('open');
            added.addClass('open');
            prizes.removeClass('open');
        }else{
            added.removeClass('open');
            $this.removeClass('open');
            prizes.addClass('open');
        }
    });

    jQuery(document).on('keyup', '.lm_coupon_value', function () {
        var $this = jQuery(this);
        var max = parseInt($this.attr('max'), 10);
        var currentValue = parseInt($this.val(), 10);
    
        if ( currentValue > max ) {
            alert(`Giá trị tối đa là ${max}`);
            $this.val(max);
        }
    });
    
}