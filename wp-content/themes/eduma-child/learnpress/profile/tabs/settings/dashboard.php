<?php
/**
 * Template for displaying editing basic information form of user in profile page.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/settings/tabs/basic-information.php.
 */

//  Profile code 
$profile = LP_Profile::instance();
$user = $profile->get_user();
$user_id = $user->get_id();
$user_obj = get_userdata($user_id);
$user_roles = $user_obj->roles;

$first_name = get_user_meta($user_id, 'first_name', true);
$student_full_name = get_user_meta($user_id, 'student_full_name', true);
$parent_name = get_user_meta($user_id, 'parent_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$email = $user->get_data('email');
$profile_img = $user->get_data('uploaded_profile_src');

// Common Fields
$billing_phone = get_user_meta($user_id, 'billing_phone', true);
$phone = get_user_meta($user_id, 'phone', true);

$birth_date = get_user_meta($user_id, 'dob', true);

$teacher_birth_date = get_user_meta($user_id, 'birth_date', true);
$address = get_user_meta($user_id, 'address', true);
$description = get_user_meta($user_id, 'description', true);

// Student-Specific
$student_class = get_user_meta($user_id, 'student_class', true);
$grade = get_user_meta($user_id, 'grade', true);
$preferred_subjects = get_user_meta($user_id, 'preferred_subjects', true);
$learning_style = get_user_meta($user_id, 'learning_style', true);
$preferred_hours = get_user_meta($user_id, 'tutoring_hours', true);


// Tutor-Specific
$experience = get_user_meta($user_id, 'experiance', true);
$subject_expertise = get_user_meta($user_id, 'subject_expertise', true);
$qualification = get_user_meta($user_id, 'qualification', true);
$availability = get_user_meta($user_id, 'availability', true);

// Determine role
$is_student = in_array('subscriber', $user_roles);
$is_tutor = in_array('lp_teacher', $user_roles);
$is_parent = in_array('parent', $user_roles);

// Calculate profile completion for tutor (example: adjust as needed)
$fields_to_check = [
    !empty(trim($profile_img)),
    !empty(trim($first_name)),
    !empty(trim($last_name)),
    !empty(trim($email)),
    !empty(trim($billing_phone)),
    !empty(trim($birth_date)),
    !empty(trim($address)),
    !empty(trim($experience)),
    !empty(trim($subject_expertise)),
    !empty(trim($qualification)),
    !empty(trim($availability)),
    !empty(trim($description))
];

$total_fields = count($fields_to_check);
$filled_fields = count(array_filter($fields_to_check)); // cleaner logic

$tutor_completion_percentage = $total_fields > 0 ? round(($filled_fields / $total_fields) * 100) : 0;

// Calculate profile completion for student (example: adjust as needed)
$student_fields_to_check = [
    !empty(trim($profile_img)),
    !empty(trim($first_name)),
    !empty(trim($email)),
    !empty(trim($billing_phone)),
    !empty(trim($birth_date)),
    !empty(trim($grade)),
    !empty(trim($address)),
    !empty(trim($preferred_subjects)),
    !empty(trim($learning_style)),
    !empty(trim($preferred_hours)),
    !empty(trim($description))
];

$student_total_fields = count($student_fields_to_check);
$student_filled_fields = count(array_filter($student_fields_to_check)); // cleaner logic

$student_completion_percentage = $student_total_fields > 0 ? round(($student_filled_fields / $student_total_fields) * 100) : 0;
$profile_class = 'lp-head-cls-adding-here-all' . (empty($profile_img) ? ' no-profile-img-availe' : '');
$formatted_birth_date = '';
if (!empty($teacher_birth_date)) {
    $date = DateTime::createFromFormat('Y-m-d', $teacher_birth_date); // assumes saved format is 'Y-m-d'
    if ($date) {
        $formatted_birth_date = $date->format('d-m-Y');
    } else {
        $formatted_birth_date = $teacher_birth_date; // fallback
    }
}
if (!empty($birth_date)) {
    $date = DateTime::createFromFormat('Y-m-d', $birth_date);
    $birth_date_formatted = $date ? $date->format('d-m-Y') : $birth_date;
} else {
    $birth_date_formatted = '';
}

?>
<div class="profiles">
    <div class="blue-light-cls-adding-here">
        <div class="spacing-cls-adding-here"></div>
        <div class="lp-profile-overview-card learn-press-form">
            <div class="<?php echo esc_attr($profile_class); ?>">
                <div class="lp-head" style="display: flex; align-items: flex-end; gap: 0px 20px; width: 92%;">
                    <?php if ($profile_img): ?>
                        <div class="dashboard-cls-adding-profile">
                            <img src="<?php echo esc_url($profile_img); ?>" alt="Profile Picture" height="50px" width="50px"
                                class="dashboard-img-cls-adding-here">
                        </div>
                    <?php endif; ?>
                    <?php if ( in_array('lp_teacher', $user_roles) ) : ?>
                        <div style="flex:1;" class="progress-bar-cls-adding-here tutor-progress-bar">
                            <div class="lp-profile-progress"
                                style="background: #D9D9D9; height: 30px; border-radius: 20px;">
                                <div class="lp-profile-progress-bar"
                                    style="background: #0FA8A4; height: 100%; width: <?php echo esc_attr($tutor_completion_percentage); ?>%; border-radius: 20px;">
                                </div>
                                <span class="profile-complete-cls-adding-here"><?php echo esc_html($tutor_completion_percentage); ?>% Profile Complete</span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ( in_array('subscriber', $user_roles) ) : ?>
                        <div style="flex:1;" class="progress-bar-cls-adding-here student-progress-bar">
                            <div class="lp-profile-progress"
                                style="background: #D9D9D9; height: 30px; border-radius: 20px;">
                                <div class="lp-profile-progress-bar"
                                    style="background: #0FA8A4; height: 100%; width: <?php echo esc_attr($student_completion_percentage); ?>%; border-radius: 20px;">
                                </div>
                                <span class="profile-complete-cls-adding-here"><?php echo esc_html($student_completion_percentage); ?>% Profile Complete</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
                $user_login = urlencode($user->get_data('user_login'));
                $edit_url = home_url("/lp-profile/{$user_login}/settings/profile/");
                ?>
                <a href="<?php echo esc_url($edit_url); ?>" title="Edit Basic Information"
                    class="dashboard-php-cls-adding-here-all">
                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M3.25 22.1738C3.25 21.7596 3.58579 21.4238 4 21.4238H20C20.4142 21.4238 20.75 21.7596 20.75 22.1738C20.75 22.588 20.4142 22.9238 20 22.9238H4C3.58579 22.9238 3.25 22.588 3.25 22.1738Z"
                            fill="white" />
                        <path
                            d="M11.5201 15.1028L11.5201 15.1028L17.4368 9.18608C16.6315 8.85093 15.6777 8.30039 14.7757 7.39837C13.8736 6.49621 13.323 5.54229 12.9879 4.73695L7.07106 10.6537L7.07101 10.6538C6.60932 11.1155 6.37846 11.3464 6.17992 11.6009C5.94571 11.9012 5.74491 12.2261 5.58107 12.5698C5.44219 12.8613 5.33894 13.171 5.13245 13.7905L4.04356 17.0572C3.94194 17.362 4.02128 17.6981 4.2485 17.9253C4.47573 18.1525 4.81182 18.2319 5.11667 18.1303L8.38334 17.0414C9.00281 16.8349 9.31256 16.7316 9.60398 16.5928C9.94775 16.4289 10.2727 16.2281 10.5729 15.9939C10.8275 15.7954 11.0584 15.5645 11.5201 15.1028Z"
                            fill="white" />
                        <path
                            d="M19.0786 7.54427C20.3071 6.31571 20.3071 4.32381 19.0786 3.09525C17.85 1.86669 15.8581 1.86669 14.6296 3.09525L13.9199 3.80488C13.9296 3.83423 13.9397 3.86398 13.9502 3.89411C14.2103 4.64383 14.701 5.62663 15.6243 6.54984C16.5475 7.47306 17.5303 7.96382 18.28 8.22392C18.31 8.23433 18.3396 8.24436 18.3688 8.25404L19.0786 7.54427Z"
                            fill="white" />
                    </svg>Edit
                </a>
            </div>
            <div class="lp-info-grid">
                 <?php if ($is_tutor): ?>
                    <div class="lp-info-item"><strong>First Name:</strong> <?php echo esc_html($first_name); ?></div>
                 <?php endif; ?>
                 <?php if ($is_student): ?>
                    <div class="lp-info-item"><strong>Full Name:</strong> <?php echo esc_html($student_full_name); ?></div>
                 <?php endif; ?>
                 <?php if ($is_parent): ?>
                    <div class="lp-info-item"><strong>Full Name:</strong> <?php echo esc_html($parent_name); ?></div>
                 <?php endif; ?>
                 

                <?php if ($is_tutor): ?>
                    <div class="lp-info-item"><strong>Last Name:</strong> <?php echo esc_html($last_name); ?></div>
                <?php endif; ?>

                <div class="lp-info-item"><strong>Email ID:</strong> <?php echo esc_html($email); ?></div>
                <div class="lp-info-item"><strong>Mobile Number:</strong> <?php echo esc_html($phone); ?></div>
                <?php if ($is_parent): ?>
                    <div class="lp-info-item"><strong>Date of Birth:</strong> <?php echo esc_html($birth_date_formatted); ?></div>
                <?php endif; ?>
               <?php if ($is_student): ?>
                    <div class="lp-info-item"><strong>Date of Birth:</strong> <?php echo esc_html($birth_date_formatted); ?></div>
              
                <?php endif; ?>
                <?php if ($is_tutor): ?>
                    <div class="lp-info-item"><strong>Date of Birth:</strong> <?php echo esc_html($formatted_birth_date); ?></div>
                <?php endif; ?>
                <?php if ($is_student): ?>
                    <div class="lp-info-item"><strong>Grade:</strong><?php echo esc_html($grade); ?></div>
                <?php endif; ?>
                <?php if ($is_tutor): ?>
                <div class="lp-info-item"><strong>Address:</strong> <?php echo esc_html($address); ?></div>
                <?php endif; ?>
            </div>

            <?php if ($is_student): ?>
                <div class="lp-info-grid new-student-tutor-cls-adding">
                    <div class="lp-info-item"><strong>Preferred Subjects:</strong>
                        <?php echo esc_html($preferred_subjects); ?>
                    </div>
                    <div class="lp-info-item"><strong>Learning Style:</strong> <?php echo esc_html($learning_style); ?>
                    </div>
                    <div class="lp-info-item"><strong>Preferred Hours:</strong> <?php echo esc_html($preferred_hours); ?>
                    </div>
                </div>
            <?php elseif ($is_tutor): ?>
                <div class="lp-info-grid new-student-tutor-cls-adding">
                    <div class="lp-info-item"><strong>Experience:</strong> <?php echo esc_html($experience); ?></div>
                    <div class="lp-info-item"><strong>Subject Expertise:</strong>
                        <?php echo esc_html($subject_expertise); ?>
                    </div>
                    <div class="lp-info-item"><strong>Qualification:</strong> <?php echo esc_html($qualification); ?></div>
                    <div class="lp-info-item"><strong>Availability:</strong> <?php echo esc_html($availability); ?></div>
                </div>
            <?php endif; ?>
            <div class="lp-bio">
                <strong>Biographical Info:</strong>
                <?php echo esc_html($description); ?>
            </div>
        </div>
    </div>
</div>

<!-- Basic information code  -->

<?php
$current_user_id = get_current_user_id();
$user_meta = get_userdata($current_user_id);
$user_roles = $user_meta->roles;
?>

<div class="basic-information basic-dashboard-profile-cls-adding" style="display: none;">
    <style type="text/css">
 
        .user-listing-cls-adding-dashboard {
            display: grid;
            grid-template-columns: repeat(2, 2fr);
        }

        .user-listing-cls-adding-dashboard .form-field {
            width: 100% !important;
        }

        .textarea-cls-adding-here-all {
            width: 100%;
        }

        .flexing-cancel-reject-btn-cls-adding-here {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 15px;
        }

        .basic-dashboard-profile-cls-adding .new-learning-style-cls-adding .regular-text {
            background: rgb(34 34 34 / 10%) !important;
            border-radius: 50px !important;
            padding: 15px 20px 15px 20px;
            font-size: 18px;
            font-weight: 400;
            line-height: 20px;
            font-family: "Urbanist", Sans-serif !important;
            color: rgb(34 34 34 / 80%) !important;
            width: 100%;
        }

        .basic-dashboard-profile-cls-adding .textarea-cls-adding-here-all {
            background: rgb(34 34 34 / 10%) !important;
            border-radius: 20px !important;
            padding: 18px 20px 18px 20px;
            font-size: 18px;
            font-weight: 400;
            line-height: 20px;
            font-family: "Urbanist", Sans-serif !important;
            color: rgb(34 34 34 / 80%) !important;
            width: 100%;
            resize: none;
            margin-bottom: 0px;
        }

        .new-profile-form-img-cls-adding-here {
            width: 120px;
            height: 120px;
            position: relative;
        }

        #new-profile-img-cls-adding-here {
            position: relative;
            margin-bottom: 20px;
        }

        .new-profile-form-img-cls-adding-here::before {
            background: rgba(15, 168, 164, 0.5);
            border-radius: 20px;
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 120px;
            height: 120px;
        }

        #new-profile-img-cls-adding-here .profile-img-type-cls-adding-here {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 11;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            margin-bottom: 0px;
            border-radius: 0px !important;
            cursor: pointer;
        }

        .new-profile-form-img-cls-adding-here::after {
            content: '';
            background: url(../../../../wp-content/uploads/2025/04/edit-white.svg);
            background-size: contain;
            background-repeat: no-repeat;
            width: 24px;
            height: 24px;
            position: absolute;
            left: 48px;
            top: 48px;
        }

        .new-profile-form-img-cls-adding-here .img-choose-file-cls-adding-here {
            width: 120px;
            height: 120px;
            margin-bottom: 0px !important;
            border-radius: 20px;
            object-fit: cover;
        }

        .new-time-picker-cls-adding-here .new-time-picker-cls-adding-here-input {
            width: 100%;
        }

        .new-time-picker-cls-adding-here-input::-webkit-calendar-picker-indicator {
            opacity: 0;
            display: block;
            position: absolute;
            right: 1rem;
            width: 1.25rem;
            height: 100%;
            cursor: pointer;
        }

        .new-time-picker-cls-adding-here-input {
            -moz-appearance: none;
            appearance: none;
            -webkit-appearance: none;
        }

        .new-time-picker-cls-adding-here-input::before{
            content: '';
            background: url(../../../../wp-content/uploads/2025/04/calendar-teal.svg);
            position: absolute;
            right: 1rem;
            top: 50%;
            width: 24px;
            height: 24px;
            pointer-events: none;
            color: rgb(255 255 255 / 50%);
            transform: translate(0%, -50%);
            background-size: contain;
        }

        .new-select-option-cls-adding-here {
            -moz-appearance: none;
            appearance: none;
            -webkit-appearance: none;
        }

        .new-learning-style-cls-adding:before{
            content: '';
            background: url(../../../../wp-content/uploads/2025/04/down-arrow.svg);
            position: absolute;
            right: 1rem;
            top: 50%;
            width: 18px;
            height: 12px;
            pointer-events: none;
            color: rgb(255 255 255 / 50%);
            transform: translate(0%, -50%);
            background-repeat: no-repeat;
            background-size: contain;
        }

        @media only screen and (max-width: 767px){
            .new-learning-style-cls-adding:before {
                width: 14px;
                height: 8px;
            }
            
            .user-listing-cls-adding-dashboard {
                grid-template-columns: repeat(1, 1fr);
            }

            .user-listing-cls-adding-dashboard .form-field {
                padding-left: 0px !important;
                padding-right: 0px !important;
            }

            .new-time-picker-cls-adding-here-input::before {
                width: 20px;
                height: 20px;
            }
        }

    </style>



    <form method="post" id="custom-profile-form" enctype="multipart/form-data" class="learn-press-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="custom_profile_nonce" value="<?php echo wp_create_nonce('custom_profile_save_action'); ?>" />

           <input type="hidden" name="action" value="update_custom_profile">
        <?php wp_nonce_field('custom_profile_save_action', 'custom_profile_nonce'); ?>

        <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
            <p style="color: green;">Profile updated successfully!</p>
        <?php endif; ?>

        <?php $image_url = $profile->get_upload_profile_src(); ?>
            <div class="form-field form-field__100 new-profile-main-managed-type-cls-adding-here" id="new-profile-img-cls-adding-here">
                <label><?php esc_html_e('Profile Image', 'learnpress'); ?></label>
                <div class="form-field-input new-profile-form-img-cls-adding-here">
                    <?php if ($image_url): ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="Profile Images" width="100" height="100"
                            style="display:block;margin-bottom:10px;" class="img-choose-file-cls-adding-here">
                    <?php endif; ?>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*" class="regular-text profile-img-type-cls-adding-here">
                </div>
            </div>

        <ul class="form-fields user-listing-cls-adding-dashboard">


             <?php if ($is_tutor): ?>
                     <li class="form-field form-field__first-name form-field__50">
                        <label for="first_name"><?php esc_html_e('First name', 'learnpress'); ?></label>
                        <div class="form-field-input">
                            <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr(get_user_meta($current_user_id, 'first_name', true)); ?>" maxlength="40">

                        </div>
                    </li>
             <?php endif; ?>
             <?php if ($is_student): ?>
                <li class="form-field form-field__first-name form-field__50">
                        <label for="student_full_name"><?php esc_html_e('First name', 'learnpress'); ?></label>
                        <div class="form-field-input">
                            <input type="text" name="student_full_name" id="student_full_name" value="<?php echo esc_attr(get_user_meta($current_user_id, 'student_full_name', true)); ?>" maxlength="40">

                        </div>
                    </li>
             <?php endif; ?>
             <?php if ($is_parent): ?>
                 <li class="form-field form-field__first-name form-field__50">
                        <label for="parent_name"><?php esc_html_e('First name', 'learnpress'); ?></label>
                        <div class="form-field-input">
                            <input type="text" name="parent_name" id="parent_name" value="<?php echo esc_attr(get_user_meta($current_user_id, 'parent_name', true)); ?>" maxlength="40">

                        </div>
                    </li>
             <?php endif; ?>



           <!--  
            <li class="form-field form-field__first-name form-field__50">
                <label for="first_name"><?php esc_html_e('First name', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr(get_user_meta($current_user_id, 'first_name', true)); ?>">

                </div>
            </li> -->

            <li class="form-field form-field__last-name form-field__50">
                <label for="last_name"><?php esc_html_e('Last name', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="last_name" id="last_name"
                        value="<?php echo esc_attr(get_the_author_meta('last_name', $current_user_id)); ?>"
                        class="regular-text" maxlength="40">
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
                        class="regular-text" maxlength="100">
                </div>
            </li>

            <li class="form-field form-field__50">
                <label for="phone"><?php esc_html_e('Mobile number', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <input type="text" name="phone" id="phone"
                        value="<?php echo esc_attr(get_user_meta($current_user_id, 'phone', true)); ?>"
                        class="regular-text" pattern="[0-9]{10}" maxlength="10">
                </div>
            </li>

            <?php if (in_array('lp_teacher', $user_roles)): ?>
                <li class="form-field form-field__50 new-time-picker-cls-adding-here">
                    <label for="birth_date"><?php esc_html_e('Birth date', 'learnpress'); ?></label>
                    <div class="form-field-input">
                        <input type="date" name="birth_date" id="birth_date"
                            value="<?php echo esc_attr(get_user_meta($current_user_id, 'birth_date', true)); ?>"
                            class="regular-text new-time-picker-cls-adding-here-input">
                    </div>
                </li>
            <?php else: ?>
                <li class="form-field form-field__50 new-time-picker-cls-adding-here">
                    <label for="dob"><?php esc_html_e('Birth date', 'learnpress'); ?></label>
                    <div class="form-field-input">
                        <input type="date" name="dob" id="birth_date"
                            value="<?php echo esc_attr(get_user_meta($current_user_id, 'dob', true)); ?>"
                            class="regular-text new-time-picker-cls-adding-here-input">
                    </div>
                </li>
            <?php endif; ?>

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
                    <label for="preferred_subjects"><?php esc_html_e('Learning Style', 'learnpress'); ?></label>
                    <div class="form-field-input">
                        <input type="text" name="learning_style" id="learning_style"
                            value="<?php echo esc_attr(get_user_meta($current_user_id, 'learning_style', true)); ?>"
                            class="regular-text">
                    </div>
                </li>

                <!-- <li class="form-field form-field__50">
                    <label for="learning_style"><?php esc_html_e('Learning Style', 'learnpress'); ?></label>
                    <div class="form-field-input new-learning-style-cls-adding">
                        <select name="learning_style" id="learning_style" class="regular-text new-select-option-cls-adding-here">
                            <?php
                            $options = ['Microlearning Learner', 'Auditory', 'Reading/Writing', 'Kinesthetic'];
                            $selected = get_user_meta($current_user_id, 'learning_style', true);
                            foreach ($options as $option) {
                                echo "<option value='" . esc_attr($option) . "'" . selected($selected, $option, false) . ">" . esc_html($option) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </li> -->

                <li class="form-field form-field__50">
                    <label for="preferred_subjects"><?php esc_html_e('Preferred Time Range', 'learnpress'); ?></label>
                    <div class="form-field-input">
                        <input type="text" name="tutoring_hours" id="tutoring_hours"
                            value="<?php echo esc_attr(get_user_meta($current_user_id, 'tutoring_hours', true)); ?>"
                            class="regular-text">
                    </div>
                </li>
                
                <!-- <li class="form-field form-field__50">
                    <label for="combined_time_range">Preferred Time Range</label>
                    <div class="form-field-input" style="position: relative;">
                        <input id="tutoring_hours" name="combined_time_range" type="text" placeholder="Select time range" readonly>
                        <input type="text" id="start_time_hidden" style="display: none;" />
                        <input type="text" id="end_time_hidden" style="display: none;" />
                    </div>
                </li> -->


             <?php endif; ?>

            <?php if (in_array('lp_teacher', $user_roles)): ?>
                <li class="form-field form-field__50">
                    <label for="experiance"><?php esc_html_e('Experience', 'learnpress'); ?></label>
                    <div class="form-field-input">
                        <input type="text" name="experiance" id="experiance"
                            value="<?php echo esc_attr(get_user_meta($current_user_id, 'experiance', true)); ?>"
                            class="regular-text" maxlength="50">
                    </div>
                </li>

                <li class="form-field form-field__50">
                    <label for="address"><?php esc_html_e('Address', 'learnpress'); ?></label>
                    <div class="form-field-input">
                        <input type="text" name="address" id="address"
                            value="<?php echo esc_attr(get_user_meta($current_user_id, 'address', true)); ?>"
                            class="regular-text" maxlength="100">
                    </div>
                </li>

                <li class="form-field form-field__50">
                    <label for="subject_expertise"><?php esc_html_e('Subject Expertise', 'learnpress'); ?></label>
                    <div class="form-field-input">
                        <input type="text" name="subject_expertise" id="subject_expertise"
                            value="<?php echo esc_attr(get_user_meta($current_user_id, 'subject_expertise', true)); ?>"
                            class="regular-text" maxlength="100">
                    </div>
                </li>

                <li class="form-field form-field__50">
                    <label for="qualification"><?php esc_html_e('Qualification', 'learnpress'); ?></label>
                    <div class="form-field-input">
                        <input type="text" name="qualification" id="qualification"
                            value="<?php echo esc_attr(get_user_meta($current_user_id, 'qualification', true)); ?>"
                            class="regular-text" maxlength="100">
                    </div>
                </li>

                <li class="form-field form-field__50">
                    <label for="availability"><?php esc_html_e('Availability', 'learnpress'); ?></label>
                    <div class="form-field-input">
                        <input type="text" name="availability" id="availability"
                            value="<?php echo esc_attr(get_user_meta($current_user_id, 'availability', true)); ?>"
                            class="regular-text" maxlength="100">
                    </div>
                </li>
            <?php endif; ?>
        </ul>

        <div class="form-field form-field__bio form-field__clear new-width-cls-adding-here new-textarea-cls-adding-here ">
                <label for="description"><?php esc_html_e('Biographical Info', 'learnpress'); ?></label>
                <div class="form-field-input">
                    <textarea name="description" id="additional_info" rows="5"
                        cols="30" class="textarea-cls-adding-here-all" maxlength="500"><?php echo esc_textarea(get_user_meta($current_user_id, 'additional_info', true)); ?></textarea>
                </div>
        </div>

        <div class="flexing-cancel-reject-btn-cls-adding-here">
            <div class="left-side-cancel-reject-btn">
            <button type="button" class="lp-button button-secondary"
            id="cancel-btn"><?php esc_html_e('Cancel', 'learnpress'); ?>
            </button>
            </div>
            <div class="right-side-cancel-reject-btn">
            <button class="lp-button" type="submit"
                name="custom_profile_submit"><?php esc_html_e('Update Profile', 'learnpress'); ?>
            </button>
            </div>
        </div>
    </form>


</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('custom-profile-form');

    form.addEventListener('submit', function (e) {
        let isValid = true;
        let errorMessage = '';

        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const phone = document.getElementById('billing_phone').value.trim();

        // Check first name and last name (only alphabets)
        const nameRegex = /^[A-Za-z\s]+$/;
        if (firstName && !nameRegex.test(firstName)) {
            isValid = false;
            errorMessage += "First name should not contain numbers or symbols.\n";
        }

        if (lastName && !nameRegex.test(lastName)) {
            isValid = false;
            errorMessage += "Last name should not contain numbers or symbols.\n";
        }

        // Check phone number (only digits)
        const phoneRegex = !/^\+?[0-9\s\-]{10,16}$/;
        if (phone && !phoneRegex.test(phone)) {
            isValid = false;
            errorMessage += "Mobile number should contain digits only.\n";
        }

        if (!isValid) {
            alert(errorMessage);
            e.preventDefault(); // Prevent form submission
        }
    });
});
</script>

<?php
$current_user    = wp_get_current_user();
$parent_username = $current_user->user_nicename; // user_nicename is slug-safe for URLs
$is_parent       = in_array('parent', (array) $current_user->roles);

// Build URLs
$children_url = home_url('/lp-profile/' . $parent_username . '/children/');
$default_url  = learn_press_user_profile_link();
?>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
    const cancelBtn = document.getElementById('cancel-btn');
    if (!cancelBtn) return;

    cancelBtn.addEventListener('click', function () {
        <?php if ( $is_parent ) : ?>
            // If the logged-in user is a parent, always go to the children tab
            window.location.href = '<?php echo esc_url( $children_url ); ?>';
        <?php else : ?>
            // Default LearnPress profile
            window.location.href = '<?php echo esc_url( $default_url ); ?>';
        <?php endif; ?>
    });
});
</script>
