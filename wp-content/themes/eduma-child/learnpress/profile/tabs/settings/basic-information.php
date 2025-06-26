<?php
/**
 * Template for displaying editing basic information form of user in profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/settings/tabs/basic-information.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.1
 */

defined('ABSPATH') || exit();

$profile = LP_Profile::instance();
$user = $profile->get_user();

if (!isset($section)) {
    $section = 'basic-information';
}

$current_user_id = get_current_user_id();
$user_meta = get_userdata($current_user_id);
$user_roles = $user_meta->roles;
?>

<form method="post" id="custom-profile-form" enctype="multipart/form-data" class="learn-press-form new-custom-profile-cls-adding-here">
    <?php wp_nonce_field('custom_profile_save_action', 'custom_profile_nonce'); ?>

    <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
        <p style="color: green;">Profile updated successfully!</p>
    <?php endif; ?>

    <ul class="form-fields">

    <?php $image_url = $profile->get_upload_profile_src(); ?>
        <li class="form-field form-field__100">
            <label for="profile_image"><?php esc_html_e('Profile Image', 'learnpress'); ?></label>
            <div class="form-field-input">
                <?php if ($image_url): ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="Profile Image" width="100" height="100"
                        style="display:block;margin-bottom:10px;">
                <?php endif; ?>
                <input type="file" name="profile_image" id="profile_image" accept="image/*" class="regular-text">
            </div>
        </li>

        <li class="form-field form-field__first-name form-field__50">
            <label for="first_name"><?php esc_html_e('First name', 'learnpress'); ?></label>
            <div class="form-field-input">
                <input type="text" name="first_name" id="first_name"
                    value="<?php echo esc_attr(get_the_author_meta('first_name', $current_user_id)); ?>"
                    class="regular-text">
            </div>
        </li>

        <li class="form-field form-field__last-name form-field__50">
            <label for="last_name"><?php esc_html_e('Last name', 'learnpress'); ?></label>
            <div class="form-field-input">
                <input type="text" name="last_name" id="last_name"
                    value="<?php echo esc_attr(get_the_author_meta('last_name', $current_user_id)); ?>"
                    class="regular-text">
            </div>
        </li>
        <?php
        $current_user = wp_get_current_user();
        $user_email = $current_user->user_email;

        // echo $user_email;
        ?>
        <li class="form-field form-field__50">
            <label for="email"><?php esc_html_e('Email ID', 'learnpress'); ?></label>
            <div class="form-field-input">
                <input type="text" name="email" id="email" disabled value="<?php echo $user_email; ?>"
                    class="regular-text">
            </div>
        </li>

        <li class="form-field form-field__50">
            <label for="billing_phone"><?php esc_html_e('Mobile number', 'learnpress'); ?></label>
            <div class="form-field-input">
                <input type="text" name="billing_phone" id="billing_phone"
                    value="<?php echo esc_attr(get_user_meta($current_user_id, 'billing_phone', true)); ?>"
                    class="regular-text">
            </div>
        </li>

        <li class="form-field form-field__50">
            <label for="birth_date"><?php esc_html_e('Birth date', 'learnpress'); ?></label>
            <div class="form-field-input">
                <input type="date" name="birth_date" id="birth_date"
                    value="<?php echo esc_attr(get_user_meta($current_user_id, 'birth_date', true)); ?>"
                    class="regular-text">
            </div>
        </li>

        <?php if (in_array('subscriber', $user_roles)): ?>
            <li class="form-field form-field__50">
                <label for="grade"><?php esc_html_e('Grade', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="grade" id="grade"
                        value="<?php echo esc_attr(get_user_meta($current_user_id, 'grade', true)); ?>"
                        class="regular-text">
                </div>
            </li>

            <li class="form-field form-field__50">
                <label for="address"><?php esc_html_e('Address', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="address" id="address"
                        value="<?php echo esc_attr(get_user_meta($current_user_id, 'address', true)); ?>"
                        class="regular-text">
                </div>
            </li>

            <li class="form-field form-field__50">
                <label for="preferred_subjects"><?php esc_html_e('Preferred Subjects', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="preferred_subjects" id="preferred_subjects"
                        value="<?php echo esc_attr(get_user_meta($current_user_id, 'preferred_subjects', true)); ?>"
                        class="regular-text">
                </div>
            </li>

            <li class="form-field form-field__50">
                <label for="learning_style"><?php esc_html_e('Learning Style', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <select name="learning_style" id="learning_style" class="regular-text">
                        <?php
                        $options = ['Microlearning Learner', 'Auditory', 'Reading/Writing', 'Kinesthetic'];
                        $selected = get_user_meta($current_user_id, 'learning_style', true);
                        foreach ($options as $option) {
                            echo "<option value='" . esc_attr($option) . "'" . selected($selected, $option, false) . ">" . esc_html($option) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </li>

            <li class="form-field form-field__50">
                <label for="preferred_hours">Preferred Hours</label>
                <div class="form-field-input">
                    <input id="demo-time" name="preferred_hours" mbsc-input data-input-style="outline"
                        data-label-style="stacked" placeholder="e.g. 12:00 PM to 04:00 PM" autocomplete="off">
                </div>
            </li>

        <?php endif; ?>

        <?php if (in_array('lp_teacher', $user_roles)): ?>
            <li class="form-field form-field__50">
                <label for="experiance"><?php esc_html_e('Experiance', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="experiance" id="experiance"
                        value="<?php echo esc_attr(get_user_meta($current_user_id, 'experiance', true)); ?>"
                        class="regular-text">
                </div>
            </li>
            
            <li class="form-field form-field__50">
                <label for="address"><?php esc_html_e('Address', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="address" id="address"
                        value="<?php echo esc_attr(get_user_meta($current_user_id, 'address', true)); ?>"
                        class="regular-text">
                </div>
            </li>

            <li class="form-field form-field__50">
                <label for="subject_expertise"><?php esc_html_e('Subject Expertise', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="subject_expertise" id="subject_expertise"
                        value="<?php echo esc_attr(get_user_meta($current_user_id, 'subject_expertise', true)); ?>"
                        class="regular-text">
                </div>
            </li>

            <li class="form-field form-field__50">
                <label for="qualification"><?php esc_html_e('Qualification', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="qualification" id="qualification"
                        value="<?php echo esc_attr(get_user_meta($current_user_id, 'qualification', true)); ?>"
                        class="regular-text">
                </div>
            </li>

            <li class="form-field form-field__50">
                <label for="availability"><?php esc_html_e('Availability', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="availability" id="availability"
                        value="<?php echo esc_attr(get_user_meta($current_user_id, 'availability', true)); ?>"
                        class="regular-text">
                </div>
            </li>
        <?php endif; ?>

        <li class="form-field form-field__bio form-field__clear">
            <label for="description"><?php esc_html_e('Biographical Info', 'learnpress'); ?></label>
            <div class="form-field-input">
                <textarea name="description" id="description" rows="5"
                    cols="30"><?php echo esc_textarea(get_user_meta($current_user_id, 'description', true)); ?></textarea>
            </div>
        </li>
    </ul>

    <p>
        <button class="lp-button" type="submit"
            name="custom_profile_submit"><?php esc_html_e('Save changes', 'learnpress'); ?></button>
    </p>
</form>