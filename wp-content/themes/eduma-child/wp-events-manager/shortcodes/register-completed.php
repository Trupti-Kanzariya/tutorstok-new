<?php
/**
 * The Template for displaying shortcode register completed.
 *
 * Override this template by copying it to yourtheme/wp-events-manager/shortcodes/register-completed.php
 *
 * @author        ThimPress, leehld
 * @package       WP-Events-Manager/Template
 * @version       2.1.7
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

wpems_print_notices();

printf(
	__( 'You have successfully registered to <strong>%1$s</strong>. We have emailed your password to <i>%2$s</i> the email address you entered sd.', 'wp-events-manager' ),
	get_bloginfo( 'name' ),
	sanitize_text_field( $_REQUEST['registered'] )
);

printf(
    __( 'A course based on the <strong>Grade</strong> you entered. <a href="%s" class="button-link">Click here to Login</a>', 'wp-events-manager' ),
    esc_url( site_url('/user-login/') ) // Change this to your desired URL
);
