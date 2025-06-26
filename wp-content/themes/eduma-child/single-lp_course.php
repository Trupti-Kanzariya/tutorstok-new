<?php
get_header();

echo "called";
$course_id = get_the_ID();
$user_type = get_query_var('course_user_type');
$tutor_template_id   = 24037;
$student_template_id = 24045;
$normal_template_id  = 24069;

// Load Elementor template based on URL
if ( $user_type === 'tutor' ) {
	echo do_shortcode('[thim_ekit id="' . $tutor_template_id . '"]');
} elseif ( $user_type === 'student' ) {
	echo do_shortcode('[thim_ekit id="' . $student_template_id . '"]');
} else {
	echo do_shortcode('[thim_ekit id="' . $normal_template_id . '"]');
}

get_footer();
