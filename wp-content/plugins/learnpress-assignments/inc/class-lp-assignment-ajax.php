<?php
/**
 * Class LP_Assignment_AJAX.
 *
 * @author  ThimPress
 * @version 1.0.0
 * @since 4.1.2
 */

use LearnPress\Models\CourseModel;
use LearnPress\Models\UserItems\UserCourseModel;
use LearnPress\Models\UserModel;
use LearnPressAssignment\Models\AssignmentPostModel;
use LearnPressAssignment\Models\UserAssignmentModel;

defined( 'ABSPATH' ) || exit();

/**
 * Class LP_Assignment
 */
class LP_Assignment_AJAX {
	/**
	 * Constructor gets the post object and sets the ID for the loaded course.
	 *
	 * @param mixed $the_assignment
	 * @param mixed $args
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_ajax' ) );
	}

	/**
	 * Register ajax
	 *
	 * @see start_assignment
	 * @see retake_assignment
	 * @see process_assignment
	 * @see lpae_evaluate
	 */
	public function register_ajax() {
		$actions = array(
			'start-assignment'    => 'start_assignment',
			'retake-assignment'   => 'retake_assignment',
			'controls-assignment' => 'process_assignment',
			'lpae-evaluate'       => 'lpae_evaluate',
		);

		foreach ( $actions as $action => $function ) {
			LP_Request::register_ajax( $action, array( __CLASS__, $function ) );
			//LP_Request::register( "lp-{$action}", array( __CLASS__, $function ) );
		}
	}

	/**
	 * Start assignment
	 *
	 * @editor tungnx
	 * @throws Exception Error start assigment.
	 * @since 4.0.0
	 */
	public static function start_assignment() {
		$response      = new LP_REST_Response();
		$assignment_id = LP_Request::get_param( 'assignment-id', 'int', 0, 'post' );
		$course_id     = LP_Request::get_param( 'course-id', 'int', 0, 'post' );

		try {
			$assignment = AssignmentPostModel::find( $assignment_id, true );
			if ( empty( $assignment ) ) {
				throw new Exception( esc_html__( 'Assignment is invalid!', 'learnpress-assignment' ) );
			}

			$courseModel = CourseModel::find( $course_id, true );
			if ( ! $courseModel ) {
				throw new Exception( esc_html__( 'Course is invalid!', 'learnpress-assignment' ) );
			}

			$userModel = UserModel::find( get_current_user_id(), true );
			if ( ! $userModel ) {
				throw new Exception( esc_html__( 'User is invalid!', 'learnpress-assignment' ) );
			}

			$result = LP_Addon_Assignment::user_start_assignment( $userModel, $courseModel, $assignment );
			if ( is_wp_error( $result ) ) {
				throw new Exception( $result->get_error_message() );
			}
		} catch ( Throwable $e ) {
			$response->message = $e->getMessage();
			//learn_press_set_message( $message );
		}

		wp_safe_redirect( LP_Helper::getUrlCurrent() );
		die;

		return $response;
	}

	/**
	 * Retake assignment
	 *
	 * @editor tungnx
	 * @modify 4.0.1
	 * @version 4.0.1
	 */
	public static function retake_assignment() {
		$response      = new LP_REST_Response();
		$assignment_id = LP_Request::get_param( 'assignment-id', 'int', 0, 'post' );
		$course_id     = LP_Request::get_param( 'course-id', 'int', 0, 'post' );

		try {
			$assignment = AssignmentPostModel::find( $assignment_id, true );
			if ( empty( $assignment ) ) {
				throw new Exception( esc_html__( 'Assignment is invalid!', 'learnpress-assignment' ) );
			}

			$courseModel = CourseModel::find( $course_id, true );
			if ( ! $courseModel ) {
				throw new Exception( esc_html__( 'Course is invalid!', 'learnpress-assignment' ) );
			}

			$userModel = UserModel::find( get_current_user_id(), true );
			if ( ! $userModel ) {
				throw new Exception( esc_html__( 'User is invalid!', 'learnpress-assignment' ) );
			}

			$userAssignmentModel = UserAssignmentModel::find( $userModel->get_id(), $courseModel->get_id(), $assignment->get_id() );
			if ( ! $userAssignmentModel ) {
				throw new Exception( esc_html__( 'User Assignment is invalid!', 'learnpress-assignment' ) );
			}

			$userAssignmentModel->handle_retake();

			$response->status  = 'success';
			$response->message = esc_html__( 'Assignment has been retaken!', 'learnpress-assignment' );
		} catch ( Throwable $e ) {
			$response->message = $e->getMessage();
		}

		learn_press_set_message(
			[
				'status'  => $response->status,
				'content' => $response->message,
			]
		);

		wp_safe_redirect( LP_Helper::getUrlCurrent() );
		die;

		return $response;
	}

	/**
	 * Send or save answer of user
	 *
	 * @editor tungnx
	 * @modify 4.0.1
	 * @version 4.0.1
	 */
	public static function process_assignment() {
		$response       = new LP_REST_Response();
		$action         = strtolower( LP_Request::get_param( 'action', '', 'key', 'post' ) );
		$user_note      = LP_Request::get_param( 'assignment-editor-frontend', '', 'html', 'post' );
		$assignment_id  = LP_Request::get_param( 'assignment-id', 0, 'int', 'post' );
		$course_id      = LP_Request::get_param( 'course-id', 0, 'int', 'post' );
		$uploaded_files = $_FILES['_lp_upload_file'] ?? '';

		try {
			$userModel = UserModel::find( get_current_user_id(), true );
			if ( ! $userModel ) {
				throw new Exception( esc_html__( 'User is invalid!', 'learnpress-assignment' ) );
			}

			$assignmentModel = AssignmentPostModel::find( $assignment_id, true );
			if ( empty( $assignmentModel ) ) {
				throw new Exception( esc_html__( 'Assignment is invalid!', 'learnpress-assignment' ) );
			}

			$courseModel = CourseModel::find( $course_id, true );
			if ( ! $courseModel ) {
				throw new Exception( esc_html__( 'Course is invalid!', 'learnpress-assignment' ) );
			}

			$userCourseModel = UserCourseModel::find( $userModel->get_id(), $courseModel->get_id(), true );
			if ( ! $userCourseModel || ! $userCourseModel->has_enrolled() ) {
				throw new Exception( __( 'User must enroll course first', 'learnpress-assignments' ) );
			}

			$userAssignmentModel = UserAssignmentModel::find( $userModel->get_id(), $courseModel->get_id(), $assignmentModel->get_id(), true );
			if ( ! $userAssignmentModel || ! in_array(
				$userAssignmentModel->get_status(),
				[
					LP_ITEM_STARTED,
					$userAssignmentModel::STATUS_DOING,
				]
			) ) {
				throw new Exception( __( 'User must start assignment', 'learnpress-assignments' ) );
			}

			// Save answer of user
			$userAssignmentModel->set_meta_value_for_key( $userAssignmentModel::META_KEY_ANSWER_NOTE, $user_note, true );
			// Save files of user upload.
			$userAssignmentModel->upload_files_of_student( $uploaded_files );

			if ( $action === 'save' ) {
				$userAssignmentModel->status = UserAssignmentModel::STATUS_DOING;
				$userAssignmentModel->save();

				$response->message = esc_html__( 'Your answer has been saved!', 'learnpress-assignments' );
			} elseif ( $action === 'send' ) {
				$userAssignmentModel->status = LP_ITEM_COMPLETED;
				$duration                    = $assignmentModel->get_duration();
				$remaining_time              = $userAssignmentModel->get_time_remaining();
				// For case unlimited time or time not expired.
				if ( (int) $duration === 0 || $remaining_time > 0 ) {
					$userAssignmentModel->end_time = gmdate( LP_Datetime::$format, time() );
				} else {
					// Set end time max duration.
					$start_time                    = $userAssignmentModel->get_start_time();
					$end_time_max_stamp            = strtotime( '+' . $duration, strtotime( $start_time ) );
					$userAssignmentModel->end_time = gmdate( LP_Datetime::$format, $end_time_max_stamp );
				}

				$userAssignmentModel->save();

				do_action( 'learn-press/assignment/student-submitted', $userModel->get_id(), $assignmentModel->get_id() );

				$response->message = esc_html__(
					'Your assignment has been submitted successfully! Please wait for your instructor to review and mark it.',
					'learnpress-assignments'
				);
			}

			$response->status = 'success';
		} catch ( Throwable $e ) {
			$response->message = $e->getMessage();
		}

		learn_press_set_message(
			[
				'status'  => $response->status,
				'content' => $response->message,
			]
		);

		wp_safe_redirect( LP_Helper::getUrlCurrent() );
		die;

		return $response;
	}

	/**
	 * Evaluate result
	 *
	 * @editor tungnx
	 * @modify 4.0.1
	 * @version 4.0.1
	 */
	public static function lpae_evaluate() {
		$page          = LP_Request::get_param( 'evaluate-page' );
		$assignment_id = LP_Request::get_param( 'assignment_id', 0, 'int' );
		$user_id       = LP_Request::get_param( 'user_id', 0, 'int' );
		$evaluate_page = get_page_link( get_option( 'assignment_evaluate_page_id' ) );
		if ( ! ( $evaluate_page === $page ) || ! $assignment_id || ! $user_id || 'post' !== strtolower( $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		$action       = LP_Request::get_param( 'action' );
		$user_item_id = LP_Request::get_param( 'user_item_id', 0, 'int' );
		$assignment   = LP_Assignment::get_assignment( $assignment_id );

		if ( ! $action || ! $user_item_id ) {
			return;
		}

		$mark = LP_Request::get_param( '_lp_evaluate_assignment_mark', 0 );

		if ( $action != 're-evaluate' ) {
			learn_press_update_user_item_meta( $user_item_id, '_lp_assignment_mark', $mark );

			learn_press_update_user_item_meta(
				$user_item_id,
				'_lp_assignment_instructor_note',
				LP_Request::get( '_lp_evaluate_assignment_instructor_note' )
			);

			$document = isset( $_POST['_lp_evaluate_assignment_document'] ) ? wp_unslash( array_filter( explode( ',', $_POST['_lp_evaluate_assignment_document'] ) ) ) : array();

			learn_press_update_user_item_meta(
				$user_item_id,
				'_lp_assignment_evaluate_upload',
				$document
			);

			learn_press_update_user_item_meta(
				$user_item_id,
				'_lp_assignment_evaluate_author',
				learn_press_get_current_user()->get_id()
			);
		}

		$course = learn_press_get_item_courses( $assignment_id );
		//$lp_course = learn_press_get_course( $course[0]->ID );
		//$user      = learn_press_get_user( $user_id );
		//$course_data = $user->get_course_data( $lp_course->get_id() );

		$user_curd = new LP_User_CURD();

		switch ( $action ) {
			case 'evaluate':
				learn_press_update_user_item_field( array( 'graduation' => ( $mark >= $assignment->get_data( 'passing_grade' ) ? 'passed' : 'failed' ) ), array( 'user_item_id' => $user_item_id ) );

				$user_curd->update_user_item_status( $user_item_id, 'evaluated' );
				//$course_data->calculate_course_results();
				do_action( 'learn-press/instructor-evaluated-assignment', $assignment_id, $user_id );
				break;
			case 're-evaluate':
				$user_curd->update_user_item_status( $user_item_id, 'completed' );
				do_action( 'learn-press/instructor-re-evaluated-assignment', $assignment_id, $user_id );
				break;
			default:
				break;
		}

		do_action( 'learn-press/save-evaluate-form', $action );
	}
}
