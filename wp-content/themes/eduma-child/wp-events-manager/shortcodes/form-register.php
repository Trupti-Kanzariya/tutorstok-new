<?php
/**
 * The Template for displaying shortcode form register.
 *
 * Override this template by copying it to yourtheme/wp-events-manager/shortcodes/form-register.php
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
?>

<form name="event_auth_register_form" action="" method="post" class="event-auth-form" novalidate>
<!-- 
    <p class="form-row form-required">
        <label for="user_login"><?php _e( 'Username', 'wp-events-manager' ); ?><span class="required">*</span></label>
        <input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr( ! empty( $_POST['user_login'] ) ? sanitize_text_field( $_POST['user_login'] ) : '' ); ?>" size="20" />
    </p> -->

    <!-- Section 2: Personal Information -->
    <h3>Account Information</h3>
    <p class="form-row form-required">
        <label for="user_email"><?php _e( 'Email', 'wp-events-manager' ); ?><span class="required">*</span></label>
        <input type="email" name="user_email" id="user_email" class="input" value="<?php echo esc_attr( ! empty( $_POST['user_email'] ) ? sanitize_text_field( $_POST['user_email'] ) : '' ); ?>" size="25" />
    </p>

    <p class="form-row form-required">
        <label for="user_pass"><?php _e( 'Password', 'wp-events-manager' ); ?><span class="required">*</span></label>
        <input type="password" name="user_pass" id="user_pass" class="input" value="" size="25" maxlength="32"/>
        <span class="toggle-password" data-target="#user_pass"><i class="fa fa-eye-slash"></i></span>
    </p>

    <p class="form-row form-required">
        <label for="confirm_password"><?php _e( 'Confirm Password', 'wp-events-manager' ); ?><span class="required">*</span></label>
        <input type="password" name="confirm_password" id="confirm_password" class="input" value="" size="25" maxlength="32"/></label>
        <span class="toggle-password" data-target="#confirm_password"><i class="fa fa-eye-slash"></i></span>
    </p>
    <!-- Section 2: Personal Information -->
    <h3>Personal Information</h3>

    <p class="form-row form-required">
        <label for="student_full_name"><?php _e( 'Student Full Name', 'wp-events-manager' ); ?><span class="required">*</span></label>
        <input type="text" name="student_full_name" id="student_full_name" class="input" placeholder="Student Full Name" value="<?php echo esc_attr($_POST['student_full_name'] ?? ''); ?>"  maxlength="50"/>
    </p>

    <p class="form-row form-required">
        <label for="parent_name">Parent/Guardian Full Name <span class="required">*</span></label>
        <input type="text" name="parent_name" id="parent_name" class="input" placeholder="Parent/Guardian Full Name" value="<?php echo esc_attr($_POST['parent_name'] ?? ''); ?>" maxlength="50"/>
    </p>

    <p class="form-row form-required">
        <label for="phone">Mobile Number <span class="required">*</span></label>
        <input type="text" name="phone" id="phone" class="input" placeholder="Mobile Number" value="<?php echo esc_attr($_POST['phone'] ?? ''); ?>" maxlength="15"/>
    </p>

    <p class="form-row">
        <label for="dob">Student Date of Birth</label>
        <input type="date" name="dob" id="dob" class="input" placeholder="Student Date of Birth" value="<?php echo esc_attr($_POST['dob'] ?? ''); ?>" 
        max="<?php echo date('Y-m-d'); ?>" />
    </p>



    <p class="form-row">
        <label for="grade">Student Grade Level</label>
        <input type="text" name="grade" id="grade" class="input" placeholder="Student Grade Level" value="<?php echo esc_attr($_POST['grade'] ?? ''); ?>" maxlength="10"/>
    </p>

    <p class="form-row">
        <label for="address">Address</label>
        <textarea name="address" id="address" class="input" placeholder="Address" maxlength="255"><?php echo esc_textarea($_POST['address'] ?? ''); ?></textarea>
    </p>  

    <!-- user role start here -->
    <p class="form-row form-required">
        <h3>User Role</h3>
        <div class="user-role-cls">
        <label for="user_type"><?php _e( 'User Type', 'wp-events-manager' ); ?><span class="required">*</span></label>
        <select name="user_type" id="user_type" required>
            <option value="">Select User Role</option>
            <option value="student" <?php selected($_POST['user_type'] ?? '', 'student'); ?>>Student</option>
            <option value="parent" <?php selected($_POST['user_type'] ?? '', 'parent'); ?>>Parent</option>
        </select>
        </div>
    </p>
    <!-- user role over here -->

    <!-- Section 3: Preferences -->
    <h3>Preferences</h3>

    <p class="form-row">
        <span>Preferred Subjects</span>
        <div class="form-cls-register">
        <?php
        $preferred_subjects = get_option('custom_preferred_subjects', []);
        foreach ($preferred_subjects as $subject) {
            $subject = trim($subject);
            echo '<div class="for-all-line">';
            echo '<input type="checkbox" name="preferred_subjects[]" value="' . esc_attr($subject) . '" class="learning-style-checkbox"> ';
            echo '<span class="learning-style-text" style="cursor: pointer;">' . esc_html($subject) . '</span><br>';
            echo '</div>';

        }
        ?>
        </div>
    </p>

    <p class="form-row">
        <span>Learning Style Preference</span>
        <div class="form-cls-register">
      <?php
        $learning_styles = get_option('custom_learning_styles', []);
        foreach ($learning_styles as $style) {
            $style = trim($style);
            echo '<div class="for-all-line">';
            echo '<input type="checkbox" name="learning_style[]" value="' . esc_attr($style) . '" class="learning-style-checkbox"> ';
            echo '<span class="learning-style-text" style="cursor: pointer;">' . esc_html($style) . '</span><br>';
            echo '</div>';
        }
        ?>
        </div>
    </p>

    <p class="form-row">
        <span>Preferred Tutoring Hours</span>
        <div class="form-cls-register">
        <?php
        $tutoring_hours = get_option('custom_tutoring_hours', []);
        foreach ($tutoring_hours as $hour) {
            $hour = trim($hour);
            echo '<div class="for-all-line">';
            echo '<input type="checkbox" name="tutoring_hours[]" value="' . esc_attr($hour) . '" class="learning-style-checkbox"> ';
            echo '<span class="learning-style-text" style="cursor: pointer;">' . esc_html($hour) . '</span><br>';
            echo '</div>';
        }
        ?>
        </div>
    </p>


    <!-- Section 4: Additional Details -->
    <h3>Additional Details</h3>

    <p class="form-row">
        <label for="additional_info">Any other details</label>
        <textarea name="additional_info" id="additional_info" class="input" placeholder="Any other details"><?php echo esc_textarea($_POST['additional_info'] ?? ''); ?></textarea>
    </p>

    <?php do_action( 'event_auth_register_form' ); ?>

    <?php $send_notify = wpems_get_option( 'register_notify', true ); ?>
    <?php if ( $send_notify ) : ?>
        <p id="reg_passmail" class="form-row">
            <?php _e( 'Registration confirmation will be emailed to you.', 'wp-events-manager' ); ?>
        </p>
    <?php endif; ?>

    <p class="submit form-row">
        <input type="hidden" name="redirect_to" value="<?php echo esc_attr( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>" />
        <?php wp_nonce_field( 'auth-reigter-nonce', 'auth-nonce' ); ?>
        <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Create Account', 'wp-events-manager' ); ?>" />

    </p>

</form>

<p id="nav">
    <a href="<?php echo esc_url( wpems_login_url() ); ?>"><?php _e( 'Log in', 'wp-events-manager' ); ?></a> |
    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found', 'wp-events-manager' ); ?>"><?php _e( 'Forgot password?', 'wp-events-manager' ); ?></a>
</p>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var texts = document.querySelectorAll('.learning-style-text');
    texts.forEach(function(text) {
        text.addEventListener('click', function() {
            var checkbox = text.previousElementSibling;
            checkbox.checked = !checkbox.checked;
        });
    });
});
  jQuery(document).ready(function($) {

    $('#phone').on('input', function () {
        // Allow only digits, space, +, -, (, )
        this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
    });

    $(document).on('click', '.toggle-password', function () {
        var input = $($(this).data('target'));
        var icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });

    
    $('form[name="event_auth_register_form"]').on('submit', function(e) {
        var valid = true;
        var email = $('#user_email').val().trim();
        var pass = $('#user_pass').val();
        var confirm = $('#confirm_password').val();
        var phone = $('#phone').val().trim();
        var fullName = $('#student_full_name').val().trim();
        var dob = $('#dob').val().trim();
        var grade = $('#grade').val().trim();
        var address = $('#address').val().trim();

        $('.form-row').removeClass('error');
        $('.form-row .error-message').remove();

    // Email required + valid
        
        if (email === '' || !/^(?!.*\.\.)(?!\.)(?!.*\.$)(?!.*\.-)(?!.*-@)[a-zA-Z0-9]+([._%+-]?[a-zA-Z0-9]+)*@[a-zA-Z0-9]+(-?[a-zA-Z0-9]+)*(\.[a-zA-Z]{2,})+$/
            .test(email)) {
            $('#user_email').closest('.form-row').addClass('error')
        .append('<span class="error-message">Please enter a valid email.</span>');
        valid = false;
    }

    if (pass === '') {
        $('#user_pass').closest('.form-row').addClass('error')
        .append('<span class="error-message">Password is required.</span>');
        valid = false;
    }else if (pass.length < 8 || pass.length > 32) {
        $('#user_pass').closest('.form-row').addClass('error')
        .append('<span class="error-message">Password must be between 8 and 32 characters.</span>');
        valid = false;
    } else {
        const upperCasePattern = /[A-Z]/;
        const lowerCasePattern = /[a-z]/;
        const specialCharPattern = /[!@#$%^&*(),.?":{}|<>]/;

        if (!upperCasePattern.test(pass) || !lowerCasePattern.test(pass) || !specialCharPattern.test(pass)) {
            $('#user_pass').closest('.form-row').addClass('error')
            .append('<span class="error-message">Password must contain at least one uppercase letter, one lowercase letter, and one special character.</span>');
            valid = false;
        }
    }

    // Confirm password required
    if (confirm === '') {
        $('#confirm_password').closest('.form-row').addClass('error')
        .append('<span class="error-message">Confirm Password is required.</span>');
        valid = false;
    } else if (pass !== confirm) {
        // Only check match if confirm password is not empty
        $('#confirm_password').closest('.form-row').addClass('error')
        .append('<span class="error-message">Passwords do not match.</span>');
        valid = false;
    }

    const phonePattern = /^[0-9+\-\s()]+$/;
    const cleanedPhone = phone.replace(/[^\d]/g, '');

    if (
        phone === '' ||
        !phonePattern.test(phone) ||  // Check if it matches the valid pattern
        cleanedPhone.length < 6 ||     // Min length of 6 digits
        cleanedPhone.length > 15      // Max length of 15 digits
        ) {
        $('#phone').closest('.form-row').addClass('error')
    .append('<span class="error-message">Please enter a valid phone number</span>');
    valid = false;
}
    // Full name required
if (fullName === '') {
    $('#student_full_name').closest('.form-row').addClass('error')
    .append('<span class="error-message">Full name is required.</span>');
    valid = false;
}
if (dob === '') {
    $('#dob').closest('.form-row').addClass('error')
    .append('<span class="error-message">Birthdate is required</span>');
    valid = false;
}
if (grade === '') {
    $('#grade').closest('.form-row').addClass('error')
    .append('<span class="error-message">Please select a grade level.</span>');
    valid = false;
}
if (address === '') {
    $('#address').closest('.form-row').addClass('error')
    .append('<span class="error-message">Address cannot be empty.</span>');
    valid = false;
}

if (!valid) {
    e.preventDefault();
}
});

});
</script>
