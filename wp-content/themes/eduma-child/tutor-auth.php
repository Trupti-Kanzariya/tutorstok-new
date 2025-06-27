<?php
/**
 * Shortcodes for Tutor Registration and Login
 * Place this file in your theme or child theme and require it from functions.php
 */

// Tutor Registration Shortcode
function tutor_registration_form() {
    if ( is_user_logged_in() ) {
        return '<p>You are already logged in.</p>';
    }

    // Only process registration if form is submitted and on POST request
    if ( isset($_POST['tutor_register']) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        $username = sanitize_user($_POST['tutor_username']);
        $email = sanitize_email($_POST['tutor_email']);
        $password = $_POST['tutor_password'];

        $user_id = wp_create_user($username, $password, $email);
        if ( ! is_wp_error($user_id) ) {
            $user = new WP_User($user_id);
            $user->set_role('lp_teacher');
            echo '<p>Registration successful! <a href="/tutorstok-new/tutor-login-2">Login here</a>.</p>';
            return; // Prevent form from showing again
        } else {
            echo '<p>Error: ' . esc_html($user_id->get_error_message()) . '</p>';
        }
    }

    ob_start();
    ?>
    <style>
    /* Modern, clean form design with transparent background, no border/shadow, only input boxes white */
    .tutor-registration-form, .tutor-login-form {
        display: block !important;
        max-width: 400px;
        margin: 40px auto 0 auto;
        padding: 0;
        background: transparent;
        border-radius: 0;
        box-shadow: none;
        z-index: 10;
        position: relative;
    }
    .tutor-registration-form label, .tutor-login-form label {
        display: block !important;
        margin-bottom: 18px;
        font-weight: 400;
        color: #222;
        font-size: 16px;
        letter-spacing: 0.01em;
    }
    .tutor-registration-form input[type="text"],
    .tutor-registration-form input[type="email"],
    .tutor-registration-form input[type="password"],
    .tutor-login-form input[type="text"],
    .tutor-login-form input[type="email"],
    .tutor-login-form input[type="password"] {
        width: 100%;
        padding: 16px 20px;
        margin-top: 8px;
        margin-bottom: 20px;
        border: none;
        border-radius: 32px;
        font-size: 17px;
        background: #fff;
        color: #222;
        box-shadow: none;
        transition: box-shadow 0.2s;
    }
    .tutor-registration-form input[type="text"]:focus,
    .tutor-registration-form input[type="email"]:focus,
    .tutor-registration-form input[type="password"]:focus,
    .tutor-login-form input[type="text"]:focus,
    .tutor-login-form input[type="email"]:focus,
    .tutor-login-form input[type="password"]:focus {
        outline: none;
        box-shadow: 0 0 0 2px #1abc9c33;
    }
    .tutor-registration-form input[type="submit"],
    .tutor-login-form input[type="submit"] {
        background: #1abc9c;
        color: #fff;
        border: none;
        border-radius: 32px;
        padding: 16px 0;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-top: 8px;
        transition: background 0.2s;
        box-shadow: none;
    }
    .tutor-registration-form input[type="submit"]:hover,
    .tutor-login-form input[type="submit"]:hover {
        background: #159c82;
    }
    .tutor-registration-form .tutor-form-title,
    .tutor-login-form .tutor-form-title {
        text-align: center;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 18px;
        color: #222;
    }
    .tutor-registration-form .tutor-form-desc,
    .tutor-login-form .tutor-form-desc {
        text-align: center;
        font-size: 1rem;
        color: #888;
        margin-bottom: 28px;
    }
    </style>
    <form method="post" class="tutor-registration-form">
        <div class="tutor-form-title">Tutor Sign up</div>
        <div class="tutor-form-desc">Please help us with your details so we can create your account and get things going for you.</div>
        <label>Email Address
            <input type="email" name="tutor_email" required placeholder="Email Address">
        </label>
        <label>Username
            <input type="text" name="tutor_username" required placeholder="Username">
        </label>
        <label>Password
            <input type="password" name="tutor_password" required placeholder="Password">
        </label>
        <input type="submit" name="tutor_register" value="Register As Tutor">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('tutor_registration', 'tutor_registration_form');

// Tutor Login Shortcode
function tutor_login_form() {
    if ( is_user_logged_in() ) {
        return '<p>You are already logged in.</p>';
    }

    // Only process login if form is submitted and on POST request
    if ( isset($_POST['tutor_login_submit']) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        $creds = array(
            'user_login'    => $_POST['tutor_login'],
            'user_password' => $_POST['tutor_password'],
            'remember'      => true
        );
        $user = wp_signon($creds, false);
        if ( ! is_wp_error($user) ) {
            if ( in_array('lp_teacher', (array) $user->roles) ) {
                wp_redirect('/tutor-dashboard'); // Change to your dashboard URL
                exit;
            } else {
                echo '<p>You are not a tutor.</p>';
            }
        } else {
            echo '<p>Login failed: ' . esc_html($user->get_error_message()) . '</p>';
        }
    }

    ob_start();
    ?>
    <style>
    /* Modern, clean form design with transparent background, no border/shadow, only input boxes white */
    .tutor-login-form {
        display: block !important;
        max-width: 400px;
        margin: 40px auto 0 auto;
        padding: 0;
        background: transparent;
        border-radius: 0;
        box-shadow: none;
        z-index: 10;
        position: relative;
    }
    .tutor-login-form label {
        display: block !important;
        margin-bottom: 18px;
        font-weight: 400;
        color: #222;
        font-size: 16px;
        letter-spacing: 0.01em;
    }
    .tutor-login-form input[type="text"],
    .tutor-login-form input[type="email"],
    .tutor-login-form input[type="password"] {
        width: 100%;
        padding: 16px 20px;
        margin-top: 8px;
        margin-bottom: 20px;
        border: none;
        border-radius: 32px;
        font-size: 17px;
        background: #fff;
        color: #222;
        box-shadow: none;
        transition: box-shadow 0.2s;
    }
    .tutor-login-form input[type="text"]:focus,
    .tutor-login-form input[type="email"]:focus,
    .tutor-login-form input[type="password"]:focus {
        outline: none;
        box-shadow: 0 0 0 2px #1abc9c33;
    }
    .tutor-login-form input[type="submit"] {
        background: #1abc9c;
        color: #fff;
        border: none;
        border-radius: 32px;
        padding: 16px 0;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-top: 8px;
        transition: background 0.2s;
        box-shadow: none;
    }
    .tutor-login-form input[type="submit"]:hover {
        background: #159c82;
    }
    .tutor-login-form .tutor-form-title {
        text-align: center;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 18px;
        color: #222;
    }
    .tutor-login-form .tutor-form-desc {
        text-align: center;
        font-size: 1rem;
        color: #888;
        margin-bottom: 28px;
    }
    </style>
    <form method="post" class="tutor-login-form">
        <div class="tutor-form-title">Tutor Login</div>
        <div class="tutor-form-desc">Please enter your details to log in as a tutor.</div>
        <label>Username or Email
            <input type="text" name="tutor_login" required placeholder="Username or Email">
        </label>
        <label>Password
            <input type="password" name="tutor_password" required placeholder="Password">
        </label>
        <input type="submit" name="tutor_login_submit" value="Login As Tutor">
        <div style="text-align:center;margin-top:18px;font-size:15px;">
            New to the platform? <a href="/tutor-registration" style="color:#1abc9c;text-decoration:underline;">Sign Up</a>
        </div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('tutor_login', 'tutor_login_form');
