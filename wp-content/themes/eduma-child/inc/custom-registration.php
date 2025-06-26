<?php 
function wpems_create_new_user( $cred = array() ) {
	$cred = wp_parse_args(
		$cred,
		array(
			'email'             => '',
			'password'          => '',
			'confirm_password'  => '',
		)
	);

	$email             = sanitize_email( $cred['email'] );
	$password          = $cred['password'];
	$confirm_password  = $cred['confirm_password'];

	$errors = new WP_Error();

	// Validate email
	if ( ! $email || ! is_email( $email ) ) {
		$errors->add( 'user_email', __( 'Please provide a valid email address.', 'wp-events-manager' ) );
	} elseif ( email_exists( $email ) ) {
		$errors->add( 'user_email', __( 'This email is already registered.', 'wp-events-manager' ) );
	}

	// Validate password
	if ( empty( $password ) ) {
		$errors->add( 'password', __( 'Password is required field.', 'wp-events-manager' ) );
	} elseif ( $password !== $confirm_password ) {
		$errors->add( 'confirm_password', __( 'Passwords do not match.', 'wp-events-manager' ) );
	}

	$errors = apply_filters( 'tp_event_register_errors', $errors, '', $email, $password );

	if ( $errors->get_error_code() ) {
		return $errors;
	}

	// Generate a username from email
	$username = sanitize_user( current( explode( '@', $email ) ) );
	$original_username = $username;
	$count = 1;

	while ( username_exists( $username ) ) {
		$username = $original_username . $count++;
	}

	$userdata = array(
		'user_login' => $username,
		'user_email' => $email,
		'user_pass'  => $password,
	);

	$user_id = wp_insert_user( $userdata );

	if ( is_wp_error( $user_id ) ) {
		$errors->add( 'insert_user_error', __( 'There was an error creating the user.', 'wp-events-manager' ) );
		return $errors;
	}

	do_action( 'tp_event_create_new_user', $user_id, $userdata );

	return $user_id;
}
