<?php
/**
 * Template for displaying change password form in profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/settings/tabs/change-password.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit();

$profile = LP_Profile::instance();

if ( ! isset( $section ) ) {
	$section = 'change-password';
}
?>

<form method="post" name="profile-change-password" enctype="multipart/form-data" class="learn-press-form">
	<?php do_action( 'learn-press/before-profile-change-password-fields', $profile ); ?>

	<ul class="form-fields">

		<?php do_action( 'learn-press/begin-profile-change-password-fields', $profile ); ?>

		<li class="form-field">
			<label for="pass0"><?php esc_html_e( 'Current password', 'learnpress' ); ?></label>
			<div class="form-field-input">
				<input type="password" id="pass0" name="pass0" autocomplete="off" maxlength="40" class="regular-text"/>
				<span class="toggle-password" data-target="#pass0"><i class="fa fa-eye-slash"></i></span>
			</div>
		</li>
		<li class="form-field">
			<label for="pass1"><?php esc_html_e( 'New password', 'learnpress' ); ?></label>
			<div class="form-field-input">
				<input type="password" name="pass1" id="pass1" class="regular-text" maxlength="40" value=""/>
				<span class="toggle-password" data-target="#pass1"><i class="fa fa-eye-slash"></i></span>
			</div>
		</li>
		<li class="form-field">
			<label for="pass2"><?php esc_html_e( 'Confirm new password', 'learnpress' ); ?></label>
			<div class="form-field-input">
				<input name="pass2" type="password" id="pass2" maxlength="40" class="regular-text" value=""/>
				<span class="toggle-password" data-target="#pass2"><i class="fa fa-eye-slash"></i></span>
				<p id="lp-password-not-match" class="description lp-field-error-message hide-if-js"><?php esc_html_e( 'The new password does not match!', 'learnpress' ); ?></p>
			</div>
		</li>

		<?php do_action( 'learn-press/end-profile-change-password-fields', $profile ); ?>

	</ul>

	<?php do_action( 'learn-press/after-profile-change-password-fields', $profile ); ?>
	<p>
		<input type="hidden" name="save-profile-password" value="<?php echo wp_create_nonce( 'learn-press-save-profile-password' ); ?>">
	</p>
	<div class="btn-bottom-row">
		<button type="button" class="lp-button button-secondary" id="cancel-button"><?php esc_html_e( 'Cancel', 'learnpress' ); ?></button>
		<button class="lp-button" type="submit" name="submit" id="submit"><?php esc_html_e( 'Update Password', 'learnpress' ); ?></button>
	</div>
	

</form>

<script>
	document.addEventListener('DOMContentLoaded', function () {

	// Toggle Password Visibility
		document.querySelectorAll('.toggle-password').forEach(btn => {
			btn.addEventListener('click', function () {
				const input = document.querySelector(this.getAttribute('data-target'));
				const icon = this.querySelector('i');
				if (input.type === 'password') {
					input.type = 'text';
					icon.classList.remove('fa-eye-slash');
					icon.classList.add('fa-eye');
				} else {
					input.type = 'password';
					icon.classList.remove('fa-eye');
					icon.classList.add('fa-eye-slash');
				}
			});
		});

	// Form validation
		const form = document.querySelector('.learn-press-form');
		form.addEventListener('submit', function (e) {
			let isValid = true;

			form.querySelectorAll('.form-error').forEach(el => el.remove());

			const pass0 = document.getElementById('pass0');
			const pass1 = document.getElementById('pass1');
			const pass2 = document.getElementById('pass2');

			const showError = (field, message) => {
				isValid = false;
				const error = document.createElement('div');
				error.className = 'form-error';
				error.style.color = 'red';
				error.style.fontSize = '13px';
				error.innerText = message;
				const eyeIcon = field.parentNode.querySelector('.toggle-password');
				field.parentNode.insertBefore(error, eyeIcon);

			};

			if (!pass0.value.trim()) showError(pass0, 'Please enter current password');
			if (!pass1.value.trim()) showError(pass1, 'Please enter new password');
			if (!pass2.value.trim()) showError(pass2, 'Please confirm new password');

		// Password complexity check
			if (pass1.value.trim()) {
				const password = pass1.value;
				const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;
				if (!regex.test(password)) {
					showError(pass1, 'Password must contain at least 8 characters, one uppercase, one lowercase, one number, and one special character.');
				}
			}

		// Same password check
			if (pass0.value && pass1.value && pass0.value === pass1.value) {
				showError(pass1, 'New password must be different from the current password.');
			}

		// Match confirm password
			if (pass1.value && pass2.value && pass1.value !== pass2.value) {
				showError(pass2, 'Confirm password does not match new password.');
			}

			if (!isValid) e.preventDefault();
		});

	// Cancel button redirect
		document.getElementById('cancel-button').addEventListener('click', function () {
			<?php $user_login = urlencode($user->get_data('user_login'));
                $cancel_url = home_url("/lp-profile/{$user_login}/settings/profile/"); ?>
			window.location.href = '<?php echo $cancel_url; ?>';
		});
	});

</script>
