<?php

function thim_child_enqueue_styles()
{
	wp_enqueue_style('thim-parent-style', get_template_directory_uri() . '/style.css', array(), THIM_THEME_VERSION);

	wp_enqueue_style('thim-child-custom-style', get_stylesheet_directory_uri() . '/assets/css/custom-style.css', array('thim-parent-style'), '1.0');
	// wp_enqueue_style('timepicker-css', get_stylesheet_directory_uri() . '/assets/css/mobiscroll.jquery.min.css', array('thim-parent-style'), '1.0.0');
	wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js', array('jquery'), null, true);
	wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css', array(), null);
	wp_enqueue_style('validation', 'https://cdn.jsdelivr.net/jquery.validation/1.19.5/jquery.validate.min.js', array(), null);
	wp_enqueue_style('additional-validation', 'https://cdn.jsdelivr.net/jquery.validation/1.19.5/additional-methods.min.js', array(), null);
	wp_enqueue_script(
		'jquery-validate',
		'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js',
		array('jquery'),
		'1.19.5',
		true
	);
	// wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css');
    // wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', [], null, true);
	// wp_enqueue_script('timepicker', get_stylesheet_directory_uri() . '/assets/js/mobiscroll.jquery.min.js', array(), '1.0.0`', true);
	wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
  	wp_enqueue_style('toastr-css', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css');
    wp_enqueue_script('toastr-js', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js', array('jquery'), null, true);
    // Flatpickr JS
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), null, true);
	wp_enqueue_script('thim-child-custom-script', get_stylesheet_directory_uri() . '/assets/js/custom-script.js', array('jquery', 'jquery-validate', 'select2-js', 'flatpickr-js'), '1.0', true);
	
	wp_localize_script('thim-child-custom-script', 'my_ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
	wp_enqueue_script('tutorstok-custom-js', get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), '1.0.0', true);
    wp_localize_script('tutorstok-custom-js', 'my_ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
    wp_localize_script('thim-child-custom-script', 'my_ajax_object', array(
	    'ajaxurl' => admin_url('admin-ajax.php')
	));
}

add_action('elementor/frontend/after_register_scripts', function() {
	// if(is_home()){
		wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css');
		wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', [], null, true);
	// }
});

add_action( 'wp_enqueue_scripts', 'remove_script_not_on_homepage', 100 );
function remove_script_not_on_homepage() {
    if ( ! is_front_page() ) {
        wp_dequeue_script( 'swiper-css' );
        wp_deregister_script( 'swiper-js' );
    }
}




require get_stylesheet_directory() . '/inc/template-functions.php';
require get_stylesheet_directory() . '/inc/custom-function.php';

add_action('wp_enqueue_scripts', 'thim_child_enqueue_styles', 1000);

// Demonstration code only. Do not use in real applications.
add_filter('eduma_license_check', function () {
	return true; // Forces license validation to always return true
});

/*Start of Calendly Integration*/

// Add meta box for Calendly links (multiple batches)
function add_calendly_batches_meta_box()
{
	add_meta_box(
		'calendly_batches_box',
		'Calendly Booking Links (Batches)',
		'render_calendly_batches_meta_box',
		'lp_course',
		'normal',
		'default'
	);
}
add_action('add_meta_boxes', 'add_calendly_batches_meta_box');

// Render the meta box with repeater fields
function render_calendly_batches_meta_box($post)
{
	$batch_links = get_post_meta($post->ID, '_calendly_batch_links', true);
	if (!is_array($batch_links)) {
		$batch_links = [];
	}
	?>
	<div id="calendly-batch-container">
		<?php foreach ($batch_links as $index => $batch): ?>
			<div class="calendly-batch-item">
				<input type="text" name="calendly_batch_links[<?php echo $index; ?>][batch_name]"
					value="<?php echo esc_attr($batch['batch_name']); ?>" placeholder="Batch Name (e.g., Morning Batch)"
					style="width: 30%;">

				<input type="text" name="calendly_batch_links[<?php echo $index; ?>][batch_link]"
					value="<?php echo esc_url($batch['batch_link']); ?>" placeholder="Calendly/Zoom Link" style="width: 65%;">

				<button type="button" class="remove-batch">Remove</button>
			</div>
		<?php endforeach; ?>
	</div>

	<button type="button" id="add-new-batch">+ Add New Batch</button>

	<script type="text/javascript">
		jQuery(function ($) {
			$('#add-new-batch').on('click', function () {
				let index = $('#calendly-batch-container .calendly-batch-item').length;
				$('#calendly-batch-container').append(`
					<div class="calendly-batch-item">
						<input type="text" name="calendly_batch_links[\${index}][batch_name]" 
							placeholder="Batch Name (e.g., Evening Batch)" style="width: 30%;">
						<input type="text" name="calendly_batch_links[\${index}][batch_link]" 
							placeholder="Calendly/Zoom Link" style="width: 65%;">
						<button type="button" class="remove-batch">Remove</button>
					</div>
				`);
			});

			$(document).on('click', '.remove-batch', function () {
				$(this).closest('.calendly-batch-item').remove();
			});
		});
	</script>

	<style>
		#calendly-batch-container .calendly-batch-item {
			margin-bottom: 10px;
			display: flex;
			gap: 10px;
		}

		#calendly-batch-container input {
			padding: 5px;
		}

		.remove-batch {
			background-color: #dc3545;
			color: white;
			border: none;
			cursor: pointer;
			padding: 5px 10px;
		}

		#add-new-batch {
			margin-top: 10px;
			background-color: #007cba;
			color: white;
			padding: 8px 15px;
			border: none;
			cursor: pointer;
		}
	</style>
	<?php
}

function add_select2_script_to_footer()
{
	?>
	<script>
		document.addEventListener("DOMContentLoaded", function () {
			jQuery('.wpcf7-form select[multiple]').each(function () {
				jQuery(this).select2({
					placeholder: "Select Subjects", // Placeholder text
					allowClear: true // Adds an 'X' to clear selection
				});
			});
		});
	</script>
	<?php
}
add_action('wp_footer', 'add_select2_script_to_footer');


// Save batch links when course is saved
function save_calendly_batches_meta_box($post_id)
{
	if (isset($_POST['calendly_batch_links'])) {
		$batches = array_values(array_filter($_POST['calendly_batch_links'], function ($batch) {
			return !empty($batch['batch_link']);
		}));
		update_post_meta($post_id, '_calendly_batch_links', $batches);
	} else {
		delete_post_meta($post_id, '_calendly_batch_links');
	}
}
add_action('save_post', 'save_calendly_batches_meta_box');


function show_calendly_batches_for_purchased_course()
{
	if (!is_singular('lp_course')) {
		// In case Elementor or custom pages are used, try to detect the course ID manually
		if (get_post_type() === 'lp_course') {
			$course_id = get_the_ID();
		} elseif (is_page()) {
			global $wp_query;
			$course_id = get_query_var('course_id') ? get_query_var('course_id') : null;
		} else {
			return '<p>Could not detect course. Please ensure you are within a course page.</p>';
		}
	} else {
		$course_id = get_the_ID();
	}

	if (!$course_id) {
		return '<p>Course not detected. Please use this shortcode only on course pages.</p>';
	}

	$user_id = get_current_user_id();
	if (!$user_id) {
		return '<p>Please log in to access your batch schedule.</p>';
	}

	// Check if user purchased the course
	if (!user_has_purchased_course($user_id, $course_id)) {
		return '<p>Please purchase this course to access batch schedules.</p>';
	}

	$batches = get_post_meta($course_id, '_calendly_batch_links', true);
	if (!is_array($batches) || empty($batches)) {
		return '<p>No batches available for this course yet.</p>';
	}

	ob_start();
	?>
	<h3>Select Your Batch & Schedule</h3>
	<ul class="calendly-batch-list new-calendly-batch-list-cls-adding-here">
		<?php foreach ($batches as $batch): ?>
			<li>
				<strong><?php echo esc_html($batch['batch_name']); ?></strong>
				<a href="<?php echo esc_url($batch['batch_link']); ?>" target="_blank">Schedule Now</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<style>
		.calendly-batch-list {
			list-style: none;
			padding: 0;
		}

		.calendly-batch-list li {
			margin: 10px 0;
		}

		.calendly-batch-list a {
			color: #007cba;
			text-decoration: underline;
		}
	</style>
	<?php
	return ob_get_clean();
}
add_shortcode('calendly_batches', 'show_calendly_batches_for_purchased_course');

function user_has_purchased_course($user_id, $course_id)
{
	if (class_exists('LP_Course')) {
		$course = learn_press_get_course($course_id);
		if (!$course) {
			return false;
		}

		$user = learn_press_get_user($user_id);
		if (!$user) {
			return false;
		}

		// Check if user has enrolled or completed the course
		if ($user->has_enrolled_course($course_id) || $user->has_finished_course($course_id)) {
			return true;
		}
	}

	return false;
}

/*End of Calendly Integration*/

/*Start of change in validation*/

// function custom_cf7_validation_messages($messages) {
//     //$messages['invalid_required']['message'] = __('Please fill in the required field.', 'contact-form-7');

//     // Custom message for email field
//     $messages['invalid_email']['message'] = __('Please fill email here.', 'contact-form-7');

//     // Custom message for phone field
//     $messages['invalid_tel']['message'] = __('Please fill number here.', 'contact-form-7');

//     return $messages;
// }
// add_filter('wpcf7_default_validation_messages', 'custom_cf7_validation_messages');

/*End of change in validation*/

/*Start of Converting Lessons into sessions*/

// Function to rename "Lesson" to "Session" throughout LearnPress
function thim_rename_lesson_to_session($translated_text, $text, $domain)
{
	if ($domain === 'learnpress') {
		$replacements = array(
			'Lesson' => 'Session',
			'lesson' => 'session',
			'Lessons' => 'Sessions',
			'lessons' => 'sessions',
		);
		$translated_text = str_ireplace(array_keys($replacements), array_values($replacements), $translated_text);
	}
	return $translated_text;
}
add_filter('gettext', 'thim_rename_lesson_to_session', 20, 3);
add_filter('ngettext', 'thim_rename_lesson_to_session', 20, 3);

// Function to rename lesson strings in LearnPress templates
function thim_rename_lesson_in_templates($text)
{
	$replacements = array(
		'Lesson' => 'Session',
		'lesson' => 'session',
		'Lessons' => 'Sessions',
		'lessons' => 'sessions',
	);
	return str_ireplace(array_keys($replacements), array_values($replacements), $text);
}
add_filter('the_title', 'thim_rename_lesson_in_templates', 20);
add_filter('lp_course_item_title', 'thim_rename_lesson_in_templates', 20);
add_filter('lp_course_item_content', 'thim_rename_lesson_in_templates', 20);
add_filter('lp_item_title', 'thim_rename_lesson_in_templates', 20);

/*End of Converting Lessons into sessions*/

/* Start of Card listing option*/

// Add meta box for course content list (repeater field)
function add_course_content_list_meta_box()
{
	add_meta_box(
		'course_content_list_box',
		'Course Content List (Repeater)',
		'render_course_content_list_meta_box',
		'lp_course',
		'normal',
		'default'
	);
}
add_action('add_meta_boxes', 'add_course_content_list_meta_box');

// Render the meta box with repeater fields
function render_course_content_list_meta_box($post)
{
	$content_list = get_post_meta($post->ID, '_course_content_list', true);
	if (!is_array($content_list)) {
		$content_list = [];
	}
	?>
	<div id="course-content-container">
		<?php foreach ($content_list as $index => $content): ?>
			<div class="course-content-item">
				<input type="text" name="course_content_list[<?php echo $index; ?>][content]"
					value="<?php echo esc_attr($content['content']); ?>" placeholder="Content (e.g., Interactive Sessions)"
					style="width: 90%;">
				<button type="button" class="remove-content">Remove</button>
			</div>
		<?php endforeach; ?>
	</div>

	<button type="button" id="add-new-content">+ Add New Content</button>

	<script type="text/javascript">
		jQuery(function ($) {
			$('#add-new-content').on('click', function () {
				let index = $('#course-content-container .course-content-item').length;
				$('#course-content-container').append(`
					<div class="course-content-item">
						<input type="text" name="course_content_list[${index}][content]" 
							placeholder="Content (e.g., Flexible Timings)" style="width: 90%;">
						<button type="button" class="remove-content">Remove</button>
					</div>
				`);
			});

			$(document).on('click', '.remove-content', function () {
				$(this).closest('.course-content-item').remove();
			});
		});
	</script>

	<style>
		#course-content-container .course-content-item {
			margin-bottom: 10px;
			display: flex;
			gap: 10px;
		}

		#course-content-container input {
			padding: 5px;
		}

		.remove-content {
			background-color: #dc3545;
			color: white;
			border: none;
			cursor: pointer;
			padding: 5px 10px;
		}

		#add-new-content {
			margin-top: 10px;
			background-color: #007cba;
			color: white;
			padding: 8px 15px;
			border: none;
			cursor: pointer;
		}
	</style>
	<?php
}

// Save the content list meta data
function save_course_content_list_meta_box($post_id)
{
	if (isset($_POST['course_content_list']) && is_array($_POST['course_content_list'])) {
		update_post_meta($post_id, '_course_content_list', $_POST['course_content_list']);
	}
}
add_action('save_post_lp_course', 'save_course_content_list_meta_box');

/* End of Card listing option*/

/* Start of Grade and Result input options */

// Add meta boxes for the course grade and result fields
function add_course_meta_boxes()
{
	add_meta_box(
		'course_grade_box',
		'Course Grade',
		'render_course_grade_meta_box',
		'lp_course',
		'normal',
		'default'
	);

	add_meta_box(
		'course_result_box',
		'Course Result',
		'render_course_result_meta_box',
		'lp_course',
		'normal',
		'default'
	);
}
add_action('add_meta_boxes', 'add_course_meta_boxes');

// Render the Grade meta box
function render_course_grade_meta_box($post)
{
	$grade = get_post_meta($post->ID, '_course_grade', true);
	?>
	<div id="course-grade-container">
		<label for="course_grade">Grade:</label>
		<input type="text" id="course_grade" name="course_grade" value="<?php echo esc_attr($grade); ?>"
			placeholder="Enter course grade (e.g., Beginner)" style="width: 100%; padding: 5px;">
	</div>
	<?php
}

// Render the Result meta box
function render_course_result_meta_box($post)
{
	$result = get_post_meta($post->ID, '_course_result', true);
	?>
	<div id="course-result-container">
		<label for="course_result">Result:</label>
		<input type="text" id="course_result" name="course_result" value="<?php echo esc_attr($result); ?>"
			placeholder="Enter result (e.g., Pass / Fail)" style="width: 100%; padding: 5px;">
	</div>
	<?php
}

// Save both Grade and Result meta data
function save_course_meta_boxes($post_id)
{
	if (isset($_POST['course_grade'])) {
		update_post_meta($post_id, '_course_grade', sanitize_text_field($_POST['course_grade']));
	}
	if (isset($_POST['course_result'])) {
		update_post_meta($post_id, '_course_result', sanitize_text_field($_POST['course_result']));
	}
}
add_action('save_post_lp_course', 'save_course_meta_boxes');

/* End of Grade and Result input options */


// validations

// Adding the customized validation rules for text fields and textareas in Contact Form 7
add_filter('wpcf7_validate_text*', 'custom_required_text_validation', 10, 2);
add_filter('wpcf7_validate_tel*', 'custom_required_phone_validation', 10, 2);
add_filter('wpcf7_validate_email*', 'custom_required_email_validation', 10, 2);

function custom_required_text_validation($result, $tag)
{
	$name = $tag->name;
	$value = isset($_POST[$name]) ? trim(sanitize_text_field($_POST[$name])) : '';

	// Define custom messages for specific fields
	$custom_messages = [
		'your-name' => __('Name is required.', 'tutorstok'),
		'last-name' => __('Last name is required.', 'tutorstok'),
		'your-parent' => __('Parent/Guardian name is required.', 'tutorstok'),
		'your-student' => __('Student name is required.', 'tutorstok'),
		'your-grade' => __('Grade is required.', 'tutorstok'),
	];

	// Check if field is empty and has a custom error message
	if (empty($value) && isset($custom_messages[$name])) {
		$result->invalidate($tag, $custom_messages[$name]);
	} elseif (!preg_match('/^[a-zA-Z\s]+$/', $value)) {
		$result->invalidate($tag, __("This field can only contain letters and spaces.", 'tutorstok'));
	}

	return $result;
}

function custom_required_phone_validation($result, $tag)
{
	$name = $tag->name;
	$value = isset($_POST[$name]) ? trim(sanitize_text_field($_POST[$name])) : '';

	if (empty($value)) {
		$result->invalidate($tag, __("Phone number is required.", 'tutorstok'));
	} elseif (!preg_match('/^\d{10,15}$/', $value)) {
		$result->invalidate($tag, __("Enter a valid phone number (only digits, 10-15 characters).", 'tutorstok'));
	}

	return $result;
}

function custom_required_email_validation($result, $tag)
{
	$name = $tag->name;
	$value = isset($_POST[$name]) ? trim(sanitize_email($_POST[$name])) : '';

	if (empty($value)) {
		$result->invalidate($tag, __("Email address is required.", 'tutorstok'));
	} elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
		$result->invalidate($tag, __("Enter a valid email address.", 'tutorstok'));
	}

	return $result;
}

// Subscribe-Now Contact form 7 validation
// function custom_cf7_validation($result, $tag)
// {
//     $tag_name = $tag->name; // Get the field name

//     if ('your-parent' === $tag_name && empty($_POST['your-parent'])) {
//         $result->invalidate($tag, "Please enter the Parent/Guardian Name.");
//     }

//     if ('phone' === $tag_name && empty($_POST['phone'])) {
//         $result->invalidate($tag, "Please enter the Parent/Guardian Phone Number.");
//     }

//     if ('email' === $tag_name && empty($_POST['email'])) {
//         $result->invalidate($tag, "Please enter the Parent/Guardian Email.");
//     }

//     if ('your-student' === $tag_name && empty($_POST['your-student'])) {
//         $result->invalidate($tag, "Please enter the Student Name.");
//     }

//     if ('your-grade' === $tag_name && empty($_POST['your-grade'])) {
//         $result->invalidate($tag, "Please enter the Student Grade.");
//     }

//     if ('interested-subjects' === $tag_name && empty($_POST['interested-subjects'])) {
//         $result->invalidate($tag, "Please select at least one subject.");
//     }

//     return $result;
// }

// add_filter('wpcf7_validate_text', 'custom_cf7_validation', 10, 2);
// add_filter('wpcf7_validate_text*', 'custom_cf7_validation', 10, 2);
// add_filter('wpcf7_validate_email', 'custom_cf7_validation', 10, 2);
// add_filter('wpcf7_validate_email*', 'custom_cf7_validation', 10, 2);
// add_filter('wpcf7_validate_intl_tel*', 'custom_cf7_validation', 10, 2);
// add_filter('wpcf7_validate_select*', 'custom_cf7_validation', 10, 2);

// add_action( 'thim_form_login_widget', 'custom_thim_login_form', 10 );

// function custom_thim_login_form( $captcha ) {
//     // Custom login form output
//     echo '<form>...custom fields...</form>';
// }

//Login Form 
add_action('after_setup_theme', 'override_eduma_login_popup', 20);
function override_eduma_login_popup()
{
	// Remove the default login form from the popup
	remove_action('thim_form_login_widget', 'thim_form_login_widget', 10, 1);

	// Add your custom Elementor login form
	add_action('thim_form_login_widget', 'custom_elementor_login_popup', 20);
}

function custom_elementor_login_popup($captcha = 'no')
{
	echo '<div class="custom-elementor-login-popup">';

	// Elementor login form
	echo do_shortcode('[thim_ekit id="21418"]');

	// Custom styled social login buttons
	echo '<div class="custom-social-buttons">';
	echo do_shortcode('[nextend_social_login provider="facebook"]');
	echo do_shortcode('[nextend_social_login provider="google" style="icon"]');
	echo '</div>';

	echo '</div>';
}

add_filter('gettext', 'custom_override_login_text', 20, 3);

function custom_override_login_text($translated_text, $text, $domain)
{
	if ('eduma' === $domain && 'Login with your site account' === $text) {
		return '';
	}

	return $translated_text;
}
add_action('wp_footer', 'add_custom_login_placeholders', 100);


add_filter('gettext', 'custom_override_register_text', 20, 3);
function custom_override_register_text($translated_text, $text, $domain)
{
	if ('eduma' === $domain && 'Not a member yet? ' === $text) {
		return 'New to the platform? ';
	}
	if ('eduma' === $domain && 'Register now' === $text) {
		return 'Sign up';
	}
	return $translated_text;
}

function add_custom_login_placeholders()
{
	?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			// Add placeholder to username field
			const usernameInput = document.querySelector('#thim-popup-login input[name="log"]');
			if (usernameInput) {
				usernameInput.placeholder = 'Enter your username';
			}

			// Add placeholder to password field
			const passwordInput = document.querySelector('#thim-popup-login input[name="pwd"]');
			if (passwordInput) {
				passwordInput.placeholder = 'Password';
			}
		});
	</script>
	<?php
}

//Sign up popup
add_action('after_setup_theme', 'override_eduma_register_popup', 20);
function override_eduma_register_popup()
{
	// Remove the default register form from the popup
	remove_action('thim_form_register_widget', 'thim_form_register_widget', 10, 1);

	// Add your custom Elementor register form
	add_action('thim_form_register_widget', 'custom_elementor_register_popup', 20);
}

function custom_elementor_register_popup($captcha = 'no')
{
	echo '<div class="custom-elementor-register-popup">';

	// Elementor register form widget (replace ID with your actual Elementor template ID)
	echo do_shortcode('[thim_ekit id="21673"]'); // Example ID for register form

	// Custom styled social login buttons (optional for register)
	// echo '<div class="custom-social-buttons">';
	// echo do_shortcode('[nextend_social_login provider="facebook"]');
	// echo do_shortcode('[nextend_social_login provider="google" style="icon"]');
	// echo '</div>';

	echo '</div>';
}

add_action('learn-press/user-profile-tab-content/courses', 'custom_course_filter_ui', 5);
function custom_course_filter_ui()
{
	?>
	<div class="lp-course-filter">
		<input type="text" id="course-search" placeholder="Search your courses..." />
	</div>
	<script>
		jQuery(document).ready(function ($) {
			$('#course-search').on('input', function () {
				var value = $(this).val().toLowerCase();
				$('.profile-courses .course-item').filter(function () {
					$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
				});
			});
		});
	</script>
	<?php
}

add_action('learn-press/profile/tab-assignments', 'add_search_to_lp_assignments_tab', 5);

function add_search_to_lp_assignments_tab()
{
	$search_term = isset($_GET['assignment_search']) ? sanitize_text_field($_GET['assignment_search']) : '';
	?>
	<form method="get" style="margin-bottom: 20px;">
		<input type="hidden" name="lp-ajax" value="profile" />
		<input type="hidden" name="tab" value="assignments" />
		<input type="text" name="assignment_search" value="<?php echo esc_attr($search_term); ?>"
			placeholder="Search assignments..." />
		<button type="submit">Search</button>
	</form>
	<?php
}
add_action('pre_get_posts', 'filter_lp_assignments_by_search');
function filter_lp_assignments_by_search($query)
{
	if (is_admin() || !$query->is_main_query()) {
		return;
	}

	if (is_page() && isset($_GET['tab']) && $_GET['tab'] === 'assignments' && !empty($_GET['assignment_search'])) {
		$query->set('s', sanitize_text_field($_GET['assignment_search']));
	}
}
add_filter('learn_press_locate_template', 'force_custom_lp_assignments_template', 10, 3);

function force_custom_lp_assignments_template($template, $template_name, $template_path)
{
	if ($template_name === 'profile/tabs/assignments.php') {
		$custom_template = get_stylesheet_directory() . '/custom-assignments/assignments.php';
		if (file_exists($custom_template)) {
			return $custom_template;
		}
	}
	return $template;
}

add_filter('learn_press_locate_template', 'custom_lp_courses_template_override', 10, 4);

function custom_lp_courses_template_override($template, $template_name, $template_path)
{
	if ($template_name === 'profile/tabs/courses/course-list.php') {
		$custom_template = get_stylesheet_directory() . '/custom-courses/course-list.php';
		if (file_exists($custom_template)) {
			return $custom_template;
		}
	}
	return $template;
}
add_filter('learn_press_locate_template', 'override_lp_orders_template', 10, 6);

function override_lp_orders_template($template, $template_name, $template_path)
{
	if ($template_name === 'profile/tabs/orders/list.php') {
		$custom = get_stylesheet_directory() . '/learnpress/profile/tabs/orders/list.php';
		// var_dump($custom);
		if (file_exists($custom)) {
			return $custom;
		}
	}
	return $template;
}
add_filter('learn_press_locate_template', 'custom_learnpress_template_override', 10, 5);

function custom_learnpress_template_override($template, $template_name, $template_path)
{
	$child_theme_template = get_stylesheet_directory() . '/learnpress/' . $template_name;

	if (file_exists($child_theme_template)) {
		return $child_theme_template;
	}

	return $template;
}
add_filter('learn-press/profile-settings-tabs', 'lp_override_basic_info_tab_template', 100);
function lp_override_basic_info_tab_template($tabs)
{
	if (isset($tabs['basic-information'])) {
		$child_template = get_stylesheet_directory() . '/learnpress/settings/tabs/basic-information.php';

		if (file_exists($child_template)) {
			$tabs['basic-information']['callback'] = function () use ($child_template) {
				include $child_template;
			};
		}
	}

	return $tabs;
}




// add_filter( 'learn_press_locate_template', function( $template, $template_name ) {
//     echo( 'Loading template: ' . $template_name . ' → ' . $template );
//     return $template;
// }, 999, 2 );



function lp_add_user_notification($user_id, $message) {
	if (!$user_id || !$message) return;

	$notifications = get_user_meta($user_id, '_lp_user_notifications', true);
	if (!is_array($notifications)) {
		$notifications = [];
	}

	$notifications[] = [
		'message'   => wp_kses_post($message),
		'timestamp' => current_time('timestamp'),
	];

	update_user_meta($user_id, '_lp_user_notifications', $notifications);
}

add_action('woocommerce_order_status_completed', 'lp_course_enrollment_notification', 20, 1);

function lp_course_enrollment_notification($order_id) {
	$order = wc_get_order($order_id);
	if (!$order) return;

	$user_id = $order->get_user_id();
	if (!$user_id) return;

	foreach ($order->get_items() as $item) {
		$product_id = $item->get_product_id();

		// Only proceed if product is linked to an LP course
		if (get_post_type($product_id) === 'lp_course') {
			$course_id = $product_id;
			$tutor_id = get_post_field('post_author', $course_id);
			$course_title = get_the_title($course_id);
			$student = get_userdata($user_id);
			$student_name = $student ? $student->display_name : 'A student';

			lp_add_user_notification($user_id, "✅ You have successfully enrolled in the course: <strong>{$course_title}</strong>");

			lp_add_user_notification($tutor_id, "👨‍🎓 {$student_name} has enrolled in your course: <strong>{$course_title}</strong>");

			wp_mail(
				get_the_author_meta('user_email', $tutor_id),
				"New student enrolled: {$course_title}",
				"{$student_name} has enrolled in your course: {$course_title}",
				['Content-Type: text/html; charset=UTF-8']
			);
		}
	}
}



function insert_lp_notification($args) {
	$user_id = $args['user_id'];

	$notifications = get_user_meta($user_id, '_lp_user_notifications', true);
	if (!is_array($notifications)) {
		$notifications = [];
	}

	$notifications[] = array(
		'message'   => $args['message'],
		'timestamp' => current_time('timestamp'),
	);

	update_user_meta($user_id, '_lp_user_notifications', $notifications);
}
add_action('save_post_lp_assignment', function ($post_ID, $post, $update) {
	// if (get_post_status($post_ID) !== 'submitted') return;

	$user_id = get_current_user_id(); // student
	$course_id = get_post_meta($post_ID, '_lp_assignment_course', true);
	if (!$course_id) return;

	$tutor_id = get_post_field('post_author', $course_id);
	if (!$tutor_id || $tutor_id === $user_id) return;

	$student_name = get_the_author_meta('display_name', $user_id);
	$assignment_title = get_the_title($post_ID);
	$course_title = get_the_title($course_id);

	$message = "$student_name submitted the assignment '$assignment_title' for your course '$course_title'.";

	insert_lp_notification([
		'user_id' => $tutor_id,
		'message' => $message,
	]);
}, 20, 3);







add_action('template_redirect', 'custom_lp_catch_assignment_submission');

function custom_lp_catch_assignment_submission() {
	if (
		is_user_logged_in() &&
		isset($_GET['lp-ajax']) &&
		$_GET['lp-ajax'] === 'submit-assignment' &&
		isset($_POST['assignment_id'])
	) {
		$user_id = get_current_user_id();
		$assignment_id = intval($_POST['assignment_id']);

		if (!$assignment_id) return;

		$assignment_title = get_the_title($assignment_id);

		$course_id = learn_press_get_course_by_assignment($assignment_id);
		$course_title = $course_id ? get_the_title($course_id) : 'Unknown Course';

		$notifications = get_user_meta($user_id, '_lp_user_notifications', true);
		if (!is_array($notifications)) $notifications = [];

		// Add new notification
		$notifications[] = [
			'message' => sprintf(
				'You submitted the assignment <strong>%s</strong> in <strong>%s</strong>',
				$assignment_title,
				$course_title
			),
			'timestamp' => time()
		];

		update_user_meta($user_id, '_lp_user_notifications', $notifications);
	}
}
add_action('template_redirect', 'lp_handle_quiz_approval');

function lp_handle_quiz_approval() {
	if (
		is_user_logged_in() &&
		current_user_can('lp_teacher') &&
		isset($_POST['approve_quiz'], $_POST['user_item_id'])
	) {
		$user_item_id = absint($_POST['user_item_id']);

		// Update the quiz status
		global $wpdb;
		$wpdb->update(
			$wpdb->prefix . 'learnpress_user_items',
			['status' => 'completed', 'graduation' => 'passed'],
			['user_item_id' => $user_item_id],
			['%s', '%s'],
			['%d']
		);

		// Send notification
		lp_send_quiz_approval_notification($user_item_id);

		// Redirect to avoid resubmission
		wp_redirect(add_query_arg('quiz_approved', '1', wp_get_referer()));
		exit;
	}
}
function lp_send_quiz_approval_notification($user_item_id) {
	global $wpdb;

	$quiz_item = $wpdb->get_row($wpdb->prepare("
		SELECT ui.*, p.post_title
		FROM {$wpdb->prefix}learnpress_user_items ui
		LEFT JOIN {$wpdb->prefix}posts p ON ui.item_id = p.ID
		WHERE ui.user_item_id = %d
	", $user_item_id));

	if (!$quiz_item) {
		return;
	}

	$student_id  = $quiz_item->user_id;
	$quiz_title  = $quiz_item->post_title;
	$tutor_id    = get_current_user_id();
	$student     = get_userdata($student_id);
	$tutor       = get_userdata($tutor_id);

	
	$student_message = sprintf(
		__('Your assignment for "%s" has been reviewed. Status: Passed.', 'learnpress'),
		$quiz_title
	);

	add_user_meta($student_id, '_lp_notification', [
		'type'    => 'quiz_approved',
		'message' => $student_message,
		'time'    => current_time('mysql'),
	]);

	
	if ($student && $tutor) {
		$tutor_message = sprintf(
			__('You reviewed "%s" assignment submitted by %s.', 'learnpress'),
			$quiz_title,
			$student->display_name
		);

		add_user_meta($tutor_id, '_lp_notification', [
			'type'    => 'quiz_review_log',
			'message' => $tutor_message,
			'time'    => current_time('mysql'),
		]);
	}
}




add_filter('learn-press/profile-tabs', function ($tabs) {
	$tabs['notification'] = [
		'title' => __('Notifications', 'text-domain'),
		'priority' => 30,
		'icon' => '<i class="lp-icon-file-alt"></i>',
		'callback' => 'lp_notifications_tab_content',
	];
	return $tabs;
});

function lp_notifications_tab_content() {
	?>
	<div class="learn-press-profile-notification-custom">
		<div class="lp-notification-header">
			<h3 class="profile-heading">Notifications</h3>
			<div class="custom-table-wrapper" id="notification-list">
				<?php custom_load_notifications_table(1); ?>
			</div>
		</div>
	</div>
	<?php
}
function custom_load_notifications_table($page = 1) {
	$user_id = get_current_user_id();

	// 1. Notifications saved using add_user_meta (multiple entries)
	$multi_notifications = get_user_meta($user_id, '_lp_notification');
	$formatted_multi = [];
	foreach ($multi_notifications as $note) {
		if (!is_array($note)) continue;
		$formatted_multi[] = (object)[
			'post_title' => wp_strip_all_tags($note['message']),
			'post_date'  => date('Y-m-d H:i:s', strtotime($note['time'] ?? current_time('mysql'))),
			'meta_type'  => 'custom',
		];
	}

	// 2. Notifications saved using update_user_meta (single array of entries)
	$single_notifications = get_user_meta($user_id, '_lp_user_notifications', true);
	if (!is_array($single_notifications)) $single_notifications = [];

	$formatted_single = [];
	foreach ($single_notifications as $note) {
		$formatted_single[] = (object)[
			'post_title' => wp_strip_all_tags($note['message']),
			'post_date'  => date('Y-m-d H:i:s', $note['timestamp']),
			'meta_type'  => 'custom',
		];
	}

	// Merge both sources
	$formatted_user_notifications = array_merge($formatted_multi, $formatted_single);

	// Load post-type-based notifications
	$args = [
		'post_type'      => ['lp_announcements', 'webinar'],
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	];
	$post_notifications = get_posts($args);
	foreach ($post_notifications as $post) {
		$post->meta_type = 'post';
	}

	$all_notifications = array_merge($formatted_user_notifications, $post_notifications);

	usort($all_notifications, function($a, $b) {
		return strtotime($b->post_date) - strtotime($a->post_date);
	});

	$posts_per_page = 10;
	$offset = ($page - 1) * $posts_per_page;
	$total = count($all_notifications);
	$total_pages = ceil($total / $posts_per_page);
	$paged_notifications = array_slice($all_notifications, $offset, $posts_per_page);

	echo '<table><tbody>';
	if (!empty($paged_notifications)) {
		foreach ($paged_notifications as $note) {
			echo '<tr class="lp-announcement">';
			echo '<td>';
			echo '<strong>' . esc_html($note->post_title) . '</strong><br>';
			echo '<span>' . date('F j, Y g:i A', strtotime($note->post_date)) . '</span>';
			echo '</td>';
			echo '</tr>';
		}
	} else {
		echo '<tr><td>No notifications found.</td></tr>';
	}
	echo '</tbody></table>';

	if ($total_pages > 1) {
		echo '<div class="lp-ajax-pagination custom-pagination">';
		for ($i = 1; $i <= $total_pages; $i++) {
			echo '<button class="lp-page-btn pagination-link ' . ($i == $page ? 'current' : '') . '" data-page="' . esc_attr($i) . '">' . esc_html($i) . '</button>';
		}
		echo '</div>';
	}
}


function lp_notifications_pagination() {
	$page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
	custom_load_notifications_table( $page );
	wp_die();
}
add_action( 'wp_ajax_lp_notifications_pagination', 'lp_notifications_pagination' );
add_action( 'wp_ajax_nopriv_lp_notifications_pagination', 'lp_notifications_pagination' );


function send_notification_on_webinar_status_change($new_status, $old_status, $post) {
    if ($post->post_type !== 'webinar') return;

    // Avoid running during autosave or revision
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post->ID)) return;

    $sanitize_webinar_title = function($title) {
        $prefixes = ['Webinar Canceled:', 'Webinar Rescheduled:', 'Webinar Scheduled:', 'Sessions Canceled:'];
        foreach ($prefixes as $prefix) {
            if (stripos($title, $prefix) === 0) {
                return trim(substr($title, strlen($prefix)));
            }
        }
        return $title;
    };

    $title       = get_the_title($post->ID);
    $clean_title = $sanitize_webinar_title($title);
    $tutor_id    = $post->post_author;
    $tutor       = get_user_by('id', $tutor_id); // FIX: Define $tutor
    $course_id   = get_post_meta($post->ID, '_webinar_course', true);
    $subject     = get_post_meta($post->ID, '_webinar_subject', true);
    $type        = get_post_meta($post->ID, '_webinar_type', true);
    $link        = get_post_meta($post->ID, '_webinar_link', true);
    $datetime    = get_post_meta($post->ID, '_webinar_datetime', true);
    $course_title = $course_id ? get_the_title($course_id) : 'N/A';

    // === CASE 1: Webinar Canceled ===
    if ($old_status === 'publish' && $new_status === 'draft') {
        $subject_line = "Webinar Canceled: $clean_title";
        $content = "The webinar \"$clean_title\" has been canceled or unpublished. Please take note.";

         $users = get_users([
		    'fields' => ['user_email'],
		]);
       	foreach ($users as $user) {
		    wp_mail($user->user_email, $subject_line, $message);
		}

        if (!empty($existing)) {
            wp_update_post([
                'ID'           => $existing[0],
                'post_title'   => $subject_line,
                'post_content' => $content,
            ]);
        } else {
            wp_insert_post([
                'post_title'   => $subject_line,
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_author'  => $tutor_id,
                'post_type'    => 'webinar',
                'meta_input'   => [
                    'related_webinar_id' => $post->ID,
                    '_auto_generated' => 'yes'
                ],
            ]);
        }
        return;
    }

    // === CASE 2: Webinar Scheduled ===
    if ($old_status === 'draft' && $new_status === 'publish') {
        update_post_meta($post->ID, '_old_webinar_datetime', $datetime);

        $message = "Hello,\n\nYou are attending the webinar: \"$clean_title\"\n";
        $subject_line = "Webinar Scheduled: $clean_title";
        $users = get_users([
		    'fields' => ['user_email'],
		]);
       	foreach ($users as $user) {
		    wp_mail($user->user_email, $subject_line, $message);
		}

        if (!empty($existing)) {
            wp_update_post([
                'ID'           => $existing[0],
                'post_title'   => $subject_line,
                'post_content' => $message,
            ]);
        } else {
            wp_insert_post([
                'post_title'   => $subject_line,
                'post_content' => $message,
                'post_status'  => 'publish',
                'post_author'  => $tutor_id,
                'post_type'    => 'webinar',
                'meta_input'   => [
                    'related_webinar_id' => $post->ID,
                    '_auto_generated' => 'yes'
                ],
            ]);
        }
        return;
    }

    // === CASE 3: Webinar Rescheduled ===
    if ($new_status === 'publish' && $old_status === 'publish') {
        $old_datetime = get_post_meta($post->ID, '_old_webinar_datetime', true);
        if ($datetime !== $old_datetime) {
            update_post_meta($post->ID, '_old_webinar_datetime', $datetime);

            $message = "Hello,\n\nYou are the host of the webinar: \"$clean_title\"\nCourse: $course_title\nSubject: $subject\nType: $type\nDate & Time: $datetime\nLink: $link\n";
            $subject_line = "Webinar Rescheduled: $clean_title";

            $users = get_users([
			    'fields' => ['user_email'],
			]);
	       	foreach ($users as $user) {
			    wp_mail($user->user_email, $subject_line, $message);
			}

            if (!empty($existing)) {
                wp_update_post([
                    'ID'           => $existing[0],
                    'post_title'   => $subject_line,
                    'post_content' => $message,
                ]);
            } else {
                wp_insert_post([
                    'post_title'   => $subject_line,
                    'post_content' => $message,
                    'post_status'  => 'publish',
                    'post_author'  => $tutor_id,
                    'post_type'    => 'webinar',
                    'meta_input'   => [
                        'related_webinar_id' => $post->ID,
                        '_notification_type' => 'rescheduled',
                        '_auto_generated' => 'yes'
                    ],
                ]);
            }
        }
    }
}
add_action('transition_post_status', 'send_notification_on_webinar_status_change', 10, 3);


function hide_auto_generated_webinars_from_admin($query) {
    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'webinar') {
        $meta_query = $query->get('meta_query') ?: [];
        $meta_query[] = [
            'key'     => '_auto_generated',
            'compare' => 'NOT EXISTS',
        ];
        $query->set('meta_query', $meta_query);
    }
}
add_action('pre_get_posts', 'hide_auto_generated_webinars_from_admin');


add_action('updated_post_meta', 'notify_tutor_on_assignment_submission_meta_hook', 10, 4);
function notify_tutor_on_assignment_submission_meta_hook($meta_id, $object_id, $meta_key, $_meta_value) {
    // Check if this is an assignment submission
    if (strpos($meta_key, '_lp_assignment_submit') !== false) {
        $assignment_id = $object_id;

        // Student ID is in meta key name like _lp_assignment_submit_{student_id}
        if (preg_match('/_lp_assignment_submit_(\d+)/', $meta_key, $matches)) {
            $student_id = intval($matches[1]);

            // Course associated with the assignment
            $course_id = get_post_meta($assignment_id, '_lp_course', true);
            if (!$course_id) return;

            // Get tutor
            $tutor_id = get_post_field('post_author', $course_id);
            if (!$tutor_id || $tutor_id == $student_id) return;

            $student = get_userdata($student_id);
            $student_name = $student ? $student->display_name : __('A student', 'text-domain');

            $course_title     = get_the_title($course_id);
            $assignment_title = get_the_title($assignment_id);

            $message = "📥 <strong>$student_name</strong> submitted the assignment <strong>\"$assignment_title\"</strong> in course <strong>\"$course_title\"</strong>.";

            $notifications = get_user_meta($tutor_id, '_lp_user_notifications', true);
            if (!is_array($notifications)) {
                $notifications = [];
            }

            $notifications[] = [
                'message'   => $message,
                'timestamp' => time(),
            ];

            update_user_meta($tutor_id, '_lp_user_notifications', $notifications);
        }
    }
}









function custom_breadcrumb_shortcode()
{
	if (!is_singular())
		return '';

	ob_start(); ?>
	<nav class="breadcrumb">

		<?php if (is_singular('post')): ?>
			<a href="<?php echo get_permalink(get_option('page_for_posts')); ?>">Blog</a>
		<?php endif; ?>

		/ <span><?php echo get_the_title(); ?></span>
	</nav>
	<?php
	return ob_get_clean();
}
add_shortcode('custom_breadcrumb', 'custom_breadcrumb_shortcode');

add_filter('learn-press/profile-tabs', function ($calendar_tabs) {
	$calendar_tabs['calendar'] = [
		'title' => __('Calendar', 'text-domain'),
		'priority' => 20,
		'icon' => '<i class="lp-icon-calendar"></i>',
		'callback' => 'custom_render_calendar_tab',
	];
	return $calendar_tabs;
});

function custom_render_calendar_tab()
{
	echo '<h3 class="profile-heading">Calendar</h3>';
	echo '<div class="overflow-type-table-responsive" id="overflow-type-table-responsive">';

	echo do_shortcode('[custom_calendar]');

	// Replace YOUR_GOOGLE_CALENDAR_ID and timezone accordingly
	// echo '<iframe 
	// src="https://calendar.google.com/calendar/embed?src=YOUR_GOOGLE_CALENDAR_ID&ctz=Asia%2FKolkata&bgcolor=%23f7f7f7"
	// style="border: 0; background-color: #f7f7f7;" 
	// width="100%" 
	// height="600" 
	// frameborder="0" 
	// scrolling="no">
	// </iframe>';

	echo '</div>';
}


// // Force LearnPress template overrides from child theme
// add_filter( 'learn-press/override-templates', '__return_true' );

// add_filter( 'learn_press_locate_template', function( $template, $template_name, $template_path ) {
// 	if ( $template_name === 'settings/tabs/basic-information.php' ) {
// 		$custom_template = get_stylesheet_directory() . '/learnpress/settings/tabs/basic-information.php';
// 		if ( file_exists( $custom_template ) ) {
// 			return $custom_template;
// 		}
// 	}
// 	return $template;
// }, 10, 3 );

// add_action( 'learn-press/before-profile-basic-information-fields', 'lp_save_profile_picture' );

// function lp_save_profile_picture() {
//     if (
//         ! empty( $_FILES['lp_user_avatar']['name'] ) &&
//         ! empty( $_POST['save-profile-basic-information'] ) &&
//         is_user_logged_in()
//     ) {
//         $user_id = get_current_user_id();

//         // Required WP media functions
//         require_once ABSPATH . 'wp-admin/includes/file.php';
//         require_once ABSPATH . 'wp-admin/includes/media.php';
//         require_once ABSPATH . 'wp-admin/includes/image.php';

//         // Upload image
//         $attachment_id = media_handle_upload( 'lp_user_avatar', 0 );

//         if ( ! is_wp_error( $attachment_id ) ) {
//             $avatar_url = wp_get_attachment_url( $attachment_id );

//             // Save avatar URL to user meta
//             update_user_meta( $user_id, 'lp_user_avatar_id', $attachment_id );
//             update_user_meta( $user_id, 'lp_user_avatar_url', esc_url( $avatar_url ) );
//         }
//     }
// }

// add_filter( 'get_avatar_url', 'lp_custom_avatar_url', 10, 2 );

// function lp_custom_avatar_url( $url, $id_or_email ) {
//     $user = false;

//     if ( is_numeric( $id_or_email ) ) {
//         $user = get_user_by( 'id', $id_or_email );
//     } elseif ( is_object( $id_or_email ) && isset( $id_or_email->user_id ) ) {
//         $user = get_user_by( 'id', $id_or_email->user_id );
//     } elseif ( is_string( $id_or_email ) ) {
//         $user = get_user_by( 'email', $id_or_email );
//     }

//     if ( $user ) {
//         $custom_avatar = get_user_meta( $user->ID, 'lp_user_avatar_url', true );
//         if ( $custom_avatar ) {
//             return esc_url( $custom_avatar );
//         }
//     }

//     return $url;
// }

// commented_by_sp_start

// add_action( 'learn-press/end-profile-basic-information-fields', 'eduma_add_custom_avatar_field' );

// function eduma_add_custom_avatar_field( $profile ) {
// 	$user_id    = $profile->get_user()->get_id();
// 	$avatar_url = get_user_meta( $user_id, 'lp_user_avatar_url', true );

// 	echo '<li class="form-field form-field__avatar form-field__clear">';
// 	echo '<label for="lp_user_avatar">' . esc_html__( 'Profile Picture', 'learnpress' ) . '</label>';
// 	echo '<div class="form-field-input">';
// 	echo '<input type="file" name="lp_user_avatar" id="lp_user_avatar" accept="image/*">';
// 	if ( $avatar_url ) {
// 		echo '<p><img src="' . esc_url( $avatar_url ) . '" style="max-width: 100px; border-radius: 5px;" /></p>';
// 	}
// 	echo '<p class="description">' . esc_html__( 'Upload your profile photo.', 'learnpress' ) . '</p>';
// 	echo '</div>';
// 	echo '</li>';
// }

// add_action( 'learn-press/profile/update-basic-information', 'save_lp_user_avatar_image', 20, 2 );

// function save_lp_user_avatar_image( $user_id, $profile ) {
// 	if ( empty( $_FILES['lp_user_avatar']['name'] ) ) {
// 		return;
// 	}

// 	// Make sure required WordPress upload functions are available
// 	if ( ! function_exists( 'wp_handle_upload' ) ) {
// 		require_once ABSPATH . 'wp-admin/includes/file.php';
// 	}

// 	$uploadedfile     = $_FILES['lp_user_avatar'];
// 	$upload_overrides = [ 'test_form' => false ];

// 	// Upload the file
// 	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

// 	if ( $movefile && ! isset( $movefile['error'] ) ) {
// 		$image_url = esc_url_raw( $movefile['url'] );

// 		// Save to user meta
// 		update_user_meta( $user_id, 'lp_user_avatar_url', $image_url );
// 	}
// }

// commented_by_sp_end

// function lp_user_reviews_only_shortcode() {

//     $recent_comments = get_comments( array( 
//         'number'      => 5,
//         'status'      => 'approve',
//         'type'        => 'review', // only get comments of type 'review' if you're using it like in your dump
//         'post_status' => 'publish'
//     ) );

//     ob_start();

//     if ( $recent_comments ) {
//         echo '<ul class="lp-reviews">';
//         foreach ( $recent_comments as $comment ) {
//             $post_title = get_the_title( $comment->comment_post_ID );
//             $post_link = get_permalink( $comment->comment_post_ID );
//             echo '<li>';
//             echo '<strong>' . esc_html( $comment->comment_author ) . '</strong><br>';
//             echo '<em>' . esc_html( $comment->comment_content ) . '</em><br>';
//             echo '<a href="' . esc_url( get_comment_link( $comment ) ) . '">' . esc_html( $post_title ) . '</a>';
//             echo '</li>';
//         }
//         echo '</ul>';
//     } else {
//         echo '<p>No reviews found.</p>';
//     }

//     return ob_get_clean();
// }
// add_shortcode( 'lp_course_review', 'lp_user_reviews_only_shortcode' );

function lp_instructor_reviews_shortcode()
{
	// Check if user is logged in
	// if (!is_user_logged_in()) {
	//     return '<p>Please log in to view reviews.</p>';
	// }

	// $current_user = wp_get_current_user();

	// Check if user has instructor role (adjust role as needed)
	// if (!in_array('lp_teacher', $current_user->roles)) {
	//     return '<p>Only instructors can view this information.</p>';
	// }

	// Get all courses by this instructor
	$instructor_courses = get_posts(array(
		'post_type' => 'lp_course', // LearnPress course post type
		// 'author'         => $current_user->ID,
		'posts_per_page' => -1, // Get all courses
		'fields' => 'ids', // Only get post IDs
	));

	if (empty($instructor_courses)) {
		return '<p>No courses found for this instructor.</p>';
	}

	// Get all reviews for these courses
	$args = array(
		'post__in' => $instructor_courses,
		'status' => 'approve',
		'type' => 'review',
		'post_status' => 'publish',
		'number' => 20, // Limit number of reviews
	);

	$course_reviews = get_comments($args);

	ob_start();

	if ($course_reviews) {
		echo '<div class="instructor-reviews-wrap">';
		echo '<h3>Course Reviews</h3>';
		echo '<ul class="instructor-reviews">';

		foreach ($course_reviews as $review) {
			$post_title = get_the_title($review->comment_post_ID);
			$post_link = get_permalink($review->comment_post_ID);
			$rating = get_comment_meta($review->comment_ID, '_lpr_rating', true); // Get rating if exists

			echo '<li class="review-item">';
			echo '<div class="review-header">';
			echo '<strong>' . esc_html($review->comment_author) . '</strong>';

			if ($rating) {
				echo '<div class="review-rating">' . esc_html($rating) . '/5</div>';
			}

			echo '</div>';
			echo '<div class="review-content">' . esc_html($review->comment_content) . '</div>';
			echo '<div class="review-course">On course: <a href="' . esc_url($post_link) . '">' . esc_html($post_title) . '</a></div>';
			echo '<div class="review-date">' . date('M j, Y', strtotime($review->comment_date)) . '</div>';
			echo '</li>';
		}

		echo '</ul>';
		echo '</div>';
	} else {
		echo '<p>No reviews found for your courses.</p>';
	}

	return ob_get_clean();
}
add_shortcode('lp_course_review', 'lp_instructor_reviews_shortcode');

add_filter('registration_errors', 'set_user_login_from_email_if_empty', 20, 3);
function set_user_login_from_email_if_empty($errors, $sanitized_user_login, $user_email)
{
	if (empty($_POST['user_login']) && !empty($user_email)) {
		$base = sanitize_user(current(explode('@', $user_email)), true);
		$login = $base;
		$i = 1;
		while (username_exists($login)) {
			$login = $base . $i;
			$i++;
		}
		$_POST['user_login'] = $login;
	}
	return $errors;
}
add_action('wp_footer', function () {
	if (is_cart() || is_checkout()) {
		// Check if cart is empty
		$cart_empty = WC()->cart->is_empty() ? 'true' : 'false';
		?>
		<script>
			jQuery(document).ready(function ($) {
				var cartIsEmpty = <?php echo $cart_empty; ?>;
				if (cartIsEmpty) {
					$('.cart-course').css('display', 'block');
				} else {
					$('.cart-course').css('display', 'none');
				}
			});
		</script>
		<?php
	}
});

add_action('plugins_loaded', 'override_wpems_create_new_user');

function override_wpems_create_new_user()
{
	require_once get_stylesheet_directory() . '/inc/custom-registration.php';
}

// add_action('save_post_lp_course', function($post_id) {
//     // Get WooCommerce product ID linked to course
//     $product_id = get_post_meta($post_id, '_lp_product', true);
//     if (!$product_id) return;

//     // Get course metadata
//     $duration = get_post_meta($post_id, '_lp_duration', true);
//     $duration_time = get_post_meta($post_id, '_lp_duration_time', true); // e.g., days/weeks
//     $lessons = learn_press_get_course_lesson_ids($post_id);
//     $grade = get_post_meta($post_id, '_lp_course_level', true); // Optional custom field

//     // Format details
//     $duration_display = $duration . ' ' . $duration_time;
//     $lesson_count = is_array($lessons) ? count($lessons) : 0;

//     // Sync to WooCommerce product meta
//     update_post_meta($product_id, '_lp_synced_duration', $duration_display);
//     update_post_meta($product_id, '_lp_synced_lessons', $lesson_count);
//     update_post_meta($product_id, '_lp_synced_grade', $grade);
// });

// add_filter('woocommerce_cart_item_name', function($name, $cart_item, $cart_item_key) {
//     $product_id = $cart_item['product_id'];
//     $duration = get_post_meta($product_id, '_lp_synced_duration', true);
//     $lessons = get_post_meta($product_id, '_lp_synced_lessons', true);
//     $grade = get_post_meta($product_id, '_lp_synced_grade', true);

//     $details = '';
//     if ($duration || $lessons || $grade) {
//         $details .= '<ul class="lp-cart-course-details">';
//         if ($grade)    $details .= '<li><strong>Grade:</strong> ' . esc_html($grade) . '</li>';
//         if ($lessons)  $details .= '<li><strong>Lessons:</strong> ' . esc_html($lessons) . '</li>';
//         if ($duration) $details .= '<li><strong>Duration:</strong> ' . esc_html($duration) . '</li>';
//         $details .= '</ul>';
//     }

//     return $name . $details;
// }, 10, 3);

/// 11 - 04 - 2025

add_filter('woocommerce_add_cart_item_data', 'add_learnpress_course_meta_to_cart', 10, 3);
function add_learnpress_course_meta_to_cart($cart_item_data, $product_id, $variation_id)
{
	if (get_post_type($product_id) === 'lp_course') {
		$course_id = $product_id;
		$course = learn_press_get_course($course_id);
		$lesson_count = $course->get_curriculum_items('lp_lesson');
		$total_lessons = count($lesson_count);
		$duration = get_post_meta($course_id, '_lp_duration', true);
		$grade = get_post_meta($course_id, '_course_grade', true);

		// Get lesson count
		$course = learn_press_get_course($course_id);
		$lessons = 0;

		if ($course && method_exists($course, 'get_curriculum_items')) {
			$items = $course->get_curriculum_items();
			foreach ($items as $item) {
				if ($item instanceof LP_Course_Item && $item->get_type() === 'lp_lesson') {
					$lessons++;
				}
			}
		}
		$lessons = get_post_meta($course_id, '_count_lesson', true);

		$cart_item_data['lp_course_data'] = [
			'duration' => $duration,
			'grade' => $grade,
			'lessons' => $total_lessons
		];
	}

	return $cart_item_data;
}




add_filter('woocommerce_get_item_data', 'display_learnpress_course_meta_in_cart', 10, 2);
function display_learnpress_course_meta_in_cart($item_data, $cart_item)
{
	if (isset($cart_item['lp_course_data'])) {
		$lp_data = $cart_item['lp_course_data'];

		if (!empty($lp_data['duration'])) {
			$item_data[] = [
				'key' => 'Course Duration',
				'value' => wc_clean($lp_data['duration']),
			];
		}

		if (!empty($lp_data['grade'])) {
			$item_data[] = [
				'key' => 'Course Grade',
				'value' => wc_clean($lp_data['grade'] . ' Grade'),
			];
		}

		if (!empty($lp_data['lessons'])) {
			$item_data[] = [
				'key' => 'Lessons',
				'value' => intval($lp_data['lessons']) . ' Sessions',
			];
		}
	}

	return $item_data;
}

add_action('woocommerce_checkout_create_order_line_item', 'add_learnpress_meta_to_order_items', 10, 4);
function add_learnpress_meta_to_order_items($item, $cart_item_key, $values, $order)
{
	if (isset($values['lp_course_data'])) {
		$lp_data = $values['lp_course_data'];

		if (!empty($lp_data['duration'])) {
			$item->add_meta_data('Course Duration', wc_clean($lp_data['duration']), true);
		}

		if (!empty($lp_data['course_grade_box'])) {
			$item->add_meta_data('Course Grade', wc_clean($lp_data['course_grade_box']), true);
		}

		if (!empty($lp_data['lessons'])) {
			$item->add_meta_data('Lessons', intval($lp_data['lessons']), true);
		}
	}
}


function shortcode_instructor_ongoing_courses_from_url($atts)
{
	$atts = shortcode_atts([
		'section_number' => '0', // default
	], $atts);

	// Get instructor's username from URL
	$request_uri = $_SERVER['REQUEST_URI'];
	$parts = explode('/', trim($request_uri, '/'));
	$username = end($parts);

	$instructor = get_user_by('slug', $username);
	if (!$instructor) {
		return '<p>Instructor not found in URL.</p>';
	}

	// Get all published courses by instructor
	$instructor_courses = get_posts([
		'post_type' => 'lp_course',
		'posts_per_page' => -1,
		'post_status' => 'publish',
		'author' => $instructor->ID,
	]);

	$courses_count = count($instructor_courses);

	if (empty($instructor_courses)) {
		return '<p>This instructor has no courses.</p>';
	}

	ob_start();

	echo '<div class="instructor-courses-wrapper">';
	echo '<div class="section-title">';
	echo '<span class="section-number">' . esc_html($courses_count) . '</span><br>';
	echo '<pclass="section-heading">Ongoing Courses</p>';
	echo '</div>';

	// Your loop for showing courses can go here (if needed)

	echo '</div>';

	return ob_get_clean();
}
add_shortcode('instructor_ongoing_courses_from_url', 'shortcode_instructor_ongoing_courses_from_url');



function get_woocommerce_cart_item_count()
{
	if (function_exists('WC')) {
		return WC()->cart->get_cart_contents_count();
	}
	return 0;
}
add_shortcode('cart_item_count', 'get_woocommerce_cart_item_count');

// Shortcode to display selected filters
function show_selected_filters_shortcode()
{
	if (empty($_GET))
		return ''; // No filters to show

	$html = '<div class="selected-filters">';
	foreach ($_GET as $key => $value) {
		if (empty($value))
			continue;

		$label = '';
		$displayValue = $value;

		switch ($key) {
			case 'c_authors':
				$label = 'Author';
				$author = get_user_by('ID', $value);
				if ($author) {
					$displayValue = $author->display_name;
				}
				break;

			case 'term_id':
				$label = 'Category';
				$term = get_term($value);
				if ($term && !is_wp_error($term)) {
					$displayValue = $term->name;
				}
				break;

			case 'c_review_star':
				$label = 'Rating';
				break;

			case 'sort_by':
				$label = 'Price';
				if ($value === 'on_paid') {
					$displayValue = 'Paid'; // Or use 'Pais' if preferred
				}
				break;

			case 'c_level':
				$label = 'Level';
				$displayValue = ucfirst($value);
				break;

			default:
				$label = ucwords(str_replace('_', ' ', $key));
				break;
		}

		$html .= '<span class="filter-tag">';
		$html .= esc_html($label . ': ' . $displayValue);
		$html .= ' <span class="remove-filter" data-key="' . esc_attr($key) . '">×</span>';
		$html .= '</span>';
	}



		$html .= '</div>';
		return $html;
	}
	add_shortcode('selected_filters', 'show_selected_filters_shortcode');

// JS to remove query param and reload
add_action('wp_footer', function () {
	?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			document.querySelectorAll('.remove-filter').forEach(function (btn) {
				btn.addEventListener('click', function () {
					const key = this.getAttribute('data-key');
					const url = new URL(window.location.href);
					url.searchParams.delete(key);
					window.location.href = url.toString(); // reload page without that param
				});
				});
			});
		</script>
		<?php
	});

	/* Starting of Converting Orders into Transactions */

	add_filter('gettext', 'lp_rename_orders_to_transactions', 999, 3);
	add_filter('ngettext', 'lp_rename_orders_to_transactions_plural', 999, 5);

function lp_rename_orders_to_transactions($translated, $original, $domain)
{
	if ($domain === 'learnpress') {
		$map = array(
			'Order' => 'Transaction',
			'Orders' => 'Transactions',
			'order' => 'transaction',
			'orders' => 'transactions',
		);

		if (isset($map[$original])) {
			return $map[$original];
		}
	}

	return $translated;
}

function lp_rename_orders_to_transactions_plural($translated, $single, $plural, $number, $domain)
{
	if ($domain === 'learnpress') {
		$replace = array(
			'Orders' => 'Transactions',
			'orders' => 'transactions',
		);

		if (isset($replace[$plural])) {
			return $number === 1 ? 'Transaction' : 'Transactions';
		}
	}

	return $translated;
}

add_filter('learn-press/profile-tabs', 'lp_change_order_tab_to_transaction');
function lp_change_order_tab_to_transaction($tabs)
{
	if (isset($tabs['orders'])) {
		$tabs['orders']['title'] = __('Transactions', 'learnpress');
	}

	return $tabs;
}

add_action('admin_menu', 'lp_rename_orders_admin_menu', 999);
function lp_rename_orders_admin_menu()
{
	global $menu;
	foreach ($menu as $key => $menu_item) {
		if (isset($menu_item[0]) && $menu_item[0] === 'Orders') {
			$menu[$key][0] = 'Transactions';
		}
	}
}

add_action('init', 'lp_change_order_post_type_labels', 20);
function lp_change_order_post_type_labels()
{
	global $wp_post_types;

	if (isset($wp_post_types['lp_order'])) {
		$labels = &$wp_post_types['lp_order']->labels;

		$labels->name = 'Transactions';
		$labels->singular_name = 'Transaction';
		$labels->menu_name = 'Transactions';
		$labels->name_admin_bar = 'Transaction';
		$labels->add_new = 'Add New';
		$labels->add_new_item = 'Add New Transaction';
		$labels->edit_item = 'Edit Transaction';
		$labels->new_item = 'New Transaction';
		$labels->view_item = 'View Transaction';
		$labels->search_items = 'Search Transactions';
		$labels->not_found = 'No transactions found.';
		$labels->not_found_in_trash = 'No transactions found in Trash.';
	}
}

/* End of Converting Orders into Transactions */

/* Start of Converting Quizzes into Tests */

add_filter('gettext', 'lp_rename_quizzes_to_tests', 999, 3);
add_filter('ngettext', 'lp_rename_quizzes_to_tests_plural', 999, 5);

function lp_rename_quizzes_to_tests($translated, $original, $domain)
{
	if ($domain === 'learnpress') {
		$map = array(
			'Quiz' => 'Test',
			'Quizzes' => 'Tests',
			'quiz' => 'test',
			'quizzes' => 'tests',
		);

		if (isset($map[$original])) {
			return $map[$original];
		}
	}

	return $translated;
}

function lp_rename_quizzes_to_tests_plural($translated, $single, $plural, $number, $domain)
{
	if ($domain === 'learnpress') {
		if ($plural === 'Quizzes' || $plural === 'quizzes') {
			return $number === 1 ? 'Test' : 'Tests';
		}
	}

	return $translated;
}

add_filter('learn-press/profile-tabs', 'lp_rename_quiz_tab_to_test');
function lp_rename_quiz_tab_to_test($tabs)
{
	if (isset($tabs['quizzes'])) {
		$tabs['quizzes']['title'] = __('Tests', 'learnpress');
	}

	return $tabs;
}

add_action('init', 'lp_rename_quiz_post_type_labels', 20);
function lp_rename_quiz_post_type_labels()
{
	global $wp_post_types;

	if (isset($wp_post_types['lp_quiz'])) {
		$labels = &$wp_post_types['lp_quiz']->labels;

		$labels->name = 'Tests';
		$labels->singular_name = 'Test';
		$labels->menu_name = 'Tests';
		$labels->name_admin_bar = 'Test';
		$labels->add_new = 'Add New Test';
		$labels->add_new_item = 'Add New Test';
		$labels->edit_item = 'Edit Test';
		$labels->new_item = 'New Test';
		$labels->view_item = 'View Test';
		$labels->search_items = 'Search Tests';
		$labels->not_found = 'No tests found.';
		$labels->not_found_in_trash = 'No tests found in Trash.';
	}
}


/* End of Converting Quizzes into Tests */

function hide_admin_ui_for_iframe()
{
	if (isset($_GET['iframe']) && $_GET['iframe'] === 'true') {
		echo '<style>
            #adminmenu, #adminmenuback, #adminmenuwrap, #wpadminbar, #screen-meta-links, #screen-meta {
    		display: none !important;
    	}
            #wpcontent, #wpfooter {
    	margin-left: 0 !important;
    }
    .wrap {
    	padding: 20px;
    }
    </style>';
	}
}
add_action('admin_head', 'hide_admin_ui_for_iframe');

function lp_force_iframe_param()
{
	if (
		is_admin() &&
		!current_user_can('administrator') &&
		!defined('DOING_AJAX') &&
		isset($_SERVER['HTTP_REFERER']) &&
		strpos($_SERVER['HTTP_REFERER'], 'iframe=true') !== false &&
		!isset($_GET['iframe'])
	) {
		wp_redirect(add_query_arg('iframe', 'true'));
		exit;
	}
}
add_action('admin_init', 'lp_force_iframe_param');


add_action('wp_footer', 'custom_expand_coupon_on_cart_page');
function custom_expand_coupon_on_cart_page()
{
	if (is_cart()) {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				const couponToggle = $('.wc-block-components-panel__button:contains("Add a coupon")');

				if (couponToggle.length && couponToggle.attr('aria-expanded') === 'false') {
					couponToggle.attr('aria-expanded', 'true');
					couponToggle.trigger('click');
				}
			});
		</script>
		<?php
	}
}

add_filter('learn-press/profile-tabs', 'lp_rename_my_course_tab_to_test');
function lp_rename_my_course_tab_to_test($tabs)
{
	if (isset($tabs['my-courses'])) {
		$tabs['my-courses']['title'] = __('Overview', 'learnpress');
	}

	return $tabs;
}

add_filter('learn-press/profile-tabs', 'lp_rename_course_tab_to_test');
function lp_rename_course_tab_to_test($tabs)
{
	if (isset($tabs['my-courses'])) {
		$tabs['courses']['title'] = __('Overview', 'learnpress');
	}

	return $tabs;
}


add_action('learnpress/user-profile/summary', 'show_instructor_courses_in_dashboard');

function show_instructor_courses_in_dashboard()
{
	// Only show to instructors
	if (!function_exists('learn_press_is_instructor') || !learn_press_is_instructor()) {
		return;
	}

	$current_user_id = get_current_user_id();

	$args = array(
		'post_type' => 'lp_course',
		'post_status' => array('publish', 'pending'),
		'posts_per_page' => -1,
		'author' => $current_user_id,
	);

	$courses = get_posts($args);

	echo '<h3>My Courses</h3>';

	if (!empty($courses)) {
		echo '<ul class="lp-instructor-course-list">';
		foreach ($courses as $course) {
			$course_url = get_permalink($course->ID);
			echo '<li><a href="' . esc_url($course_url) . '">' . esc_html($course->post_title) . '</a></li>';
		}
		echo '</ul>';
	} else {
		echo '<p>You have not created any courses yet.</p>';
	}
}

// Validate login fields
// Custom login error messages
add_filter('authenticate', function ($user, $username, $password) {
	if (empty($username) || empty($password)) {
		$error = new WP_Error();

		if (empty($username)) {
			$error->add('empty_username', '<strong>ERROR</strong>: Please enter your username or email.');
		}

		if (empty($password)) {
			$error->add('empty_password', '<strong>ERROR</strong>: Please enter your password.');
		}

		return $error;
	}
	return $user;
}, 20, 3);

// Redirect after successful login (only non-admins)
add_filter('login_redirect', function ($redirect_to, $request, $user) {
	if (!is_wp_error($user)) {
		// Check if the user is NOT an admin
		if (!in_array('administrator', (array) $user->roles)) {
			return home_url(); // Redirect non-admins to home
		}
	}
	return $redirect_to; // Default redirect for admins
}, 10, 3);


add_action('wp_enqueue_scripts', function () {
	if (is_cart()) {
		wp_enqueue_script('jquery');

		$script = <<<JS
        jQuery(document).ready(function ($) {
            $('.wp-block-woocommerce-cart-line-items-block .wc-block-cart-item__product').each(function () {
                const staticDiv = '<div class="my-static-cart-banner" style="margin-bottom: 8px; color: #0073aa; font-size: 14px;">⭐ LearnPress Course Highlight</div>';
                $(this).prepend(staticDiv);
            });
        });
        JS;

		wp_add_inline_script('jquery', $script);
	}
});

/* End of Add button Click */

add_action('wp_footer', function () {
	?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			// Store referrer in sessionStorage when the page loads
			if (document.referrer && !sessionStorage.getItem('customReferrer')) {
				sessionStorage.setItem('customReferrer', document.referrer);
			}

			document.addEventListener('click', function (e) {
				// Check if the clicked element is the <i> inside .back_course
				if (e.target.closest('.back_course') && e.target.tagName.toLowerCase() === 'i') {
					e.preventDefault();

					let storedReferrer = sessionStorage.getItem('customReferrer');

					if (storedReferrer) {
						window.location.href = storedReferrer;
					} else {
						window.location.href = '/courses'; // fallback
					}
				}
			});
		});
	</script>
	<?php
});

function move_lp_assignment_tab_up($tabs)
{
	if (isset($tabs['assignments'])) {
		$assignment_tab = $tabs['assignments'];
		unset($tabs['assignments']);

		// Rebuild the tabs array with "assignments" first
		$new_tabs = array('assignments' => $assignment_tab);

		foreach ($tabs as $key => $tab) {
			$new_tabs[$key] = $tab;
		}

		return $new_tabs;
	}

	return $tabs;
}
add_filter('learn-press/profile-tabs', 'move_lp_assignment_tab_up', 90);

/* Start of Add Weekly assignment html call */

add_action('wp_ajax_render_add_weekly_assignment_form', 'render_add_weekly_assignment_form_callback');

function render_add_weekly_assignment_form_callback()
{
	ob_start();
	?>
	<div class="assignment-wrapper">
		<div class="assignment-title">
			<h2>Add Assignment</h2>
		</div>
		<div class="assignment-content">
			<form id="add-assignment-form" method="post" enctype="multipart/form-data">
				<div class="form-row">
					<label for="title">Title <span style="color:red;">*</span></label>
					<br>
					<input type="text" name="assignment_title" placeholder="Assignment Title" required>
				</div>
				<div class="form-row">
					<label for="description">Description</label><br>
					<textarea name="assignment_description" placeholder="Assignment Description" required></textarea>
				</div>
				
				<div class="form-row">
						<!-- Course dropdown (replace with dynamic courses if needed) -->
						<select name="assignment_course_id" class="responsive-desktop-cls-adding-here">
							<option value="">Select Course</option>
							<?php
							$current_user_id = get_current_user_id();

							$courses = get_posts([
								'post_type' => 'lp_course',
								'post_status' => 'publish',
								'numberposts' => -1,
								'author'      => $current_user_id
							]);
							foreach ($courses as $course) {
								echo '<option value="' . esc_attr($course->ID) . '">' . esc_html($course->post_title) . '</option>';
							}
							?>
						</select>
					</div>

					<div class="bottom-form">
						<h3>Documentation</h3>

						<div class="form-row">
						    <label for="assignment_introduction">Introduction</label>
							<div class="input-content">
						    <textarea name="assignment_introduction" placeholder="Introduction" required></textarea>
							</div>
						</div>

						<div class="form-row new-attachment-cls-adding-od">
						    <label for="attachment">Attachment</label>
							<div class="input-content">
						    <input type="file" name="assignment_attachment" accept=".pdf, image/*, .doc, .docx" id="assignment_attachment" />
						    <p>Upload a file (PDF, Image, or DOC file).</p>
							</div>
						</div>


					</div>


				<div class="bottom-form">
					<h3>General Settings</h3>
					<div class="form-row">
						<label for="duration">Duration</label>
						<div class="input-content">
							<input type="number" name="assignment_duration" placeholder="Duration" required>
							<select name="assignment_duration_unit" class="duration-generation-setting-cls-adding">
								<option value="minute">Minutes</option>
								<option value="hour">Hours</option>
								<option value="day" selected>Days</option>
							</select>
							<p>Set 0 for unlimited time.</p>
						</div>
					</div>

					<div class="form-row">
						<label for="mark">Mark</label>
						<div class="input-content">
							<input type="number" name="assignment_mark" placeholder="Mark" required>
							<p>Maximum mark can the students receive.</p>
						</div>
					</div>

					<div class="form-row">
						<label for="passing-grade">Passing grade</label>
						<div class="input-content">
							<input type="number" name="assignment_passing_grade" placeholder="Passing Grade (%)" required>
							<p>Requires user reached this point to pass the assignment.</p>
						</div>
					</div>
					
					<div class="form-row">
						<label for="re-take">Re-take</label>
						<div class="input-content">
							<input type="number" name="assignment_retake_limit" placeholder="Retake Limit" required>
							<p>How many times the user can re-take this assignment. set to 0 to disable.</p>
						</div>
					</div>

					<div class="form-row">
						<label for="upload-files">Upload files</label>
						<div class="input-content">
							<input type="number" name="assignment_upload_files" placeholder="Upload Files" required>
							<p>Number files the user can upload with this assignment. Set to 0 to disable</p>
						</div>
					</div>

					<div class="form-row">
						<label for="size-limit">File Limit</label>
						<div class="input-content">
							<input type="number" name="assignment_file_limit" placeholder="File Upload Limit" required>
							<p>Set maximum attachment size for upload (Set less than 128 MB)</p>
						</div>
					</div>

				</div>

				<input type="hidden" name="action" value="save_custom_assignment">
				<div class="button-row">
					<button type="submit" class="btn-custom primary">Save Assignment</button>
				</div>
			</form>
		</div>
	</div>

	<script>
	jQuery(document).ready(function($) {
		$('#assignment_description').attr('placeholder', 'Enter assignment description here...');
	});
	</script>
	<?php
	echo ob_get_clean();
	wp_die();
}

// Ajax function for assignment listing for Pagination

add_action( 'wp_ajax_load_instructor_assignments', 'load_instructor_assignments_callback' );

function load_instructor_assignments_callback() {
    $paged = isset( $_POST['paged'] ) ? max( 1, intval( $_POST['paged'] ) ) : 1;
    $current_user_id = get_current_user_id();

    $args = array(
        'post_type'      => 'lp_assignment',
        'posts_per_page' => 5,
        'paged'          => $paged,
        'author'         => $current_user_id,
        'post_status'    => array( 'publish', 'draft', 'pending' ),
    );

    $assignment_query = new WP_Query( $args );
    error_log( 'Found assignments: ' . $assignment_query->found_posts );

    ob_start();
    if ( $assignment_query->have_posts() ) {
        ?>
        <div class="lp-instructor-assignments">
        	<h3 class="profile-heading"><?php esc_html_e( 'Added Assignments List', 'learnpress-assignments' ); ?></h3>
	        <div class="custom-table-wrapper">
		        <table class="lp-list-table instructor-assignment-list">
		            <?php if ( ! empty( $assignment_query ) ) : ?>
		            <thead>
		                <tr>
		                    <th><?php esc_html_e( 'Assignment Name', 'learnpress-assignments' ); ?></th>
		                    <th><?php esc_html_e( 'Assignment Date', 'learnpress-assignments' ); ?></th>
		                    <th><?php esc_html_e( 'Submission date', 'learnpress-assignments' ); ?></th>
		                    <th><?php esc_html_e( 'Actions', 'learnpress-assignments' ); ?></th>
		                </tr>
		            </thead>
		            <tbody>
		                <?php foreach ( $assignment_query->posts as $assignment ) :
		                    $assignment_id = $assignment->ID;

		                    // echo '<pre>'; print_r($assignment_id );
		                    $due_date      = get_post_meta( $assignment_id, '_lp_duration', true );
		                    $course_id     = get_post_meta( $assignment_id, '_lp_course', true );
		                    $course_title  = $course_id ? get_the_title( $course_id ) : __( 'Not Assigned', 'learnpress-assignments' );
		                    $course_link   = $course_id ? get_permalink( $course_id ) : '#';
		                    
		                    $edit_url   = get_edit_post_link( $assignment_id );
		                    $delete_url = get_delete_post_link( $assignment_id, '', true );

		                    $post_status = get_post_status_object( get_post_status( $assignment_id ) );
		                    $status_label = $post_status ? $post_status->label : __( 'Unknown', 'learnpress-assignments' );
		                    $publish_date = get_the_date( 'Y-m-d', $assignment_id );

		                    global $wpdb;
		                    $submission_count = $wpdb->get_var( $wpdb->prepare(
		                        "SELECT COUNT(*) FROM {$wpdb->prefix}learnpress_user_items 
		                         WHERE item_id = %d AND item_type = %s AND user_id != 0",
		                        $assignment_id,
		                        'lp_assignment'
		                    ) );

							$submission_date = $wpdb->get_var( $wpdb->prepare(
								"SELECT MAX(start_time) FROM {$wpdb->prefix}learnpress_user_items 
								WHERE item_id = %d AND item_type = %s AND user_id != 0",
								$assignment_id,
								'lp_assignment'
							));
		                ?>
		                    <tr>
		                        <td><a href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( $assignment->post_title ); ?></a>
		                            <br>
		                            <?php if ( $course_id ) : ?>
		                                <small> 
		                                    <a href="<?php echo esc_url( $course_link ); ?>" target="_blank"><?php echo esc_html( $course_title ); ?></a>
		                                </small>
		                            <?php else : ?>
		                                <small><?php esc_html_e( 'Course: Not Assigned', 'learnpress-assignments' ); ?></small>
		                            <?php endif; ?>
		                        </td>
		                        <td><?php echo $publish_date ? esc_html( date( 'M d, Y', strtotime( $publish_date ) ) ) : __( 'NA', 'learnpress-assignments' ); ?></td>
		                        <td><?php echo $submission_date; ?></td>
		                        <td>
		                            <a href="javascript:void(0)" class="button button-primary view-assignment-btn" data-assignment-id="<?php echo esc_attr( $assignment_id ); ?>">
		                                <?php esc_html_e( 'View', 'learnpress-assignments' ); ?>
		                            </a>
		                            <a href="javascript:void(0)" class="button button-primary edit-assignment-btn" data-assignment-id="<?php echo esc_attr( $assignment_id ); ?>">
		                                <?php esc_html_e( 'Edit', 'learnpress-assignments' ); ?>
		                            </a>
		                            <a href="javascript:void(0)" 
		                               class="button button-secondary view-submission" data-assignment-id="<?php echo esc_attr( $assignment_id ); ?>">
		                                <?php esc_html_e( 'View Submissions', 'learnpress-assignments' ); ?>
		                            </a>
		                             <a href="<?php echo esc_url( $delete_url ); ?>" 
		                               class="button button-link delete-assignment" 
		                               data-delete-url="<?php echo esc_url( $delete_url ); ?>">
		                               <?php esc_html_e( 'Delete', 'learnpress-assignments' ); ?>
		                            </a>
		                        </td>
		                    </tr>
		                <?php endforeach; ?>
		            </tbody>

		            <?php else : ?>
		                <p><?php esc_html_e( 'No assignments found.', 'learnpress-assignments' ); ?></p>
		            <?php endif; ?>
		            <tfoot>
		                <tr>
		                    <td colspan="4">
		                        <div class="add-assignment-button" style="margin-top: 20px;">
		                            <a href="#" class="button button-primary add-weekly-assignments">
		                                <?php esc_html_e( 'Add Weekly Assignments', 'learnpress-assignments' ); ?>
		                            </a>
		                        </div>
		                    </td>
		                </tr>
		            </tfoot>
		        </table>
		         <div id="deleteModal" class="lp-modal-overlay" style="display:none;">
		          <div class="lp-modal">
		            <p>Are you sure you want to delete this assignment?</p>
		            <div class="lp-modal-actions">
		              <button id="cancelDelete" class="lp-btn lp-btn-cancel">Cancel</button>
		              <a id="confirmDelete" class="lp-btn lp-btn-delete" href="#">Delete</a>
		            </div>
		          </div>
		        </div>
		        <style type="text/css">
				    .lp-modal-overlay {
				  position: fixed;
				  top: 0;
				  left: 0;
				  width: 100%;
				  height: 100%;
				  background: rgba(0,0,0,0.5);
				  display: flex;
				  align-items: center;
				  justify-content: center;
				  z-index: 9999;
				}
				.lp-modal {
				  background: #fff;
				  padding: 20px;
				  border-radius: 8px;
				  text-align: center;
				}
				.lp-modal-actions {
				  margin-top: 20px;
				}
				.lp-btn {
				  padding: 8px 16px;
				  margin: 0 5px;
				  cursor: pointer;
				}
				.lp-btn-delete {
				  background-color: #d9534f;
				  color: #fff;
				  border: none;
				}
				.lp-btn-cancel {
				  background-color: #6c757d;
				  color: #fff;
				  border: none;
				}

				</style>
		        <?php
		        // Simple pagination (replace with paginate_links if desired)
		        $total_pages = $assignment_query->max_num_pages;
		        echo '<div class="custom-pagination">';
		        for ( $i = 1; $i <= $total_pages; $i++ ) {
		            echo '<a href="#" class="pagination-link' . ( $i === $paged ? ' current' : '' ) . '" data-page="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</a> ';
		        }
		        echo '</div>'; ?>
		    </div>
		    <div id="assignment-edit-container" style="display: none;"></div>
        	<!-- Each assignment item here for adding new -->
		    <div id="assignment-form-response"></div>

		    <!-- <div id="assignment-listing" class="assignment-listing"> -->
		        <!-- Each assignment item here for Editing-->
		    <!-- </div> -->

		    <div id="assignment-edit-form-container"></div> <!-- The form will be loaded here -->

		    <!-- Assignment List Container -->
		    <div class="lp-list-table">
		        <!-- Your assignment listing content -->
		    </div>

		    <!-- View Submission List Container -->
		    <div id="assignment-submissions-container" style="display: none;" class="back-to-assignment-cls-adding-here"></div><!-- Submission list will be loaded here -->
		</div>

    <?php } else {
        echo '<p>No assignments found.</p>';
    }

    wp_send_json_success( ob_get_clean() );
}


/* End of Add Weekly assignment html call */

/* Start of Add Weekly assignment Save call */

add_action('wp_ajax_save_custom_assignment', 'save_custom_assignment_callback');
add_action('wp_ajax_nopriv_save_custom_assignment', 'save_custom_assignment_callback');

function save_custom_assignment_callback() {
    // Security & permissions
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Please login first.']);
    }

    if (!isset($_POST['assignment_title'])) {
        wp_send_json_error('Missing required fields');
    }

    // Sanitize and validate fields
    $title = sanitize_text_field($_POST['assignment_title']);
    $desc = sanitize_textarea_field($_POST['assignment_description']);
    $duration = isset($_POST['assignment_duration']) ? absint($_POST['assignment_duration']) : 0;
    $unit = isset($_POST['assignment_duration_unit']) ? sanitize_text_field($_POST['assignment_duration_unit']) : 'day';
    $duration_string = $duration . ' ' . $unit;
    $mark = intval($_POST['assignment_mark']);
    $upload_files = intval($_POST['assignment_upload_files']);
    $passing_grade = intval($_POST['assignment_passing_grade']);
    $file_limit = sanitize_text_field($_POST['assignment_file_limit']);
    $retake_limit = sanitize_text_field($_POST['assignment_retake_limit']);
    $course_id = intval($_POST['assignment_course_id']);
    $introduction = sanitize_textarea_field($_POST['assignment_introduction']); 

    // Create Assignment Post
    $assignment_id = wp_insert_post([
        'post_title' => $title,
        'post_content' => $desc,
        'post_status' => 'publish',
        'post_type' => LP_ASSIGNMENT_CPT,
        'post_parent' => $course_id
    ]);

    if (is_wp_error($assignment_id) || !$assignment_id) {
        wp_send_json_error('Failed to create assignment');
    }

    // Save meta data
    update_post_meta($assignment_id, '_lp_assignment_type', 'upload_file');
    update_post_meta($assignment_id, '_lp_duration', $duration_string);
    update_post_meta($assignment_id, '_lp_mark', $mark);
    update_post_meta($assignment_id, '_lp_upload_files', $upload_files);
    update_post_meta($assignment_id, '_lp_passing_grade', $passing_grade);
    update_post_meta($assignment_id, '_lp_file_extension', $file_limit);
    update_post_meta($assignment_id, '_lp_upload_file_limit', $file_limit);
    update_post_meta($assignment_id, '_lp_retake_count', $retake_limit);
    update_post_meta($assignment_id, '_lp_introduction', $introduction);

    // Handle file upload (Attachment)
   if (!empty($_FILES['assignment_attachment']['name'])) {
    $allowed_mimes = array(
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // .docx
    );

    $file_type = $_FILES['assignment_attachment']['type'];

    // Double check the file extension too
    $file_name = $_FILES['assignment_attachment']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

    if (!in_array($file_type, $allowed_mimes) || !in_array($file_ext, $allowed_extensions)) {
        wp_send_json_error(['message' => 'Invalid file type.']);
    }

    // Proceed with upload
    $attachment_id = media_handle_upload('assignment_attachment', $assignment_id);

    if (!is_wp_error($attachment_id)) {
        update_post_meta($assignment_id, '_lp_attachments', $attachment_id);
    } else {
        wp_send_json_error(['message' => 'File upload failed.']);
    }
}


    wp_send_json_success([
        'message' => 'Assignment created successfully!',
        'assignment_id' => $assignment_id
    ]);
}


/* End of Add Weekly assignment Save call */

/* Start of Edit Assignment */

add_action('wp_ajax_load_edit_assignment_form', 'load_edit_assignment_form_callback');

function load_edit_assignment_form_callback() {
    $assignment_id = intval($_POST['assignment_id']);
    $duration_raw = get_post_meta($assignment_id, '_lp_duration', true);
    $attachment_id = get_post_meta($assignment_id, '_lp_attachments', true);
	$duration_value = 0;
	$duration_unit = 'day';

	if (!empty($duration_raw) && is_string($duration_raw)) {
	    $parts = explode(' ', $duration_raw);
	    $duration_value = isset($parts[0]) ? intval($parts[0]) : 0;
	    $duration_unit = isset($parts[1]) ? $parts[1] : 'day';
	}

    if (!current_user_can('edit_post', $assignment_id)) {
        wp_send_json_error(__('Unauthorized', 'learnpress-assignments'));
    }

    $assignment = get_post($assignment_id);
    if (!$assignment || $assignment->post_type !== 'lp_assignment') {
        wp_send_json_error(__('Invalid assignment', 'learnpress-assignments'));
    }

    ob_start();
    ?>
    <div class="assignment-wrapper">
		<div class="assignment-title">
			<h2>Edit Assignment</h2>
		</div>
		<div class="assignment-content">
		    <form id="edit-assignment-form" enctype="multipart/form-data">
			    <input type="hidden" name="assignment_id" value="<?php echo esc_attr($assignment_id); ?>" />

				<div class="bottom-form">
			    <div class="form-row">
			        <label for="assignment_title">Title <span style="color:red;">*</span></label>
					<div class="input-content">
			        <input type="text" name="title" value="<?php echo esc_attr($assignment->post_title); ?>" />
					</div>
			    </div>

			    <div class="form-row">
			        <label for="assignment_description">Description</label>
					<div class="input-content">
			        <textarea name="description"><?php echo esc_attr($assignment->post_content); ?></textarea>
					</div>
			    </div>

			    <div class="form-row">
				    <label for="assignment_introduction">Introduction</label>
					<div class="input-content">
				    <textarea name="assignment_introduction"><?php echo esc_textarea(get_post_meta($assignment_id, '_lp_introduction', true)); ?></textarea>
					</div>
				</div>

				<div class="form-row new-attachment-cls-adding">
				    <label for="attachment">Attachment</label>
					<div class="input-content">
				     <input type="file" name="assignment_attachment" accept=".pdf, image/*, .doc, .docx" id="assignment_attachment" />

				    <p>Re-Upload a file (PDF, Image, or DOC file).</p>
				    <?php 
				    // Display existing attachment (if any)
				    if ($attachment_id) {
					    $attachment_url = wp_get_attachment_url($attachment_id);
					    echo '<p>Current Attachment: <a href="' . esc_url($attachment_url) . '" target="_blank" class="all-view-attachment-cls-adding">View Attachment</a></p>';
					}
				    ?>
					</div>
				</div>


				<div class="form-row">
						<label for="duration">Duration</label>
						<div class="input-content">
							<input type="number" name="assignment_duration" value="<?php echo esc_attr($duration_value); ?>">
							<select name="assignment_duration_unit" class="duration-generation-setting-cls-adding">
							    <option value="minute" <?php selected($duration_unit, 'minute'); ?>>Minutes</option>
							    <option value="hour" <?php selected($duration_unit, 'hour'); ?>>Hours</option>
							    <option value="day" <?php selected($duration_unit, 'day'); ?>>Days</option>
							    <option value="week" <?php selected($duration_unit, 'week'); ?>>Weeks</option>
							</select>
							<p>Set 0 for unlimited time.</p>
						</div>
					</div>
			    <!-- <div class="form-row">
			        <label for="assignment_due_date">Duration (minutes)</label>
			        <input type="number" name="due_date" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_duration', true)); ?>" />
			    </div> -->

			    <div class="form-row">
			        <label for="assignment_mark">Mark</label>
					<div class="input-content">
			        <input type="number" name="mark" min="0" max="100" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_mark', true)); ?>" />
					</div>
			    </div>

			    <div class="form-row">
			        <label for="assignment_passing_grade">Passing Grade (%)</label>
				<div class="input-content">
			        <input type="number" name="passing_grade" min="0" max="100" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_passing_grade', true)); ?>" />
				</div>
			    </div>

			    <div class="form-row">
			        <label for="assignment_retake_limit">Retake Limit</label>
				<div class="input-content">
			        <input type="number" name="retake_count" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_retake_count', true)); ?>" />
				</div>
			    </div>

			    <div class="form-row">
			        <label for="assignment_upload_files">Upload Files</label>
					<div class="input-content">
			        <input type="number" name="upload_files" min="0" max="100" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_upload_files', true)); ?>" />
					</div>
			    </div>

			    <!-- <div class="form-row">
			        <label for="assignment_file_ext">Allowed File Extensions (comma-separated)</label>
			        <input type="text" name="file_ext" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_file_extension', true)); ?>" />
			    </div> -->

			    <div class="form-row">
			        <label for="assignment_file_limit">File Limit</label>
					<div class="input-content">
			        <input type="number" name="file_limit" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_upload_file_limit', true)); ?>" />
					</div>
			    </div>

				</div>


			    <button type="submit" id="save-assignment-btn" class="without-border-typed">Update</button>
			    
			</form>
		</div>
	</div>
    <?php
    $html = ob_get_clean();

    wp_send_json_success($html);
}


add_action('wp_ajax_save_edit_assignment', 'save_edit_assignment_callback');

function save_edit_assignment_callback() {
    // Check required ID
    if (empty($_POST['assignment_id'])) {
        wp_send_json_error(__('Missing assignment ID.', 'learnpress-assignments'));
    }

    $assignment_id = intval($_POST['assignment_id']);

    // Basic sanitization
    $title         = sanitize_text_field($_POST['title'] ?? '');
    $description   = sanitize_textarea_field($_POST['description'] ?? '');
    $introduction  = sanitize_textarea_field($_POST['assignment_introduction'] ?? '');
    $duration      = absint($_POST['assignment_duration'] ?? 0);
    $unit          = sanitize_text_field($_POST['assignment_duration_unit'] ?? 'day');
    $duration_str  = $duration . ' ' . $unit;

    // Permission check
    if (!current_user_can('edit_post', $assignment_id)) {
        wp_send_json_error(__('Unauthorized', 'learnpress-assignments'));
    }

    // Update post
    wp_update_post([
        'ID'           => $assignment_id,
        'post_title'   => $title,
        'post_content' => $description,
    ]);

    // Meta fields
    update_post_meta($assignment_id, '_lp_duration', $duration_str);
    update_post_meta($assignment_id, '_lp_description', $description);
    update_post_meta($assignment_id, '_lp_introduction', $introduction);
    update_post_meta($assignment_id, '_lp_mark', sanitize_text_field($_POST['mark'] ?? ''));
    update_post_meta($assignment_id, '_lp_passing_grade', sanitize_text_field($_POST['passing_grade'] ?? ''));
    update_post_meta($assignment_id, '_lp_file_limit', sanitize_text_field($_POST['file_limit'] ?? ''));
    update_post_meta($assignment_id, '_lp_upload_files', sanitize_text_field($_POST['upload_files'] ?? ''));
    update_post_meta($assignment_id, '_lp_retake_count', sanitize_text_field($_POST['retake_count'] ?? ''));
    update_post_meta($assignment_id, '_lp_file_extension', sanitize_text_field($_POST['file_ext'] ?? ''));

    // Handle file upload
    if (!empty($_FILES['assignment_attachment']['name'])) {
        $allowed_mimes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $allowed_exts = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

        $file_type = $_FILES['assignment_attachment']['type'];
        $file_name = $_FILES['assignment_attachment']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_type, $allowed_mimes) || !in_array($file_ext, $allowed_exts)) {
            wp_send_json_error(['message' => 'Invalid file type.']);
        }

        $attachment_id = media_handle_upload('assignment_attachment', $assignment_id);

        if (is_wp_error($attachment_id)) {
            wp_send_json_error(['message' => 'File upload failed.']);
        }

        $old_attachment = get_post_meta($assignment_id, '_lp_attachments', true);
        if ($old_attachment && $old_attachment != $attachment_id) {
            wp_delete_attachment($old_attachment, true);
        }

        update_post_meta($assignment_id, '_lp_attachments', $attachment_id);
    }

    wp_send_json_success(__('Assignment updated successfully!', 'learnpress-assignments'));
}


/* End of Edit Assignment */

/* Start of View Assignment */

add_action('wp_ajax_load_view_assignment_form', 'load_view_assignment_form_callback');

function load_view_assignment_form_callback() {
    $assignment_id = intval($_POST['assignment_id']);
    $attachment_id = get_post_meta($assignment_id, '_lp_attachments', true);
    $duration_raw  = get_post_meta($assignment_id, '_lp_duration', true);
	$duration_value = 0;
	$duration_unit = 'day';

	if (!empty($duration_raw) && is_string($duration_raw)) {
	    $parts = explode(' ', $duration_raw);
	    $duration_value = isset($parts[0]) ? intval($parts[0]) : 0;
	    $duration_unit  = isset($parts[1]) ? $parts[1] : 'day';
	}

    if (!current_user_can('edit_post', $assignment_id)) {
        wp_send_json_error(__('Unauthorized', 'learnpress-assignments'));
    }

    $assignment = get_post($assignment_id);
    if (!$assignment || $assignment->post_type !== 'lp_assignment') {
        wp_send_json_error(__('Invalid assignment', 'learnpress-assignments'));
    }

    ob_start();
    ?>
    <div class="assignment-wrapper">
		<div class="assignment-title">
			<h2>View Assignment Detail</h2>
		</div>
		<div class="assignment-content">
		    <form id="edit-assignment-form" enctype="multipart/form-data">
			    <input type="hidden" name="assignment_id" value="<?php echo esc_attr($assignment_id); ?>" />

			    <div class="form-row">
			        <label for="assignment_title">Title</label>
			        <input type="text" readonly name="title" value="<?php echo esc_attr($assignment->post_title); ?>" />
			    </div>

			    <div class="form-row">
			        <label for="assignment_description">Description</label>
			        <textarea readonly name="description"><?php echo esc_attr($assignment->post_content); ?></textarea>
			    </div>

			    <div class="form-row">
				    <label for="assignment_introduction">Introduction</label>
				    <textarea readonly name="assignment_introduction"><?php echo esc_textarea(get_post_meta($assignment_id, '_lp_introduction', true)); ?></textarea>
				</div>

				<div class="bottom-form">
				<div class="form-row">
				    <label for="attachment">Attachment</label>
					<div class="input-content">
				    <!-- <input type="file" name="assignment_attachment" accept="application/pdf,image/*" />
				    <p>Re-Upload a file (PDF, Image, etc.).</p> -->
				    <?php 
				    // Display existing attachment (if any)
				    if ($attachment_id) {
					    $attachment_url = wp_get_attachment_url($attachment_id);
					    echo '<p class="current-attachment-type-cls-adding">Current Attachment: <a href="' . esc_url($attachment_url) . '" target="_blank" class="newall-view-attachment-cls-adding">View Attachment</a></p>';
					}
				    ?>
					</div>
				</div>	
				</div>


				<div class="bottom-form">
			    <div class="form-row">
			    	<label for="duration">Duration</label>
					<div class="input-content">
			        		<input type="number" name="assignment_duration" readonly value="<?php echo esc_attr($duration_value); ?>">
							<select name="assignment_duration_unit" disabled class="duration-generation-setting-cls-adding">
							    <option value="minute" <?php selected($duration_unit, 'minute'); ?>>Minutes</option>
							    <option value="hour" <?php selected($duration_unit, 'hour'); ?>>Hours</option>
							    <option value="day" <?php selected($duration_unit, 'day'); ?>>Days</option>
							    <option value="week" <?php selected($duration_unit, 'week'); ?>>Weeks</option>
							</select>
				</div>
			    </div>
				</div>

			    <div class="form-row">
			        <label for="assignment_mark">Mark</label>
			        <input type="number" readonly name="mark" min="0" max="100" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_mark', true)); ?>" />
			    </div>

			    <div class="form-row">
			        <label for="assignment_passing_grade">Passing Grade (%)</label>
			        <input type="number" readonly name="passing_grade" min="0" max="100" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_passing_grade', true)); ?>" />
			    </div>

			    <div class="form-row">
			        <label for="assignment_retake_limit">Retake Limit</label>
			        <input type="number" readonly name="retake_count" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_retake_count', true)); ?>" />
			    </div>

			    <div class="form-row">
			        <label for="assignment_upload_files">Upload Files</label>
			        <input type="number" readonly name="upload_files" min="0" max="100" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_upload_files', true)); ?>" />
			    </div>

			    <!-- <div class="form-row">
			        <label for="assignment_file_ext">Allowed File Extensions (comma-separated)</label>
			        <input type="text" readonly name="file_ext" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_file_extension', true)); ?>" />
			    </div> -->

			    <div class="form-row">
			        <label for="assignment_file_limit">File Limit</label>
			        <input type="number" readonly name="file_limit" value="<?php echo esc_attr(get_post_meta($assignment_id, '_lp_upload_file_limit', true)); ?>" />
			    </div>

			    <!-- <button type="submit" id="save-assignment-btn" class="without-border-typed">Save</button> -->
			</form>
		</div>
	</div>
    <?php
    $html = ob_get_clean();

    wp_send_json_success($html);
}


/* End of View Assignment */

/* Start of List of Submission Ajax call */

add_action('wp_ajax_load_assignment_submissions', 'load_assignment_submissions_callback');

function load_assignment_submissions_callback() {
    global $wpdb;

    $assignment_id = intval($_POST['assignment_id']);

    if (!current_user_can('edit_post', $assignment_id)) {
        echo '<p>' . esc_html__('Unauthorized', 'learnpress-assignments') . '</p>';
        wp_die();
    }

    $submissions = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}learnpress_user_items 
         WHERE item_id = %d AND item_type = %s",
         $assignment_id, 'lp_assignment'
    ));

    if (empty($submissions)) {
        echo '<p class="no-submission-cls-adding-here">No submissions yet.</p>';
        wp_die();
    }
	echo '<h2 class="profile-heading">Submission List</h2>';
    echo '<div class="custom-table-wrapper assignment-wrapper">';
    echo '<table class="lp-submissions-table widefat striped">';
    echo '<thead><tr>
            <th>Student</th>
            <th>Status</th>
            <th>Submitted At</th>
            <th>Actions</th>
          </tr></thead><tbody>';

    foreach ($submissions as $submission) {
        $user_id = $submission->user_id;
        $user = get_userdata($user_id);
        $user_item_id = $submission->user_item_id;
        $submitted_at = $submission->submitted_at ?: $submission->start_time;

        $course_id = $submission->ref_id;

        // Evaluate URL
        $evaluate_nonce = wp_create_nonce("evaluate-assignment-{$assignment_id}-{$user_id}");
        $evaluate_url = admin_url(add_query_arg(array(
            'page'              => 'assignment-evaluate',
            'assignment_id'     => $assignment_id,
            'user_id'           => $user_id,
            'course_id'         => $course_id,
            'assignment-nonce'  => $evaluate_nonce,
        ), 'admin.php'));

        echo '<tr class="submission-row">';
        echo '<td>' . esc_html($user->display_name) . '</td>';
        echo '<td>' . esc_html($submission->status) . '</td>';
        echo '<td>' . esc_html(date('M-d-y', strtotime($submitted_at))) . '</td>';
        echo '<td class="actions column-actions">';
        echo '<div class="assignment-students-actions" 
                    data-user_id="' . esc_attr($user_id) . '" 
                    data-assignment_id="' . esc_attr($assignment_id) . '" 
                    data-course_id="' . esc_attr($course_id) . '" 
                    data-user-item-id="' . esc_attr($user_item_id) . '">';
        
        echo '<a href="javascript:void(0)" 
			    class="check-submission" 
			    title="' . esc_attr($dynamic_title) . '" 
			    data-assignment-id="' . esc_attr($assignment_id) . '" 
			    data-user_id="' . esc_attr($user_id) . '" 
			    data-course_id="' . esc_attr($course_id) . '" 
			    data-user_item_id="' . esc_attr($user_item_id) . '">
			    <i class="dashicons dashicons-admin-generic"></i>
			</a>';

        echo '<a href="javascript:void(0)" class="delete-submission" 
                    data-user-item-id="' . esc_attr($user_item_id) . '" 
                    title="Delete submission">
                <i class="dashicons dashicons-trash"></i>
              </a>';
        echo '</div></td></tr>';
    }

    echo '</tbody></table></div>';
    wp_die();
}

/* End of List of Submission Ajax call */

/* Start of Check submission Ajax call */

add_action('wp_ajax_load_evaluate_submission_form', 'load_evaluate_submission_form_callback');

function load_evaluate_submission_form_callback() {
    global $wpdb;
    $assignment_id = intval($_POST['assignment_id']);
    $user_id = intval($_POST['user_id']);
    $course_id = intval($_POST['course_id']);
    $user_item_id = intval($_POST['user_item_id']);

    //print_r($user_item_id);

    if (!current_user_can('edit_post', $assignment_id)) {
        wp_send_json_error(__('Unauthorized', 'learnpress-assignments'));
    }

    // Load existing data
    $user_info = get_userdata($user_id);
    $username = $user_info->user_login;
    $display_name = $user_info->display_name;

    // Fetch assignment submission details
    $results = $wpdb->get_results($wpdb->prepare("
        SELECT *
        FROM {$wpdb->prefix}learnpress_user_itemmeta
        WHERE learnpress_user_item_id = %d
    ", $user_item_id), ARRAY_A);

    $assignment_data = [];
    foreach ($results as $meta) {
        $key = $meta['meta_key'];
        $value = !empty($meta['extra_value']) ? $meta['extra_value'] : $meta['meta_value'];

        if ($key === '_lp_assignment_answer_upload') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $upload = current($decoded);
                $value = $upload;
            }
        }
        $assignment_data[$key] = $value;
    }

    ob_start();
    ?>
    <div class="evaluate-submission-form">
        <div class="assignment-wrapper">
            <h3 class="profile-heading">Evaluate Form</h3>

            <div class="assignment-content">
                <form id="evaluate-submission-form">
                	<input type="hidden" name="action" value="save_evaluate_submission_form">
                    <input type="hidden" name="assignment_id" value="<?php echo esc_attr($assignment_id); ?>">
                    <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id); ?>">
                    <input type="hidden" name="course_id" value="<?php echo esc_attr($course_id); ?>">
                    <input type="hidden" name="user_item_id" value="<?php echo esc_attr($user_item_id); ?>">

                    <div class="form-row">
                        <label>Assignment:</label>
                        <p><?php echo esc_html(get_the_title($assignment_id)); ?></p>
                    </div>

                    <div class="form-row">
                        <label>Student Name:</label>
                        <p><?php echo esc_html($display_name); ?></p>
                    </div>

                    <div class="form-row">
                        <label>Student Username:</label>
                        <p><?php echo esc_html($username); ?></p>
                    </div>

                    <div class="form-row">
                        <label>Submitted Answer:</label>
                        <p><?php echo isset($assignment_data['_lp_assignment_answer_note']) ? esc_html($assignment_data['_lp_assignment_answer_note']) : ''; ?></p>
                    </div>

                    <?php if (!empty($assignment_data['_lp_assignment_answer_upload']) && is_array($assignment_data['_lp_assignment_answer_upload'])): 
                        $file = $assignment_data['_lp_assignment_answer_upload'];
                    ?>
                        <div class="form-row">
                            <label>Submitted File:</label>
                            <div class="assignment-file">
                                <a href="<?php echo esc_url(home_url($file['url'])); ?>" 
                                   target="_blank" 
                                   download="<?php echo esc_attr($file['filename']); ?>">
                                   <?php echo esc_html($file['filename']); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <label>Grade</label>
                        <input type="number" name="grade" value="<?php echo isset($assignment_data['_lp_assignment_mark']) ? esc_attr($assignment_data['_lp_assignment_mark']) : ''; ?>" />
                    </div>

                    <div class="form-row">
                        <label>Feedback</label>
                        <textarea name="feedback"><?php echo isset($assignment_data['_lp_assignment_instructor_note']) ? esc_textarea($assignment_data['_lp_assignment_instructor_note']) : ''; ?></textarea>
                    </div>

                    <div class="type-cls-adding-here-all">
                        <button type="submit" class="button button-primary lp-btn-action-instructor-assignment without-border-typed">Save Evaluation</button>
                        <button type="button" id="back-to-submissions" class="button common-btn-remove-cls-adding-here">Back to Submissions</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php

    $html = ob_get_clean();
    wp_send_json_success($html);
}

add_action('wp_ajax_save_evaluate_submission_form', 'save_evaluate_submission_form_callback');

function save_evaluate_submission_form_callback() {
    global $wpdb;
    
    $assignment_id = intval($_POST['assignment_id']);
    $user_id       = sanitize_text_field($_POST['user_id']);
    $user_item_id  = sanitize_text_field($_POST['user_item_id']);
    $grade         = sanitize_text_field($_POST['grade']);
    $feedback      = sanitize_textarea_field($_POST['feedback']);

    if (!current_user_can('edit_post', $assignment_id)) {
        wp_send_json_error(__('Unauthorized', 'learnpress'));
    }

    // Save grade
    learn_press_update_user_item_meta($user_item_id, '_lp_assignment_mark', $grade);

    // Save feedback
    learn_press_update_user_item_meta($user_item_id, '_lp_assignment_instructor_note', $feedback);

    if (empty($user_item_id)) {
        wp_send_json_error('Invalid User Item ID');
    }

    // Update status and end time directly with a SQL query
    $table_name = $wpdb->prefix . 'learnpress_user_items';

    $wpdb->update(
        $table_name,
        array(
            'status'    => 'evaluated',             // Update status to 'evaluated'
            'end_time'  => current_time('mysql'),   // Set the current time for 'end_time'
        ),
        array('user_item_id' => $user_item_id),    // Where the user_item_id matches
        array('%s', '%s'),                          // Format for the status and end_time
        array('%d')                                 // Format for user_item_id
    );

    // Check for any errors in the query
    if ($wpdb->last_error) {
        wp_send_json_error(__('Database update failed: ', 'learnpress') . $wpdb->last_error);
    }

    // Trigger LearnPress hook
    do_action('learn-press/after-evaluate-assignment', $user_item_id, $assignment_id, $user_id, $grade, $feedback);

    wp_send_json_success(__('Evaluation saved successfully.', 'learnpress'));
}

/* End of check submission Ajax call*/

/* Starting of Creating Custom Tab for Question in IP Instructor */

add_filter('learn-press/profile-tabs', function ($tabs) {
    $user = wp_get_current_user();

    // Check if user has 'lp_instructor' role
    if (in_array('lp_teacher', (array) $user->roles)) {
        $tabs['question'] = [
            'title'    => __('Question', 'text-domain'),
            'priority' => 30,
            'icon'     => '<i class="lp-icon-file-alt"></i>',
            'callback' => function () {
                $file_path = get_stylesheet_directory() . '/custom-question/custom-question.php';
                if (file_exists($file_path)) {
                    include($file_path);
                } else {
                    echo '<p>' . __('Custom Question tab content not found.', 'text-domain') . '</p>';
                }
            }
        ];
    }

    return $tabs;
});

/* End of Creating Custom Tab for Question in IP Instructor */

/*Start of Custom Question Interface AJAX call*/

/*Inner Start Add Question Funtion*/

add_action('wp_ajax_save_custom_question', function () {
    $user_id = get_current_user_id();
    $question_id = absint($_POST['question_id']);
    $post_data = [
        'post_title'   => sanitize_text_field($_POST['question_title']),
        'post_content' => wp_kses_post($_POST['question_content']),
        'post_status'  => 'publish',
        'post_type'    => 'lp_question',
        'post_author'  => $user_id,
    ];

    if ($question_id && get_post($question_id)) {
        $post_data['ID'] = $question_id;
        $question_id = wp_update_post($post_data);
    } else {
        $question_id = wp_insert_post($post_data);
    }

    if (is_wp_error($question_id)) {
        wp_send_json_error('Failed to save question.');
    }

    $type = sanitize_text_field($_POST['question_type']);
    update_post_meta($question_id, '_lp_type', $type);
    update_post_meta($question_id, '_lp_mark', intval($_POST['question_mark']));
    update_post_meta($question_id, '_lp_explanation', sanitize_text_field($_POST['question_explanation']));
    update_post_meta($question_id, '_lp_hint', sanitize_text_field($_POST['question_hint']));
    update_post_meta($question_id, '_lp_required', isset($_POST['question_required']) ? 'yes' : 'no');
    update_post_meta($question_id, '_lp_shuffle_answer', isset($_POST['shuffle_answers']) ? 'yes' : 'no');

    // Correctly format and save answers
    $answers = [];
    if (!empty($_POST['answers'])) {
        foreach ($_POST['answers'] as $index => $text) {
            $answers[] = [
                'value'    => sanitize_text_field($text),
                'is_true'  => (isset($_POST['correct_answers']) && in_array($index, $_POST['correct_answers'])) ? 'yes' : 'no'
            ];
        }
    }

    update_post_meta($question_id, '_lp_question_answer', $answers);

    // LearnPress may expect default correct_answer index too
    $correct_answer = '';
    foreach ($answers as $index => $ans) {
        if ($ans['is_true'] === 'yes') {
            $correct_answer = $index;
            break;
        }
    }

    update_post_meta($question_id, '_lp_correct_answer', $correct_answer);

    wp_send_json_success();
});

add_action('wp_ajax_load_custom_question', function () {
    $question_id = absint($_POST['question_id']);
    $post = get_post($question_id);

    if (!$post || $post->post_type !== 'lp_question') {
        wp_send_json_error('Invalid question.');
    }

    $answers = get_post_meta($question_id, '_lp_question_answer', true) ?: [];

    // Normalize to structure: value + is_true
    foreach ($answers as &$ans) {
        if (!isset($ans['value']) && isset($ans['text'])) {
            $ans['value'] = $ans['text'];
        }
        if (!isset($ans['is_true'])) {
            $ans['is_true'] = 'no';
        }
    }

    $response = [
        'id'          => $question_id,
        'title'       => $post->post_title,
        'content'     => $post->post_content,
        'type'        => get_post_meta($question_id, '_lp_type', true),
        'mark'        => get_post_meta($question_id, '_lp_mark', true),
        'explanation' => get_post_meta($question_id, '_lp_explanation', true),
        'hint' 		  => get_post_meta($question_id, '_lp_hint', true),
        'required'    => get_post_meta($question_id, '_lp_required', true),
        'shuffle'     => get_post_meta($question_id, '_lp_shuffle_answer', true),
        'answers'     => $answers
    ];

    wp_send_json_success($response);
});

add_action('wp_ajax_delete_custom_question', function () {
    $question_id = absint($_POST['question_id']);

    if (!$question_id || get_post_type($question_id) !== 'lp_question') {
        wp_send_json_error('Invalid question.');
    }

    if (wp_delete_post($question_id, true)) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to delete question.');
    }
});


/*Inner End Update Question Funtion*/

/*End of Custom Question Interface AJAX call*/

/* Starting of Creating Custom Tab for Quiz in IP Instructor */

add_filter('learn-press/profile-tabs', function ($tabs) {
    $user = wp_get_current_user();

    // Check if user has 'lp_instructor' role
    if (in_array('lp_teacher', (array) $user->roles)) {
        $tabs['test'] = [
            'title'    => __('Test', 'text-domain'),
            'priority' => 30,
            'icon'     => '<i class="lp-icon-file-alt"></i>',
            'callback' => function () {
                $file_path = get_stylesheet_directory() . '/custom-quiz/custom-quiz.php';
                if (file_exists($file_path)) {
                    include($file_path);
                } else {
                    echo '<p>' . __('Custom Quiz tab content not found.', 'text-domain') . '</p>';
                }
            }
        ];
    }

    return $tabs;
});

/* End of Creating Custom Tab for Quiz in IP Instructor */

/*Start of Custom Quiz Interface AJAX call*/

// Save Quiz
add_action('wp_ajax_save_custom_quiz', function () {
    $user_id = get_current_user_id();
    $quiz_id = absint($_POST['quiz_id']);

    $post_data = [
        'post_title'   => sanitize_text_field($_POST['quiz_title']),
        'post_content' => wp_kses_post($_POST['quiz_content']),
        'post_status'  => 'publish',
        'post_type'    => 'lp_quiz',
        'post_author'  => $user_id,
    ];

    if ($quiz_id && get_post($quiz_id)) {
        $post_data['ID'] = $quiz_id;
        $quiz_id = wp_update_post($post_data);
    } else {
        $quiz_id = wp_insert_post($post_data);
    }

    if (is_wp_error($quiz_id)) {
        wp_send_json_error('Failed to save quiz.');
    }

    // Save meta
    $linked = array_map('absint', $_POST['linked_questions'] ?? []);
    //$duration = [absint($_POST['quiz_duration'][0]), sanitize_text_field($_POST['quiz_duration'][1])];
    $duration = absint($_POST['quiz_duration']);
    $duration_type  = sanitize_text_field($_POST['quiz_duration_1']); 
    $passing_grade = absint($_POST['quiz_passing_grade']);

    update_post_meta($quiz_id, '_lp_questions', $linked);
    update_post_meta($quiz_id, '_lp_duration', $duration);
    update_post_meta($quiz_id, 'quiz_duration_type', $duration_type); 
    update_post_meta($quiz_id, '_lp_passing_grade', $passing_grade);

    wp_send_json_success();
});

// Load Quiz
add_action('wp_ajax_load_custom_quiz', function () {
    $quiz_id = absint($_POST['quiz_id']);
    $post = get_post($quiz_id);

    if (!$post || $post->post_type !== 'lp_quiz') {
        wp_send_json_error('Invalid quiz.');
    }

    $response = [
        'id'            => $quiz_id,
        'title'         => $post->post_title,
        'content'       => $post->post_content,
        'quiz_duration' => get_post_meta($quiz_id, 'quiz_duration', true),
  		'quiz_duration_type' => get_post_meta($quiz_id, 'quiz_duration_type', true),
        'duration'      => get_post_meta($quiz_id, '_lp_duration', true),
        'passing_grade' => get_post_meta($quiz_id, '_lp_passing_grade', true),
        'questions'     => array_map('strval', (array) get_post_meta($quiz_id, '_lp_questions', true))
    ];

    wp_send_json_success($response);
});

// Delete & Duplicate (same as before)
add_action('wp_ajax_delete_custom_quiz', function () {
    $quiz_id = absint($_POST['quiz_id']);
    if (get_post_type($quiz_id) === 'lp_quiz') {
        wp_delete_post($quiz_id, true);
    }
    wp_die();
});

add_action('wp_ajax_duplicate_custom_quiz', function () {
    $quiz_id = absint($_POST['quiz_id']);
    $post = get_post($quiz_id);
    if (!$post || $post->post_type !== 'lp_quiz') {
        wp_send_json_error();
    }

    $new_id = wp_insert_post([
        'post_title'   => $post->post_title . ' (Copy)',
        'post_content' => $post->post_content,
        'post_status'  => 'publish',
        'post_type'    => 'lp_quiz',
        'post_author'  => get_current_user_id(),
    ]);

    if ($new_id && !is_wp_error($new_id)) {
        update_post_meta($new_id, '_lp_questions', get_post_meta($quiz_id, '_lp_questions', true));
        update_post_meta($new_id, '_lp_duration', get_post_meta($quiz_id, '_lp_duration', true));
        update_post_meta($new_id, '_lp_passing_grade', get_post_meta($quiz_id, '_lp_passing_grade', true));
        wp_send_json_success();
    }

    wp_send_json_error();
});


/*End of Custom Quiz Interface AJAX call*/

/* Start */

add_action('init', function () {
    global $wp_roles;

    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }

    if ($wp_roles->is_role('lp_teacher')) {
        $wp_roles->roles['lp_teacher']['name'] = 'Tutor';
        $wp_roles->role_names['lp_teacher'] = 'Tutor';
    }

    if ($wp_roles->is_role('subscriber')) {
        $wp_roles->roles['subscriber']['name'] = 'Student';
        $wp_roles->role_names['subscriber'] = 'Student';
    }

    if ($wp_roles->is_role('editor')) {
        $wp_roles->roles['editor']['name'] = 'Sub Admin';
        $wp_roles->role_names['editor'] = 'Sub Admin';
    }
});

/*End*/

add_filter('manage_lp_course_posts_columns', function($columns) {
    if (isset($columns['author'])) {
        $columns['author'] = __('Tutor', 'learnpress');
    }
    return $columns;
});

add_filter( 'learn-press/profile-tabs', function( $tabs ) {
    $user = wp_get_current_user();
    if ( in_array( 'subscriber', (array) $user->roles ) || user_can( $user, 'subscriber' ) ) {
        unset( $tabs['enrolled_students'] );
		
    }
    return $tabs;
}, 20 );

add_filter( 'learn-press/profile-tabs', function( $tabs ) {
    $user = wp_get_current_user();
    if ( in_array( 'lp_teacher', (array) $user->roles ) || user_can( $user, 'lp_teacher' ) ) {
        unset( $tabs['quizzes'] );
		unset( $tabs['orders'] );
		unset( $tabs['my-courses'] );
		unset( $tabs['children'] );
		unset( $tabs['gradebook'] );
		unset( $tabs['preferred_course'] );
		
    }
    return $tabs;
}, 20 );

add_filter( 'learn-press/profile-tabs', function( $tabs ) {
    $user = wp_get_current_user();
    if ( in_array( 'parent', (array) $user->roles ) || user_can( $user, 'parent' ) ) {
        unset( $tabs['overview'] );
        unset( $tabs['calendar'] );
        unset( $tabs['quizzes'] );
        unset( $tabs['preferred_course'] );
        unset( $tabs['assignments'] );
        unset( $tabs['notification']);
		unset( $tabs['orders'] );
		unset( $tabs['enrolled_students'] );
		unset( $tabs['my-courses'] );
		unset( $tabs['test'] );
		unset( $tabs['preferred_course'] );
		
    }
    return $tabs;
}, 20 );


add_filter('learn-press/profile-tabs', function ($tabs) {
    $user = wp_get_current_user();
    
    // If user is a child (subscriber), rename tab
    if (in_array('subscriber', (array) $user->roles) || user_can($user, 'subscriber')) {
        if (isset($tabs['children'])) {
            $tabs['children']['title'] = __('My Parent', 'text-domain');
        }
    }

    return $tabs;
}, 20);

/* Start of Changing Author label to Tutor in Course tab in admin dashboard*/

add_action('admin_footer', function () {
    global $post, $pagenow;

    if ($pagenow === 'post.php' && get_post_type($post) === 'lp_course') {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const interval = setInterval(() => {
                const authorLabel = document.querySelector('.post-author-ts-control label');
                if (authorLabel && authorLabel.textContent.trim() === 'Author') {
                    authorLabel.textContent = 'Tutor';
                    clearInterval(interval);
                }
            }, 300);
        });
        </script>
        <?php
    }
});


/* End of Changing Author label to Tutor in Course tab in admin dashboard*/

/* Start of creating Child-parent relationship */

add_action('init', 'lp_register_parent_user_role');
function lp_register_parent_user_role() {
    if (!get_role('parent')) {
        add_role('parent', 'Parent', [
            'read' => true,
        ]);
    }
}

add_filter('learn-press/profile-tabs', function ($tabs) {
	$tabs['children'] = [
		'title' => __('My Children', 'text-domain'),
		'priority' => 30,
		'icon' => '<i class="lp-icon-file-alt"></i>',
		'callback' => 'lp_render_parent_dashboard',
	];
	return $tabs;
});

function lp_render_parent_dashboard() {
    $current_user_id = get_current_user_id();
    $user = get_userdata($current_user_id);
    $is_parent = in_array('parent', (array) $user->roles);
    $is_child  = in_array('subscriber', (array) $user->roles);
    $children_ids = get_user_meta($current_user_id, 'lp_children_users', true);

    // Show Back to Parent button if acting as child
    if (isset($_SESSION['parent_user_id']) && $current_user_id != $_SESSION['parent_user_id']) {
        $parent_user = get_userdata($_SESSION['parent_user_id']);
        // echo '<div style="margin-bottom: 20px;">';
        // echo '<a href="' . esc_url(add_query_arg('back_to_parent', '1')) . '" class="button" style="background-color:#d9534f; color: white;">Back to Parent (' . esc_html($parent_user->display_name) . ')</a>';
        // echo '</div>';
    }
    // Only allow parents to view child list and linking form
    elseif (in_array('parent', (array) $user->roles)) {
        echo '<div id="lp-child-list-view">';
        echo '<h2 class="profile-heading">Linked Children</h2>';
        if (!is_array($children_ids) || empty($children_ids)) {
            echo '<p>No child accounts linked yet.</p>';
        } else {
            echo '<div class="user-listing-typed-all"><ul>';
            foreach ($children_ids as $child_id) {
                $child_user = get_userdata($child_id);
                echo '<li>' . esc_html($child_user->display_name) . ' (' . esc_html($child_user->user_email) . ') ';
                echo '<a href="' . esc_url(add_query_arg('switch_to_child', $child_id)) . '" class="button" style="margin-left:10px;">View Progress of ' . esc_html($child_user->display_name) . '</a>';
                echo '</li>';
            }
            echo '</ul></div>';
        }
        echo '</div>';

        // Link a child form
        echo '<div class="linekd-child-adding add-inner-webmodal-cls-adding"><h2 class="profile-heading">Link a Child</h2>';
        echo '<form method="post" class="form-type-head-cls">
			<div class="grid-type-cls-added">
			<div class="left-side-grid-input">
            <input type="text" name="lp_child_user_input" placeholder="Enter child\'s email" required />
			</div>
			<div class="right-side-grid-input">
            <button type="submit" name="link_lp_child" class="link-child-added-cls-all">Link Child</button>
			</div>
			</div>
        </form></div>';

        if (isset($_POST['link_lp_child']) && !empty($_POST['lp_child_user_input'])) {
            lp_handle_child_linking_form($current_user_id);
        }

        // Hidden child detail view section
        echo '<div id="lp-child-details-view" style="display:none;">';
        echo '<button id="lp-back-to-child-list">Back to Children List</button>';
        echo '<div id="lp-child-course-details"></div>';
        echo '</div>';
    }

    // If child is logged in, show pending parent requests
	if ($is_child) {
	    $pending_parents = get_user_meta($current_user_id, 'lp_pending_parent_requests', true);
	    if (!is_array($pending_parents)) $pending_parents = [];

	    echo '<div class="pendingpayment-req"><h2 class="pending-parent-link">Pending Parent Link Requests</h2>';
	    if (!empty($pending_parents)) {
	        foreach ($pending_parents as $parent_id) {
	            $parent_user = get_userdata($parent_id);
	            echo '<div class="accept-reject-cls">';
	            echo '<div class="accept-reject-cls-adding-type"><p>' . esc_html($parent_user->display_name . ' (' . $parent_user->user_email . ')') . '</p>';
	            echo ' <form method="post" style="display:inline;">
	                    <input type="hidden" name="parent_id" value="' . esc_attr($parent_id) . '"/>
						<div class="input-type-added-all">
	                    <button name="accept_parent_request" class="accept-req" type="submit">Accept</button>
	                    <button name="reject_parent_request" class="reject-req" type="submit">Reject</button>
						</div></div>
	                   </form>';
	            echo '</div></div>';
	        }
	    } else {
	        echo '<p class="nopending-req-all">No pending requests.</p></div>';
	    }

	    // Show already linked parents
		$linked_parents = [];

		// Search all users with role 'parent'
		$parent_users = get_users(['role' => 'parent']);
		foreach ($parent_users as $parent) {
		    $children = get_user_meta($parent->ID, 'lp_children_users', true);
		    if (is_array($children) && in_array($current_user_id, $children)) {
		        $linked_parents[] = $parent;
		    }
		}

		echo '<div class="starting-payment"><h2 class="sub-heading-parents">Linked Parents</h2>';
		if (!empty($linked_parents)) {
    echo '<div class="linked-parent-cls">';
    foreach ($linked_parents as $parent_user) {
        echo '<div>';
        echo '<p>' . esc_html($parent_user->display_name . ' (' . $parent_user->user_email . ')') . '</p>';
        echo '</div>';
    }
    echo '</div>';
}
 else {
		    echo '<p>No linked parents.</p>';
		}

	    // Handle form actions
	    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['parent_id'])) {
		    $parent_id = intval($_POST['parent_id']);
		    $pending_parents = array_diff($pending_parents, [$parent_id]);
		    update_user_meta($current_user_id, 'lp_pending_parent_requests', $pending_parents);

		    if (isset($_POST['accept_parent_request'])) {
		        $linked_children = get_user_meta($parent_id, 'lp_children_users', true);
		        if (!is_array($linked_children)) $linked_children = [];

		        if (!in_array($current_user_id, $linked_children)) {
		            $linked_children[] = $current_user_id;
		            update_user_meta($parent_id, 'lp_children_users', $linked_children);
		        }
		    }

		    // Redirect to avoid POST resubmission
		    wp_redirect(add_query_arg('request_processed', '1'));
		    exit;
		}

	}
}

add_action('wp_footer', 'lp_floating_back_to_parent_button');
function lp_floating_back_to_parent_button() {
    if (is_user_logged_in() && isset($_SESSION['parent_user_id'])) {
        $current_user = wp_get_current_user();
        $parent_user_id = $_SESSION['parent_user_id'];
        $parent_user = get_userdata($parent_user_id);

        if ($current_user->ID !== $parent_user_id) {
            // Base profile URL
            $profile_base = trailingslashit(get_permalink(get_option('learn_press_profile_page_id')));
            // Construct pretty permalink: /lp-profile/username/my-children/
            $profile_url = $profile_base . $parent_user->user_nicename . '/children/';
            // Append switch trigger
            $back_to_parent_url = add_query_arg('back_to_parent', '1', $profile_url);
            ?>
            <style>
                #lp-back-to-parent-btn {
                    position: fixed;
                    top: 115px;
                    right: 50px;
                    background-color: #d9534f;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 5px;
                    z-index: 9999;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                    cursor: pointer;
                }
            </style>
            <a id="lp-back-to-parent-btn" href="<?php echo esc_url($back_to_parent_url); ?>">
                <?php echo 'Back to Parent (' . esc_html($parent_user->display_name) . ')'; ?>
            </a>
            <?php
        }
    }
}



function lp_handle_child_linking_form($parent_user_id) {
    if (isset($_POST['lp_child_user_input'])) {
        $child_input = sanitize_text_field($_POST['lp_child_user_input']);
        $child_user = get_user_by('login', $child_input) ?: get_user_by('email', $child_input);

        if ($child_user) {
            $child_user_id = $child_user->ID;

            // Add to pending requests in child's meta
            $pending = get_user_meta($child_user_id, 'lp_pending_parent_requests', true);
            if (!is_array($pending)) $pending = [];

            if (!in_array($parent_user_id, $pending)) {
                $pending[] = $parent_user_id;
                update_user_meta($child_user_id, 'lp_pending_parent_requests', $pending);
                echo '<p style="color:green;">Link request sent to child. Awaiting approval.</p>';
            } else {
                echo '<p style="color:orange;">Request already sent.</p>';
            }
        } else {
            echo '<p style="color:red;">Child not found. Please check the username or email.</p>';
        }
    }
}

// Start session early
add_action('init', function () {
    if (!session_id()) session_start();
}, 1);

// Login as child via GET param
add_action('init', function () {
    if (!isset($_GET['switch_to_child'])) return;

    $child_id = intval($_GET['switch_to_child']);
    $parent_id = get_current_user_id();
    $linked_children = get_user_meta($parent_id, 'lp_children_users', true);
    if (!is_array($linked_children)) $linked_children = [];

    if (in_array($child_id, $linked_children)) {
        $_SESSION['parent_user_id'] = $parent_id;
        wp_set_auth_cookie($child_id);
        wp_redirect(remove_query_arg('switch_to_child'));
        exit;
    }
});

// Return to parent from child
add_action('init', function () {
    if (!isset($_GET['back_to_parent'])) return;

    if (isset($_SESSION['parent_user_id'])) {
        $parent_id = $_SESSION['parent_user_id'];
        wp_set_auth_cookie($parent_id);
        unset($_SESSION['parent_user_id']);
        wp_redirect(remove_query_arg('back_to_parent'));
        exit;
    }
});

function lp_parent_dashboard_scripts() {
    wp_enqueue_script('jquery');  // Ensure jQuery is loaded

    // Inline JavaScript
    $js_code = "
    jQuery(document).ready(function($) {
	    $('.view-child-progress').on('click', function() {
	        const childId = $(this).data('child-id');
	        
	        // Hide child list and show child details view
	        $('#lp-child-list-view').hide();
	        $('#lp-child-details-view').show();
	        $('#lp-child-course-details').html('<p>Loading...</p>');
	        
	        // Make AJAX request to fetch child progress
	        $.post('" . admin_url('admin-ajax.php') . "', {
	            action: 'lp_get_child_progress',
	            child_id: childId
	        }, function(response) {
	            if (response.success) {
	                $('#lp-child-course-details').html(response.data);
	            } else {
	                $('#lp-child-course-details').html('<p>Error loading data.</p>');
	            }
	        });
	    });

	    // Back button functionality
	    $('#lp-back-to-child-list').on('click', function() {
	        $('#lp-child-details-view').hide();
	        $('#lp-child-course-details').html('');
	        $('#lp-child-list-view').show();
	    });
	});
    ";

    // Add the inline script
    wp_add_inline_script('jquery', $js_code);
}
add_action('wp_enqueue_scripts', 'lp_parent_dashboard_scripts');

/* End of creating Child-parent relationship */

/* Start of Additional Resgistration Feilds */

add_action('wpems_user_register_success', function($user_id) {
    $fields = [
        'student_full_name', 'parent_name', 'phone', 'dob', 'grade',
        'address', 'preferred_subjects', 'learning_style', 'tutoring_hours', 'additional_info'
    ];

    foreach ($fields as $field) {
        if (!empty($_POST[$field])) {
            update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Save textarea fields properly
    if (!empty($_POST['learning_style'])) {
        update_user_meta($user_id, 'learning_style', sanitize_textarea_field($_POST['learning_style']));
    }

    if (!empty($_POST['address'])) {
        update_user_meta($user_id, 'address', sanitize_textarea_field($_POST['address']));
    }

    if (!empty($_POST['additional_info'])) {
        update_user_meta($user_id, 'additional_info', sanitize_textarea_field($_POST['additional_info']));
    }
});

/* End of Additional Resgistration Feilds */

/* Start of adding additional feilds in User in WP dashboard */

// Show extra fields in admin user profile
function custom_show_extra_user_profile_fields($user) {
    ?>
    <h2>Additional Registration Information</h2>
    <table class="form-table">
        <tr>
            <th><label for="student_full_name">Student Full Name</label></th>
            <td>
                <input type="text" name="student_full_name" id="student_full_name"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'student_full_name', true)); ?>"
                       class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="parent_name">Parent/Guardian Full Name</label></th>
            <td><input type="text" name="parent_name" id="parent_name"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'parent_name', true)); ?>"
                       class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="phone">Mobile Number</label></th>
            <td><input type="text" name="phone" id="phone"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'phone', true)); ?>"
                       class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="dob">Date of Birth</label></th>
            <td><input type="date" name="dob" id="dob"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'dob', true)); ?>"
                       class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="grade">Student Grade Level</label></th>
            <td><input type="text" name="grade" id="grade"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'grade', true)); ?>"
                       class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="address">Address</label></th>
            <td><textarea name="address" id="address" rows="3"
                          class="regular-text"><?php echo esc_textarea(get_user_meta($user->ID, 'address', true)); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="preferred_subjects">Preferred Subjects</label></th>
            <td><input type="text" name="preferred_subjects" id="preferred_subjects"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'preferred_subjects', true)); ?>"
                       class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="learning_style">Learning Style Preference</label></th>
            <td><textarea name="learning_style" id="learning_style" rows="3"
                          class="regular-text"><?php echo esc_textarea(get_user_meta($user->ID, 'learning_style', true)); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="tutoring_hours">Preferred Tutoring Hours</label></th>
            <td><input type="text" name="tutoring_hours" id="tutoring_hours"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'tutoring_hours', true)); ?>"
                       class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="additional_info">Additional Details</label></th>
            <td><textarea name="additional_info" id="additional_info" rows="3"
                          class="regular-text"><?php echo esc_textarea(get_user_meta($user->ID, 'additional_info', true)); ?></textarea></td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'custom_show_extra_user_profile_fields');
add_action('edit_user_profile', 'custom_show_extra_user_profile_fields');


// Save the extra fields
function custom_save_extra_user_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;

    $fields = [
        'student_full_name', 'parent_name', 'phone', 'dob', 'grade',
        'address', 'preferred_subjects', 'learning_style', 'tutoring_hours', 'additional_info'
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = is_array($_POST[$field])
                ? array_map('sanitize_text_field', $_POST[$field])
                : (in_array($field, ['address', 'learning_style', 'additional_info'])
                    ? sanitize_textarea_field($_POST[$field])
                    : sanitize_text_field($_POST[$field]));
            update_user_meta($user_id, $field, $value);
        }
    }
}
add_action('personal_options_update', 'custom_save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'custom_save_extra_user_profile_fields');

/* End of adding additional feilds in User in WP dashboard */

/* Start of saving registration data */

add_action( 'user_register', 'save_custom_event_registration_fields', 10, 1 );

function save_custom_event_registration_fields( $user_id ) {
    if ( isset( $_POST['student_full_name'] ) ) {
        update_user_meta( $user_id, 'student_full_name', sanitize_text_field( $_POST['student_full_name'] ) );
    }
    if ( isset( $_POST['parent_name'] ) ) {
        update_user_meta( $user_id, 'parent_name', sanitize_text_field( $_POST['parent_name'] ) );
    }
    if ( isset( $_POST['phone'] ) ) {
        update_user_meta( $user_id, 'phone', sanitize_text_field( $_POST['phone'] ) );
    }
    if ( isset( $_POST['dob'] ) ) {
        update_user_meta( $user_id, 'dob', sanitize_text_field( $_POST['dob'] ) );
    }
    if ( isset( $_POST['grade'] ) ) {
        update_user_meta( $user_id, 'grade', sanitize_text_field( $_POST['grade'] ) );
    }
    if ( isset( $_POST['address'] ) ) {
        update_user_meta( $user_id, 'address', sanitize_textarea_field( $_POST['address'] ) );
    }
    if (isset($_POST['preferred_subjects'])) {
        update_user_meta($user_id, 'preferred_subjects', implode(', ', array_map('sanitize_text_field', $_POST['preferred_subjects'])));
    }
    if (isset($_POST['learning_style'])) {
        update_user_meta($user_id, 'learning_style', implode(', ', array_map('sanitize_text_field', $_POST['learning_style'])));
    }
    if (isset($_POST['tutoring_hours'])) {
        update_user_meta($user_id, 'tutoring_hours', implode(', ', array_map('sanitize_text_field', $_POST['tutoring_hours'])));
    }
    if ( isset( $_POST['additional_info'] ) ) {
        update_user_meta( $user_id, 'additional_info', sanitize_textarea_field( $_POST['additional_info'] ) );
    }
    if (!isset($_POST['user_type'])) {
        return;
    }

    $user_type = sanitize_text_field($_POST['user_type']);

    // Set role based on user_type
    if (in_array($user_type, ['subscriber', 'parent'])) {
        $user = new WP_User($user_id);
        $user->set_role($user_type);
    }
}

/* End of saving registration data */

/* Start of General Setting Page in Dashboard */

function custom_general_settings_page() {
    add_menu_page(
        'General Settings',
        'General Settings',
        'manage_options',
        'custom-general-settings',
        'render_custom_general_settings_page',
        'dashicons-admin-generic',
        80
    );
}
add_action('admin_menu', 'custom_general_settings_page');

function render_custom_general_settings_page() {
    $subjects = get_option('custom_preferred_subjects', []);
    $styles = get_option('custom_learning_styles', []);
    $hours = get_option('custom_tutoring_hours', []);
    ?>
    <div class="wrap">
        <h1>General Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('custom_settings_group'); ?>
            <h2>Preferred Subjects</h2>
            <div id="preferred_subjects_wrapper">
                <?php if (!empty($subjects)) : foreach ($subjects as $subject) : ?>
                    <div><input type="text" name="custom_preferred_subjects[]" value="<?php echo esc_attr($subject); ?>" />
                        <button type="button" class="remove-field button">Remove</button>
                    </div>
                <?php endforeach; else: ?>
                    <div><input type="text" name="custom_preferred_subjects[]" value="" />
                        <button type="button" class="remove-field button">Remove</button>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" id="add_subject_field" class="button">Add Subject</button>

            <h2>Learning Style Preference</h2>
            <div id="learning_styles_wrapper">
                <?php if (!empty($styles)) : foreach ($styles as $style) : ?>
                    <div><input type="text" name="custom_learning_styles[]" value="<?php echo esc_attr($style); ?>" />
                        <button type="button" class="remove-field button">Remove</button>
                    </div>
                <?php endforeach; else: ?>
                    <div><input type="text" name="custom_learning_styles[]" value="" />
                        <button type="button" class="remove-field button">Remove</button>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" id="add_style_field" class="button">Add Style</button>

            <h2>Preferred Tutoring Hours</h2>
            <div id="tutoring_hours_wrapper">
                <?php if (!empty($hours)) : foreach ($hours as $hour) : ?>
                    <div><input type="text" name="custom_tutoring_hours[]" value="<?php echo esc_attr($hour); ?>" />
                        <button type="button" class="remove-field button">Remove</button>
                    </div>
                <?php endforeach; else: ?>
                    <div><input type="text" name="custom_tutoring_hours[]" value="" />
                        <button type="button" class="remove-field button">Remove</button>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" id="add_hour_field" class="button">Add Hour</button>

            <?php submit_button(); ?>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const repeater = (wrapperId, buttonId, inputName) => {
                document.getElementById(buttonId).addEventListener('click', () => {
                    const wrapper = document.getElementById(wrapperId);
                    const div = document.createElement('div');
                    div.innerHTML = `<input type="text" name="${inputName}" value="" />
                        <button type="button" class="remove-field button">Remove</button>`;
                    wrapper.appendChild(div);
                });

                document.getElementById(wrapperId).addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-field')) {
                        e.target.parentElement.remove();
                    }
                });
            };

            repeater('preferred_subjects_wrapper', 'add_subject_field', 'custom_preferred_subjects[]');
            repeater('learning_styles_wrapper', 'add_style_field', 'custom_learning_styles[]');
            repeater('tutoring_hours_wrapper', 'add_hour_field', 'custom_tutoring_hours[]');
        });
    </script>
    <?php
}

function register_custom_settings() {
    register_setting('custom_settings_group', 'custom_preferred_subjects');
    register_setting('custom_settings_group', 'custom_learning_styles');
    register_setting('custom_settings_group', 'custom_tutoring_hours');
}
add_action('admin_init', 'register_custom_settings');

/* End of General Setting Page in Dashboard*/

/* Start of Email verification after registration */

add_action('user_register', 'send_verification_email_after_registration', 100);

function send_verification_email_after_registration($user_id) {
    $token = wp_generate_password(32, false);
    update_user_meta($user_id, 'email_verification_token', $token);
    update_user_meta($user_id, 'email_verified', 0); // false by default

    $user = get_userdata($user_id);

    $verification_url = add_query_arg([
	    'verify_email' => $token,
	    'user_id'      => $user_id,
	], home_url('/user-login'));

    $subject = 'Please verify your email';
    $message = 'Hi ' . $user->display_name . ",\n\n";
    $message .= "Please verify your email by clicking the link below:\n";
    $message .= $verification_url . "\n\n";
    $message .= "Thanks!";

    wp_mail($user->user_email, $subject, $message);
}

add_action('init', 'verify_user_email_token');

function verify_user_email_token() {
    if (isset($_GET['verify_email'], $_GET['user_id'])) {
        $user_id = absint($_GET['user_id']);
        $token = sanitize_text_field($_GET['verify_email']);

        $saved_token = get_user_meta($user_id, 'email_verification_token', true);

        if ($token === $saved_token) {
            // Set as verified
            update_user_meta($user_id, 'email_verified', 1);
            delete_user_meta($user_id, 'email_verification_token');

            // Optionally send a welcome email
            $user = get_userdata($user_id);
            wp_mail($user->user_email, 'Welcome!', 'Your email has been verified. You can now log in.');

            // Redirect to login
            wp_redirect(wp_login_url());
            exit;
        }
    }
}


/* End of Email verification after registration*/

/* Start of Creating Prefered Course Tab */

//Creating Custom Prefered Course

add_filter('learn-press/profile-tabs', function ($tabs) {
	$tabs['preferred_course'] = [
		'title' => __('Preferred Course', 'text-domain'),
		'priority' => 10,
		'icon' => '<i class="lp-icon-file-alt"></i>',
		'callback' => 'render_preferred_course_tab',
	];
	return $tabs;
});


// Callback function for rendering course list 

function render_preferred_course_tab() {
    $user_id = get_current_user_id();
    $grade = get_user_meta( $user_id, 'grade', true );

    if ( empty( $grade ) ) {
        echo '<div class="grade-type-cls-adding-here"><p>Your grade information is missing. Please update your profile.</p></div>';
        return;
    }

    global $wpdb;

    $post_ids = $wpdb->get_col( $wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta}
        WHERE meta_key LIKE %s AND meta_value LIKE %s",
        '%course_grade%', '%' . $wpdb->esc_like($grade) . '%'
    ) );

    if ( empty( $post_ids ) ) {
        echo '<p>No courses found for your grade level: <strong>' . esc_html( $grade ) . '</strong>.</p>';
        return;
    }

    $args = array(
        'post_type' => 'lp_course',
        'post_status' => 'publish',
        'post__in' => $post_ids
    );

    $courses = new WP_Query($args);

    if ( $courses->have_posts() ) {
        echo '<div class="lp-preferred-courses-grid preferred-course-typed-cls" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">';

        while ( $courses->have_posts() ) {
            $courses->the_post();

            $course_id = get_the_ID();
            $course_url = get_the_permalink();
            $course_title = get_the_title();
            $course_excerpt = get_the_excerpt();
            $course_thumbnail = get_the_post_thumbnail( $course_id, 'medium', ['style' => 'width: 100%; height: auto; border-radius: 8px;'] );
            //$grade = get_post_meta($post->ID, '_course_grade', true);
            $course_grade = get_post_meta($course_id, '_course_grade', true);
            $duration = get_post_meta( $course_id, '_lp_duration', true );
            $duration_unit = get_post_meta( $course_id, '_lp_duration_type', true );
            $curriculum = get_post_meta( $course_id, '_lp_curriculum', true );
            $lessons = is_array($curriculum) ? count(array_filter($curriculum, function($item) {
                return strpos($item, 'lp_lesson') !== false;
            })) : 0;

            echo '<div class="lp-course-card">';

            // Thumbnail
            echo '<a href="' . esc_url( $course_url ) . '">' . $course_thumbnail . '</a>';

            // RATING ABOVE TITLE
			$num_reviews = get_comments_number( $course_id ); // Or use WP_Comment_Query if you need more control

			if ( function_exists( 'learn_press_get_course_rate' ) ) {
			    $rating = learn_press_get_course_rate( $course_id );
			} else {
			    $rating = 0;
			}

			echo '<div class="thim-ekits-course__meta-data new-meted-cls-adding-here-star" style="margin: 10px 0;">';
			echo '<span class="thim-ekits-course__rating-number new-meted-cls-adding-here-star-rating">' . esc_html( number_format( $rating, 1 ) ) . '</span>';
			echo '<span class="thim-ekits-course__rating-stars new-meted-cls-adding-here-star-thim-ekit-course">';
			for ( $i = 1; $i <= 5; $i++ ) {
			    if ( $i <= floor( $rating ) ) {
			        echo '<i class="fas fa-star" style="color: #FFD700;"></i>';
			    } elseif ( $i - $rating < 1 ) {
			        echo '<i class="fas fa-star-half-alt" style="color: #FFD700;"></i>';
			    } else {
			        echo '<i class="far fa-star" style="color: #FFD700;"></i>';
			    }
			}
			echo '</span>';

			if ( $num_reviews > 0 ) {
			    echo '<span class="thim-ekits-course__review-count"> (' . esc_html( $num_reviews ) . ' reviews)</span>';
			} else {
			    echo '<span class="thim-ekits-course__review-count"> (No reviews)</span>';
			}
			echo '</div>';

            // Title
            echo '<h3><a href="' . esc_url( $course_url ) . '">' . esc_html( $course_title ) . '</a></h3>';

            // Excerpt
            echo '<p class="min-h-set-only-this-cls">' . esc_html( wp_trim_words( $course_excerpt, 15 ) ) . '</p>';

            // Featured Point list
			

			// Fetch the course content list
			$content_list = get_post_meta($course_id, '_course_content_list', true);

			if (!empty($content_list) && is_array($content_list)) {
			    echo '<div class="min-h-cls-adding"><ul class="course-content-list">';
			    foreach ($content_list as $content) {
			        echo '<li>' . esc_html($content['content']) . '</li>';
			    }
			    echo '</ul></div>';
			} else {
			    echo '<p class="no-content-available-cls-adding">No content available.</p>';
			}

            // Meta info
            echo '<ul class="user-type-added-all">';
            echo '<li class="user-type-listing-cls-adding"><strong class="user-type-listing-cls-adding">Grade:</strong> ' . esc_html( $course_grade ) . '</li>';
            echo '<li class="user-type-listing-cls-adding"><strong class="user-type-listing-cls-adding">Duration:</strong> ' . esc_html( $duration ) . ' ' . esc_html( $duration_unit ) . '</li>';
            echo '<li class="user-type-listing-cls-adding"><strong class="user-type-listing-cls-adding">Lessons:</strong> ' . esc_html( $lessons ) . '</li>';
            echo '</ul>';
            // Get course object
			$course = learn_press_get_course( $course_id );

			// Display price
			$price_html = $course->get_price_html();
			if ( $price_html ) {
			    echo '<div class="new-course-type-ending-price-all"><div class="course-price new-course-price-type-cls-adding-here">' . $price_html . '</div>';
			}

			// Start Now button linking to course detail page
			echo '<div class="course-start-now">';
			echo '<a href="' . esc_url( $course_url ) . '" class="start-now-button new-start-button-enroll-cls-adding-here">Enroll Now <svg xmlns="http://www.w3.org/2000/svg" width="28" height="29" viewBox="0 0 28 29" fill="none"><rect y="0.186005" width="28" height="28" rx="14" fill="white"></rect><path d="M10 18.186L18 10.186M18 10.186H12M18 10.186V16.186" stroke="#0FA8A4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></a>';
			echo '</div></div>';

            echo '</div>';
        }

        echo '</div>';
    } else {
        echo '<p>No courses found for your grade level: <strong>' . esc_html( $grade ) . '</strong>.</p>';
    }

    wp_reset_postdata();
}


/* End of Creating Prefered Course Tab*/

/* Start of script for visibility of the Contact form entry in dashboard */

/* Function for force opacity of contact form inquiry list visiblity */

add_action("vsz_cf7_admin_after_body_field", "vsz_cf7_admin_after_body_field_callback", 10, 2);
function vsz_cf7_admin_after_body_field_callback($fid, $row_id) {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    // Check if our form is inside the DOM
                    if ($('#cf7d-modal-form-edit-value').length > 0) {
                        // Now set opacity and height when form is loaded
                        $('#cf7d-modal-form-edit-value ul#cf7d-list-field-for-edit li.clearfix').css({
                            'opacity': '1',
                            'height': 'auto'
                        });

                        // Stop observing after applying once
                        observer.disconnect();
                    }
                });
            });

            // Start observing body for any added child nodes
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            // Optional: also bind on click in case Thickbox triggers very late
            $(document).on('click', 'a.cf7d-edit-value', function(e) {
                // Trigger observation again if needed
            });
        });
    </script>
    <?php
}

/* End of Script for visibility of the Contact form entry in dashboard*/

/* Start of Test Result Customization */

// Disable instant evaluation of quizzes
add_filter('learn-press/quiz/evaluate-immediately', '__return_false');

// Set quiz as pending review on submission
add_action('learn-press/user-quizzes/checked', function($quiz_id, $user_id) {
    global $wpdb;

    // Find the latest quiz submission (user_item_id)
    $user_item_id = $wpdb->get_var($wpdb->prepare("
        SELECT user_item_id FROM {$wpdb->prefix}learnpress_user_items
        WHERE user_id = %d AND item_id = %d AND item_type = %s
        ORDER BY user_item_id DESC LIMIT 1
    ", $user_id, $quiz_id, 'lp_quiz'));

    if ($user_item_id) {
        // Override LearnPress's evaluation by forcing pending-review
        $wpdb->update(
            "{$wpdb->prefix}learnpress_user_items",
            ['status' => 'pending-review'],
            ['user_item_id' => $user_item_id]
        );
    }
}, 20, 2); // Priority 20 to run after default LearnPress evaluation

// Add Instructor Dashboard tab

add_filter('learn-press/profile-tabs', function($tabs) {
	if (current_user_can('lp_teacher')) {
		$tabs['submitted-quizzes'] = [
			'title'    => __('Submitted Quizzes', 'learnpress'),
			'priority' => 35,
			'icon' => '<i class="lp-icon-list"></i>',
			'callback' => 'lp_render_submitted_quizzes_tab',
		];
	}
	return $tabs;
});

function lp_get_quiz_submissions($paged = 1, $per_page = 10) {
	global $wpdb;
	$offset = ($paged - 1) * $per_page;

	$total = $wpdb->get_var("
		SELECT COUNT(*) 
		FROM {$wpdb->prefix}learnpress_user_items ui
		WHERE ui.item_type = 'lp_quiz'
	");

	$submissions = $wpdb->get_results($wpdb->prepare("
		SELECT ui.user_item_id, ui.user_id, ui.item_id, ui.status, ui.graduation, ui.start_time, ui.end_time,
		       u.display_name, p.post_title
		FROM {$wpdb->prefix}learnpress_user_items ui
		JOIN {$wpdb->users} u ON ui.user_id = u.ID
		JOIN {$wpdb->posts} p ON ui.item_id = p.ID
		WHERE ui.item_type = 'lp_quiz'
		ORDER BY ui.end_time DESC
		LIMIT %d OFFSET %d
	", $per_page, $offset));

	return [
		'submissions' => $submissions,
		'total' => $total,
	];
}

function lp_render_submission_table_rows($submissions) {
	foreach ($submissions as $s) {
		echo '<tr>';
		echo '<td>' . esc_html($s->display_name) . '</td>';
		echo '<td>' . esc_html($s->post_title) . '</td>';
		echo '<td>' . esc_html($s->end_time) . '</td>';
		echo '<td style="color:' . ($s->status === 'completed' ? 'green' : 'orange') . ';">' . esc_html(ucwords(str_replace('-', ' ', $s->status))) . '</td>';
		echo '<td>';
		if ($s->status === 'pending-review') {
			echo '<form method="post" style="display:inline;">
				<input type="hidden" name="approve_quiz" value="1">
				<input type="hidden" name="user_item_id" value="' . esc_attr($s->user_item_id) . '">
				<button type="submit" class="approve-cls-adding-all new-view-mission-cls-adding same-for-all-btn-bg-added">Approve</button>
			</form>';
		} else {
			echo '';
		}
		echo '<button class="view-submission-btn new-view-mission-cls-adding same-for-all-btn-bg-added" data-user-item-id="' . esc_attr($s->user_item_id) . '">View Submission</button>';
		echo '</td>';
		echo '</tr>';
	}
}

function lp_render_submitted_quizzes_tab() {
	$page = 1;
	$per_page = 8;
	$data = lp_get_quiz_submissions($page, $per_page);
	$total_pages = ceil($data['total'] / $per_page);

	ob_start(); ?>
	<div id="lp-quiz-submissions-list-wrapper">
		<h3 class="profile-heading">Submitted & Evaluated Quizzes</h3>
		
		<div class="custom-table-wrapper">
		<table class="lp-quiz-submissions" border="1" style="width:100%; border-collapse:collapse;">
			<thead>
				<tr>
					<th>Student</th>
					<th>Quiz</th>
					<th>Submitted At</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody id="quiz-submission-rows">
				<?php lp_render_submission_table_rows($data['submissions']); ?>
			</tbody>
		</table>
		</div>

		<div class="lp-pagination custom-pagination">
			<?php for ($i = 1; $i <= $total_pages; $i++): ?>
				<button class="lp-page-btn pagination-link <?php echo $i === 1 ? 'current' : ''; ?>" data-page="<?= $i ?>">
					<?= $i ?>
				</button>
			<?php endfor; ?>

		</div>
	</div>
	<div id="quiz-submission-detail"></div>

	<script>
	jQuery(document).ready(function($) {
		function loadQuizPage(page) {
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'lp_get_paginated_quiz_submissions',
					page: page
				},
				success: function(response) {
					if (response.success) {
						$('#quiz-submission-rows').html(response.data.table);
					}
				}
			});
		}

		$(document).on('click', '.lp-page-btn', function() {
			var page = $(this).data('page');
			$('.lp-page-btn').removeClass('current');
			$(this).addClass('current');
			loadQuizPage(page);
		});

		$(document).on('click', '.view-submission-btn', function(e) {
			e.preventDefault();
			var user_item_id = $(this).data('user-item-id');
			$('#lp-quiz-submissions-list-wrapper').hide();
			$('#quiz-submission-detail').html('<p>Loading submission...</p>').show();

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'lp_fetch_quiz_submission',
					user_item_id: user_item_id
				},
				success: function(response) {
					if (response.success) {
						$('#quiz-submission-detail').html(
							'<button id="back-to-list" class="back-to-list-cls-adding">Back to List</button>' +
							response.data
						);
					} else {
						$('#quiz-submission-detail').html('<p>Error loading submission.</p>');
					}
				}
			});
		});

		$(document).on('click', '#back-to-list', function() {
			$('#quiz-submission-detail').hide().html('');
			$('#lp-quiz-submissions-list-wrapper').show();
		});
	});
	</script>
	<?php echo ob_get_clean();
}

add_action('wp_ajax_lp_get_paginated_quiz_submissions', 'lp_get_paginated_quiz_submissions');
function lp_get_paginated_quiz_submissions() {
	if (!is_user_logged_in() || !current_user_can('lp_teacher')) {
		wp_send_json_error('Unauthorized');
	}

	$page = absint($_POST['page'] ?? 1);
	$per_page = 8;

	$data = lp_get_quiz_submissions($page, $per_page);

	ob_start();
	lp_render_submission_table_rows($data['submissions']);
	$html = ob_get_clean();

	wp_send_json_success([
		'table' => $html
	]);
}

add_action('wp_ajax_lp_fetch_quiz_submission', 'lp_fetch_quiz_submission_callback');

function lp_fetch_quiz_submission_callback() {
	global $wpdb;

    // Ensure user is logged in
	if (!is_user_logged_in()) {
		wp_send_json_error('Not logged in');
	}

	$current_user = wp_get_current_user();

    // Check if the user has the instructor role (you can adjust this if needed)
	if (in_array('lp_teacher', (array) $user->roles)) {
		wp_send_json_error('Permission denied');
	}

	$user_item_id = absint($_POST['user_item_id'] ?? 0);

	if (!$user_item_id) {
		wp_send_json_error('Invalid submission ID');
	}

    // Fetch quiz user item record
	$user_item = $wpdb->get_row($wpdb->prepare("
		SELECT * FROM {$wpdb->prefix}learnpress_user_items
		WHERE user_item_id = %d
		", $user_item_id));

	if (!$user_item || $user_item->item_type !== 'lp_quiz') {
		wp_send_json_error('Invalid submission');
	}

    // Security: Tutor can only view quizzes they created
	$quiz_post = get_post($user_item->item_id);
	if (
		!$quiz_post || 
		$quiz_post->post_type !== 'lp_quiz' || 
		!(current_user_can('lp_teacher') || current_user_can('administrator'))
	) {
		wp_send_json_error('Unauthorized access');
	}


    // Fetch submitted answers and questions from wp_learnpress_user_item_results
	$results = $wpdb->get_results($wpdb->prepare("
		SELECT * FROM {$wpdb->prefix}learnpress_user_item_results
		WHERE user_item_id = %d
		", $user_item_id));

	if (!$results) {
		wp_send_json_error('No questions found in submission.');
	}

	$question_ids = [];
	$submitted_answers = [];

	$result_data = json_decode($results[0]->result, true);

	if (isset($result_data['questions']) && is_array($result_data['questions'])) {
		foreach ($result_data['questions'] as $question_id => $qdata) {
			$question_ids[] = (int)$question_id;
			$submitted_answers[$question_id] = [
				'answer' => $qdata['answered'] ?? '',
				'is_correct' => $qdata['correct'] ?? false,
				'mark' => $qdata['mark'] ?? 0,
			];
		}
	}

	ob_start();
	?>
	<div class="lp-review-wrap view-submission-cls-adding">
		<div class="inner-profile-added-view">
		<h4 class="profile-heading"><?= esc_html(get_the_title($quiz_post)) ?> - Student Review</h4>
		<div class="inside-type-added-learn">
		<p><strong class="taken-width-type">Student:</strong> <span class="taken-span-width"><?= esc_html(get_userdata($user_item->user_id)->display_name) ?></span></p>
		<p><strong class="taken-width-type">Submitted at:</strong> <span class="taken-span-width"><?= esc_html($user_item->end_time) ?></span></p>

		<?php if (!empty($question_ids)): ?>
			<?php foreach ($question_ids as $question_id): ?>
				<?php
				$question_post = get_post($question_id);
				if (!$question_post) continue;

				$title = $question_post->post_title;
				$content = $question_post->post_content;
				$answer_data = $submitted_answers[$question_id] ?? [];
				$submitted = (array)($answer_data['answer'] ?? []);

                // Get question options and correct answers
				$options = get_post_meta($question_id, '_lp_options', true);
				$correct = get_post_meta($question_id, '_lp_correct', true);

                // Convert selected answer values to labels
				$answer_labels = [];
				if (!empty($submitted)) {
					$placeholders = implode(',', array_fill(0, count($submitted), '%s'));
					$query = $wpdb->prepare("
						SELECT value, title 
						FROM {$wpdb->prefix}learnpress_question_answers 
						WHERE question_id = %d AND value IN ($placeholders)
						", array_merge([$question_id], $submitted));

					$answers = $wpdb->get_results($query);
					foreach ($answers as $answer) {
						$answer_labels[] = $answer->title;
					}
				}
				?>
				<div class="lp-review-question">
					<p id="highlight-question-cls"><?= esc_html($title) ?></p>
					<div><?= wpautop($content) ?></div>
					<?php if (is_array($options)): ?>
						<ul>
							<?php foreach ($options as $opt): ?>
								<?php
								$is_selected = in_array($opt['value'], $submitted);
								$is_correct = in_array($opt['value'], (array)$correct);
								$label = esc_html($opt['text']);
								?>
								<li>
									<?= $is_selected ? '✅' : '⬜' ?>
									<?= $label ?>
									<?php if ($is_correct): ?>
										<strong style="color:green;">(Correct)</strong>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if (!empty($answer_labels)): ?>
						<p><strong class="taken-width-type">Answer:</strong> <span class="taken-span-width"><?= esc_html(implode(', ', $answer_labels)) ?></span></p>
					<?php elseif (!empty($submitted)): ?>
						<p><strong class="taken-width-type">Answer:</strong> <span class="taken-span-width"><?= esc_html(implode(', ', $submitted)) ?></span></p>
					<?php else: ?>
						<p><strong class="taken-width-type">Answer:</strong> <span class="taken-span-width">Not answered</span></p>
					<?php endif; ?>

					<?php if (isset($answer_data['mark'])): ?>
						<p><strong class="taken-width-type">Mark:</strong> <span class="taken-span-width"><?= esc_html($answer_data['mark']) ?></span></p>
					<?php endif; ?>
				</div>
				<hr>
			<?php endforeach; ?>
		
			<?php else: ?>
			<p>No questions in this quiz.</p>
		<?php endif; ?>
		</div>
	</div>
	</div>
	<?php

	wp_send_json_success(ob_get_clean());
}




// Approve quiz via AJAX
add_action('template_redirect', function() {
    if (isset($_POST['approve_quiz']) && current_user_can('lp_teacher')) {
        global $wpdb;
        $user_item_id = absint($_POST['user_item_id']);

        $wpdb->update(
            "{$wpdb->prefix}learnpress_user_items",
            ['status' => 'completed'],
            ['user_item_id' => $user_item_id]
        );

        wp_redirect(add_query_arg('approved', '1'));
        exit;
    }
});

/* End of Test Result Customization */

/* Start of Test Page Reload after Submission */

function enqueue_modal_ok_reload_script() {
    if (is_singular('lp_course')) {
        add_action('wp_footer', function () {
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const okayButton = document.querySelector('.modal-button-ok');

                    if (okayButton) {
                        okayButton.addEventListener('click', function() {
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000); // 1-second delay (1000ms)
                        });
                    }
                });
            </script>
            <?php
        });
    }
}
add_action('wp', 'enqueue_modal_ok_reload_script');

/* End of Test Page Reload after Submission */

/* Start of Child-Parent checkout flow if user is not logged in */

// 1. Add custom checkout fields
add_action('woocommerce_before_order_notes', 'lp_add_parent_checkout_fields');
function lp_add_parent_checkout_fields($checkout) {
    echo '<div id="lp-parent-checkout"><h3>' . __('Purchase for a Child') . '</h3>';

    woocommerce_form_field('lp_purchase_for_child', [
        'type' => 'checkbox',
        'label' => __('Yes, this purchase is for my child'),
        'required' => false,
    ], $checkout->get_value('lp_purchase_for_child'));

    echo '<div id="lp-parent-fields" style="display:none;">';

    woocommerce_form_field('lp_parent_name', [
        'type' => 'text',
        'label' => __('Parent Full Name'),
        'required' => true,
    ], $checkout->get_value('lp_parent_name'));

    woocommerce_form_field('lp_parent_email', [
        'type' => 'email',
        'label' => __('Parent Email'),
        'required' => true,
    ], $checkout->get_value('lp_parent_email'));

    echo '</div></div>';

    // Simple toggle script
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.querySelector('#lp_purchase_for_child');
            const fields = document.querySelector('#lp-parent-fields');
            function toggleFields() {
                fields.style.display = checkbox.checked ? 'block' : 'none';
            }
            checkbox.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
    <?php
}

// 2. Validate custom fields
add_action('woocommerce_checkout_process', 'lp_validate_parent_checkout_fields');
function lp_validate_parent_checkout_fields() {
    if (isset($_POST['lp_purchase_for_child'])) {
        if (empty($_POST['lp_parent_name']) || empty($_POST['lp_parent_email'])) {
            wc_add_notice(__('Please fill out parent name and email.'), 'error');
        }
    }
}

// 3. Process fields after order is submitted
add_action('woocommerce_checkout_update_order_meta', 'lp_save_parent_order_meta');
function lp_save_parent_order_meta($order_id) {
    if (!isset($_POST['lp_purchase_for_child'])) return;

    $parent_name  = sanitize_text_field($_POST['lp_parent_name']);
    $parent_email = sanitize_email($_POST['lp_parent_email']);

    update_post_meta($order_id, '_lp_parent_name', $parent_name);
    update_post_meta($order_id, '_lp_parent_email', $parent_email);
    update_post_meta($order_id, '_lp_purchase_for_child', 'yes');
}

// 4. After order is complete: create or link parent, and link child
add_action('woocommerce_thankyou', 'lp_process_parent_child_accounts', 10, 1);
function lp_process_parent_child_accounts($order_id) {
    $order = wc_get_order($order_id);
    if (!$order || get_post_meta($order_id, '_lp_purchase_for_child', true) !== 'yes') return;

    $parent_name  = get_post_meta($order_id, '_lp_parent_name', true);
    $parent_email = get_post_meta($order_id, '_lp_parent_email', true);
    $child_email  = $order->get_billing_email();
    $child_name   = $order->get_billing_first_name();

    if (empty($parent_email) || empty($parent_name) || empty($child_email)) return;

    // Get or create parent user
    $parent_user = get_user_by('email', $parent_email);
    $random_password = '';

    if (!$parent_user) {
        $base_username = sanitize_user(current(explode('@', $parent_email)));
        $username = $base_username;
        $suffix = 1;

        // Ensure unique username
        while (username_exists($username)) {
            $username = $base_username . $suffix;
            $suffix++;
        }

        $random_password = wp_generate_password(10, true);
        $parent_user_id = wp_create_user($username, $random_password, $parent_email);

        if (!is_wp_error($parent_user_id)) {
            wp_update_user([
                'ID' => $parent_user_id,
                'display_name' => $parent_name,
                'first_name'   => $parent_name,
            ]);

            $parent_user = get_user_by('ID', $parent_user_id);
            $parent_user->set_role('parent');

            // Send welcome email with password
            $subject = 'Your Parent Account is Ready';
            $message = "Hello $parent_name,\n\n";
            $message .= "An account has been created for you as a Parent on our site.\n\n";
            $message .= "Login Email: $parent_email\n";
            $message .= "Temporary Password: $random_password\n\n";
            $message .= "You can log in here: " . wp_login_url() . "\n\n";
            $message .= "Please log in and change your password after your first login.\n\nThank you!";

            wp_mail($parent_email, $subject, $message);
        }
    }

    // Ensure child exists (auto-created by WooCommerce at checkout)
    $child_user = get_user_by('email', $child_email);
    if ($child_user && $parent_user) {
        $parent_id = $parent_user->ID;
        $child_id  = $child_user->ID;

        // Link child to parent
        $linked_children = get_user_meta($parent_id, 'lp_children_users', true);
        if (!is_array($linked_children)) $linked_children = [];

        if (!in_array($child_id, $linked_children)) {
            $linked_children[] = $child_id;
            update_user_meta($parent_id, 'lp_children_users', $linked_children);
        }
    }
}


// 5. Ensure 'parent' role exists
add_action('init', function () {
    if (!get_role('parent')) {
        add_role('parent', 'Parent', ['read' => true]);
    }
});


/* End of Child-Parent checkout flow if user is not logged in */


// function custom_lp_inline_script() {
//     wp_register_script('custom-lp-script', '');
//     wp_enqueue_script('custom-lp-script');

//     $js = <<<JS
// jQuery(function($) {
//     console.log('LearnPress inline JS loaded');
//     $(document).on('click', '#view-more-courses', function () {
//         $('.hidden-row').css('display', 'table-row').removeClass('hidden-row');
//         $(this).hide();
//     });
// });
// JS;

//     wp_add_inline_script('custom-lp-script', $js);
// }
// add_action('wp_enqueue_scripts', 'custom_lp_inline_script');

/* Start of View student for course */

// add_action( 'wp_ajax_lp_render_gradebook_for_course', 'lp_render_gradebook_for_course' );

// function lp_render_gradebook_for_course() {
//     if ( ! current_user_can( 'edit_lp_course' ) ) {
//         wp_send_json_error( 'Not allowed' );
//     }

//     $course_id = intval( $_POST['course_id'] );

//     if ( ! $course_id ) {
//         wp_send_json_error( 'Missing Course ID' );
//     }

//     ob_start();

//     // Mimic the admin gradebook output
//     include_once ABSPATH . 'wp-admin/includes/admin.php'; // Ensure admin functions are available

//     // Include LearnPress gradebook admin file
//     $gradebook_file = WP_PLUGIN_DIR . '/learnpress-gradebook/admin/views/html-admin-page-gradebook.php';

//     if ( file_exists( $gradebook_file ) ) {
//         // This file expects global $post
//         global $post;
//         $post = get_post( $course_id );

//         include $gradebook_file;

//         wp_send_json_success( ob_get_clean() );
//     } else {
//         wp_send_json_error( 'Gradebook file not found.' );
//     }
// }

// 1. Add a new tab in instructor profile
add_filter('learn-press/profile-tabs', function ($tabs) {
    // if (in_array('lp_teacher', (array) $user->roles))  return $tabs;

    $tabs['enrolled_students'] = [
        'title'    => __('Enrolled Students', 'text-domain'),
        'slug'     => 'enrolled-students',
        'callback' => 'lp_render_enrolled_students_tab',
        'icon'     => '<i class="lp-icon-file-alt"></i>',
        'priority' => 10,
    ];
    return $tabs;
});

// 2. Tab content with form submission (no JS)
// function lp_render_enrolled_students_tab() {
//     $instructor_id = get_current_user_id();

//     // Get all courses created by this instructor
//     $courses = get_posts([
//         'post_type'      => 'lp_course',
//         'posts_per_page' => -1,
//         'author'         => $instructor_id,
//         'orderby'        => 'title',
//         'order'          => 'ASC',
//     ]);

//     // Preselect course via GET (from thumbnail click)
//     $selected_course_id = isset($_GET['course_id']) ? absint($_GET['course_id']) : 0;

//     // POST takes precedence if form was submitted
//     if (isset($_POST['lp_selected_course'])) {
//         $selected_course_id = absint($_POST['lp_selected_course']);
//     }

//     echo '<h2>Enrolled Students</h2>';
//     echo '<form method="POST">';
//     echo '<select name="lp_selected_course" onchange="this.form.submit()">';
//     echo '<option value="">Select a course</option>';

//     foreach ($courses as $course) {
//         $selected = selected($selected_course_id, $course->ID, false);
//         echo "<option value='{$course->ID}' {$selected}>" . esc_html($course->post_title) . "</option>";
//     }

//     echo '</select>';
//     echo '</form>';

//     // Display enrolled students if a course is selected
//     if ($selected_course_id) {
//         lp_dashboard_course_charts_with_toggle($selected_course_id);
//     }
// }
function lp_render_enrolled_students_tab() {
    $instructor_id = get_current_user_id();

    // Get the instructor's username
    $instructor_username = get_userdata($instructor_id)->user_login;

    // Get all courses created by this instructor
    $courses = get_posts([
        'post_type'      => 'lp_course',
        'posts_per_page' => -1,
        'author'         => $instructor_id,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    // Determine selected course from GET or POST
    $selected_course_id = isset($_GET['course_id']) ? absint($_GET['course_id']) : 0;

    // If the form was submitted, update the selected course and redirect to set it in the URL
    if (isset($_POST['lp_selected_course'])) {
        $selected_course_id = absint($_POST['lp_selected_course']);

        // Redirect to the URL with the instructor's username and course_id
        wp_redirect( home_url("/lp-profile/{$instructor_username}/enrolled-students/?course_id={$selected_course_id}") );
        exit;
    }

    echo '<h2 class="profile-heading">Enrolled Students</h2>';
    echo '<div class="new-table-wrapper-cls-adding"><form method="POST">';
    echo '<select name="lp_selected_course" onchange="this.form.submit()">';
    echo '<option value="">Select a course</option>';

    foreach ($courses as $course) {
        $selected = selected($selected_course_id, $course->ID, false);
        echo "<option value='{$course->ID}' {$selected}>" . esc_html($course->post_title) . "</option>";
    }

    echo '</select>';
    echo '</form></div>';

    // Show enrolled student data if course selected
    if ($selected_course_id) {
        lp_dashboard_course_charts_with_toggle($selected_course_id);
    }
}


function lp_dashboard_course_charts_with_toggle($course_id) {
    global $wpdb;

    // Get user progress data
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT user_id, status, start_time 
         FROM {$wpdb->prefix}learnpress_user_items 
         WHERE item_id = %d AND item_type = 'lp_course' AND status IN ('enrolled', 'finished')
         ORDER BY start_time ASC",
        $course_id
    ));

    $chart_data = [];
    $total_enrolled = 0;
    $total_finished = 0;

    foreach ($results as $row) {
        $date = date('Y-m-d', strtotime($row->start_time));
        if (!isset($chart_data[$date])) {
            $chart_data[$date] = ['enrolled' => 0, 'finished' => 0];
        }
        $chart_data[$date]['enrolled']++;
        if ($row->status === 'finished') {
            $chart_data[$date]['finished']++;
        }
    }

    $labels = array_keys($chart_data);
    $enrolls = array_column($chart_data, 'enrolled');
    $finishes = array_column($chart_data, 'finished');

    foreach ($enrolls as $e) $total_enrolled += $e;
    foreach ($finishes as $f) $total_finished += $f;
    ?>

    <!-- View Chart Button -->
	 <div class="view-chart-btn">
    <button id="view-chart-toggle" type="button" class="components-button is-primary has-text has-icon view-chart-btn-cls-adding">
        <svg xmlns="http://www.w3.org/2000/svg" class="share-ics" viewBox="0 0 24 24" width="20" height="20"><path d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"></path></svg>
        View Chart
    </button>
	</div>

    <!-- Chart Section -->
    <div id="chart-section" style="display: none;" class="chart-section-edit-here">
        <div class="chart_sc">
            <!-- Line Chart -->
            <div class="detail-chart">
                <div style="margin-bottom: 20px;">
                    <div role="group" class="components-button-group">
                        <button type="button" class="components-button is-primary common-btn-type-here">Last 7 days</button>
                        <button type="button" class="components-button common-btn-type-here">Last 30 days</button>
                        <button type="button" class="components-button common-btn-type-here">Last 12 months</button>
                    </div>
                </div>
				
				<div class="middle-graph-chart">
  				<div class="canvas-graph-cls-adding-here">
                    <canvas id="lpChartLine" class="chart-one-cls"></canvas>
                    <div class="title-chart">
                        <h3 class="heading-chart-point">Chart 1:</h3>
                        <p class="paragrph-chart-point">Number of students registered and finished the course by date</p>
                    </div>
                </div>

					<div class="detail-chart">
						<div class="ct-chart">
							<div class="ct-left">
								<canvas id="lpChartPie" class="chart-two-cls"></canvas>
							</div>
							<div class="ct-right new-controlright-graph-set">
								<p class="paragrph-chart-point">Total students enrolled: <b class="strong-tag-cls"><?php echo esc_html($total_enrolled); ?></b></p>
								<p class="paragrph-chart-point">Total students finished: <b class="strong-tag-cls"><?php echo esc_html($total_finished); ?></b></p>
								<p class="paragrph-chart-point">Total: <b class="strong-tag-cls"><?php echo esc_html($total_enrolled); ?></b></p>
							</div>
						</div>
						<div class="title-chart total-title-chart-cls-adding">
							<h3 class="heading-chart-point">Chart 2:</h3>
							<p class="paragrph-chart-point">All students registered and finished the course</p>
						</div>
					</div>
				</div>
              
            </div>

          

        </div>
    </div>

    <!-- Chart Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.getElementById('view-chart-toggle').addEventListener('click', function () {
            const section = document.getElementById('chart-section');
            section.style.display = section.style.display === 'none' ? 'block' : 'none';
        });

        const ctxLine = document.getElementById('lpChartLine').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [
                    {
                        label: 'Enrolled',
                        data: <?php echo json_encode($enrolls); ?>,
                        borderColor: '#0073aa',
                        backgroundColor: 'rgba(0,115,170,0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Finished',
                        data: <?php echo json_encode($finishes); ?>,
                        borderColor: '#46b450',
                        backgroundColor: 'rgba(70,180,80,0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } }
            }
        });

        const ctxPie = document.getElementById('lpChartPie').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Finished', 'In Progress'],
                datasets: [{
                    data: [<?php echo $total_finished; ?>, <?php echo $total_enrolled - $total_finished; ?>],
                    backgroundColor: ['#46b450', '#ff9800'],
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>

    <!-- Student Table -->
    <?php
    // Get enrolled user IDs
    $user_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT user_id 
         FROM {$wpdb->prefix}learnpress_user_items 
         WHERE item_id = %d AND item_type = 'lp_course' AND status IN ('enrolled', 'finished')",
        $course_id
    ));

    if (!$user_ids) {
        echo '<div class="noenrolled-student-cls"><p>No students enrolled in this course.</p></div>';
        return;
    }

    $students = get_users(['include' => $user_ids]);

   	echo '<div class="heading-sub-type"><h3 class="subheading-student">Student List</h3></div>';
	echo '<div class="custom-table-wrapper new-custom-wrapper-cls-adding">';
	if (count($students) > 0) {
		echo '<table>';
		echo '<thead><tr><th>Student Name</th><th>Email</th><th>Start time</th><th>Status</th><th>Progress</th></tr></thead>';
		echo '<tbody>';

		foreach ($students as $student) {

			$status = $wpdb->get_var($wpdb->prepare(
				"SELECT status FROM {$wpdb->prefix}learnpress_user_items 
				WHERE user_id = %d AND item_id = %d AND item_type = 'lp_course' ORDER BY user_item_id DESC LIMIT 1",
				$student->ID, $course_id
			));

			$progress = $wpdb->get_var($wpdb->prepare(
				"SELECT progress FROM {$wpdb->prefix}learnpress_user_items 
				WHERE user_id = %d AND item_id = %d AND item_type = 'lp_course' ORDER BY user_item_id DESC LIMIT 1",
				$student->ID, $course_id
			));

			$start_time = $wpdb->get_var($wpdb->prepare(
				"SELECT start_time FROM {$wpdb->prefix}learnpress_user_items 
				WHERE user_id = %d AND item_id = %d AND item_type = 'lp_course' ORDER BY user_item_id DESC LIMIT 1",
				$student->ID, $course_id
			));
			$start_time_display = $start_time ? date('Y-m-d H:i:s', strtotime($start_time)) : 'Not Started';

			$progress_percentage = $progress !== null ? round($progress * 100) : 0;

			echo '<tr>';
			echo '<td>' . esc_html($student->display_name) . '</td>';
			echo '<td>' . esc_html($student->user_email) . '</td>';
			echo '<td>' . esc_html($start_time_display) . '</td>';
			echo '<td>' . esc_html(ucfirst($status)) . '</td>';
			echo '<td>' . esc_html($progress_percentage) . '%</td>';
			echo '</tr>';
		}

		echo '</tbody></table>';
	} else {
		echo '<p>No students are enrolled in this course.</p>';
	}
	echo '</div>';
}

function add_course_session_metabox() {
	add_meta_box(
		'lp_course_session_info',
		'Session Info (Date & Time)',
		'render_course_session_metabox',
		'lp_course',
		'side',
		'default'
	);
}
add_action('add_meta_boxes', 'add_course_session_metabox');

// Render the contents of the session info meta box
function render_course_session_metabox($post) {
	$date = get_post_meta($post->ID, 'course_date', true);
	$time = get_post_meta($post->ID, 'course_time', true);
	?>
	<label for="course_date">Date:</label><br>
	<input type="date" name="course_date" value="<?php echo esc_attr($date); ?>" style="width:100%;"><br><br>

	<label for="course_time">Time:</label><br>
	<input type="time" name="course_time" value="<?php echo esc_attr($time); ?>" style="width:100%;">
	<?php
}

// Save the date and time values when the course is saved
function save_course_session_meta($post_id) {
	if (array_key_exists('course_date', $_POST)) {
		update_post_meta($post_id, 'course_date', sanitize_text_field($_POST['course_date']));
	}
	if (array_key_exists('course_time', $_POST)) {
		update_post_meta($post_id, 'course_time', sanitize_text_field($_POST['course_time']));
	}
}
add_action('save_post_lp_course', 'save_course_session_meta');

// Send email notification to the assigned tutor and enrolled students when a course is updated

function send_email_to_tutor_on_course_assignment($post_id, $post, $update) {

	if ( $post->post_type !== 'lp_course' ) {
		return;
	}
	if (!class_exists('LearnPress')) {
		return;
	}
	$tutor_id = $post->post_author;
	$tutor = get_userdata($tutor_id);

	if (!$tutor || !isset($tutor->user_email)) {
		return;
	}
	if (!$update) {
		return;
	}

	$course_title = get_the_title( $post_id );
	$course_link  = get_permalink( $post_id );
	$course_date  = get_post_meta( $post_id, 'course_date', true );
	$course_time  = get_post_meta( $post_id, 'course_time', true );
	$course_topic = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );

	if ( function_exists( 'get_enrolled_students_detailed_info' ) ) {
		$students = get_enrolled_students_detailed_info( $post_id );
	} else {
		$students = [];
	}

	$student_list = '';
	if ( !empty( $students ) ) {
		foreach ( $students as $student ) {
			$student_list .= "- " . $student['name'];
			if ( !empty( $student['enroll_date'] ) ) {
				$student_list .= " (Enrolled: " . $student['enroll_date'] . ")";
			}
			$student_list .= "\n";			
			$student_email = $student['email'];
			$student_subject = "Confirmation of tutor for your session";
			$student_message = "Dear " . $student['name'] . ",\n\nYou have been enrolled in the course. Your tutor is " . $tutor->display_name . ".\n\nCourse details: " . $course_title;
			wp_mail($student_email, $student_subject, $student_message);
			
		}
	} else {
		$student_list = "No students enrolled yet.";
	}
	$subject = "You've been assigned to a new course: $course_title";
	$message = "Hello " . $tutor->display_name . ",\n\n";
	$message .= "You have been assigned to the course: \"" . $course_title . "\".\n";
	$message .= "Please review it here: $course_link\n\n";
	$message .= "Session Details:\n";
	$message .= "---------------------------------------\n";
	$message .= "Course: $course_title\n";
	$message .= "Date: $course_date\n";
	$message .= "Time: $course_time\n";
	$message .= "Topic: $course_topic\n\n";
	$message .= "Enrolled Students:\n$student_list";

	wp_mail($tutor->user_email, $subject, $message);

	$notification_post = array(
		'post_title'   => 'New Course Assignment: ' . $course_title,
		'post_content' => $message,
		'post_status'  => 'publish',
		'post_author'  => $tutor_id,
		'post_type'    => 'lp_announcements',
	);
	wp_insert_post($notification_post);
}
add_action('save_post_lp_course', 'send_email_to_tutor_on_course_assignment', 10, 3);

//Each lesson breakdown duration should display on the course detail.

add_action( 'wp_ajax_get_lesson_duration', 'get_lesson_duration' );
add_action( 'wp_ajax_nopriv_get_lesson_duration', 'get_lesson_duration' );

function get_lesson_duration() {

	$item_id = isset( $_POST['item_id'] ) ? intval( $_POST['item_id'] ) : 0;

	if ( ! $item_id ) {
		wp_send_json_error( ['message' => 'Invalid item ID'] );
	}

	$item_type     = get_post_type( $item_id );
	$item_duration = get_post_meta( $item_id, '_lp_duration', true );

	if ( ! $item_duration ) {
		wp_send_json_error( ['message' => 'No duration found for item'] );
	}

	$duration_arr    = explode( ' ', $item_duration );
	$duration_number = floatval( $duration_arr[0] ?? 0 );
	$duration_type   = $duration_arr[1] ?? '';

	if ( $duration_number > 0 ) {
		$item_duration_plural = LP_Datetime::get_string_plural_duration( $duration_number, $duration_type );
		wp_send_json_success( ['duration' => $item_duration_plural] );
	} else {
		wp_send_json_error( ['message' => 'Invalid duration format'] );
	}
}

// Display a link showing the number of students enrolled in a course.

add_action( 'learnpress_loop_item_course_meta', 'add_enrolled_students_link_to_course_meta', 20 );

function add_enrolled_students_link_to_course_meta() {
	global $post;

	if ( $post->post_type !== 'lp_course' ) {
		return;
	}

	$course_id           = $post->ID;
	$instructor_id       = $post->post_author;
	$instructor_username = get_userdata( $instructor_id )->user_login;

	global $wpdb;
	$enrolled_students = $wpdb->get_var( $wpdb->prepare(
	"SELECT COUNT(DISTINCT user_id)
	FROM {$wpdb->prefix}learnpress_user_items
	WHERE item_id = %d AND item_type = 'lp_course'",
	$course_id
));

	// var_dump($enrolled_students);
	$link = home_url( "/lp-profile/{$instructor_username}/enrolled-students/?course_id={$course_id}" );

	echo '<div class="enrolled-student-link fdwerf">';
	echo '<a href="' . esc_url( $link ) . '">';
	echo ' <i class="lp-icon-students"></i> ' . intval( $enrolled_students );
	echo '</a>';
	echo '</div>';
}

add_action('pre_get_posts', 'childtheme_filter_reviews_by_star');
function childtheme_filter_reviews_by_star($query) {
    if ( is_admin() || !$query->is_main_query() ) {
        return;
    }

    // Check if the rating filter is applied
    if ( isset($_GET['c_review_star']) && is_numeric($_GET['c_review_star']) ) {
        $rating = floatval($_GET['c_review_star']);

        if ($rating > 0) {
            // Modify the query to include only courses with the selected rating and above
            $meta_query = array(
                'relation' => 'AND',
                array(
                    'key'     => 'average_rating',
                    'value'   => $rating,
                    'compare' => '>=',
                    'type'    => 'NUMERIC',
                ),
                array(
                    'key'     => 'average_rating',
                    'value'   => array(0, 0.0, '0', '0.0'),
                    'compare' => 'NOT IN', // Exclude courses with 0.0 ratings
                    'type'    => 'NUMERIC',
                ),
            );

            $query->set('meta_query', $meta_query);

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Review Star Filter Triggered');
                error_log('Rating selected: ' . $rating);
                error_log(print_r($meta_query, true));
            }
        }
    }
}


add_action( 'wp_footer', 'ct_hide_low_rated_courses' );
function ct_hide_low_rated_courses() {
    if ( ! is_post_type_archive('courses') && ! is_page('courses') ) {
        return;
    }
    ?>
    <script>
    (function($){
        // get the selected rating threshold
        var params = new URLSearchParams(window.location.search);
        var minRating = parseFloat(params.get('c_review_star')) || 0;

        function hideLowRatings(){
            // loop each course meta block
            $('.thim-ekits-course__meta-data').each(function(){
                var $meta    = $(this),
                    ratingEl = $meta.find('.thim-ekits-course__rating-number'),
                    rating   = parseFloat(ratingEl.text()) || 0;

                if ( rating < minRating ) {
                    // adjust this selector to match your course-item wrapper
                    $meta.closest('.tutor-course-loop, .thim-ekits-single-course, .course-item').hide();
                }
            });
        }

        // run on initial load
        $(document).ready(hideLowRatings);
        // run after any AJAX
        $(document).ajaxComplete(hideLowRatings);
    })(jQuery);
    </script>
    <?php
}

// Add this to your theme's `functions.php`
add_action('pre_get_posts', 'filter_courses_by_rating');

function filter_courses_by_rating($query) {
    // Ensure we are modifying the correct query (main query on the front end)
    if (!is_admin() && $query->is_main_query()) {
        if (isset($_GET['c_review_star']) && !empty($_GET['c_review_star'])) {
            // Get the rating from the filter (e.g., c_review_star=3)
            $rating = intval($_GET['c_review_star']);

            if ($rating > 0) {
                // Modify the query to include only courses with a rating >= the selected value
                $meta_query = array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'average_rating', // This assumes the rating is stored as 'average_rating'
                        'value'   => $rating,
                        'compare' => '>=',
                        'type'    => 'NUMERIC',
                    ),
                    array(
                        'key'     => 'average_rating',
                        'value'   => '', // Exclude courses with empty ratings
                        'compare' => '!=', 
                    ),
                );
                // Set the meta_query to the query
                $query->set('meta_query', $meta_query);
            }
        }
    }
}


function display_course_rating($course_id) {
    // Get the number of reviews and course rating
    $num_reviews = get_comments_number($course_id);
    $rating = (function_exists('learn_press_get_course_rate')) ? learn_press_get_course_rate($course_id) : 0;

    // Check if there are reviews before displaying the rating
    if ($num_reviews > 0) {
        // Output the rating and stars above the title
        echo '<div class="thim-ekits-course__meta-data new-rating-start-cls-adding-here" style="margin: 10px 0;">';
        echo '<span class="thim-ekits-course__rating-number new-rating-start-cls-adding-all-number">' . esc_html(number_format($rating, 1)) . '</span>';
        echo '<span class="thim-ekits-course__rating-stars new-rating-start-cls-adding-all-number-course">';
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= floor($rating)) {
                echo '<i class="fas fa-star" style="color: #FFD700;"></i>';
            } elseif ($i - $rating < 1) {
                echo '<i class="fas fa-star-half-alt" style="color: #FFD700;"></i>';
            } else {
                echo '<i class="far fa-star" style="color: #FFD700;"></i>';
            }
        }

        echo '</span>';
        echo '<span class="thim-ekits-course__review-count"> (' . $num_reviews . ' reviews)</span>';
        echo '</div>';
    } else {
        // Optionally, you can output a message indicating no reviews are available
        echo '<div class="thim-ekits-course__meta-data new-rated-start-meta-data" style="margin: 10px 0;">';
        echo '<span class="thim-ekits-course__review-count main-new-rated-start-meta-data"> (No reviews yet)</span>';
        echo '</div>';
    }
}


add_action('wp_footer', function () {
    ?>
    <div id="course-filter-loader" class="course-filter-loader" style="display: none;">
        <img decoding="async" class="loader-spinner" src="http://10.0.4.208/tutorstok/wp-content/uploads/2025/04/pause-gIF-image-ezgif.com-gif-maker.gif" alt="Loading...">
    </div>
    <?php
});


function get_lp_course_result_from_db( $user_id, $course_id ) {
    global $wpdb;
    $raw_sql = $wpdb->prepare("SELECT user_item_id FROM {$wpdb->prefix}learnpress_user_items WHERE user_id = %d AND item_id = %d AND item_type = 'lp_course'", $user_id, $course_id);
    $user_item_id = $wpdb->get_var( $raw_sql ); // execute query
    if ( ! $user_item_id ) {
        return null;
    }
    $query = $wpdb->prepare("
        SELECT * FROM {$wpdb->prefix}learnpress_user_item_results
        WHERE user_item_id = %d
    ", $user_item_id);
    echo '<pre>Query: ' . $query . '</pre>';
    $result = $wpdb->get_row( $query, ARRAY_A );
    echo '<pre>Result: ' . print_r( $result, true ) . '</pre>';
    return $result;
}

function create_user_course_progress_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'user_course_progress';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        course_id BIGINT(20) UNSIGNED NOT NULL,
        progress FLOAT DEFAULT 0,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY user_course (user_id, course_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
add_action('after_setup_theme', 'create_user_course_progress_table');

// Add custom REST API endpoint to get tutor name by course slug
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/tutor-name', [
        'methods'  => 'GET',
        'callback' => 'get_tutor_name_by_course_slug',
        'args'     => [
            'slug' => [
                'required' => true,
            ],
        ],
        'permission_callback' => '__return_true',
    ]);
});

function get_tutor_name_by_course_slug($request) {
    $slug = sanitize_text_field($request->get_param('slug'));
    $course = get_page_by_path($slug, OBJECT, 'lp_course');

    if (!$course) {
        return new WP_Error('not_found', 'Course not found', ['status' => 404]);
    }

    $author_id = $course->post_author;
    $author_name = get_the_author_meta('display_name', $author_id);

    return ['tutor_name' => $author_name];
}



// Schedule a daily cron event if not scheduled
if ( ! wp_next_scheduled( 'lp_notify_upcoming_courses_event' ) ) {
    wp_schedule_event( time(), 'daily', 'lp_notify_upcoming_courses_event' );
}

// 1) Print the modal markup in your footer
add_action( 'wp_footer', function() {
    ?>
    <div id="custom-video-modal" class="cv-modal" style="display:none">
      <div class="cv-modal__overlay"></div>
      <div class="cv-modal__content">
        <button type="button" class="cv-modal__close">&times;</button>
        <div class="cv-modal__body"></div>
      </div>
    </div>
    <?php
} );


add_action('wp_ajax_check_user_email_exists', 'check_user_email_exists');
add_action('wp_ajax_nopriv_check_user_email_exists', 'check_user_email_exists');

function check_user_email_exists() {
    error_log('AJAX function started');

    if (!isset($_POST['user_login'])) {
        wp_send_json(['exists' => false]);
    }

    $email = sanitize_email($_POST['user_login']);
    error_log('AJAX email received: ' . $email);

    $user = get_user_by('email', $email);

    if ($user) {
        error_log('User exists: ID ' . $user->ID);
        wp_send_json(['exists' => true]);
    } else {
        error_log('User not found for email: ' . $email);
        wp_send_json(['exists' => false]);
    }

    wp_die();
}

add_action('admin_post_nopriv_custom_login', 'handle_custom_login');
add_action('admin_post_custom_login', 'handle_custom_login');

function handle_custom_login() {
	if (!session_id()) {
		session_start();
	}

	$_SESSION['login_error_code'] = '';
	$_SESSION['field_errors'] = array();
	$_SESSION['login_log_value'] = $_POST['log'];

	$field_errors = array();

	if (empty($_POST['log'])) {
		$field_errors['log'] = 'Please enter username or email.';
	}

	if (empty($_POST['pwd'])) {
		$field_errors['pwd'] = 'Please enter password.';
	}

	$redirect_url = isset($_POST['redirect_url']) ? esc_url_raw($_POST['redirect_url']) : home_url();

	if (!empty($field_errors)) {
		$_SESSION['field_errors'] = $field_errors;
		wp_redirect($redirect_url);
		exit;
	}

	$login_input = sanitize_text_field($_POST['log']);
	$password = $_POST['pwd'];

	if (is_email($login_input)) {
		$user = get_user_by('email', $login_input);
	} else {
		$user = get_user_by('login', $login_input);
	}

	if (!$user) {
		$_SESSION['login_error_code'] = 'not_registered';
		wp_redirect($redirect_url);
		exit;
	}

	$creds = array(
		'user_login'    => $user->user_login,
		'user_password' => $password,
		'remember'      => isset($_POST['rememberme'])
	);

	$login = wp_signon($creds, false);

	if (is_wp_error($login)) {
		$_SESSION['login_error_code'] = $login->get_error_code();
		wp_redirect($redirect_url);
		exit;
	}

	// Success
	wp_redirect(home_url());
	exit;
}
function remove_thim_edit_item_link() {
	remove_action( 'learn-press/after-course-item-content', 'thim_content_item_edit_link', 3 );
}
add_action( 'init', 'remove_thim_edit_item_link' );
