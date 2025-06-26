<?php
/*
Plugin Name: LearnPress/Tutor Notifications
Description: Custom notification system for LearnPress and Tutor LMS
Version: 1.0
Author: CMARIX
*/

function lp_notification_activate() {
	global $wpdb;
	$table = $wpdb->prefix . 'learnpress_notifications';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table (
		notification_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		user_id BIGINT UNSIGNED NOT NULL,
		title TEXT NOT NULL,
		created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
		level TINYINT DEFAULT 0,
		status TINYINT DEFAULT 0
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta($sql);
}
register_activation_hook(__FILE__, 'lp_notification_activate');

function lp_add_notification( $user_id, $title, $level = 0 ) {

	global $wpdb;
	$table = $wpdb->prefix . 'learnpress_notifications';

	$wpdb->insert($table, [
		'user_id'    => $user_id,
		'title'      => $title,
		'created_at' => current_time('mysql'),
		'level'      => $level,
		'status'     => 0
	]);

}


add_action('learnpress/user/enrolled', function( $course_id, $user_id ) {
    // Get course title
	$course_title = get_the_title($course_id);

    // STUDENT NOTIFICATIONS
	lp_add_notification($user_id, 'Successfully enrolled for a course: "' . $course_title . '"');
	lp_add_notification($user_id, 'Intro Planning Session');
	lp_add_notification($user_id, $course_title . ' - Course Updates');
	lp_add_notification($user_id, '- Tutors Notes');
	lp_add_notification($user_id, 'Upcoming Sessions');
	lp_add_notification($user_id, '-- Cancel or reschedule the sessions');
	lp_add_notification($user_id, '- Testor assignment review status and status.');
	lp_add_notification($user_id, '- Testor assignment submitted and review status.');

    // TUTOR NOTIFICATIONS
	$tutor_id = get_post_field('post_author', $course_id);
	if ($tutor_id && $tutor_id != $user_id) {
		lp_add_notification($tutor_id, 'New student enrolled in your course: "' . $course_title . '"');
		lp_add_notification($tutor_id, 'Intro Planning Session');
		lp_add_notification($tutor_id, $course_title . ' - Course Updates');
		lp_add_notification($tutor_id, '- Tutors Notes');
		lp_add_notification($tutor_id, '-- Upcoming Sessions');
		lp_add_notification($tutor_id, 'Cancel or reschedule the sessions');
		lp_add_notification($tutor_id, '- Testor assignment submitted and review status.');
	}
}, 10, 2);



add_filter( 'learn-press/profile-tabs', function( $tabs ) {
	$tabs['notification'] = array(
		'title'    => __( 'Notifications', 'text-domain' ),
		'priority' => 30,
		'icon'     => '<i class="lp-icon-file-alt"></i>',
		'callback' => 'lp_notifications_tab_content',
	);
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


function custom_load_notifications_table( $page = 1 ) {
	$user_id       = get_current_user_id();
	$notifications = lp_get_user_notifications( $user_id );

	if ( empty( $notifications )) {
		echo '<p>No notifications found</p>';
		return;
	}

	$per_page            = 10;
	$total_notifications = count( $notifications );
	$total_pages         = ceil( $total_notifications / $per_page );
	$offset              = ( $page - 1 ) * $per_page;
	$paged_notifications = array_slice( $notifications, $offset, $per_page );

	echo '<table><tbody>';

	foreach ( $paged_notifications as $n ) {
		echo '<tr class="lp-announcement">';
		echo '<td>';
		echo '<strong>' . esc_html( $n->title ) . '</strong><br>';
		echo '<span>' . date( 'F j, Y g:i A', strtotime( $n->created_at ) ) . '</span>';
		echo '</td>';
		echo '</tr>';
	}

	echo '</tbody></table>';

    // AJAX Pagination buttons
	if ( $total_pages > 1 ) {
		echo '<div class="lp-ajax-pagination custom-pagination">';
		for ( $i = 1; $i <= $total_pages; $i++ ) {
			$active_class = ( $i == $page ) ? 'current' : '';
			echo '<button class="lp-page-btn pagination-link ' . $active_class . '" data-page="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</button>';
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


function lp_get_user_notifications( $user_id ) {
	global $wpdb;
	$table = $wpdb->prefix . 'learnpress_notifications';

	return $wpdb->get_results(
		$wpdb->prepare("SELECT notification_id, title, created_at, level, status FROM $table WHERE user_id = %d ORDER BY created_at DESC", $user_id)
	);
}


add_action( 'save_post_lp_course', 'lp_notify_on_course_update', 10, 3 );
function lp_notify_on_course_update( $post_ID, $post, $update ) {
	if ( ! $update ) return;

	$course_id    = $post_ID;
	$author_id    = $post->post_author;
	$course_title = get_the_title( $course_id );
	$students     = learn_press_get_users_enrolled_in_course( $course_id );

	if ( !empty( $students ) ) {
		foreach ( $students as $student_id ) {
			lp_add_notification( $student_id, 'Course Updates save: "' . $course_title . '"' );
		}
	}
	if ( $author_id ) {
		lp_add_notification( $author_id, '-Course Updates save: "' . $course_title . '"' );
	}
}
add_action('save_post_lp_course', 'lp_notify_on_schedule_course_created', 10, 3);
function lp_notify_on_schedule_course_created($post_ID, $post, $update) {
    // Only run if post is newly created AND status is 'future' (scheduled)
    if ( $post->post_status !== 'future' || $update ) return;
    $course_id    = $post_ID;
    $course_title  = get_the_title( $course_id );
    $course_author = $post->post_author;

    // Notify the tutor only
    if ( $course_author ) {
        lp_add_notification( $course_author, '🆕 Your course "' . $course_title . '" has been scheduled.' );
    }
}

function learn_press_get_users_enrolled_in_course( $course_id ) {
	global $wpdb;
	$results = $wpdb->get_col($wpdb->prepare("
		SELECT user_id 
		FROM {$wpdb->prefix}learnpress_user_items 
		WHERE item_id = %d AND item_type = 'lp_course' AND status = 'enrolled'
		", $course_id));
	return $results;
}

add_action( 'learn-press/user-enroll-course', 'lp_notify_course_enroll', 10, 2 );

function lp_notify_course_enroll( $user_id, $course_id ) {
	$title    = get_the_title( $course_id );
	$tutor_id = get_post_field( 'post_author', $course_id );

	lp_add_notification( $user_id, 'Successfully enrolled for a course: "' . $title . '"' );

    // Tutor notification
	if ( $tutor_id && $tutor_id != $user_id ) {
		lp_add_notification( $tutor_id, 'New student enrolled in your course: "' . $title . '"' );
	}
}

add_action( 'learn_press_after_submit_assignment', 'notify_tutor_on_assignment_submission', 10, 2 );

function notify_tutor_on_assignment_submission($assignment_id, $user_id) {
    // Get course ID
    error_log("Assignment {$assignment_id} submitted by user {$user_id}");

    $course_id = get_post_meta($assignment_id, '_lp_assignment_course', true);
    if (!$course_id) {
        return;
    }

    // Get tutor (course author)
    $tutor_id = get_post_field('post_author', $course_id);
    if (!$tutor_id || $tutor_id == $user_id) {
        return;
    }

    // Notification message
    $student = get_userdata($user_id);
    $student_name = $student ? $student->display_name : 'A student';
    $course_title = get_the_title($course_id);
    $assignment_title = get_the_title($assignment_id);

    $message = "📥 <strong>{$student_name}</strong> has submitted the assignment <strong>\"{$assignment_title}\"</strong> in the course <strong>\"{$course_title}\"</strong>.";

    // Send notification
    if (function_exists('lp_add_notification')) {
        lp_add_notification($tutor_id, $message);
    }
}

add_action( 'save_post_lp_course', 'lp_notify_on_course_schedule_update', 10, 3 );
function lp_notify_on_course_schedule_update( $post_ID, $post, $update ) {
	if ( ! $update ) return; 

	$course_id = $post_ID;
	$author_id = $post->post_author;
	$course_title = get_the_title( $course_id );

	// Optional: Check if a specific field like 'schedule' has changed
	$schedule_updated = isset($_POST['acf']) && !empty($_POST['acf']['field_123456789abc']); // Replace with real ACF field key
	if ( !$schedule_updated ) return; // Exit if no schedule field changed

	// Notify Tutor (course author)
	if ( $author_id ) {
		lp_add_notification( $author_id, 'You updated the schedule for course: "' . $course_title . '"' );
	}

	// Get enrolled students
	$students = learn_press_get_users_enrolled_in_course( $course_id );
	if ( !empty( $students ) ) {
		foreach ( $students as $student_id ) {
			lp_add_notification( $student_id, '🗓 Schedule updated for course: "' . $course_title . '". Check the course page for details.' );
		}
	}
}
