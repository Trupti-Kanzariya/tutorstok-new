<?php
/**
 * Template for displaying title of quiz.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/content-quiz/title.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  3.0.0
 */

defined( 'ABSPATH' ) || exit();

$quiz   = LP_Global::course_item_quiz();
$course = learn_press_get_course();
$title  = $quiz->get_heading_title( 'display' );

if ( ! $title ) {
	return;
}

// Get current user and quiz item status
$user_id    = get_current_user_id();
$item_id    = $quiz->get_id();
$course_id  = $course ? $course->get_id() : 0;
$user_item  = learn_press_get_user( $user_id )->get_item( $item_id, $course_id );
$status     = $user_item ? $user_item->get_status() : '';

?>

<h1 class="course-item-title quiz-title"><?php echo esc_html( $title ); ?></h1>

<?php if ( $status === 'pending-review' ) : ?>
	<div class="lp-message lp-message-info" style="margin-top: 10px;">
		<?php esc_html_e( 'Your test is submitted and is waiting for your tutor to review.', 'learnpress' ); ?>
	</div>
<?php endif; ?>

