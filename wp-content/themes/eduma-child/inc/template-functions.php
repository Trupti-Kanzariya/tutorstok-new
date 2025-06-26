<?php

add_action('wp_head', 'myplugin_ajaxurl');
function myplugin_ajaxurl()
{
    echo '<script type="text/javascript">
    var ajaxurl = "' . admin_url('admin-ajax.php') . '";
    </script>';
}

$profile = LP_Profile::instance();
$user_id = get_current_user_id();
$image_url = $profile ? $profile->get_upload_profile_src() : '';

// 1. Show Avatar Upload on Basic Info Tab
add_action('learn-press/end-profile-basic-information-fields', function () {
    $user_id = get_current_user_id();
    $profile = learn_press_get_profile($user_id);
    $avatar_url = $profile ? $profile->get_upload_profile_src() : get_avatar_url($user_id);

    ?>
    <div class="lp-profile-avatar-upload" style="margin-top: 20px;">
        <label><strong>Upload Avatar:</strong></label><br>
        <img src="<?php echo esc_url($avatar_url); ?>" width="100" height="100"
        style="border-radius: 50%; margin-bottom: 10px;" />
        <input type="file" name="lp_profile_avatar" accept="image/*" />
        <input type="hidden" name="lp_avatar_upload_action" value="1" />
    </div>
    <?php
});

// $current_user = wp_get_current_user();
// $roles = $current_user->roles; // This is an array
// echo $roles[0];

// 2. Handle Avatar Upload
add_action('learn-press/user-profile-basic-information-saved', 'custom_after_lp_profile_save', 10, 1);

function custom_after_lp_profile_save($user_id)
{
    error_log('Profile basic information saved for user ID: ' . $user_id);

    // For example: update custom avatar image
    if (!empty($_FILES['custom_avatar']) && !empty($_FILES['custom_avatar']['tmp_name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $file = $_FILES['custom_avatar'];
        $upload = wp_handle_upload($file, ['test_form' => false]);

        if (!isset($upload['error']) && isset($upload['file'])) {
            $filetype = wp_check_filetype($upload['file']);
            $attachment = [
                'post_mime_type' => $filetype['type'],
                'post_title' => sanitize_file_name($file['name']),
                'post_content' => '',
                'post_status' => 'inherit'
            ];

            $attach_id = wp_insert_attachment($attachment, $upload['file']);
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);

            // Save attachment ID to user meta
            update_user_meta($user_id, 'custom_avatar_id', $attach_id);

            error_log("Avatar uploaded and set for user ID: $user_id");
        } else {
            error_log("Upload error: " . $upload['error']);
        }
    } else {
        error_log("No custom_avatar uploaded");
    }
}

function custom_lp_render_custom_section()
{

    $template_path = get_stylesheet_directory() . '/learnpress/profile/tabs/settings/dashboard.php';

    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<p>Template not found: ' . esc_html($template_path) . '</p>';
    }
}

add_action('learn-press/profile-tab-content-courses', 'add_statistics_box_to_my_courses');

add_action('init', 'handle_custom_profile_form_submission');
function handle_custom_profile_form_submission()
{
    if (
        isset($_POST['custom_profile_submit']) &&
        check_admin_referer('custom_profile_save_action', 'custom_profile_nonce')
    ) {
        $user_id = get_current_user_id();
        if (!$user_id)
            return;

        // Validate student birth date (not for teachers)
        if (!empty($_POST['birth_date'])) {
            $birth_date = sanitize_text_field($_POST['birth_date']);
            $birth_timestamp = strtotime($birth_date);
            $min_birth_timestamp = strtotime('-2 years');

            if ($birth_timestamp > $min_birth_timestamp) {
                learn_press_add_message(__('You must be at least 2 years old.', 'learnpress'), 'error');
                return; // Stop saving if validation fails
            }
        }

        // Update text fields
        $fields = [
            'first_name',
            'student_full_name',
            'parent_name',
            'last_name',
            'billing_phone',
            'phone',
            'birth_date_teacher',
            'dob',
            'birth_date',
            'grade',
            'address',
            'preferred_subjects',
            'learning_style',
            'preferred_hours',
            'subject_expertise',
            'qualification',
            'experiance',
            'availability',
            'description'
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_user_meta($user_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        // Update first and last name in user meta too
        if (isset($_POST['first_name'])) {
            wp_update_user(['ID' => $user_id, 'first_name' => sanitize_text_field($_POST['first_name'])]);
        }
         if (isset($_POST['phone'])) {
            wp_update_user(['ID' => $user_id, 'phone' => sanitize_text_field($_POST['phone'])]);
        }
        if (isset($_POST['student_full_name'])) {
            wp_update_user(['ID' => $user_id, 'student_full_name' => sanitize_text_field($_POST['student_full_name'])]);
        }
        if (isset($_POST['last_name'])) {
            wp_update_user(['ID' => $user_id, 'last_name' => sanitize_text_field($_POST['last_name'])]);
        }
        if (isset($_POST['dob'])) {
            wp_update_user(['ID' => $user_id, 'dob' => sanitize_text_field($_POST['dob'])]);
        }
         if (isset($_POST['parent_name'])) {
            wp_update_user(['ID' => $user_id, 'parent_name' => sanitize_text_field($_POST['parent_name'])]);
        }

        // Handle profile image upload
        if (!empty($_FILES['profile_image']['name'])) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $file = $_FILES['profile_image'];
            $upload = wp_handle_upload($file, ['test_form' => false]);

            if (!isset($upload['error']) && isset($upload['file'])) {
                // Optional: attach as media (not required by LearnPress but useful)
                $filename = $upload['file'];
                $attachment_id = wp_insert_attachment([
                    'post_mime_type' => $upload['type'],
                    'post_title' => sanitize_file_name($file['name']),
                    'post_content' => '',
                    'post_status' => 'inherit'
                ], $filename, 0);

                require_once ABSPATH . 'wp-admin/includes/image.php';
                $attach_data = wp_generate_attachment_metadata($attachment_id, $filename);
                wp_update_attachment_metadata($attachment_id, $attach_data);

                // Save only the relative path for LearnPress
                $upload_dir = wp_upload_dir();
                $relative_path = str_replace($upload_dir['basedir'], '', $upload['file']);
                update_user_meta($user_id, '_lp_profile_picture', $relative_path);

                // Clear LearnPress cached image path
                if (class_exists('LP_Profile')) {
                    $profile = LP_Profile::instance($user_id);
                    if (method_exists($profile, 'set_data')) {
                        $profile->set_data('uploaded_profile_src', '');
                    }
                }
            }
        }
        $user = wp_get_current_user();
        $username = $user->user_login;
        $profile_url = home_url("/lp-profile/{$username}/settings/profile/");
        wp_redirect($profile_url);
        exit;
    }
}

add_filter('learn-press/profile-tabs', 'remove_learnpress_avatar_tab', 20);

function remove_learnpress_avatar_tab($tabs)
{
    if (isset($tabs['settings']['sections']['avatar'])) {
        unset($tabs['settings']['sections']['avatar']);
    }
    if (isset($tabs['settings']['sections']['cover-image'])) {
        unset($tabs['settings']['sections']['cover-image']);
    }
    if (isset($tabs['settings']['sections']['basic-information'])) {
        unset($tabs['settings']['sections']['basic-information']);
    }
    return $tabs;
}

add_action('learn-press/user-profile/courses', 'custom_lp_statistics_box', 5);

function custom_lp_statistics_box()
{
    $user = learn_press_get_current_user();
    if (!$user) {
        return;
    }

    $enrolled = count($user->get_enrolled_courses());
    $completed = count($user->get_finished_courses());

    echo '<div class="lp-statistics-box" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; background-color: #f1f1f1;">';
    echo '<h3>📊 Your Learning Statistics</h3>';
    echo '<ul style="list-style: none; padding-left: 0;">';
    echo '<li><strong>Enrolled Courses:</strong> ' . esc_html($enrolled) . '</li>';
    echo '<li><strong>Completed Courses:</strong> ' . esc_html($completed) . '</li>';
    echo '</ul>';
    echo '</div>';
}


function get_lp_filter_params()
{
    $filters = [];
    if (isset($_GET['search'])) {
        $filters['search'] = sanitize_text_field($_GET['search']);
    }
    if (isset($_GET['progress_range']) && is_array($_GET['progress_range'])) {
        $filters['progress_range'] = array_map('sanitize_text_field', $_GET['progress_range']);
    }
    if (isset($_GET['grade']) && is_array($_GET['grade'])) {
        $filters['grade'] = array_map('sanitize_text_field', $_GET['grade']);
    }
    if (isset($_GET['subject']) && is_array($_GET['subject'])) {
        $filters['subject'] = array_map('sanitize_text_field', $_GET['subject']);
    }
    return $filters;
}

function get_post_title_shortcode_logged_in()
{
    if (is_user_logged_in()) {
        return get_the_title();
    }
}
add_shortcode('post_title', 'get_post_title_shortcode_logged_in');

add_action('wp_head', function () {
    if (is_user_logged_in()) {
        echo '<style>.couse-detail-banner11 { display: none !important; }</style>';
    }
});

function hide_tab_students_list_for_subscribers()
{
    $current_user = wp_get_current_user();
    $user_roles = (array) $current_user->roles;
    $is_allowed = (is_user_logged_in() && !in_array('subscriber', $user_roles)) ? 'true' : 'false';
    ?>
    <script>
        jQuery(document).ready(function ($) {
            var isAllowed = <?php echo wp_json_encode($is_allowed); ?>;
            if (isAllowed !== 'true') {
                $('#tab-students-list').hide(); // or .addClass('hide') if you're using custom CSS
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'hide_tab_students_list_for_subscribers', 100);

function show_student_progress_for_subscribers()
{
    $current_user = wp_get_current_user();
    $user_roles = (array) $current_user->roles;
    if (is_user_logged_in() && (in_array('subscriber', $user_roles) || in_array('lp_teacher', $user_roles))) {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                // $('.student_course_progress').show();
            });
        </script>
        <?php
    }
    if (is_user_logged_in() && in_array('subscriber', $user_roles)) {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                // $('.student_schedule_cta').show();
                $('#tab-instructor').hide();
                $('.student_course_progress').show();
            });
        </script>
        <?php
    }
    if (is_user_logged_in() && in_array('lp_teacher', $user_roles)) {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                $('.custom_tutor_card').hide();
                $('.elementor-widget-thim-ekits-minicart').hide();
                $('.cart-header').addClass('cart-hide');
                $('.custom_date_card').show();
                $('.teacher_schedule_cta').show();
                $('.course_custom_ratting').hide();
                $('.student_course_material_list').hide();
            });
        </script>
        <?php
    }
    if (is_user_logged_in()) {
        // echo "hello";
        $course_id = get_the_ID(); // or specific course ID
        $user = learn_press_get_current_user();
        if (!$user->has_purchased_course($course_id)) { ?>
            <script>
                jQuery(document).ready(function ($) {
                    $('.add_to_cart_course_teacher').show();
                });
            </script>
        <?php } ?>
        <script>
            jQuery(document).ready(function ($) {
                $('.testimonial-custom-course-detail').hide();
                $('.faq-custom-course-detail').hide();
            });
        </script>
        <?php
    } else { ?>
        <script>
            jQuery(document).ready(function ($) {
                $('.add_to_cart_course_teacher').show();
                $('.course_custom_ratting').show();
                $('#tab-instructor').hide();
                $('.student_course_progress').hide();
                $('.student_course_material_list').hide();
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'show_student_progress_for_subscribers', 101);

// function get_enrolled_students_count($course_id)
// {
//     if (!class_exists('LP_User_Items_DB') || !class_exists('LP_User_Items_Filter')) {
//         return 0;
//     }

//     $lp_user_items_db = LP_User_Items_DB::getInstance();
//     $filter = new LP_User_Items_Filter();

//     $filter->item_id = $course_id;
//     $filter->item_type = LP_COURSE_CPT;
//     $filter->where[] = $lp_user_items_db->wpdb->prepare(
//         'AND ui.status IN (%s, %s)',
//         LP_COURSE_ENROLLED,
//         LP_COURSE_FINISHED
//     );
//     $filter->where[] = 'AND ui.user_id != 0';

//     $students = $lp_user_items_db->get_user_items($filter);

//     if (empty($students) || !is_array($students))
//         return 0;

//     // Get unique user IDs only
//     $user_ids = array_unique(array_map(function ($item) {
//         return intval($item->user_id);
//     }, $students));

//     return count($user_ids);
// }

function get_enrolled_students_detailed_info($course_id)
{
    if (!function_exists('learn_press_get_user'))
        return [];

    // Ensure classes exist
    if (!class_exists('LP_User_Items_DB') || !class_exists('LP_User_Items_Filter')) {
        return [];
    }

    // Instance of the DB
    $lp_user_items_db = LP_User_Items_DB::getInstance();
    $filter = new LP_User_Items_Filter();

    // Filter to check course enrollment status
    $filter->item_id = $course_id;
    $filter->item_type = LP_COURSE_CPT;
    $filter->where[] = $lp_user_items_db->wpdb->prepare(
        'AND ui.status IN (%s, %s)',
        LP_COURSE_ENROLLED,
        LP_COURSE_FINISHED
    );
    $filter->where[] = 'AND ui.user_id != 0';

    // Get students enrolled in this course
    $students = $lp_user_items_db->get_user_items($filter);

    // If no students found, return empty array
    if (empty($students) || !is_array($students))
        return [];

    $results = [];

    // Loop through students
    foreach ($students as $student_item) {
        $user_id = intval($student_item->user_id);
        $user = get_userdata($user_id);
        // Skip if no user found
        if (!$user)
            continue;

        $lp_user = learn_press_get_user($user_id);
        $course_data = $lp_user ? $lp_user->get_course_data($course_id) : null;
        // Check performance and attendance
        $performance = $course_data ? $course_data->get_result('number') : 'N/A';
        $enroll_date = !empty($student_item->start_time) ? date_i18n('Y-m-d', strtotime($student_item->start_time)) : 'N/A';
        $attendance = $course_data ? count((array) $course_data->get_completed_items()) : 0;
        $notes = get_user_meta($user_id, 'lp_student_notes_' . $course_id, true);
        // Add results for each student
        $results[] = [
            'name' => esc_html($user->display_name),
            'performance' => is_numeric($performance) ? $performance . '%' : $performance,
            'attendance' => $attendance,
            'notes' => $notes ? esc_html($notes) : 'No notes',
            'enroll_date' => $enroll_date,
        ];
    }
    return $results;
}

function lp_students_list_shortcode($atts)
{

    $default_course_id = is_singular('lp_course') ? get_the_ID() : 0;

    $atts = shortcode_atts(
        ['course_id' => $default_course_id],
        $atts,
        'lp_students_list'
    );
    $course_id = intval($atts['course_id']);

    if (!$course_id)
        return '<p><strong>Invalid course ID.</strong></p>';

    $students = get_enrolled_students_detailed_info($course_id);

    $search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

    if (!empty($search_query)) {
        $students = array_filter($students, function ($student) use ($search_query) {
            return stripos($student['name'], $search_query) !== false;
        });
    }


    ob_start();
    ?>
    <div class="learn-press-profile-student_list">
        <div class="learn-press-student_list__content enrolled-student-cls-adding" data-tab="all" style="">
            <div class="lp-student_list-header">
                <h3 class="lp-student_list-title">Enrolled Students</h3>
                <div class="lp-student_list-search">
                    <form method="get" action="#tab-students-list">
                       <input type="text" name="search" placeholder="Search by Student name" value="<?php echo esc_attr($search_query); ?>">
                       <button type="submit">Search</button>
                   </form>
               </div>
           </div>
           <div class="custom-table-wrapper">

            <table class="lp-student-list" border="1" cellpadding="6" cellspacing="0"
            style="width:100%;border-collapse:collapse;">

            <thead style="background:#f4f4f4;">
                <tr>
                    <th>No.</th>
                    <th>Student Name</th>
                    <th>Performance</th>
                    <th>Attendance</th>
                    <th>Notes</th>
                    <th>Enroll Date</th>
                </tr>
            </thead>
            <?php if (!empty($students)) : ?>
                <tbody>
                    <?php

                    $i = 0;
                    foreach ($students as $student): ?>
                        <?php
                        $i++;
                            // echo "<pre>";
                            // var_dump($student);
                            // echo "</pre>";
                        $performance_raw = floatval(rtrim($student['performance']['result']));
                            // echo $performance_raw;
                        if ($performance_raw >= 90) {
                            $status = 'Excellent';
                            $class = 'performance-excellent';
                        } elseif ($performance_raw >= 75) {
                            $status = 'Good';
                            $class = 'performance-good';
                        } else {
                            $status = 'Average';
                            $class = 'performance-average';
                        }
                        ?>

                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $student['name']; ?></td>
                            <td><div class="<?php echo esc_attr($class).' badge-wrapper'; ?>"><?php echo esc_html($status); ?></div></td>
                            <td><?php // echo $student['attendance']; ?>54%</td>
                            <td><?php echo $student['notes']; ?></td>
                            <td> <?php 
                            $date_raw = $student['enroll_date'];
                            $date_obj = date_create($date_raw);
                            echo $date_obj ? date_format($date_obj, 'd-m-Y') : 'Invalid date';
                        ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; font-weight: bold; font-size: 1.2em;">
                        No students found for "<?php echo esc_html($search_query); ?>"
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>

    </table>

</div>
</div>

</div>
<?php
    // Return the output
return ob_get_clean();
}

// Register the shortcode [lp_students_list]
add_shortcode('lp_students_list', 'lp_students_list_shortcode');

function custom_learnpress_course_rewrites()
{
    add_rewrite_rule(
        '^(tutor|student)/courses/([^/]+)/?$',
        'index.php?post_type=lp_course&name=$matches[2]&course_user_type=$matches[1]',
        'top'
    );

    add_rewrite_rule(
        '^courses/([^/]+)/?$',
        'index.php?post_type=lp_course&name=$matches[1]',
        'top'
    );
}
add_action('init', 'custom_learnpress_course_rewrites');

function add_course_user_type_query_var($vars)
{
    $vars[] = 'course_user_type';
    return $vars;
}
add_filter('query_vars', 'add_course_user_type_query_var');

// function load_custom_course_template($template)
// {
//     if (is_singular('courses')) {
//         $current_url = $_SERVER['REQUEST_URI'];
//         if (strpos($current_url, '/tutor/courses/') !== false) {
//             $new_template = get_stylesheet_directory() . '/single-course-tutor.php';
//         } elseif (strpos($current_url, '/student/courses/') !== false) {
//             $new_template = get_stylesheet_directory() . '/single-course-student.php';
//         } else {
//             $new_template = get_stylesheet_directory() . '/single-course-default.php';
//         }
//         if (file_exists($new_template)) {
//             return $new_template;
//         }
//     }
//     return $template;
// }
// add_filter('template_include', 'load_custom_course_template');

function load_course_template_based_on_url()
{
    if (is_singular('lp_course')) {
        $user_type = get_query_var('course_user_type');
        if ($user_type === 'tutor') {
            echo do_shortcode('[thim_ekit id="24037"]');
        } elseif ($user_type === 'student') {
            echo do_shortcode('[thim_ekit id="24045"]');
        } else {
            echo do_shortcode('[thim_ekit id="24069"]');
        }
    }
}
add_shortcode('custom_course_template', 'load_course_template_based_on_url');

function shortcode_course_title_and_start_date($atts)
{
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(),
        'format' => get_option('date_format'),
    ), $atts);

    $post_id = $atts['post_id'];
    $date_formatted = get_the_date($atts['format'], $post_id);

    if ( $date_formatted ) {
        echo '<h4 class="lp-course-start-date">' . esc_html($date_formatted) . '</h4><span class="lp-course-start-title">Published Date</span>';
    }
}
add_shortcode('lp_course_start_title', 'shortcode_course_title_and_start_date');


function shortcode_course_title_and_end_date($atts)
{
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(),
    ), $atts);

    $post_id = $atts['post_id'];
    $duration = get_post_meta($post_id, '_lp_duration', true);
    $duration_time = get_post_meta($post_id, '_lp_duration_time', true); // Example: 'hour', 'day', etc.

    // Format duration text
    if (!empty($duration)) {
        $unit = !empty($duration_time) ? ucfirst($duration_time) : '';
        $duration_text = $duration . ' ' . $unit . (intval($duration) > 1 ? '' : '');
        echo '<h4 class="lp-course-start-date">' . esc_html($duration_text) . '</h4><span class="lp-course-start-title">Duration</span>';
    } else {
        echo '<h4 class="lp-course-duration">N/A</h4><span class="lp-course-start-title">Duration</span>';
    }
}
add_shortcode('lp_course_end_title', 'shortcode_course_title_and_end_date');

// Your function to get count of enrolled students (make sure this exists)
function get_enrolled_students_count($course_id) {
    global $wpdb;

    if (!$course_id) {
        return 0;
    }

    // $count = $wpdb->get_var($wpdb->prepare(
    //     "SELECT COUNT(DISTINCT user_id)
    //     FROM {$wpdb->prefix}learnpress_user_items
    //     WHERE item_id = %d AND item_type = 'lp_course' AND status = 'enrolled'",
    //     $course_id
    // ));
    $count = $wpdb->get_var( $wpdb->prepare(
    "SELECT COUNT(DISTINCT user_id)
    FROM {$wpdb->prefix}learnpress_user_items
    WHERE item_id = %d AND item_type = 'lp_course'",
    $course_id
));


    return intval($count);
}

// Shortcode to display enrolled student count with label
function shortcode_course_count($atts) {
    // Extract course_id from shortcode attribute or get current course ID
    $atts = shortcode_atts(
        array(
            'course_id' => 0,
        ),
        $atts,
        'enrolled_student_count'
    );

    $course_id = intval($atts['course_id']);

    if (!$course_id && is_singular('lp_course')) {
        $course_id = get_the_ID();
    }

    if (!$course_id) {
        return '<p>Invalid course ID.</p>';
    }

    $count = get_enrolled_students_count($course_id);

    // Pluralize "Student" based on count
    $student_label = ($count === 1) ? 'Enrolled Student' : 'Enrolled Students';

    // Return output, do NOT echo in shortcode functions!
    return '<h4 class="lp-course-start-date">' . esc_html($count) . '</h4><span class="lp-course-start-title"> ' . esc_html($student_label) . '</span>';
}
add_shortcode('enrolles_student_count', 'shortcode_course_count');



function shortcode_lp_course_progress($atts)
{
    global $wpdb;
    $atts = shortcode_atts([
        'course_id' => get_the_ID(),
    ], $atts);

    $course_id = absint($atts['course_id']);
    $user_id = get_current_user_id();

    
    if (!$user_id || !$course_id) {
        return '<h4 class="lp-course-start-date">-</h4><span class="lp-course-start-title"> Course Progress </span>';
    }


    // Get LP_User directly
    $user = learn_press_get_user($user_id);

    if (!$user->has_enrolled_course($course_id)) {
        return '<h4 class="lp-course-start-date">-</h4><span class="lp-course-start-title"> Course Progress </span>';
    }
    
    // $course_data = $user->get_course_data($course_id);

    // $progress = 0;
    // if ($course_data && method_exists($course_data, 'get_progress')) {
    //     $progress = $course_data->get_progress(); // Should be 0–100
    // }   
    // echo '<h4 class="lp-course-start-date">'. esc_html($progress) .'%</h4><span class="lp-course-start-title"> Course Progress </span>';

    // ✅ Get progress from custom table

    $table_name = $wpdb->prefix . 'user_course_progress';
    $progress = $wpdb->get_var( $wpdb->prepare(
        "SELECT progress FROM $table_name WHERE user_id = %d AND course_id = %d",
        $user_id,
        $course_id
    ) );

    $progress = is_null($progress) ? 0 : floatval($progress);

    return '<h4 class="lp-course-start-date">'. esc_html($progress) .'%</h4><span class="lp-course-start-title"> Course Progress </span>';

}
add_shortcode('lp_course_progress', 'shortcode_lp_course_progress');

add_action('elementor/widgets/widgets_registered', 'register_lp_widget_from_child');
function register_lp_widget_from_child()
{
    if (class_exists('Elementor\Plugin')) {
        require_once get_stylesheet_directory() . '/elementor-widgets/class-lp-course-material-widget.php';
        \Elementor\Plugin::instance()->widgets_manager->register(new \LP_Course_Material_Widget());

        // require_once get_stylesheet_directory() . '/elementor-widgets/class-tutorstok-slider-widget.php';
        // \Elementor\Plugin::instance()->widgets_manager->register(new \TutorsTok_Slider_Widget());
    }
}

// Init file
add_action('elementor/widgets/widgets_registered', function($widgets_manager) {
    require_once get_stylesheet_directory() . '/elementor-widgets/custom-video-slider-widget.php';
    $widgets_manager->register(new \Elementor_Video_Slider_Widget());
});


// function tutorstok_register_elementor_widgets( $widgets_manager ) {
//     require_once( __DIR__ . '/widgets/class-tutorstok-slider-widget.php' );
//     $widgets_manager->register( new \TutorsTok_Slider_Widget() );
// }
// add_action( 'elementor/widgets/register', 'tutorstok_register_elementor_widgets' );


// add_action('elementor/frontend/after_enqueue_scripts', function () {
//     wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css');
//     wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', [], null, true);
// });

function custom_course_breadcrumb_shortcode()
{
    ob_start();
    ?>
    <nav class="thim-ekit-breadcrumb">
        <?php

        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $user_login = $user->user_login;
            $user_id = $user->ID;

            $edit_url = home_url("/lp-profile/{$user_login}/"); ?>
            <a href="<?php echo $edit_url; ?>">Overview</a> /

            <?php echo '<span>Course Detail</span>';
        } else {
            ?>
            <a href="<?php echo get_post_type_archive_link('lp_course'); ?>">Courses</a> /
            <?php echo '<span>' . get_the_title($post->ID) . '</span>';
        }
        ?>

    </nav>
    <?php
    return ob_get_clean();
}
add_shortcode('course_breadcrumb', 'custom_course_breadcrumb_shortcode');

// // Enqueue Mobiscroll
// function enqueue_mobiscroll_assets()
// {
//     wp_enqueue_style('mobiscroll-css', 'https://cdn.mobiscroll.com/5.26.2/css/mobiscroll.min.css');
//     wp_enqueue_script('mobiscroll-js', 'https://cdn.mobiscroll.com/5.26.2/js/mobiscroll.jquery.min.js', array('jquery'), null, true);
// }
// add_action('wp_enqueue_scripts', 'enqueue_mobiscroll_assets');

//add_action('wp_ajax_course_filter', 'handle_ajax_course_filter');

// 1. Ensure LearnPress is loaded during AJAX
add_action('wp_loaded', function() {
    if (defined('DOING_AJAX') && DOING_AJAX && !class_exists('LP_Plugin')) {
        include_once WP_PLUGIN_DIR . '/learnpress/learnpress.php';  // Path to LearnPress plugin
        if (class_exists('LP_Plugin')) {
            LP_Plugin::instance(); // Initialize LearnPress if not loaded already
        }
    }
});
// Run early

// 2. Register AJAX handler

function shortcode_lp_course_materials_from_db( $atts ) {
    global $wpdb;

    $atts = shortcode_atts( array(
        'course_id' => get_the_ID(),
    ), $atts );

    $course_id = intval( $atts['course_id'] );

    $user_id = get_current_user_id();

    // Check for valid user & course
    if ( ! $user_id || ! $course_id ) {
        return '<p>Login and enroll to access course materials.</p>';
    }
    $user = learn_press_get_user( $user_id );

    // Check if user has enrolled in the course
    if ( ! $user || ! method_exists( $user, 'has_enrolled_course' ) || ! $user->has_enrolled_course( $course_id ) ) {
    ?><script>
        jQuery(document).ready(function ($) {
            $('.student_schedule_cta').hide();
        });
    </script>
    <?php
    return '<p>You must enroll in the course to access the materials.</p>';
} else { ?>
    <script>
        jQuery(document).ready(function ($) {
            $('.student_schedule_cta').show();
        });
    </script>
<?php }

$table_name = $wpdb->prefix . 'learnpress_files';

$materials = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT file_name, file_path, file_type FROM $table_name 
        WHERE item_id = %d AND item_type = %s 
        ORDER BY orders ASC",
        $course_id, 'lp_course'
    )
);

if ( empty( $materials ) ) {
    return '<p>No course materials found.</p>';
}

ob_start();
echo '<div class="material-wrapper">';
$upload_dir = wp_upload_dir();
    $base_dir = trailingslashit( $upload_dir['basedir'] ); // File system path
    $base_url  = trailingslashit( $upload_dir['baseurl'] ); // Public URL

    foreach ( $materials as $material ) {

        $file_path_relative = ltrim( $material->file_path, '/' );
        $full_file_path = $base_dir . $file_path_relative;
        $file_url = $base_url . $file_path_relative;
       // $file_url = site_url( $material->file_path ); // If file path is relative
        echo '<div class="material-card"><a href="' . esc_url( $file_url ) . '" target="_blank" rel="noopener"></a>
        <p>' . esc_html( $material->file_name ).'</p></div>';
    }
    echo '</div>';

    return ob_get_clean();
}
add_shortcode( 'lp_course_materials', 'shortcode_lp_course_materials_from_db' );


// Register the shortcode
function course_materials_shortcode($atts) {
    global $wpdb;

    $atts = shortcode_atts(array(
        'course_id' => 0,
    ), $atts, 'course_materials');

    $course_id = get_the_ID();
    if (empty($course_id)) {
        return 'Course ID is required.';
    }

    $user_id = get_current_user_id();

    // Check for valid user & course
    if ( ! $user_id || ! $course_id ) {
        return '<p>Login and enroll to access course materials.</p>';
    }
    $user = learn_press_get_user( $user_id );

    // Check if user has enrolled in the course
    if ( ! $user || ! method_exists( $user, 'has_enrolled_course' ) || ! $user->has_enrolled_course( $course_id ) ) {
        return '<p>You must enroll in the course to access the materials.</p>';
    }

    $upload_dir = wp_upload_dir();
    $base_dir = trailingslashit($upload_dir['basedir']);
    $base_url  = trailingslashit($upload_dir['baseurl']);

    $materials = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}learnpress_files WHERE item_id = %d AND item_type = 'lp_course'",
            $course_id
        )
    );

    $output = '<div class="material-wrapper">';
    foreach ($materials as $material) {
        $file_path_relative = ltrim($material->file_path, '/');
        $file_url = esc_url($base_url . $file_path_relative);

        $output .= '<div class="material-card">';
        $output .= '<a href="' . $file_url . '" target="_blank"></a>';
        $output .= '<p>' . esc_html($material->file_name) . '</p>';
        $output .= '</div>';
    }
    if (current_user_can('upload_files')) {
        $input_id = 'upload_material_' . $course_id;
        $output .= '
        <div class="material-card">
        <input type="file" id="' . esc_attr($input_id) . '" class="upload-material" data-course-id="' . esc_attr($course_id) . '" style="display: none;" />
        <button type="button" onclick="document.getElementById(\'' . esc_attr($input_id) . '\').click()">📤 Upload Document</button>
        <p>Upload</p>
        </div>';
        $output .= '
        <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".upload-material").forEach(function (input) {
                if (!input.dataset.listenerAdded) {
                    input.dataset.listenerAdded = true;
                    input.addEventListener("change", function () {
                        var file = this.files[0];
                        if (!file) return;

                        var courseId = this.dataset.courseId;
                        var formData = new FormData();
                        formData.append("action", "upload_course_material");
                        formData.append("material_file", file);
                        formData.append("course_id", courseId);
                        formData.append("_wpnonce", "' . wp_create_nonce("upload_material_nonce") . '");

                        var statusDiv = document.getElementById("upload-status");
                        if (!statusDiv) {
                            statusDiv = document.createElement("div");
                            statusDiv.id = "upload-status";
                            this.parentNode.appendChild(statusDiv);
                        }
                        statusDiv.innerHTML = "Uploading...";

                        fetch("' . admin_url("admin-ajax.php") . '", {
                            method: "POST",
                            body: formData
                            }).then(response => response.json()).then(data => {
                                if (data.success) {
                                    location.reload();
                                    } else {
                                        statusDiv.innerHTML = "<p style=\'color:red;\'>Error: " + data.data + "</p>";
                                    }
                                    }).catch(error => {
                                        statusDiv.innerHTML = "<p style=\'color:red;\'>Upload failed.</p>";
                                        });
                                        });
                                    }
                                    });
                                    });
                                    </script>
                                    ';
                                }
                                $output .= '</div>';
                                return $output;
                            }
                            add_shortcode('course_materials', 'course_materials_shortcode');

                            add_action('wp_ajax_upload_course_material', 'handle_ajax_course_material_upload');
                            function handle_ajax_course_material_upload() {
                                check_ajax_referer('upload_material_nonce');

                                if (!current_user_can('upload_files')) {
                                    wp_send_json_error('Permission denied.');
                                }

                                if (empty($_FILES['material_file']) || empty($_POST['course_id'])) {
                                    wp_send_json_error('Missing file or course ID.');
                                }

                                $file = $_FILES['material_file'];
                                $course_id = intval($_POST['course_id']);
                                $upload_result = wp_handle_upload($file, array('test_form' => false));

                                if (isset($upload_result['file'])) {
                                    global $wpdb;
                                    $upload_dir = wp_upload_dir();
                                    $base_dir = trailingslashit($upload_dir['basedir']);

                                    $file_path = $upload_result['file'];
                                    $file_name = basename($file_path);

                                    $wpdb->insert(
                                        $wpdb->prefix . 'learnpress_files',
                                        array(
                                            'file_name'   => $file_name,
                                            'file_type'   => $file['type'],
                                            'item_id'     => $course_id,
                                            'item_type'   => 'lp_course',
                                            'file_path'   => str_replace($base_dir, '', $file_path),
                                            'orders'      => 1,
                                            'created_at'  => current_time('mysql'),
                                        )
                                    );

                                    wp_send_json_success('File uploaded successfully.');
                                } else {
                                    wp_send_json_error('Upload failed.');
                                }
                            }


// add_shortcode('course_materials', 'course_materials_shortcode');

// AJAX handler function
// add_action('wp_ajax_filter_courses', 'handle_course_filter_ajax');
// add_action('wp_ajax_nopriv_filter_courses', 'handle_course_filter_ajax');
// function handle_course_filter_ajax() {
//     if (!is_user_logged_in()) {
//         wp_send_json_error('User not logged in');
//     }
//     $user_id = get_current_user_id();
//     $args = [
//         'post_type' => 'course',  // Custom post type for courses
//         'post_status' => 'publish',
//         'author' => $user_id, // Optional: Filter by user if needed
//         'posts_per_page' => -1, // Get all courses
//     ];
//     if (isset($_POST['progress_range']) && !empty($_POST['progress_range'])) {
//         $ranges = $_POST['progress_range'];
//         $args['meta_query'][] = [
//             'key' => '_course_progress_' . $user_id,  // Replace '_course_progress_' with the correct custom field key
//             'value' => $ranges,
//             'compare' => 'IN',
//             'type' => 'NUMERIC',
//         ];
//     }

//     // Filter by grade (custom field)
//     if (isset($_POST['grade']) && !empty($_POST['grade'])) {
//         $grades = $_POST['grade'];
//         $args['meta_query'][] = [
//             'key' => '_course_grade_' . $user_id,  // Replace '_course_grade_' with the correct custom field key
//             'value' => $grades,
//             'compare' => 'IN',
//         ];
//     }

//     // Filter by subject (course category taxonomy)
//     if (isset($_POST['subject']) && !empty($_POST['subject'])) {
//         $args['tax_query'] = [
//             [
//                 'taxonomy' => 'course_category',  // Replace 'course_category' with your taxonomy
//                 'field' => 'slug',
//                 'terms' => $_POST['subject'],
//                 'operator' => 'IN',
//             ],
//         ];
//     }

//     var_dump($args);

//     // Query the courses
//     $query = new WP_Query($args);

//     if ($query->have_posts()) {
//         ob_start();

//         // Loop through the courses
//         $index = 1;
//         while ($query->have_posts()) {
//             $query->the_post();
//             $course_id = get_the_ID();
//             $course_title = get_the_title($course_id);
//             $course_link = get_permalink($course_id);

//             // Get progress and grade (from user meta)
//             $progress = get_user_meta($user_id, "_course_progress_{$course_id}", true);
//             $grade = get_user_meta($user_id, "_course_grade_{$course_id}", true);

//             // Output course info
//             echo "<tr>
//                 <td>{$index}</td>
//                 <td><a href='{$course_link}' target='_blank'>{$course_title}</a></td>
//                 <td>{$progress}%</td>
//                 <td>{$grade}</td>
//             </tr>";

//             $index++;
//         }

//         wp_reset_postdata();
//         $html = ob_get_clean();
//         wp_send_json_success($html);
//     } else {
//         wp_send_json_success('<tr><td colspan="4">No courses found matching the filters.</td></tr>');
//     }
// }
// }

// this is workinf start
// add_action('wp_ajax_filter_courses', 'handle_ajax_course_filter');
// add_action('wp_ajax_nopriv_filter_courses', 'handle_ajax_course_filter');

// function handle_ajax_course_filter() {
//     $args = [
//         'post_type'      => 'lp_course',
//         'posts_per_page' => -1,
//         'post_status'    => 'publish',
//         'meta_query'     => [],
//         'tax_query'      => [],
//     ];

//     // Subject filter (assumes taxonomy 'course_category')
//     if (!empty($_POST['subject'])) {
//         $args['tax_query'][] = [
//             'taxonomy' => 'course_category',
//             'field'    => 'slug',
//             'terms'    => array_map('sanitize_text_field', $_POST['subject']),
//         ];
//     }

//     // Grade filter (assumes stored as meta '_course_grade')
//     if (!empty($_POST['grade'])) {
//         $args['meta_query'][] = [
//             'key'     => '_course_grade',
//             'value'   => array_map('sanitize_text_field', $_POST['grade']),
//             'compare' => 'IN',
//         ];
//     }

//     $current_user_roles = wp_get_current_user()->roles;
//     $is_student = in_array('subscriber', $current_user_roles);
//     $is_tutor = in_array('lp_teacher', $current_user_roles);

//     $query = new WP_Query($args);

//     if ($query->have_posts()) {
//         $index = 1;
//         while ($query->have_posts()) {
//             $query->the_post();

//             $course_id = get_the_ID();
//             $grade = get_post_meta($course_id, '_course_grade', true);
//             $subject_terms = wp_get_post_terms($course_id, 'course_category', ['fields' => 'names']);
//             $subject = !empty($subject_terms) ? implode(', ', $subject_terms) : 'N/A';
//             $enrolled_count = '';
//             $expire_time = '—'; // Replace with real value if needed
//             $end_time = '—';    // Replace with real value if needed

//             // Simulated progress (replace with real progress lookup per user)
//             $progress = rand(0, 100);

//             if (!empty($_POST['progress_range'])) {
//                 $in_range = false;
//                 foreach ($_POST['progress_range'] as $range) {
//                     [$min, $max] = explode('-', sanitize_text_field($range));
//                     if ($progress >= $min && $progress <= $max) {
//                         $in_range = true;
//                         break;
//                     }
//                 }
//                 if (!$in_range) {
//                     continue;
//                 }
//             }
//             if($is_student){
//                 echo '<tr class="lp_profile_course_progress__item">';
//                 echo '<td>' . esc_html($index++) . '</td>';
//                 echo '<td><a href="' . esc_url(get_permalink()) . '" title="' . esc_attr(get_the_title()) . '">' . wp_kses_post(get_the_title()) . '</a></td>';
//                 echo '<td>' . esc_html($progress) . '%</td>';
//                 echo '<td>' . esc_html($grade ?: 'NA') . '</td>';
//                 echo '<td>' . $expire_time . '</td>';
//                 echo '<td>' . $end_time . '</td>';
//                 echo '</tr>';
//             }else{
//                 echo '<tr class="lp_profile_course_progress__item">';
//                 echo '<td>' . esc_html($index++) . '</td>';
//                 echo '<td><a href="' . esc_url(get_permalink()) . '" title="' . esc_attr(get_the_title()) . '">' . wp_kses_post(get_the_title()) . '</a></td>';
//                 echo '<td>' .  $subject . '</td>';
//                 echo '<td>9</td>';
//                 echo '<td>' . $progress . '%</td>';
//                 echo '<td>' . $expire_time . '</td>';
//                 echo '<td>' . $end_time . '</td>';
//                 echo '</tr>';
//             }

//         }
//         wp_reset_postdata();
//     } else {
//         echo '<p>No courses found.</p>';
//     }

//     wp_die();
// }
// this is working end

add_action('wp_ajax_filter_courses', 'filter_courses_function'); // For logged-in users
add_action('wp_ajax_nopriv_filter_courses', 'filter_courses_function'); // For non-logged-in users

function filter_courses_function() {
    // Ensure LearnPress is loaded
    if (!class_exists('LearnPress')) {
        wp_send_json_error('LearnPress is not loaded');
    }

    // Get current user roles
    $current_user_roles = wp_get_current_user()->roles;
    $is_student = in_array('subscriber', $current_user_roles);
    $is_tutor = in_array('lp_teacher', $current_user_roles);

    // Prepare query arguments
    $args = [
        'post_type'      => 'lp_course',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => [],
        'tax_query'      => [],
    ];

    // Add custom filters if available (Progress Range, Grade, Subject)
    if (!empty($_POST['progress_range'])) {
        $args['meta_query'][] = [
            'key' => '_course_progress',
            'value' => $_POST['progress_range'],
            'compare' => 'BETWEEN',
        ];
    }

    // Perform the query
    $query = new WP_Query($args);
    $index = 1;
    $output = ''; // Initialize output

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $course_id = get_the_ID();
            $courseModel = learn_press_get_course($course_id);

            // Ensure courseModel is valid
            if ($courseModel) {
                $user_id = get_current_user_id();
                $userCourseModel = learn_press_get_user($user_id)->get_course_data($course_id);

                $progress = $userCourseModel ? $userCourseModel->get_progress() : 0;
                $grade = get_post_meta($course_id, '_course_grade', true);
                $subject_terms = wp_get_post_terms($course_id, 'course_category', ['fields' => 'names']);
                $subject = !empty($subject_terms) ? implode(', ', $subject_terms) : 'N/A';
                $enrolled_count = learn_press_get_course_user_count($course_id);

                // Filter progress range
                if (!empty($_POST['progress_range'])) {
                    $in_range = false;
                    foreach ($_POST['progress_range'] as $range) {
                        [$min, $max] = explode('-', sanitize_text_field($range));
                        if ($progress >= $min && $progress <= $max) {
                            $in_range = true;
                            break;
                        }
                    }
                    if (!$in_range) {
                        continue;
                    }
                }

                if ($is_student) {
                    // Student view
                    $output .= '<tr class="lp_profile_course_progress__item">';
                    $output .= '<td>' . esc_html($index++) . '</td>';
                    $output .= '<td><a href="' . esc_url($courseModel->get_permalink()) . '" title="' . esc_attr($courseModel->get_title()) . '">' . wp_kses_post($courseModel->get_title()) . '</a></td>';
                    $output .= '<td>' . esc_html($progress) . '%</td>';
                    $output .= '<td>' . esc_html($grade ?: 'NA') . '</td>';
                    $output .= '<td>' . $userCourseTemplate->html_expire_date_time($userCourseModel) . '</td>';
                    $output .= '<td>' . $userCourseTemplate->html_end_date_time($userCourseModel) . '</td>';
                    $output .= '</tr>';
                } elseif ($is_tutor) {
                    // Tutor view
                    $output .= '<tr class="lp_profile_course_progress__item">';
                    $output .= '<td>' . esc_html($index++) . '</td>';
                    $output .= '<td><a href="' . esc_url($courseModel->get_permalink()) . '" title="' . esc_attr($courseModel->get_title()) . '">' . wp_kses_post($courseModel->get_title()) . '</a></td>';
                    $output .= '<td>' . esc_html($subject) . '</td>';
                    $output .= '<td>' . esc_html($enrolled_count) . '</td>';
                    $output .= '<td>' . esc_html($progress) . '%</td>';
                    $output .= '<td>' . $userCourseTemplate->html_expire_date_time($userCourseModel) . '</td>';
                    $output .= '<td>' . $userCourseTemplate->html_end_date_time($userCourseModel) . '</td>';
                    $output .= '</tr>';
                }
            }
        }
    }
    echo $output;
    // Return the response
    wp_reset_postdata();
 // Send success response with filtered results
    exit();
}


function add_logged_in_body_class( $classes ) {
    if ( is_user_logged_in() && is_singular( 'lp_course' ) ) {
        $classes[] = 'course-without-banner';
    }
    return $classes;
}
add_filter( 'body_class', 'add_logged_in_body_class' );



// code for custom checkout *****************************************************

function create_children_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'parent_children';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        parent_id BIGINT(20) NOT NULL,
        child_name VARCHAR(100) NOT NULL,
        child_email VARCHAR(100) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_children_table');

// add_action('woocommerce_before_order_notes', 'custom_child_checkout_fields');

// function custom_child_checkout_fields($checkout) {
//     $user_id = get_current_user_id();
//     global $wpdb;
//     echo '<h3>' . __('Child Information') . '</h3>';
//     echo '<p class="form-row form-row-wide">';
//     echo '<label><input type="checkbox" id="show_child_fields" /> ' . __('Purchase for a child') . '</label>';
//     echo '</p>';
//     echo '<div id="child_fields_wrapper" style="display:none;">';
//     if (is_user_logged_in()) {
//         $table = $wpdb->prefix . 'parent_children';
//         $children = $wpdb->get_results("SELECT * FROM $table WHERE parent_id = $user_id");

//         if ($children) {
//             echo '<p class="form-row form-row-wide">';
//             echo '<label>' . __('Select Existing Child') . '</label>';
//             echo '<select name="existing_child" id="existing_child">';
//             echo '<option value="">-- Select Child --</option>';
//             foreach ($children as $child) {
//                 echo '<option value="' . esc_attr($child->id) . '">' . esc_html($child->child_name . ' (' . $child->child_email . ')') . '</option>';
//             }
//             echo '</select>';
//             echo '</p>';
//         }
//     }

//     // Add new child fields
//     echo '<div id="new_children_wrapper">';
//     echo '<div class="child-entry">';
//     woocommerce_form_field('new_child_name', [
//         'type'     => 'text',
//         'required' => false,
//         'label'    => __('Child Name'),
//     ]);
//     woocommerce_form_field('new_child_email', [
//         'type'     => 'email',
//         'required' => false,
//         'label'    => __('Child Email'),
//     ]);
//     echo '</div>';
//     echo '</div>';
//     echo '</div>';
// }
// add_action('woocommerce_before_checkout_form', 'custom_child_checkout_fields', 10);

// add_action('woocommerce_checkout_process', 'validate_child_checkout_fields');
// function validate_child_checkout_fields() {
//     // if (!is_user_logged_in()) return;
//     var_dump($_POST);
//     $child_emails = $_POST['new_child_email'] ?? [];
//     $existing = $_POST['existing_child'] ?? '';
//     $name = sanitize_text_field($_POST['new_child_name'] ?? '');
//     // foreach ($child_emails as $raw_email) {
//     //     $email = sanitize_email($raw_email);
//     // }

//     if (empty($name) && empty($child_emails)) {
//         wc_add_notice(__('Please select an existing child or provide new child\'s name and email.'), 'error');
//     }
// }

// add_action('woocommerce_checkout_create_order', 'save_child_data_in_order', 20, 2);
// function save_child_data_in_order($order, $data) {
//     $existing = sanitize_text_field($_POST['existing_child'] ?? '');
//     $name = sanitize_text_field($_POST['new_child_name'] ?? '');
//     $email = sanitize_email($_POST['new_child_email'] ?? '');
//     if ($existing) {
//         $order->update_meta_data('_child_selected', $existing);
//     } elseif ($name && $email) {
//         global $wpdb;
//         $table = $wpdb->prefix . 'parent_children';
//         $wpdb->insert($table, [
//             'parent_id' => get_current_user_id(),
//             'child_name' => $name,
//             'child_email' => $email
//         ]);
//         $order->update_meta_data('_child_name', $name);
//         $order->update_meta_data('_child_email', $email);
//     }
// }

add_action('woocommerce_checkout_create_order', 'override_order_customer_with_child_and_handle_guest_parent', 5, 2);
function override_order_customer_with_child_and_handle_guest_parent($order, $data) {
    error_log('--- Processing order creation ---');

    $child_name  = sanitize_text_field($_POST['new_child_name'] ?? '');
    $child_email = sanitize_email($_POST['new_child_email'] ?? '');

    error_log("Child Name: $child_name");
    error_log("Child Email: $child_email");

    if (empty($child_name) || empty($child_email)) {
        error_log('Child name or email is missing.');
        return;
    }

    $child_user = get_user_by('email', $child_email);
    if (!$child_user) {
        $child_password = wp_generate_password();
        $child_user_id = wp_create_user($child_email, $child_password, $child_email);

        if (is_wp_error($child_user_id)) {
            error_log('Failed to create child user: ' . $child_user_id->get_error_message());
            return;
        }

        wp_update_user([
            'ID'           => $child_user_id,
            'first_name'   => $child_name,
            'display_name' => $child_name,
        ]);

        error_log("Created child user: $child_user_id");
        $child_user = get_user_by('id', $child_user_id);
    } else {
        error_log("Using existing child user: {$child_user->ID}");
    }

    $order->set_customer_id($child_user->ID);
    $order->set_billing_first_name($child_name);
    $order->set_billing_email($child_email);

    $parent_user = is_user_logged_in() ? wp_get_current_user() : null;

    if (!$parent_user || !$parent_user->ID) {
        $parent_email = sanitize_email($_POST['billing_email'] ?? '');
        error_log("Guest parent email: $parent_email");

        if (!empty($parent_email)) {
            $existing_parent = get_user_by('email', $parent_email);

            if (!$existing_parent) {
                $parent_password = wp_generate_password();
                $parent_user_id = wp_create_user($parent_email, $parent_password, $parent_email);

                if (is_wp_error($parent_user_id)) {
                    error_log('Failed to create parent user: ' . $parent_user_id->get_error_message());
                } else {
                    error_log("Created parent user: $parent_user_id");
                    $parent_user = get_user_by('id', $parent_user_id);
                }
            } else {
                $parent_user = $existing_parent;
                error_log("Using existing parent user: {$parent_user->ID}");
            }
        }
    } else {
        error_log("Logged-in parent user: {$parent_user->ID}");
    }

    if ($parent_user && $parent_user->ID) {
        $order->update_meta_data('parent_user_id', $parent_user->ID);
        $order->update_meta_data('parent_email', $parent_user->user_email);
    }

    $order->update_meta_data('child_name', $child_name);
    $order->update_meta_data('child_email', $child_email);

    error_log('--- Order processing completed ---');
}

// add_action('woocommerce_checkout_order_processed', 'create_child_order_on_checkout', 20, 1);
// function create_child_order_on_checkout($order_id) {
//     $parent_order = wc_get_order($order_id);

//     if (!$parent_order || !is_user_logged_in()) return;

//     $user_id = get_current_user_id();

//     // Get child info
//     $child_name = sanitize_text_field($_POST['new_child_name'] ?? '');
//     $child_email = sanitize_email($_POST['new_child_email'] ?? '');

//     if (empty($child_name) || empty($child_email)) return;

//     // Create the child order
//     $child_order = wc_create_order([
//         'customer_id' => $user_id,
//         'created_via' => 'child-course',
//         'parent_id'   => $order_id,
//     ]);

//     // Clone items from parent order
//     foreach ($parent_order->get_items() as $item) {
//         $child_order->add_product($item->get_product(), $item->get_quantity(), [
//             'subtotal' => 0,
//             'total' => 0,
//         ]);
//     }

//     // Set billing (optional)
//     $child_order->set_billing_first_name($child_name);
//     $child_order->set_billing_email($child_email);

//     // Set total to 0 and mark as complete
//     $child_order->set_total(0);
//     $child_order->set_status('completed'); // or 'processing' if needed
//     $child_order->save();

//     // Save metadata
//     $child_order->update_meta_data('is_child_order', 'yes');
//     $child_order->update_meta_data('child_name', $child_name);
//     $child_order->update_meta_data('child_email', $child_email);
//     $child_order->save();
// }


add_action('woocommerce_admin_order_data_after_billing_address', 'show_child_in_admin', 10, 1);
function show_child_in_admin($order) {
    $name = $order->get_meta('Child Name');
    $email = $order->get_meta('Child Email');

    if ($name || $email) {
        echo '<p><strong>Child Info:</strong><br>' . esc_html($name) . ' (' . esc_html($email) . ')</p>';
    }
}

add_action('wp_footer', 'toggle_and_add_child_script', 100);
function toggle_and_add_child_script() {
    if (!is_checkout()) return; ?>
    <script>
        jQuery(function($) {
        // Hide/show child info based on checkbox
            function toggleChildFields() {
                if ($('#show_child_fields').is(':checked')) {
                    $('#child_fields_wrapper').slideDown();
                } else {
                    $('#child_fields_wrapper').slideUp();
                }
            }

        // Initialize on DOM ready and WooCommerce updates
            toggleChildFields();
            $('#show_child_fields').on('change', toggleChildFields);
            $(document.body).on('updated_checkout', toggleChildFields);

        // Add additional child fields
        // $('#add_child_button').on('click', function(e) {
        //     e.preventDefault();
        //     const index = $('.child-entry').length;
        //     const newFields = `
        //         <div class="child-entry">
        //             <p class="form-row form-row-wide">
        //                 <label for="new_child_name_${index}">Child Name</label>
        //                 <input type="text" name="new_child_name[]" id="new_child_name_${index}" />
        //             </p>
        //             <p class="form-row form-row-wide">
        //                 <label for="new_child_email_${index}">Child Email</label>
        //                 <input type="email" name="new_child_email[]" id="new_child_email_${index}" />
        //             </p>
        //         </div>
        //     `;
        //     $('#new_children_wrapper').append(newFields);
        // });
        });
    </script>
    <?php
}


add_action('woocommerce_checkout_order_processed', 'handle_guest_and_children_accounts', 20, 1);
function handle_guest_and_children_accounts($order_id) {
    $order = wc_get_order($order_id);

    // 1. Handle guest user (create account and send reset password email)
    if (!$order->get_user_id()) {
        $email = $order->get_billing_email();
        $name  = $order->get_billing_first_name();

        if (!email_exists($email)) {
            $password = wp_generate_password();
            $user_id = wp_create_user($email, $password, $email);
            wp_update_user([
                'ID' => $user_id,
                'first_name' => $name,
                'role' => 'customer'
            ]);

            // Attach user to order
            $order->set_customer_id($user_id);
            $order->save();

            // Send password reset email
            send_reset_email_to_user($email);
        }
    }

    // 2. Handle new children added during checkout
    if (!empty($_POST['new_child_name']) && !empty($_POST['new_child_email'])) {
        $names = $_POST['new_child_name'];
        $emails = $_POST['new_child_email'];

        foreach ($emails as $index => $child_email) {
            $child_name = sanitize_text_field($names[$index]);

            if (!email_exists($child_email)) {
                $password = wp_generate_password();
                $child_user_id = wp_create_user($child_email, $password, $child_email);
                wp_update_user([
                    'ID' => $child_user_id,
                    'first_name' => $child_name,
                    'role' => 'subscriber' // Or your custom role like 'child'
                ]);

                // Save parent-child relation (optional)
                global $wpdb;
                $table = $wpdb->prefix . 'parent_children';
                $parent_id = $order->get_user_id();
                $wpdb->insert($table, [
                    'parent_id' => $parent_id,
                    'child_id' => $child_user_id,
                    'child_name' => $child_name,
                    'child_email' => $child_email,
                ]);

                // Send password reset link to child
                send_reset_email_to_user($child_email);
            }
        }
    }
}


function send_reset_email_to_user($email) {
    $user = get_user_by('email', $email);
    if (!$user) return;

    $reset_key = get_password_reset_key($user);
    $reset_link = network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user->user_login), 'login');

    $subject = 'Set your password';
    $message = "Hi {$user->user_login},\n\nPlease set your password by clicking the link below:\n\n$reset_link\n\nThanks!";

    wp_mail($email, $subject, $message);
}

// Before cheakout trigger

add_action('init', function() {
    if (!is_admin()) return;

    $args = array(
        'role'    => 'customer',
        'number'  => -1,
        'fields'  => array('ID')
    );

    $customers = get_users($args);

    foreach ($customers as $user) {
        $user = new WP_User($user->ID);
        $user->remove_role('customer');
        $user->add_role('subscriber');
    }
});


// Calender module start

// Load FullCalendar assets
function load_custom_calendar_assets() {
    wp_enqueue_script('fullcalendar-js', 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js', array('jquery'), null, true);
    wp_enqueue_style('fullcalendar-css', 'https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css');
}
add_action('wp_enqueue_scripts', 'load_custom_calendar_assets');

// Register Custom Post Type Webinar
function register_webinar_post_type() {
    $labels = array(
        'name' => 'Webinars',
        'singular_name' => 'Webinar',
        'menu_name' => 'Webinars',
        'name_admin_bar' => 'Webinar',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Webinar',
        'edit_item' => 'Edit Webinar',
        'new_item' => 'New Webinar',
        'view_item' => 'View Webinar',
        'all_items' => 'All Webinars',
        'search_items' => 'Search Webinars',
    );

    $args = array(
        'label' => 'Webinar',
        'labels' => $labels,
        'supports' => array('title'),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'webinar'),
        'show_in_rest' => true, // For Gutenberg and REST API
    );

    register_post_type('webinar', $args);
}
add_action('init', 'register_webinar_post_type');

// Add Metaboxes for Webinar Fields
function add_webinar_metaboxes() {
    add_meta_box('webinar_details', 'Webinar Details', 'render_webinar_metabox', 'webinar', 'normal', 'high');
}
add_action('add_meta_boxes', 'add_webinar_metaboxes');

function render_webinar_metabox($post) {
    $datetime = get_post_meta($post->ID, '_webinar_datetime', true);
    $link = get_post_meta($post->ID, '_webinar_link', true);
    $course = get_post_meta($post->ID, '_webinar_course', true);
    $subject = get_post_meta($post->ID, '_webinar_subject', true);
    $type = get_post_meta($post->ID, '_webinar_type', true);

    echo '<p><label>Webinar DateTime:</label><br>';
    echo '<input type="datetime-local" name="webinar_datetime" value="' . esc_attr($datetime) . '" style="width:100%;"></p>';

    echo '<p><label>Webinar Link:</label><br>';
    echo '<input type="url" name="webinar_link" value="' . esc_attr($link) . '" style="width:100%;"></p>';

    $courses = get_posts(array('post_type' => 'lp_course', 'numberposts' => -1));
    echo '<p><label>Course:</label><br><select name="webinar_course" style="width:100%;">';
    echo '<option value="">Select Course</option>';
    foreach ($courses as $c) {
        echo '<option value="' . $c->ID . '" ' . selected($course, $c->ID, false) . '>' . esc_html($c->post_title) . '</option>';
    }
    echo '</select></p>';

    echo '<p><label>Subject:</label><br>';
    echo '<input type="text" name="webinar_subject" value="' . esc_attr($subject) . '" style="width:100%;"></p>';

    echo '<p><label>Type:</label><br>';
    echo '<select name="webinar_type" style="width:100%;">';
    $types = array('Session', 'Assignment', 'Test');
    foreach ($types as $t) {
        echo '<option value="' . $t . '" ' . selected($type, $t, false) . '>' . $t . '</option>';
    }
    echo '</select></p>';
}

// Save Webinar Metabox Data
function save_webinar_metabox($post_id) {
    if (isset($_POST['webinar_datetime'])) {
        update_post_meta($post_id, '_webinar_datetime', sanitize_text_field($_POST['webinar_datetime']));
    }
    if (isset($_POST['webinar_link'])) {
        update_post_meta($post_id, '_webinar_link', esc_url_raw($_POST['webinar_link']));
    }
    if (isset($_POST['webinar_course'])) {
        update_post_meta($post_id, '_webinar_course', intval($_POST['webinar_course']));
    }
    if (isset($_POST['webinar_subject'])) {
        update_post_meta($post_id, '_webinar_subject', sanitize_text_field($_POST['webinar_subject']));
    }
    if (isset($_POST['webinar_type'])) {
        update_post_meta($post_id, '_webinar_type', sanitize_text_field($_POST['webinar_type']));
    }
}
add_action('save_post', 'save_webinar_metabox');

// Get Custom Calendar Events
function get_custom_calendar_events() {
    global $wpdb;

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $user_role = !empty($current_user->roles) ? $current_user->roles[0] : '';

    if (!$user_id) {
        return new WP_Error('no_user', 'User not logged in', array('status' => 401));
    }

    $args = array(
        'post_type'      => 'webinar',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    );

    if ($user_role === 'lp_teacher') {
        $current_user_id = get_current_user_id();
        $assigned_courses = get_user_meta($current_user_id, 'assigned_courses', true);
        // error_log( var_dump($assigned_courses) );
    }

    $query = new WP_Query($args);
    $events = array();
    $purchased_courses = array();

    if (in_array($user_role, array('subscriber', 'lp_student'))) {
        $sql = $wpdb->prepare("
            SELECT item_id
            FROM {$wpdb->prefix}learnpress_user_items 
            WHERE user_id = %d
            AND item_type = 'lp_course'
            AND status IN ('enrolled', 'finished')
            ", $user_id);

        $purchased_courses = $wpdb->get_col($sql);
    }

    while ($query->have_posts()) {
        $query->the_post();
        $webinar_id = get_the_ID();
        $datetime = get_post_meta($webinar_id, '_webinar_datetime', true);
        $link = get_post_meta($webinar_id, '_webinar_link', true);
        $course_id = intval(get_post_meta($webinar_id, '_webinar_course', true));

        if (empty($datetime) || empty($course_id)) {
            continue;
        }

        $allowed = false;
        if ($user_role === 'lp_teacher') {
            $allowed = true;
        } elseif (in_array($user_role, array('subscriber', 'lp_student'))) {
            $allowed = in_array($course_id, $purchased_courses);
        }

        if (!$allowed) {
            continue;
        }

        $datetime_formatted = str_replace(' ', 'T', $datetime);

        $events[] = array(
            'title' => get_the_title(),
            'start' => $datetime_formatted,
            'url'   => esc_url($link),
            'extendedProps' => array(
                'course' => $course_id,
                'type'   => get_post_meta($webinar_id, '_webinar_type', true),
            ),
        );
    }

    wp_reset_postdata();

    return rest_ensure_response($events);
}

add_action('rest_api_init', function () {
    register_rest_route('custom-calendar/v1', '/events/', array(
        'methods'  => 'GET',
        'callback' => 'get_custom_calendar_events',
        'permission_callback' => function() {
            return is_user_logged_in();
        },
    ));
});

function render_custom_calendar() {
    $nonce = wp_create_nonce('wp_rest');
    $current_user = wp_get_current_user();
    $user_role = !empty($current_user->roles) ? $current_user->roles[0] : '';
    $user_login = urlencode($current_user->get_data('user_login'));
    $edit_url = home_url("/lp-profile/{$user_login}/settings/scheduled_events/");
    ob_start();
    ?>
    <?php if ($user_role === 'lp_teacher') : ?>
        <div class="manage-webinar-cls-adding-templated template-function-worked-cls-adding">
            <a href="<?php echo $edit_url; ?>" class="fc-button fc-button-primary cancel-template-typed-cls-adding">
                Manage Webinars
            </a>
        </div>
    <?php endif; ?>

    <div id="calendar" class="table-wrapped-scroll-cls"></div>

    <!-- Popup Modal -->
    <div id="eventModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999;">
        <div style="background:#fff; padding:20px; max-width:500px; margin:100px auto; border-radius:10px; position:relative;">
            <span id="closeModal" style="position:absolute; top:10px; right:15px; cursor:pointer; font-size:20px;">&times;</span>
            <h2 id="eventTitle"></h2>
            <p id="eventDate"></p>
            <a id="joinButton" href="#" target="_blank" style="padding:10px 20px; background:#3788d8; color:white; border:none; border-radius:5px; text-decoration:none;">Join Now</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                views: {
                    dayGridMonth: { buttonText: 'Month' },
                    timeGridWeek: { buttonText: 'Week' },
                    timeGridDay: { buttonText: 'Day' }
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('<?php echo site_url('/wp-json/custom-calendar/v1/events/'); ?>', {
                        method: 'GET',
                        headers: {
                            'X-WP-Nonce': '<?php echo $nonce; ?>'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(events => {
                        successCallback(events);
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                        failureCallback(error);
                        alert('There was an error while fetching events!');
                    });
                },
               eventClick: function(info) {
                info.jsEvent.preventDefault(); // Stop default redirection

                // Format the start date to dd-mm-yyyy HH:mm
                var startDate = new Date(info.event.start);
                var day = String(startDate.getDate()).padStart(2, '0');
                var month = String(startDate.getMonth() + 1).padStart(2, '0');
                var year = startDate.getFullYear();
                var hours = String(startDate.getHours()).padStart(2, '0');
                var minutes = String(startDate.getMinutes()).padStart(2, '0');
                var formattedDate = day + '-' + month + '-' + year + ' , ' + hours + ':' + minutes;

                // Set popup content
                document.getElementById('eventTitle').innerText = info.event.title;
                document.getElementById('eventDate').innerText = "Start: " + formattedDate;

                // Set "Join Now" button link
                var joinUrl = info.event.url || '#';
                document.getElementById('joinButton').setAttribute('href', joinUrl);

                // Open popup
                document.getElementById('eventModal').style.display = 'block';
            }

        });

            calendar.render();

        // Close Modal
            document.getElementById('closeModal').addEventListener('click', function() {
                document.getElementById('eventModal').style.display = 'none';
            });

        // Close modal if clicked outside popup
            window.onclick = function(event) {
                var modal = document.getElementById('eventModal');
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('custom_calendar', 'render_custom_calendar');

// Add Calendar tab to the LearnPress profile
add_filter('learn-press/profile-tabs', function ($calendar_tabs) {
    $calendar_tabs['scheduled_events'] = [
        'title'    => __('Scheduled Events', 'text-domain'),  // Renamed tab to "Scheduled Events"
        'priority' => 10,
        'icon'     => '<i class="lp-icon-calendar"></i>',
        'callback' => 'custom_render_scheduled_events_tab',  // Callback to render the scheduled events section
    ];
    return $calendar_tabs;
});

// Callback function to render the scheduled events tab content
function custom_render_scheduled_events_tab() {
    ?>

    <div class="main-add-webinar-typed">
        <button id="add-webinar-btn" class="add-webinar-type-cls-adding-here">Add Webinar</button>
    </div>

    <h3 class="profile-heading">Add Webinar</h3>
    <div class="custom-table-wrapper">
        <table class="template--innner-type-cls-adding">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>DateTime</th>
                    <th class="course-title-typed-cls-adding-here">Course</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $args = array(
                    'post_type'      => 'webinar',
                    'posts_per_page' => -1,
                'author'         => get_current_user_id(), // Only current user's webinars
            );
                $webinars = get_posts($args);

                if ($webinars) {
                    foreach ($webinars as $webinar) {
                        $datetime = get_post_meta($webinar->ID, '_webinar_datetime', true);
                        $link     = get_post_meta($webinar->ID, '_webinar_link', true);
                        $course   = get_post_meta($webinar->ID, '_webinar_course', true);
                        $subject  = get_post_meta($webinar->ID, '_webinar_subject', true);
                        $type     = get_post_meta($webinar->ID, '_webinar_type', true);
                        $course_title = $course ? get_the_title($course) : '';
                        ?>
                        <tr>
                            <td><?php echo esc_html($webinar->post_title); ?></td>
                            <?php
                            if ( $datetime ) {
                                $date_obj = new DateTime($datetime);
                               echo '<td>' . esc_html($date_obj->format('d-m-Y\TH:i')) . '</td>';

                            } else {
                                echo '<td>—</td>';
                            }
                            ?>
                            <td class="course-title-typed-cls-adding-here"><?php echo esc_html($course_title); ?></td>
                            <td>
                                <button class="edit-webinar-btn new-template-type-cls-adding-here" 
                                data-id="<?php echo esc_attr($webinar->ID); ?>"
                                data-title="<?php echo esc_attr($webinar->post_title); ?>"
                                data-datetime="<?php echo esc_attr($datetime); ?>"
                                data-link="<?php echo esc_url($link); ?>"
                                data-course="<?php echo esc_attr($course); ?>"
                                data-subject="<?php echo esc_attr($subject); ?>"
                                data-type="<?php echo esc_attr($type); ?>"
                                ></button>
                            </td>
                        </tr>
                    <?php }
                } else {
                    echo '<tr><td colspan="4" style="text-align:center; padding:15px;">No webinars found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php
    // Modal popup HTML
    custom_render_webinar_modal();
}

// 3. Modal Form inside Tab
function custom_render_webinar_modal() {
    ?>
    <div id="webinar-modal-overlay" style="display:none; position: fixed;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    transform: translate(-50%, -50%);" class="main-add-inner-webmodal-cls-adding"></div>

    <div id="webinar-modal" style="display:none; position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgb(255, 255, 255);
    padding: 20px;
    border-radius: 8px;
    max-width: 600px;
    z-index: 1000;" class="add-inner-webmodal-cls-adding">
    <div class="add-webinar-cls-adding-here">
        <div class="left-side-added-webinar">
            <h2 id="modal-title" class="add-innar-title-cls-adding-here">Add Webinar</h2>
        </div>
        <div class="right-side-added-webinar-title">
            <button id="modal-close-button-closed-modal" class="logout-modalclose-ics"><span class="close-btn-new-cls-adding-here">×</span></button>
        </div>
    </div>


    <form method="post" id="webinar-form">
       <div class="form-template-type-adding-here">
        <input type="hidden" name="webinar_id" id="webinar_id" value="">

        <p>
            <input type="text" name="webinar_title" id="webinar_title" required style="width:100%;" placeholder="Title">
        </p>

        <p>
            <input type="datetime-local" name="webinar_datetime" id="webinar_datetime" required style="width:100%;" placeholder="DateTime" >
        </p>

        <p>
            <input type="url" name="webinar_link" id="webinar_link" style="width:100%;" placeholder="Link">
        </p>

        <p><label>Course:</label><br>
            <select name="webinar_course" id="webinar_course" style="width:100%;">
                <option value="">Select Course</option>
                <?php
                $current_user_id = get_current_user_id();
                $courses = get_posts(array('post_type' => 'lp_course', 'numberposts' => -1, 'author'      => $current_user_id,));
                foreach ($courses as $c) {
                    echo '<option value="' . esc_attr($c->ID) . '">' . esc_html($c->post_title) . '</option>';
                }
                ?>
            </select></p>

            <p>
                <input type="text" name="webinar_subject" id="webinar_subject" style="width:100%;" placeholder="Subject">
            </p>

    <!-- 
    <p><label>Type:</label><br>
    <select name="webinar_type" id="webinar_type" style="width:100%;">
        <option value="">Select Type</option>
        <option value="Session">Session</option>
        <option value="Assignment">Assignment</option>
        <option value="Test">Test</option>
    </select></p> 
            -->
        </div>
        <div class="template-function-worked-cls-adding">
            <button type="submit" name="save_webinar" id="save-webinar-btn" class="save-template-typed-cls-adding">Save</button>
            <button type="button" id="close-webinar-modal" class="cancel-template-typed-cls-adding">Cancel</button>
        </div>
    </form>

    

</div>

<script>
   // 1. Set min date-time to now
document.addEventListener("DOMContentLoaded", function () {
    const datetimeField = document.getElementById("webinar_datetime");

    // Set minimum to current datetime in correct format
    const now = new Date();
    const offset = now.getTimezoneOffset();
    const localNow = new Date(now.getTime() - offset * 60000).toISOString().slice(0, 16); // format: YYYY-MM-DDTHH:MM

    datetimeField.min = localNow;

    // 2. Validate format on blur
    datetimeField.addEventListener("blur", function (e) {
        const value = e.target.value;
        const isValid = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(value);

        if (!isValid) {
            
            e.target.value = '';
        } else {
            // Optional: Prevent past dates from being entered manually
            if (value < localNow) {
               
                e.target.value = '';
            }
        }
    });

    // 3. Optional: Prevent typing (force calendar picker only)
    datetimeField.addEventListener('keydown', function (e) {
        e.preventDefault();
    });
});
    document.addEventListener('DOMContentLoaded', function () {
        const modal             = document.getElementById('webinar-modal');
        const overlay           = document.getElementById('webinar-modal-overlay');
        const openBtn           = document.getElementById('add-webinar-btn');
        const closeBtn          = document.getElementById('close-webinar-modal');
        const iconCloseBtn      = document.getElementById('modal-close-button-closed-modal');
        const form              = document.getElementById('webinar-form');
        const editBtns          = document.querySelectorAll('.edit-webinar-btn');

        openBtn.addEventListener('click', function () {
            document.getElementById('modal-title').innerText = 'Add Webinar';
            form.reset();
            document.getElementById('webinar_id').value = '';
            modal.style.display   = 'block';
            overlay.style.display = 'block';
        });

        closeBtn.addEventListener('click', function () {
            modal.style.display   = 'none';
            overlay.style.display = 'none';
        });

        iconCloseBtn.addEventListener('click', function () {
            modal.style.display   = 'none';
            overlay.style.display = 'none';
        });

        editBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('modal-title').innerText = 'Edit Webinar';

                const idField       = document.getElementById('webinar_id');
                const titleField    = document.getElementById('webinar_title');
                const datetimeField = document.getElementById('webinar_datetime');
                const linkField     = document.getElementById('webinar_link');
                const courseField   = document.getElementById('webinar_course');
                const subjectField  = document.getElementById('webinar_subject');
                const typeField     = document.getElementById('webinar_type');

                if (idField)       idField.value       = this.dataset.id;
                if (titleField)    titleField.value    = this.dataset.title;
                if (datetimeField) datetimeField.value = this.dataset.datetime;
                if (linkField)     linkField.value     = this.dataset.link;
                if (courseField)   courseField.value   = this.dataset.course;
                if (subjectField)  subjectField.value  = this.dataset.subject;
                if (typeField)     typeField.value     = this.dataset.type;

                modal.style.display   = 'block';
                overlay.style.display = 'block';
            });
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
    const saveButton = document.getElementById('save-webinar-btn');
    const webinarIdInput = document.getElementById('webinar_id');

    // Handle Edit button clicks
    document.querySelectorAll('.edit-webinar-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            // Set form values from data attributes
            webinarIdInput.value = this.dataset.id || '';
            document.getElementById('webinar_title').value = this.dataset.title || '';
            document.getElementById('webinar_datetime').value = this.dataset.datetime || '';
            document.getElementById('webinar_link').value = this.dataset.link || '';
            document.getElementById('webinar_course').value = this.dataset.course || '';
            document.getElementById('webinar_subject').value = this.dataset.subject || '';

            // Change button text to Update
            saveButton.textContent = 'Update';

            // Optional: Show the form/modal
        });
    });

    // Handle Add Webinar button click
    document.getElementById('add-webinar-btn').addEventListener('click', function () {
        // Reset form and ID
        webinarIdInput.value = '';
        document.getElementById('webinar-form').reset();

        // Change button text to Save
        saveButton.textContent = 'Save';

        // Optional: Show the form/modal
    });

    // Optional: Cancel button resets form and button
    document.getElementById('close-webinar-modal').addEventListener('click', function () {
        webinarIdInput.value = '';
        document.getElementById('webinar-form').reset();
        saveButton.textContent = 'Save';
    });
});
</script>
<?php
}

// 4. Save Webinar (Add/Edit)
add_action('template_redirect', function () {
    if (isset($_POST['save_webinar'])) {
        $webinar_id = intval($_POST['webinar_id']);
        $title      = sanitize_text_field($_POST['webinar_title']);
        $datetime   = sanitize_text_field($_POST['webinar_datetime']);
        $link       = esc_url_raw($_POST['webinar_link']);
        $course     = intval($_POST['webinar_course']);
        $subject    = sanitize_text_field($_POST['webinar_subject']);
        $type       = sanitize_text_field($_POST['webinar_type']);

        if ($webinar_id > 0) {
            // Update webinar
            wp_update_post([
                'ID'         => $webinar_id,
                'post_title' => $title,
            ]);
        } else {
            // Insert new webinar
            $webinar_id = wp_insert_post([
                'post_title'  => $title,
                'post_type'   => 'webinar',
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
            ]);
        }

        if ($webinar_id) {
            update_post_meta($webinar_id, '_webinar_datetime', $datetime);
            update_post_meta($webinar_id, '_webinar_link', $link);
            update_post_meta($webinar_id, '_webinar_course', $course);
            update_post_meta($webinar_id, '_webinar_subject', $subject);
            update_post_meta($webinar_id, '_webinar_type', $type);
        }

        wp_safe_redirect(esc_url_raw($_SERVER['REQUEST_URI']));
        exit;
    }
});


// add_action('wp_login', 'redirect_parent_after_login', 10, 2);
// function redirect_parent_after_login($user_login, $user) {
//     if (in_array('parent', (array) $user->roles)) {
//         wp_redirect(home_url('/lp-profile/newparent/children/'));
//         exit;
//     }
// }
add_action('wp_login', 'redirect_parent_after_login', 10, 2);
function redirect_parent_after_login($user_login, $user) {
    if (in_array('parent', (array) $user->roles)) {
        // Use the user_login slug or user_nicename (safer for URLs)
        $username_slug = $user->user_nicename;

        // Redirect to dynamic lp-profile/{username}/children/
        wp_redirect(home_url("/lp-profile/{$username_slug}/children/"));
        exit;
    }
}


add_action('after_setup_theme', 'hide_admin_bar_for_tutor');
function hide_admin_bar_for_tutor() {
    if (current_user_can('lp_teacher') && !current_user_can('administrator')) {
        show_admin_bar(false);
    }
}

add_action('wp_head', function () {
    if ( ! is_user_logged_in() && is_singular('lp_course') ) {
        echo '<style>.elementor-24650 { display: none !important; }</style>';
    }
});


// add_action( 'wp_head', 'custom_logged_out_course_header' );
// function custom_logged_out_course_header() {
//     if ( ! is_user_logged_in() && is_singular( 'lp_course' ) ) {
//         echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display(18651); // Replace 789 with your guest header template ID
//     }
// }
function add_search_query_var( $vars ) {
    $vars[] = 'search';
    return $vars;
}
add_filter( 'query_vars', 'add_search_query_var' );
