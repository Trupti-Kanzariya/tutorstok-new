<?php
/**
 * Template for displaying assignments tab in user profile page.
 */

defined( 'ABSPATH' ) || exit();

use LearnPress\Models\CourseModel;
use LearnPressAssignment\Models\AssignmentPostModel;
use LearnPressAssignment\Models\UserAssignmentModel;

$profile        = LP_Profile::instance();
$user           = $profile->get_user();
$current_user   = wp_get_current_user();
$is_instructor  = in_array( 'lp_teacher', $current_user->roles );
$search_query   = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
?>

<div class="learn-press-subtab-content">

    <?php if ( $is_instructor ) : ?>

        <!-- ========== INSTRUCTOR VIEW ========== -->
        <?php
        $paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;

        $args = array(
            'post_type'      => 'lp_assignment',
        'posts_per_page' => 5, // Adjust as needed
        'paged'          => $paged,
        'author'         => get_current_user_id(),
        'post_status'    => array( 'publish', 'draft', 'pending' ),
        's'              => $search_query,
    );

        $assignment_query = new WP_Query( $args );
    //print_r($assignment_query);

        ?>
        <style>
            #assignment-toast-message {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                display: none;
                min-width: 250px;
                padding: 15px 20px;
                color: #fff;
                font-size: 16px;
                border-radius: 6px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
                transition: all 0.3s ease-in-out;
                opacity: 0.95;
            }
        </style>

        <div id="assignment-toast-message" style="display:none; padding:10px; margin-bottom:10px; border-radius:5px;"></div>

        <div class="lp-instructor-assignments">
            <h3 class="profile-heading"><?php esc_html_e( 'Added Assignments List', 'learnpress-assignments' ); ?></h3>
            <div class="custom-table-wrapper">
                <table class="lp-list-table instructor-assignment-list">
                    <?php if ( ! empty( $assignment_query ) ) : ?>
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'Assignment Name', 'learnpress-assignments' ); ?></th>
                                <th><?php esc_html_e( 'Assignment Date', 'learnpress-assignments' ); ?></th>
                                <!-- <th><?php // esc_html_e( 'Submission date', 'learnpress-assignments' ); ?></th> -->
                                <th><?php esc_html_e( 'Actions', 'learnpress-assignments' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $assignment_query->posts as $assignment ) :
                                $assignment_id = $assignment->ID;
                                $course_id = get_post_meta( $assignment_id, 'lp-item-assigned', true );
                    // echo $course_id.'ww';
                                $course_title = $course_id ? get_the_title( $course_id ) : '';
                                $course_link = $course_id ? get_permalink( $course_id ) : '#';
                    // echo '<pre>'; print_r($assignment_id );
                                $due_date      = get_post_meta( $assignment_id, '_lp_duration', true );

                                $course_link   = $course_id ? get_permalink( $course_id ) : '#';

                                $edit_url   = get_edit_post_link( $assignment_id );
                                $delete_url = get_delete_post_link( $assignment_id, '', true );

                                $post_status = get_post_status_object( get_post_status( $assignment_id ) );
                                $status_label = $post_status ? $post_status->label : __( 'Unknown', 'learnpress-assignments' );
                                $publish_date = get_the_date( 'Y-m-d', $assignment_id );

                                global $wpdb;
                                $submission_count = $wpdb->get_var( $wpdb->prepare(
                                    "SELECT COUNT(*) FROM {$wpdb->prefix}learnpress_user_items 
                                    WHERE item_id = %d AND item_type = %s AND user_id != 0",
                                    $assignment_id,
                                    'lp_assignment'
                                ) );

                                $submission_date = $wpdb->get_var( $wpdb->prepare(
                                    "SELECT MAX(start_time) FROM {$wpdb->prefix}learnpress_user_items 
                                    WHERE item_id = %d AND item_type = %s AND user_id != 0",
                                    $assignment_id,
                                    'lp_assignment'
                                ));
                                ?>
                                <tr>
                                    <td><a href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( $assignment->post_title ); ?></a>
                                        <br>
                                        <?php if ( $course_id ) : ?>
                                            <small> 
                                                <a href="<?php echo esc_url( $course_link ); ?>" target="_blank"><?php echo esc_html( $course_title ); ?></a>
                                            </small>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $publish_date ? esc_html( date( 'M d, Y', strtotime( $publish_date ) ) ) : __( 'NA', 'learnpress-assignments' ); ?></td>
                                    <!-- <td><?php // echo $submission_date; ?></td> -->
                                    <td>
                                        <a href="javascript:void(0)" class="button button-primary view-assignment-btn" data-assignment-id="<?php echo esc_attr( $assignment_id ); ?>">
                                            <?php esc_html_e( 'View', 'learnpress-assignments' ); ?>
                                        </a>
                                        <a href="javascript:void(0)" class="button button-primary edit-assignment-btn" data-assignment-id="<?php echo esc_attr( $assignment_id ); ?>">
                                            <?php esc_html_e( 'Edit', 'learnpress-assignments' ); ?>
                                        </a>
                                        <a href="javascript:void(0)" 
                                        class="button button-secondary view-submission" data-assignment-id="<?php echo esc_attr( $assignment_id ); ?>">
                                        <?php esc_html_e( 'View Submissions', 'learnpress-assignments' ); ?>
                                    </a>
                                    <a href="<?php echo esc_url( $delete_url ); ?>" 
                                       class="button button-link delete-assignment" 
                                       data-delete-url="<?php echo esc_url( $delete_url ); ?>">
                                       <?php esc_html_e( 'Delete', 'learnpress-assignments' ); ?>
                                   </a>

                               </td>
                           </tr>
                       <?php endforeach; ?>
                   </tbody>
               <?php else : ?>
                <p><?php esc_html_e( 'No assignments found.', 'learnpress-assignments' ); ?></p>
            <?php endif; ?>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <div class="add-assignment-button" style="margin-top: 20px;">
                            <a href="#" class="button button-primary add-weekly-assignments">
                                <?php esc_html_e( 'Add Weekly Assignments', 'learnpress-assignments' ); ?>
                            </a>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div id="deleteModal" class="lp-modal-overlay" style="display:none;">
          <div class="lp-modal">
            <p>Are you sure you want to delete this assignment?</p>
            <div class="lp-modal-actions">
              <button id="cancelDelete" class="lp-btn lp-btn-cancel">Cancel</button>
              <a id="confirmDelete" class="lp-btn lp-btn-delete" href="#">Delete</a>
          </div>
      </div>
  </div>
  <style type="text/css">
    .lp-modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
  }
  .lp-modal {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
  }
  .lp-modal-actions {
      margin-top: 20px;
  }
  .lp-btn {
      padding: 8px 16px;
      margin: 0 5px;
      cursor: pointer;
  }
  .lp-btn-delete {
      background-color: #d9534f;
      color: #fff;
      border: none;
  }
  .lp-btn-cancel {
      background-color: #6c757d;
      color: #fff;
      border: none;
  }

</style>
<?php
        //$big = 999999999; // need an unlikely integer

$total_pages = $assignment_query->max_num_pages;
$current_page = max( 1, $paged );

echo '<div class="custom-pagination new-custom-pagination-cls">';
for ( $i = 1; $i <= $total_pages; $i++ ) {
    echo '<a href="#" class="pagination-link new-custom-pagination-cls-link' . ( $i === $current_page ? ' current' : '' ) . '" data-page="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</a> ';
}
echo '</div>';

?>
</div>


<div id="assignment-edit-container" style="display: none;"></div>
<!-- Each assignment item here for adding new -->
<div id="assignment-form-response"></div>

<!-- <div id="assignment-listing" class="assignment-listing"> -->
    <!-- Each assignment item here for Editing-->
    <!-- </div> -->

    <div id="assignment-edit-form-container"></div> <!-- The form will be loaded here -->

    <!-- Assignment List Container -->
    <div class="lp-list-table">
        <!-- Your assignment listing content -->
    </div>

    <!-- View Submission List Container -->
    <div id="assignment-submissions-container" style="display: none;" class="back-to-assignment-cls-adding-here"></div><!-- Submission list will be loaded here -->

</div>

<?php /* Start Script for Add Weekly Assignment */ ?>
<script>
    // Your delete handler in functions.php
    function handle_delete_assignment_page() {
        $assignment_id = intval($_GET['assignment_id'] ?? 0);
        if ($assignment_id) {
            wp_delete_post($assignment_id, true);
        }
    wp_redirect(add_query_arg('deleted', 'true', get_permalink())); // ← must add this
    exit;
}

function bindDeleteButtons() {
  const deleteLinks = document.querySelectorAll('.delete-assignment');
  const modal = document.getElementById('deleteModal');
  const confirmBtn = document.getElementById('confirmDelete');
  const cancelBtn = document.getElementById('cancelDelete');

  deleteLinks.forEach(link => {
    link.removeEventListener('click', handleDeleteClick); // Avoid duplicate binding
    link.addEventListener('click', handleDeleteClick);
});

  function handleDeleteClick(e) {
    e.preventDefault();
    const url = this.getAttribute('data-delete-url');
    confirmBtn.setAttribute('href', url);
    modal.style.display = 'flex';
}

cancelBtn.addEventListener('click', function () {
    modal.style.display = 'none';
});

window.addEventListener('click', function(e) {
    if (e.target === modal) {
      modal.style.display = 'none';
  }
});
}

document.addEventListener('DOMContentLoaded', function () {
  bindDeleteButtons();
  const params = new URLSearchParams(window.location.search);
  if (params.get('deleted') === '1') {
    toastr.success('Assignment deleted successfully!');
    // Remove query param from URL
    window.history.replaceState({}, document.title, window.location.pathname);
}
});

</script>

<script>
    jQuery(document).ready(function($) {
       function showMessage(message, type = 'success') {
        const $msgBox = $('#assignment-toast-message');
        $msgBox
        .text(message)
        .css({
            background: type === 'success' ? '#28a745' : '#dc3545',
            color: '#fff'
        })
        .fadeIn(300);

        setTimeout(() => $msgBox.fadeOut(500), 3000);
    }

    $(document).on('submit', '#add-assignment-form', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $submitButton = $form.find('button[type="submit"]');

        if ($submitButton.prop('disabled')) {
            return false;
        }

        var fileInput = $form.find('input[type="file"]')[0];
        var allowedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (fileInput.files.length > 0) {
            var file = fileInput.files[0];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Invalid file type!');
                $submitButton.prop('disabled', false).text('Save Assignment');
                return false;
            }
        }

        $submitButton.prop('disabled', true).text('Saving...');

        var formData = new FormData($form[0]);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.data.message || 'Assignment saved successfully!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    $submitButton.prop('disabled', false).text('Save Assignment');
                    var errMsg = (typeof response.data === 'object' && response.data.message) ? response.data.message : response.data;
                    toastr.error(errMsg || 'Error saving assignment.');
                }
            },
            error: function() {
                $submitButton.prop('disabled', false).text('Save Assignment');
                toastr.error('Server error occurred while saving assignment.');
            }
        });

        return false;
    });



});
</script>

<script>
    jQuery(document).ready(function($) {
    // Delegated click handler for dynamic Add Weekly Assignments button
        $(document).on('click', '.add-weekly-assignments', function(e) {
            e.preventDefault();
            const container = $('#assignment-edit-container');

        // Hide list view
            $('.lp-list-table').hide();
            $('.profile-heading').hide();
            $('.custom-pagination').hide();

            container.html('<p>Loading form...</p>').show();

            $.post(ajaxurl, { action: 'render_add_weekly_assignment_form' }, function(response) {
                container.html(response);
                window.scrollTo({ top: container.offset().top - 100, behavior: 'smooth' });
            });
        });
    });

</script>

<?php 
/* End Script for Add Weekly Assignment */

/* Start of adding pagination Script*/ ?>

<script>
    jQuery(document).ready(function($) {
        $(document).on('click', '.pagination-link', function(e) {
            e.preventDefault();
            var page = $(this).data('page');

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'load_instructor_assignments',
                    paged: page
                },
                success: function(response) {
                    if (response.success && response.data) {
                        $('.lp-instructor-assignments').html(response.data);
                        bindDeleteButtons();
                    } else {
                        $('.lp-instructor-assignments').html('<p>Error loading assignments.</p>');
                    }
                }
            });
        });
    });

</script>

<?php /* End of adding pagination Script */

/* Start Script for Edit Assignment*/
?>

<script>
    jQuery(document).ready(function($) {

     function showMessage(message, type = 'success') {
        const $msgBox = $('#assignment-toast-message');
        $msgBox
        .text(message)
        .css({
            background: type === 'success' ? '#28a745' : '#dc3545',
            color: '#fff'
        })
        .fadeIn(300);

        setTimeout(() => $msgBox.fadeOut(500), 3000);
    }
    // Delegated click handler for dynamically loaded edit buttons
    $(document).on('click', '.edit-assignment-btn', function(e) {
        e.preventDefault();
        const assignmentId = $(this).data('assignment-id');
        const container = $('#assignment-edit-form-container');

        // Hide list view and pagination
        $('.lp-list-table').hide();
        $('.profile-heading').hide();
        $('.custom-pagination').hide();

        container.html('<p>Loading form...</p>').show();

        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            method: 'POST',
            data: {
                action: 'load_edit_assignment_form',
                assignment_id: assignmentId
            },
            success: function(response) {
                if (response.success) {
                    container.html(response.data);
                    window.scrollTo({ top: container.offset().top - 100, behavior: 'smooth' });
                } else {
                    alert(response.data || 'Failed to load form.');
                }
            }
        });
    });

    // Delegated submit handler for edit form (loads via AJAX)
//    $(document).on('submit', '#edit-assignment-form', function(e) {
//     e.preventDefault();

//     const $form = $(this);
//     const fileInput = $form.find('input[type="file"]')[0];
    
//     if (fileInput && fileInput.files.length > 0) {
//         const file = fileInput.files[0];
//         const allowedTypes = [
//             'application/pdf', 
//             'image/jpeg', 
//             'image/png', 
//             'image/gif', 
//             'application/msword', 
//             'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
//         ];

//         if (!allowedTypes.includes(file.type)) {
//             alert('Invalid file type! Please upload a PDF, Image, or DOC file.');
//             return false; // stop submission
//         }
//     }

//     const formData = $form.serialize();

//     $.ajax({
//         url: '<?php echo admin_url("admin-ajax.php"); ?>',
//         method: 'POST',
//         data: {
//             action: 'save_edit_assignment',
//             assignment_data: formData
//         },
//         success: function(response) {
//             if (response.success) {
//                 showMessage('Assignment updated!');
//                 location.reload();
//             } else {
//                 alert(response.data || 'Error saving.');
//             }
//         }
//     });
// });
    $(document).on('submit', '#edit-assignment-form', function (e) {
        e.preventDefault();

        const $form = $(this);
        const formData = new FormData(this);
        formData.append('action', 'save_edit_assignment');

    // Optional: File validation
        const fileInput = $form.find('input[type="file"]')[0];
        if (fileInput && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const allowedTypes = [
                'application/pdf',
                'image/jpeg',
                'image/png',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Invalid file type. Only PDF, images, DOC, and DOCX files are allowed.');
                return false;
            }
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    toastr.success('Assignment updated successfully!');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    toastr.error(response.data?.message || 'Error saving.');
                }
            },
            error: function () {
                toastr.error('Server error occurred.');
            }
        });
    });


});

</script>

<?php 
/* End Script for Edit Assignment */

/* Start Script for View Assignment*/
?>

<script>
    jQuery(document).ready(function($) {

    // Delegated click handler for 'View Assignment' buttons
        $(document).on('click', '.view-assignment-btn', function(e) {
            e.preventDefault();

            const assignmentId = $(this).data('assignment-id');
            const container = $('#assignment-edit-form-container');

        // Hide list view and pagination
            $('.lp-list-table').hide();
            $('.profile-heading').hide();
            $('.custom-pagination').hide();

            container.html('<p>Loading...</p>').show();

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                method: 'POST',
                data: {
                    action: 'load_view_assignment_form',
                    assignment_id: assignmentId
                },
                success: function(response) {
                    if (response.success) {
                        container.html(response.data);
                        window.scrollTo({ top: container.offset().top - 100, behavior: 'smooth' });
                    } else {
                        alert(response.data || 'Error loading assignment.');
                    }
                }
            });
        });

    });
</script>

<?php 
/* End Script for View Assignment */ 

/* Start list of Submission */ 
?>

<script>
    jQuery(document).ready(function($) {
        const adminAjax = '<?php echo admin_url('admin-ajax.php'); ?>';

    // View Submissions (delegated for pagination-safe behavior)
        $(document).on('click', '.view-submission', function(e) {
            e.preventDefault();

            const assignmentId = $(this).data('assignment-id');
            const $container = $('#assignment-submissions-container');
            const $listTable = $('.lp-list-table');
            const $heading = $('.profile-heading');
            const $pagination = $('.custom-pagination');

            $listTable.hide();
            $heading.hide();
            $pagination.hide();

            $container.html('<p>Loading submissions...</p>').show();

            $.ajax({
                url: adminAjax,
                method: 'POST',
                data: {
                    action: 'load_assignment_submissions',
                    assignment_id: assignmentId
                },
                success: function(response) {
                    $container.html(response);

                // Add back button
                    const $backBtn = $('<button class="backtoassignment-cls-adding-here">Back to Assignments</button>');
                    $backBtn.on('click', function() {
                        $container.hide().empty();
                        $listTable.show();
                        $heading.show();
                        $pagination.show();
                    });
                    $container.prepend($backBtn);

                    window.scrollTo({ top: $container.offset().top - 100, behavior: 'smooth' });
                }
            });
        });

    // Delete Submission (delegated as well)
        $(document).on('click', '.delete-submission', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const userItemId = $btn.data('user-item-id');

            if (!confirm("Are you sure you want to delete this submission?")) return;

            const formData = new FormData();
            formData.append('action', 'lp_assignment_delete_submission');
            formData.append('user_item_id', userItemId);

            fetch(adminAjax, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    alert('Submission deleted.');
                    $btn.closest('.submission-row').remove();
                } else {
                    alert(response.data || 'Error deleting submission.');
                }
            });
        });
    });
</script>

<?php /* End list of Submission */

/* Start of check Submission */ ?>

<script>
    jQuery(document).ready(function($) {
        const adminAjax = '<?php echo admin_url('admin-ajax.php'); ?>';

    // Check Submission - Delegated for dynamic content
        $(document).on('click', '.check-submission', function(e) {
            e.preventDefault();

            const $btn = $(this);
            const assignmentId = $btn.data('assignment-id');
            const userId = $btn.data('user_id');
            const courseId = $btn.data('course_id');
            const userItemId = $btn.data('user_item_id');

            const $container = $('#assignment-submissions-container');
            const $listTable = $('.lp-list-table');
            const $heading = $('.profile-heading');
            const $pagination = $('.custom-pagination');

            $listTable.hide();
            $heading.hide();
            $pagination.hide();

            $container.html('<p>Loading evaluation form...</p>').show();

            $.post(adminAjax, {
                action: 'load_evaluate_submission_form',
                assignment_id: assignmentId,
                user_id: userId,
                course_id: courseId,
                user_item_id: userItemId
            }, function(response) {
                if (response.success) {
                    $container.html(response.data);

                // Add back button logic
                    $('#back-to-submissions').on('click', function() {
                        $container.hide().empty();
                        $listTable.show();
                        $heading.show();
                        $pagination.show();
                    });
                } else {
                    $container.html('<p>Error loading evaluation form.</p>');
                }
            }, 'json');
        });
    });
</script>

<?php // Save check submission form ?>

<script>
    jQuery(document).ready(function($) {
        const adminAjax = '<?php echo admin_url('admin-ajax.php'); ?>';

    // Delegated form submission for dynamically loaded evaluation form
        $(document).on('submit', '#evaluate-submission-form', function(e) {
            e.preventDefault();

            const $form = $(this);
            const formData = {
                action: 'save_evaluate_submission_form',
                assignment_id: $form.find('[name="assignment_id"]').val(),
                user_id: $form.find('[name="user_id"]').val(),
                course_id: $form.find('[name="course_id"]').val(),
                user_item_id: $form.find('[name="user_item_id"]').val(),
                grade: $form.find('[name="grade"]').val(),
                feedback: $form.find('[name="feedback"]').val()
            };

            $.post(adminAjax, formData, function(response) {
                if (response.success) {
                    alert(response.data || 'Evaluation saved!');
                } else {
                    alert('Error saving: ' + (response.data || 'Unknown error'));
                }
            }, 'json');
        });
    });
</script>

<?php /* End of Check Submission*/

else : ?>

    <!-- ========== STUDENT VIEW ========== -->

    <?php
    $filter_status = LP_Request::get_param( 'filter-status', 'all' );
    $curd          = new LP_Assignment_CURD();
    $query         = $curd->query_profile_assignments( $profile->get_user_data( 'id' ), $filter_status );
    $filters       = $curd->get_assignments_filters( $profile );
    $items         = $query->get_items();
    $tab_active    = $filter_status ?: 'all';
    $search_query = LP_Request::get_param( 'search' );

    // Apply search filter
    if ( $search_query ) {
        $items = array_filter( $items, function ( $item ) use ( $search_query ) {
            $course = CourseModel::find( $item->ref_id, true );
            return $course && stripos( $course->get_title(), $search_query ) !== false;
        });
    }
    ?>

    <?php if ( $filters ) : ?>
        <div class="learn-press-tabs">
            <ul class="lp-sub-menu learn-press-filters">
                <?php foreach ( $filters as $class => $url ) :
                    $class_active = ( $class === $tab_active ) ? 'active' : '';
                    ?>
                    <li class="<?php echo esc_attr( "$class $class_active" ); ?>">
                        <a href="#" class="assignment-filter-tab" data-filter="<?php echo esc_attr( $class ); ?>">
                            <?php echo esc_html( ucfirst( $class ) ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>


    <div class="lp-assignment-header">
        <h3 class="lp-assignment-title"><?php esc_html_e( 'Assignments', 'learnpress-assignments' ); ?></h3>
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

    <?php if ( ! empty( $items ) ) : ?>
        <div class="custom-table-wrapper">
            <table class="lp-list-table profile-list-assignments profile-list-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Assignment Name', 'learnpress-assignments' ); ?></th>
                        <th><?php esc_html_e( 'Due Date', 'learnpress-assignments' ); ?></th>
                        <th><?php esc_html_e( 'Note', 'learnpress-assignments' ); ?></th>
                        <th><?php esc_html_e( 'Grade', 'learnpress-assignments' ); ?></th>
                        <th><?php esc_html_e( 'Instructor Note', 'learnpress-assignments' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'learnpress-assignments' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $items as $user_assignment ) :
                        //global $wpdb;
                        $userAssigmentModel = new UserAssignmentModel( $user_assignment );
                        $assignment = AssignmentPostModel::find( $user_assignment->item_id, true );
                        $course     = CourseModel::find( $user_assignment->ref_id, true );
                        
                        


                        if ( ! $course || ! $assignment ) {
                            continue;
                        }

                        $status   = $user_assignment->status;
                        $due_date = get_post_meta( $assignment->get_id(), '_lp_duration', true );
                        $note     = get_post_meta( $assignment->get_id(), 'assignment_note', true );
                        $introduction = get_post_field( 'post_content', $assignment->get_id() );
                        $student_grade  = $userAssigmentModel->get_user_mark(); // student's achieved grade
                        $instructor_note = $userAssigmentModel->get_instructor_note();
                        $submitted_time = $userAssigmentModel->get_end_time();

                        ?>
                        <tr>
                            <td>
                                <div class="assignment-wrap">
                                    <a href="<?php echo esc_url( $course->get_item_link( $assignment->get_id() ) ); ?>">
                                        <?php echo esc_html( $assignment->get_the_title() ); ?>
                                    </a><br>
                                    <a href="<?php echo esc_url( $course->get_permalink() ); ?>">
                                        <span style="font-size: 0.9em; color: #1abc9c;">
                                            <?php echo esc_html( $course->get_title() ); ?>
                                        </span>
                                    </a>
                                </div>
                            </td>
                            <td><?php echo $due_date ? esc_html( date( 'd M Y', strtotime( $due_date ) ) ) : __( 'NA', 'learnpress-assignments' ); ?></td>
                            <td><?php echo $introduction ? esc_html( $introduction ) : __( 'NA', 'learnpress-assignments' ); ?></td>
                            <td><?php echo $student_grade ? esc_html( $student_grade ) : __( 'NA', 'learnpress-assignments' ); ?></td>
                            <td><?php echo $instructor_note ? esc_html( $instructor_note ) : __( 'NA', 'learnpress-assignments' ); ?></td>
                            <td>
                                <?php
                                $base_class = 'submit-button';
                                $status_class = ( $status === 'completed' ) ? 'submit-button-completed' : '';
                                $final_class = trim( "$base_class $status_class" );
                                ?>
                                <!-- <a href="<?php echo esc_url( $course->get_permalink() ); ?>" class="<?php echo esc_attr( $final_class ); ?>"> -->
                                    <a href="<?php echo esc_url( $course->get_item_link( $assignment->get_id() ) ); ?>" class="<?php echo esc_attr( $final_class ); ?>">
                                        <?php echo empty( $status ) ? __( '--', 'learnpress-assignments' ) : LP_Addon_Assignment::get_i18n_value( $status ); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <?php
            if ( $search_query ) {
                LearnPress\Helpers\Template::print_message( __( 'No assignments found for your search.', 'learnpress-assignments' ), 'info' );
            } else {
                LearnPress\Helpers\Template::print_message( __( 'No assignments!', 'learnpress-assignments' ), 'info' );
            }
            ?>
        <?php endif; ?>

    <?php endif; ?>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tabs = document.querySelectorAll('.assignment-filter-tab');
        const tableRows = document.querySelectorAll('.profile-list-assignments tbody tr');

        tabs.forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                const filter = this.getAttribute('data-filter');

            // Highlight the active tab
                tabs.forEach(t => t.closest('li').classList.remove('active'));
                this.closest('li').classList.add('active');

            // Show/hide rows
                tableRows.forEach(row => {
                const statusCell = row.querySelector('td:last-child a'); // Adjust if needed
                const statusText = statusCell ? statusCell.textContent.trim().toLowerCase() : '';

                if (filter === 'all' || statusText === filter.toLowerCase()) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            });
        });
    });
</script>
