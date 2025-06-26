<?php
$user_id   = get_current_user_id();
$quiz_id   = get_the_ID();
$course_id = learn_press_get_course_id();

$user_item = learn_press_get_user_item( $user_id, $quiz_id, $course_id );

if ( $user_item && $user_item->status === 'pending-review' ) {
    echo '<div class="lp-pending-review-message">';
    echo '<h3>Test is pending tutor review.</h3>';
    echo '<p>You will see your result once your tutor has reviewed and approved your submission.</p>';
    echo '</div>';
    return; // prevents quiz questions from showing
}
?>
