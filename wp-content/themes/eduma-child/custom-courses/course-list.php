<?php
/**
 * Template for displaying own courses in courses tab of user profile page.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.13
 */

defined( 'ABSPATH' ) || exit();
use LearnPress\Models\CourseModel;
use LearnPress\Models\UserItems\UserCourseModel;
use LearnPress\TemplateHooks\Course\SingleCourseTemplate;
use LearnPress\TemplateHooks\UserItem\UserCourseTemplate;


if ( ! isset( $user ) || ! isset( $course_ids ) || ! isset( $current_page ) || ! isset( $num_pages ) ) {
    return;
}

$userCourseTemplate   = UserCourseTemplate::instance();
$singleCourseTemplate = SingleCourseTemplate::instance();

// Get filters
$search_query   = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
echo $search_query;
$filters = get_lp_filter_params();
$search_query             = isset($filters['search']) ? $filters['search'] : '';
$selected_progress_ranges = isset($filters['progress_range']) ? $filters['progress_range'] : [];
$selected_grades          = isset($filters['grade']) ? $filters['grade'] : [];
$selected_subjects        = isset($filters['subject']) ? $filters['subject'] : [];

// var_dump($search_query);
// echo "</br>";
// var_dump($selected_progress_ranges);
// echo "</br>";
// var_dump($selected_grades);
// echo "</br>";
// var_dump($selected_subjects );

$current_user = wp_get_current_user();
$current_user_roles = (array) $current_user->roles;
$is_student = in_array( 'subscriber', $current_user_roles );
$is_tutor   = in_array( 'lp_teacher', $current_user_roles );

?>

<style>
/* Modal Styles */
.filter-modal {
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.8);
    align-items: center;
    justify-content: center;
}

.filter-modal-content {
    background-color: #fff;
    margin: auto;
    padding: 20px;
    border-radius: 8px;
    width: 300px;
    position: relative;
}

/* Tab Styles */
.filter-tabs {
    display: flex;
    margin-bottom: 10px;
}

.filter-tabs .tablinks {
    flex: 1;
    padding: 10px;
    cursor: pointer;
    background-color: #f1f1f1;
    border: none;
    outline: none;
}

.filter-tabs .tablinks.active {
    background-color: #ccc;
}

.tabcontent {
    display: none;
}
</style>

<div class="lp-assignment-header">
    <?php if($is_student){ ?>
        <h3 class="lp-assignment-title"><?php esc_html_e( 'Enrolled Courses List', 'learnpress-assignments' ); ?></h3>
    <?php }else{ ?>
        <h3 class="lp-assignment-title"><?php esc_html_e( 'Assigned Courses List', 'learnpress-assignments' ); ?></h3>
    <?php } ?>
    <button id="filter-button" class="filter-button"><?php esc_html_e( 'Filter', 'learnpress' ); ?></button>
    <div class="lp-assignment-search">
        <form method="get" action="">
            <input type="text" class="course-search-input" id="course-search-input" name="search" placeholder="<?php esc_attr_e( 'Search by Course name', 'learnpress-assignments' ); ?>" value="" />
            <button class="search_course"><?php esc_html_e( 'Search', 'learnpress-assignments' ); ?></button>
        </form>
    </div>
</div>

<!-- Filter Modal -->
<div id="filter-modal" class="filter-modal" style="display:none;">
    <div class="filter-modal-content">
        <div class="courder-list-cls-adding-here">
            <h3><?php esc_html_e('Filter Courses', 'learnpress'); ?></h3>
            <span class="filter-close">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.9961 1.00007L0.99617 19M0.996094 1L18.996 18.9999" stroke="#222222" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </span>
        </div>
        <div class="flexing-cls-adding">

            <div class="filter-tabs">
                <button class="tablinks active" data-tab="Progress"><?php esc_html_e('Progress', 'learnpress'); ?></button>
                <?php if ( $is_student ) : ?>
                    <button class="tablinks" data-tab="Grade"><?php esc_html_e('Grade', 'learnpress'); ?></button>
                <?php endif; ?>
                <?php if ( $is_tutor ) : ?>
                    <button class="tablinks" data-tab="Subject"><?php esc_html_e('Subject', 'learnpress'); ?></button>
                <?php endif; ?>
            </div>
            <div class="right-side-cls-adding">
                <form method="get" action="">
                    <div class="new-progress-tab-cls-adding-here">
                        <div id="Progress" class="tabcontent Progress" style="display:block;">
                            <?php for ( $i = 0; $i < 100; $i += 10 ) :
                                $range = $i . '-' . ( $i + 10 );
                                ?>
                                <input type="checkbox" name="progress_range[]" value="<?php echo esc_attr( $range ); ?>" <?php checked( in_array( $range, $selected_progress_ranges ) ); ?> /> <?php echo esc_html( $range ); ?>%<br>
                            <?php endfor; ?>
                        </div>

                        <?php if ( $is_student ) : ?>
                            <div id="Grade" class="tabcontent Grade">
                                <?php
                                $grades = array('A', 'B', 'C', 'D', 'Fail');
                                foreach ( $grades as $grade ) :
                                    ?>
                                    <input type="checkbox" name="grade[]" value="<?php echo esc_attr( $grade ); ?>" <?php checked( in_array( $grade, $selected_grades ) ); ?> /> <?php echo esc_html( $grade ); ?><br>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $is_tutor ) : ?>
                            <div id="Subject" class="tabcontent Subject">
                                <?php
                                $subjects = get_terms( array(
                                    'taxonomy'   => 'course_category',
                                    'hide_empty' => false,
                                ) );
                                if ( ! empty( $subjects ) && ! is_wp_error( $subjects ) ) :
                                    foreach ( $subjects as $subject ) :
                                        ?>
                                        <input type="checkbox" name="subject[]" value="<?php echo esc_attr( $subject->slug ); ?>" <?php checked( in_array( $subject->slug, $selected_subjects ) ); ?> />
                                        <?php echo esc_html( $subject->name ); ?><br>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
        <div class="courser-list-btn-cls-adding-here">
            <button type="submit" class="type-submit-cls-adding-here"><?php esc_html_e('Apply Filter', 'learnpress'); ?></button>
            <button type="button" onclick="window.location.href=window.location.pathname" class="type-reset-cls-adding-here"><?php esc_html_e('Reset', 'learnpress'); ?></button>
        </div>
    </div>
</div>

<div class="no-record-message" style="display:none;">
    <table>
        <tbody>
            <tr class="no-record-img">
                <td colspan="" style="text-align:center">
                    <img src="<?php echo get_home_url(); ?>/wp-content/uploads/2025/04/Group-59581.webp" class="avatar avatar-32 photo" height="404" width="505" decoding="async">
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php if ( $current_page === 1 ) : ?>
    <div class="custom-table-wrapper">
        <table class="lp_profile_course_progress lp-list-table">
            <thead>
                <?php if ( $is_student ) : ?>
                    <tr>
                        <th><?php esc_html_e( 'No.', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Course Name', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Progress', 'learnpress' ); ?></th>
                        <!-- <th><?php // esc_html_e( 'Result', 'learnpress' ); ?></th> -->
                        <th><?php esc_html_e( 'Enroll Date', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Duration', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Assignments', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Drop', 'learnpress' ); ?></th>
                    </tr>
                <?php elseif ( $is_tutor ) : ?>
                    <tr>
                        <th><?php esc_html_e( 'No.', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Course Name', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Subject', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Enrolled', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Progress', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Enroll Date', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Finish Date', 'learnpress' ); ?></th>
                        <th><?php esc_html_e( 'Student List', 'learnpress' ); ?></th>
                    </tr>
                <?php endif; ?>


            </thead>
        <?php endif; ?>

        <tbody>
            <?php
            $index = 1;
            foreach ( $course_ids as $id ) {
                $courseModel = CourseModel::find( $id, true );
                if ( ! $courseModel ) continue;

                $userCourseModel = UserCourseModel::find( $user->get_id(), $id, true );
                if ( ! $userCourseModel ) continue;

                $course_result = $userCourseModel->calculate_course_results();
                $progress      = isset( $course_result['result'] ) ? floatval( $course_result['result'] ) : 0;
                $grade         = $id;

                global $wpdb;
                $wpdb->replace(
                    $wpdb->prefix . 'user_course_progress',
                    [
                        'user_id'   => $user->get_id(),
                        'course_id' => $id,
                        'progress'  => $progress,
                    ],
                    [ '%d', '%d', '%f' ]
                );

                // Search filter
                if ( $search_query && stripos( $courseModel->get_title(), $search_query ) === false ) {
                    continue;
                }

                // Progress filter
                if ( ! empty( $selected_progress_ranges ) ) {
                    $in_range = false;
                    foreach ( $selected_progress_ranges as $range ) {
                        list( $min, $max ) = explode( '-', $range );
                        if ( $progress >= (int)$min && $progress < (int)$max ) {
                            $in_range = true;
                            break;
                        }
                    }
                    if ( ! $in_range ) continue;
                }

                if ( ! empty( $selected_grades ) && ! in_array( $grade, $selected_grades ) ) {
                    continue;
                }

                if ( ! empty( $selected_subjects ) ) {
                    $course_terms = wp_get_post_terms( $id, 'course_category', array( 'fields' => 'slugs' ) );
                    $has_subject = array_intersect( $selected_subjects, $course_terms );
                    if ( empty( $has_subject ) ) continue;
                }
                $course_terms = wp_get_post_terms( $id, 'course_category', array( 'fields' => 'names' ) );
                $subject = ! empty( $course_terms ) ? implode( ', ', $course_terms ) : __( 'NA', 'learnpress' );
                $course_terms_1 = wp_get_post_terms( $id, 'course_category', array( 'fields' => 'slugs' ) );
                $subject_1 = ! empty( $course_terms_1 ) ? implode( ', ', $course_terms_1 ) : __( 'NA', 'learnpress' );
                $enrolled_count = get_enrolled_students_count( $id );
                // var_dump($enrolled_count);
                $row_class = $index > 10 ? 'hidden-row' : '';
                $row_style = $index > 10 ? 'display:none;' : '';
                $enrollment_date = '';
                global $wpdb;

                $enroll_time = $wpdb->get_var( $wpdb->prepare(
                    "SELECT start_time FROM {$wpdb->prefix}learnpress_user_items 
                    WHERE user_id = %d AND item_id = %d AND item_type = %s LIMIT 1",
                    $user->get_id(),
                    $id,
                    'lp_course'
                ) );

                if ( $enroll_time ) {
                    $enrollment_date = date_i18n( 'j F, Y', strtotime( $enroll_time ) );
                }
                // Get course duration
                $course         = learn_press_get_course( $id );
                $duration_text = __( 'NA', 'learnpress' );

                if ( $course && method_exists( $course, 'get_duration' ) ) {
                    $duration_text = $course->get_duration( 'display' ); // e.g. "5 weeks"
                }


                ?>
                <?php if ( $is_student ) : ?>
                    <tr class="course-row <?php echo esc_attr($row_class); ?>" style="<?php echo esc_attr($row_style); ?>" data-progress="<?php echo esc_attr( $progress ); ?>" data-grade="<?php echo esc_html( $grade ?: '' ); ?>" >
                        <td><?php echo esc_html( $index ); ?></td>
                        <td>
                            <a href="<?php echo esc_url( $courseModel->get_permalink() ); ?>" title="<?php echo esc_attr( $courseModel->get_title() ); ?>" class="course-title-row">
                                <?php echo wp_kses_post( $singleCourseTemplate->html_title( $courseModel ) ); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html( $progress ); ?>%</td>
                        <!-- <td><?php //echo esc_html( $grade ?: 'NA' ); ?></td> -->
                        <td <?php echo $enrollment_date; ?>><?php echo $enrollment_date; ?></td>
                        <td><?php echo esc_html( $duration_text ); ?></td>
                        <!-- CONTINUE BUTTON URL added here -->
                        <td>
                            <?php
                            $continue_url = '';
                            $item_continue = $userCourseModel->get_item_continue();
                            if ( empty( $item_continue ) ) {
                                $continue_url = $courseModel->get_permalink();
                            } else {
                                $continue_url = $courseModel->get_item_link( $item_continue->ID );
                            }
                            ?>
                            <a href="<?php echo esc_url( $continue_url ); ?>" class="lp-button course-btn-continue od-new-course-listed-type" target="_blank">
                                <?php esc_html_e( 'Continue', 'learnpress' ); ?>
                            </a>
                        </td>
                        <td>
                            <form method="post" onsubmit="return confirm('Are you sure you want to drop this course?');">
                                <input type="hidden" name="lp_drop_course_id" value="<?php echo esc_attr( $courseModel->get_id() ); ?>">
                                <button type="submit" class="lp-button course-btn-drop" style="background: #dc3545; color: #fff; border: none; padding: 6px 12px; cursor: pointer;">
                                    <?php esc_html_e( 'Drop Course', 'learnpress' ); ?>
                                </button>
                            </form>
                        </td>
                        <!-- End Continue Button -->
                    </tr>


                <?php elseif ( $is_tutor ) : 

                    ?>
                    <tr class="course-row" data-progress="<?php echo esc_attr( $progress ); ?>" data-subject="<?php echo $subject_1; ?>">
                        <td><?php echo esc_html( $index ); ?></td>
                        <td>
                            <a href="<?php echo esc_url( $courseModel->get_permalink() ); ?>" title="<?php echo esc_attr( $courseModel->get_title() ); ?>">
                                <?php echo wp_kses_post( $singleCourseTemplate->html_title( $courseModel ) ); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html( $subject ); ?></td>

                        <td><?php echo esc_html( $enrolled_count ); ?></td>
                        <td><?php echo esc_html( $progress ); ?>%</td>
                        <td><?php echo $userCourseTemplate->html_expire_date_time( $userCourseModel ); ?></td>
                        <td><?php echo $duration_text; ?></td>
                    </tr>
                <?php endif; ?>
                <?php
                $index++;
            }
            ?>
            <tr id="no-results-message" style="display: none;">
              <td colspan="7" style="text-align:center; color:#dc3545; font-weight:bold;">
                No results found.
              </td>
            </tr>
        </tbody>

        <?php // if ( $current_page === 1 ) : ?>
        </table>

    </div>
<?php //endif; ?>

<?php if ( $index > 10 ) : ?>
    <div class="lp_profile_course_progress__nav">
        <button id="view-more-courses" class="lp-button view-more-btn">
            <?php esc_html_e( 'View more', 'learnpress' ); ?>
        </button>
    </div>
<?php endif; ?>

<script>
    document.getElementById("filter-button").addEventListener("click", function () {
        document.getElementById("filter-modal").style.display = "block";
    });
    document.querySelector(".filter-close").addEventListener("click", function () {
        document.getElementById("filter-modal").style.display = "none";
    });
    document.querySelectorAll(".tablinks").forEach(button => {
        button.addEventListener("click", function () {
            document.querySelectorAll(".tabcontent").forEach(tab => tab.style.display = "none");
            document.querySelectorAll(".tablinks").forEach(btn => btn.classList.remove("active"));
            document.getElementById(this.getAttribute("data-tab")).style.display = "block";
            this.classList.add("active");
        });
    });
</script>

<script>
    jQuery(document).ready(function($) {
    // document('.type-submit-cls-adding-here').on('click', function(e) {
        alert('hey');
        $(document).on('click', '.type-submit-cls-adding-here', function(e) {
            alert('hey');
            e.preventDefault();
            let formData = $('#filter-modal form').serialize();
            formData += '&action=filter_courses';
            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: 'GET',
                data: formData,
                beforeSend: function() {
                    $('.lp_profile_course_progress tbody').html('<tr><td colspan="10">Loading...</td></tr>');
                },
                success: function(response) {
                    $('.lp_profile_course_progress tbody').html(response);
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('.course-row');
        const viewMoreBtn = document.querySelector('.view-more-btn');
        const rowsPerClick = 10;
        let currentIndex = 0;

        function showNextRows() {
            for (let i = currentIndex; i < currentIndex + rowsPerClick && i < rows.length; i++) {
                rows[i].style.display = 'table-row';
            }
            currentIndex += rowsPerClick;

            if (currentIndex >= rows.length) {
                viewMoreBtn.style.display = 'none';
            }
        }
        rows.forEach(row => {
            row.style.display = 'none';
        });
        showNextRows();
        viewMoreBtn.addEventListener('click', showNextRows);
    });
</script>
