<?php
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('Lucky_MoneyEmail')) :

    class Lucky_MoneyEmail {

        function __construct() {
            $this->init_hooks();
        }

        private function init_hooks() {
            add_action('lm_send_winner_email', [$this, 'lm_lucky_money_send_winner_email']);
        }

        // Main function to send winner email
        public function lm_lucky_money_send_winner_email($data) {
            
            $email_enable = get_option( 'lucky_money_email_enable' );
            if( !$email_enable ){
                return false;
            }

            $email_title  = get_option('lucky_money_email_title');
            $header_image = get_option('lucky_money_email_header_image');
            $footer_image = get_option('lucky_money_email_footer_image');

            // Get header and footer HTML
            $header = $this->get_email_header($header_image);
            $body   = $this->get_email_body($data);
            $footer = $this->get_email_footer($footer_image);

            // Replace placeholders in the email title
            $placeholders = [
                '{program_name}'        => esc_html( mb_strtoupper( $data['program_name'], 'UTF-8' ) ),
                '{prize_owner}'         => esc_html($data['fullname']),
                '{prize_name}'          => esc_html($data['prize']['prize_name']),
                '{prize_value}'         => $data['prize_value'] ? esc_html($data['prize_value']) : null,
                '{prize_description}'   => $data['prize']['prize_description'] ? esc_html($data['prize']['prize_description']) : null,
            ];

            $winner_email = [ get_bloginfo('admin_email'), $data['email'] ];
            $winner_subject = strtr( $email_title, $placeholders );

            // Combine all parts into one message
            $message = $header . $body . $footer;
            $this->send_email($winner_email, $winner_subject, $message);
        }

        private function send_email($to, $subject, $message) {
            $headers = array('Content-Type: text/html; charset=UTF-8'); // HTML format
            $sent = wp_mail($to, $subject, $message, $headers);

            if (!$sent) {
                error_log('Failed to send email to ' . implode(', ', $to));
            }
        }

        private function get_email_header($header_image) {
            ob_start(); // Start output buffering
            ?>
            <!DOCTYPE html>
            <html <?php language_attributes(); ?>>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
                <meta content="width=device-width, initial-scale=1.0" name="viewport">
                <title><?php echo get_bloginfo('name', 'display'); ?></title>
            </head>
            <body>
                <div style="width:100%;background-color: #f7f7f7;padding: 6px 0;text-align: center;">
                    <div style="display:block;margin:10px auto;max-width:600px">
                        <?php if ($header_image) : ?>
                            <div style="text-align:center;">
                                <img src="<?php echo esc_url($header_image); ?>" alt="Header Image" style="max-width:100%; height:auto;">
                            </div>
                        <?php endif; ?>
            <?php
            $header = ob_get_clean(); // Get the contents of the buffer and clean it
            return $header;
        }

        private function get_email_body($data) {
            if( $data['prize_type'] != 'none' ){
                $email_body = get_option('lucky_money_email_template');
            }else{
                $email_body = get_option('lucky_money_email_failed_template');
            }
            $placeholders = [
                '{program_name}'        => esc_html($data['program_name']),
                '{prize_owner}'         => esc_html($data['fullname']),
                '{prize_name}'          => esc_html($data['prize']['prize_name']),
                '{prize_value}'         => $data['prize_value'] ? esc_html($data['prize_value']) : null,
                '{prize_description}'   => $data['prize']['prize_description'] ? esc_html($data['prize']['prize_description']) : null,
            ];
            $message_body = strtr($email_body, $placeholders);
            ob_start();
            ?>
            <div style="padding:20px;text-align:left;color:#333;">
                <div style="margin:20px 0;line-height:1.6;">
                    <?php echo nl2br($message_body); ?>
                </div>
            </div>
            <?php
            $body = ob_get_clean();
            return $body;
        }

        private function get_email_footer($footer_image) {
            ob_start(); // Start output buffering
            ?>
                        <?php if ($footer_image) : ?>
                            <div style="text-align:center;">
                                <img src="<?php echo esc_url($footer_image); ?>" alt="Footer Image" style="max-width:100%; height:auto;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </body>
            </html>
            <?php

            $footer = ob_get_clean(); // Get the contents of the buffer and clean it
            return $footer;
        }
    }

    global $lucky_moneyEmail;
    $lucky_moneyEmail = new Lucky_MoneyEmail();
endif;
