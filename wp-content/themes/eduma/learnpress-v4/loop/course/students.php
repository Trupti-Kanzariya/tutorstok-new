<?php
/**
 * Template for displaying course students within the loop.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/loop/course/students.php.
 *
 * @author  ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$course = learn_press_get_course();
if ( ! $course ) {
	return;
}
$count = $course->get_users_enrolled();
?>

<div class="course-students new-course-student-cls-adding-here">
    <label class="none-label-cls-adding"><?php esc_html_e( 'Students', 'eduma' ); ?></label>
	<div class="value new-course-ics-cls-adding"><i class="<?php echo eduma_font_icon('students'); ?>"></i>
		<?php echo esc_html( $count ); ?>
    </div>
    <span><?php echo $count > 1 ? sprintf( __( 'students', 'eduma' ) ) : sprintf( __( 'student', 'eduma' ) ); ?></span>
</div>
