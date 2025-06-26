<?php
/**
 * Template for displaying course content within the loop.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/content-course.php
 *
 * @author  ThimPress
 * @package LearnPress/Templates
 * @version 4.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();

$index = get_query_var( 'course_index', 0 ); 
$is_instructor = current_user_can( 'lp_teacher' );

?>

<?php if ( $is_instructor ) : 
	 ?>
	<tr id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>
		<td class="course-thumb">
			<?php echo esc_html( $index ); ?>
		</td>
		<td class="course-title-desc ">
			<?php
			do_action( 'learnpress_loop_item_title' );
			
			?>
		</td>
		<td>
			<?php
				$terms = get_the_terms( get_the_ID(), 'course_category' );
				if ( $terms && ! is_wp_error( $terms ) ) {
					echo '<div class="course-category">';
					foreach ( $terms as $term ) {
						echo '<span>' . esc_html( $term->name ) . '</span> ';
					}
					echo '</div>';
				}
			?>
		</td>
		<td class="course-thumb">
			<?php do_action( 'learnpress_loop_item_course_meta' ); ?>
		</td>
		<!-- <td class="course-meta">
			<?php echo do_shortcode( '[lp_course_progress]' );  ?>
		</td> -->
		<td class="course-instructor-info">
			<!-- Instructor content -->
			<?php if ( current_user_can( 'edit_posts' ) ) : ?>
				<?php
				echo do_shortcode( '[lp_course_start_title]' );
				//echo do_shortcode( '[lp_course_end_title]' );
				?>
			<?php endif; ?>
		</td>
		<td class="course-instructor-info">
			<!-- Instructor content -->
			<?php if ( current_user_can( 'edit_posts' ) ) : ?>
				<?php
				//echo do_shortcode( '[lp_course_start_title]' );
				echo do_shortcode( '[lp_course_end_title]' );
				?>
			<?php endif; ?>
		</td>
		<!-- <td class="course-instructor-info student-info">
			<?php if ( current_user_can( 'lp_teacher' ) ) : ?>
				<button class="button view-enrolled-students-btn" data-course-id="<?php echo esc_attr( get_the_ID() ); ?>">
					View Enrolled Students
				</button>
			<?php endif; ?>
		</td> -->
	</tr>
<?php else : ?>
	<li id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>
		<div class="course-item">
			<?php do_action( 'thim_courses_loop_item_thumb' ); ?>
			<div class="thim-course-content">
				<?php
				do_action( 'learnpress_loop_item_title' );
				do_action( 'learnpress_loop_item_desc' );
				do_action( 'learnpress_loop_item_course_meta' );
				?>
			</div>
		</div>
	</li>
<?php endif; ?>

