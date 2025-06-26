<?php
/**
 * Template for displaying own courses in courses tab of user profile page.
 * Edit by Nhamdv
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.12
 */

use LearnPress\Helpers\Template;
use LearnPress\Models\CourseModel;
use LearnPress\TemplateHooks\Course\ListCoursesTemplate;

defined( 'ABSPATH' ) || exit();

$search_query = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';

if ( ! isset( $user ) || ! isset( $course_ids ) || ! isset( $current_page ) || ! isset( $num_pages ) ) {
	return;
}

// Filter courses by title if search query exists
if ( ! empty( $search_query ) && ! empty( $course_ids ) ) {
	$filtered_ids = [];

	foreach ( $course_ids as $course_id ) {
		$title = get_the_title( $course_id );
		if ( stripos( $title, $search_query ) !== false ) {
			$filtered_ids[] = $course_id;
		}
	}

	$course_ids = $filtered_ids;
}


$is_instructor = current_user_can( 'lp_teacher' );
?>

<?php if ( $current_page === 1 ) : ?>
	<div class="lp-archive-courses new-custom-archive-table-cls-adding-course">
		<?php if ( $is_instructor ) : ?>
            <div class="lp-assignment-header">
				<h3 class="lp-assignment-title">Assigned Courses List</h3>
				<div class="lp-assignment-search">
		            <form method="get" action="">
		                <input 
		                    type="text" 
		                    name="search" 
		                    placeholder="<?php esc_attr_e( 'Search by Course name', 'learnpress-assignments' ); ?>" 
		                    value="<?php echo esc_attr( $search_query ); ?>" 
		                />
		                <button type="submit"><?php esc_html_e( 'Search', 'learnpress-assignments' ); ?></button>
		            </form>
		        </div>
			</div>
			<div class="custom-table-wrapper">
			<table class="lp-course-table lp_profile_course_progress lp-list-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'No.', 'learnpress' ); ?></th>
						<th><?php esc_html_e( 'Course Name', 'learnpress' ); ?></th>
						<th><?php esc_html_e( 'Subject', 'learnpress' ); ?></th>
						<th><?php esc_html_e( 'Enrolled', 'learnpress' ); ?></th>
						<!-- <th><?php esc_html_e( 'Progress', 'learnpress' ); ?></th> -->
						<th><?php esc_html_e( 'Start Date', 'learnpress' ); ?></th>
						<th><?php esc_html_e( 'Duration', 'learnpress' ); ?></th>
						<!-- <th><?php esc_html_e( 'Student List', 'learnpress' ); ?></th> -->
					</tr>
				</thead>
				<tbody>
		<?php else : ?>
			<ul <?php lp_item_course_class( array( 'profile-courses-list' ) ); ?> data-layout="grid" data-size="3">
		<?php endif; ?>
<?php endif; ?>

<?php
$template_is_override = Template::check_template_is_override( 'content-course.php' );
$listCoursesTemplate  = ListCoursesTemplate::instance();
if ( $template_is_override ) {
	global $post;
}

$list_count = 1;
foreach ( $course_ids as $id ) {
	$course = CourseModel::find( $id, true );
	if ( ! $course ) {
		continue;
	}

	if ( $template_is_override ) {
		$post = get_post( $id );
		setup_postdata( $post );
		// Pass list count to the template
		set_query_var( 'course_index', $list_count );

		learn_press_get_template( 'content-course.php' );
	} else {
		echo $listCoursesTemplate::render_course( $course );
	}
	$list_count++;
}

if ( $template_is_override ) {
	wp_reset_postdata();
}
?>

<?php if ( $current_page === 1 ) : ?>
		<?php if ( $is_instructor ) : ?>
				</tbody>
			</table>
			</div>
		<?php else : ?>
			</ul>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if ( $num_pages > 1 && $current_page < $num_pages && $current_page === 1 ) : ?>
	<div class="lp_profile_course_progress__nav">
		<button class="lp-button" data-paged="<?php echo absint( $current_page + 1 ); ?>"
				data-number="<?php echo absint( $num_pages ); ?>">
			<?php esc_html_e( 'View more', 'learnpress' ); ?>
		</button>
	</div>
<?php endif; ?>

