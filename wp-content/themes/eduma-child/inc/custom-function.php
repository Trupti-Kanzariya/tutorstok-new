<?php

/**
 * Shortcode: Get All Courses + Ratings for an Instructor
 * Usage: [instructor_courses_with_ratings instructor_id="123"]
 */
function lp_instructor_courses_ratings_shortcode( $atts ) {

	$request_uri = $_SERVER['REQUEST_URI'];
	$parts       = explode('/', trim($request_uri, '/'));
	$username    = end( $parts );
	$user        = get_user_by( 'slug', $username );

	if (!$user) {
		return '<p>Could not get instructor from URL.</p>';
	}
	$user_id = $user->ID;

	ob_start();
	$courses = new WP_Query([
		'post_type'      => 'lp_course',
		'author'         => $user_id,
		'posts_per_page' => -1,
	]);

	$total_courses = $courses->found_posts;
	$total_reviews = 0;
	$total_rating  = 0;
	$rating_counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
	$all_reviews   = [];

	if ( $courses->have_posts() ) {
		while ( $courses->have_posts() ) {
			$courses->the_post();
			$course_id = get_the_ID();

			$reviews = get_comments([
				'post_id' => $course_id,
				'status'  => 'approve',
				'type'    => 'review',
			]);

			foreach ( $reviews as $review ) {
				$rating = (int) get_comment_meta( $review->comment_ID, '_lpr_rating', true );
				if ($rating >= 1 && $rating <= 5) {
					$total_rating += $rating;
					$rating_counts[$rating]++;
					$total_reviews++;
				}
				$all_reviews[] = $review;
			}
		}
		wp_reset_postdata();

		$average_rating = ($total_reviews > 0) ? round($total_rating / $total_reviews, 1) : 0;

		echo '<div class="instructor-summary-box">';
		echo '<div class="instructor-stats">';
		echo '<div class="review-wrapper"><h3 class="review-title">Reviews</h3>';
		 // Trigger modal display
		echo '<button class="write-review-btn" onclick="openReviewModal()">Write a Review</button></div>';

        // Modal HTML structure for review
		echo '<div id="writeReviewModal" class="modal">';
		echo '<div class="modal-content">';
		echo '<span class="close-btn" onclick="closeReviewModal()">&times;</span>';
		echo '<div class="modal-body">';
		echo '<h3 class="modal-title">Review</h3>';

		if ( comments_open( $course_id ) ) {
			
			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				$user_name = $current_user->display_name;
				$user_avatar = get_avatar_url($current_user->ID);

				comment_form([
					'title_reply'         => 'Please share your opinion',
					'label_submit'        => 'Submit',
					'comment_notes_after' => wp_nonce_field('submit_instructor_review', '_wpnonce', true, false),
					'fields'              => [],
					'comment_field'       => '<p class="comment-form-rating">
					<div class="star-rating-wrap">
					<input type="hidden" name="rating" id="rating" required />
					<div class="star-rating">
					<span data-value="1">&#9733;</span>
					<span data-value="2">&#9733;</span>
					<span data-value="3">&#9733;</span>
					<span data-value="4">&#9733;</span>
					<span data-value="5">&#9733;</span>
					</div>
					</div>
					<span class="error-message rating-error" style="color: red; display: none;">Please select a rating.</span>
					</p>

					<p class="comment-form-comment">
					<label for="comment">Your Review</label>
					<textarea id="comment" name="comment" placeholder="Enter here" maxlength="500" required></textarea>
					<span class="char-count" style="display:block; margin-top:5px;">0 / 500</span>
					<span class="error-message comment-error" style="color: red; display: none;">Please enter your review.</span>
					
					</p>',
					'hidden_fields'       => [
						'comment_post_ID' => $course_id
					]
				], $course_id);

			} else {
				echo '<p class="no-login">You must be logged in to leave a review.</p>';
			}
		} else {
			echo '<p class="comment-closed">Comments are closed for this course.</p>';
		}
	?>  <script>
		jQuery(document).ready(function($) {
			$('.close-btn').on('click', function() {
				$('#writeReviewModal').hide();
				$('#rating').val('');
				$('.star-rating span').removeClass('selected');
				$('#comment').val('');
				$('.char-count').text('0 / 500');
				$('.error-message').hide();  
				$('.comment-limit-error').hide();  
				$('.rating-error').hide();  
				$('.comment-error').hide();  
			});

			$('.star-rating span').on('click', function() {
				var value = $(this).data('value');
				$(this).parent().find('span').removeClass('selected');
				$(this).prevAll().addBack().addClass('selected');
				$('#rating').val(value);
			});

			$('#comment').on('input', function() {
				var maxLength = 500; 
				var currentLength = $(this).val().length;
				$('.char-count').text(currentLength + ' / ' + maxLength);
				if (currentLength >= maxLength) {
					$('.comment-limit-error').show();
				} else {
					$('.comment-limit-error').hide();
				}
			});

			$('#commentform').on('submit', function(e) {
				e.preventDefault();

				var form = $(this);
				var rating = $('#rating').val();
				var comment = $('#comment').val().trim();
		        var maxLength = 501;  // Max length is 500 characters
		        $('.error-message').hide();

		        let hasError = false;

		        if (!rating) {
		        	$('.rating-error').show();
		        	hasError = true;
		        }
		        if (!comment) {
		        	$('.comment-error').show();
		        	hasError = true;
		        } else if (comment.length >= maxLength) {
		            $('.comment-limit-error').show();  // Show validation error if limit is exceeded
		            hasError = true;
		        }

		        if (hasError) {
		        	return;
		        }
		        var formData = form.serialize();

		        $.ajax({
		        	type: 'POST',
		        	url: '<?php echo admin_url('admin-ajax.php'); ?>',
		        	data: formData + '&action=submit_instructor_review',
		        	beforeSend: function() {
		        		form.find('input[type="submit"]').prop('disabled', true).val('Submitting...');
		        	},
		        	success: function(response) {
		        		if (response.success) {
		        			$('.reviews-list').prepend(response.data.html);                        
		        			form.hide();                        
		        			form.after('<p class="review-success-message" style="color: green; font-weight: 600;">Your Review has been submitted.</p>');
		        			setTimeout(function() {
		        				location.reload();
		        			}, 2000);
		        		} else {
		        			alert(response.data);
		        		}
		        	},
		        	error: function() {
		        		alert('An error occurred. Please try again.');
		        	},
		        	complete: function() {
		        		form.find('input[type="submit"]').prop('disabled', false).val('Submit');
		        	}
		        });
		    });
		});

		function openReviewModal() {
			document.getElementById('writeReviewModal').style.display = 'block';
		}

		function closeReviewModal() {
			document.getElementById('writeReviewModal').style.display = 'none';
		}
		</script><?php
		echo '</div>';
		echo '</div>';
        echo '</div>'; // End modal

        echo '<div class="rating-main"><div class="rating-block"><h4 class="rating-title">Average Ratings</h4><div class="rating-box">';
        echo '<div class="average-value">' . esc_html($average_rating) . '</div>';
        echo '<div class="star-rating">';

        $full_stars = floor( $average_rating );
        $half_star = ( $average_rating - $full_stars ) >= 0.5;
        $empty_stars = 5 - $full_stars - ( $half_star ? 1 : 0 );

        for ( $i = 0; $i < $full_stars; $i++ ) {
        	echo '★';
        }
        if ( $half_star ) {
        	echo '½'; 
        }
        for ( $i = 0; $i < $empty_stars; $i++ ) {
        	echo '☆';
        }

        echo '</div>';
        echo '<div class="review-amount">' . esc_html($average_rating) . ' Rating</div></div></div>';
        echo '<div class="ratings-summary"><h4 class="rating-title">Detailed Ratings</h4><div class="rating-box">';
        echo '<div class="detailed-ratings">';
        for ( $i = 5; $i >= 1; $i-- ) {
        	$count = $rating_counts[$i];
        	$percent = ($total_reviews > 0) ? round(( $count / $total_reviews ) * 100) : 0;
        	echo '<div class="rating-bar">';
        	echo '<span>' . $i . ' ★</span>';
        	echo '<div class="bar"><div class="fill" style="width: ' . $percent . '%;"></div></div>';
        	echo '<span>' . $percent . '%</span>';
        	echo '</div>';
        }
        echo '</div>';
        echo '</div></div></div>';

        if ( ! empty( $all_reviews ) ) {
        	echo '<div class="reviews-list">';
        	foreach ( $all_reviews as $review ) {
        		$rating = get_comment_meta($review->comment_ID, '_lpr_rating', true);
        		echo '<div class="review-item">';
        		echo get_avatar( $review, 48 );
        		echo '<div class="review-content">';
        		echo '<div class="review-head"><strong>' . ucfirst( esc_html( $review->comment_author ) ) . '</strong>';

        		echo '<div class="stars">';
        		echo number_format( (float) $rating, 1 ) . ' ';

        		for ( $i = 1; $i <= 5; $i++ ) {
        			if ( $rating >= $i ) {
        				echo '<i class="fas fa-star"></i>';
        			} elseif ( $rating >= ($i - 0.5) ) {
        				echo '<i class="fas fa-star-half-alt"></i>';
        			} else {
        				echo '<i class="far fa-star"></i>';
        			}
        		}
        		echo '</div></div>';
        		echo '<p class="review-desc">' . esc_html( $review->comment_content ) . '</p>';
        		echo '</div>';
        		echo '</div>';
        	}
        	echo '</div>';
        }
		echo '</div>'; // .instructor-summary-box
	} else {
		echo '<p>No courses found for this instructor.</p>';
	}

	return ob_get_clean();
}
add_shortcode( 'instructor_courses_with_ratings', 'lp_instructor_courses_ratings_shortcode' );

add_action('comment_post', 'save_lp_review_rating');
function save_lp_review_rating($comment_id) {
	if (isset($_POST['rating']) && $_POST['rating'] >= 1 && $_POST['rating'] <= 5) {
		update_comment_meta($comment_id, '_lpr_rating', intval($_POST['rating']));
	}
}

function redirect_after_comment( $location, $comment ) {
	return add_query_arg( 'review_submitted', '1', get_comment_link( $comment ) );
}
add_filter( 'comment_post_redirect', 'redirect_after_comment', 10, 2 );


add_action('template_redirect', function () {
	if (isset($_POST['wp-submit']) && isset($_POST['log'])) {
		if (!session_id()) {
			session_start();
		}
		$creds = array(
			'user_login'    => sanitize_user($_POST['log']),
			'user_password' => $_POST['pwd'],
			'remember'      => isset($_POST['rememberme'])
		);
		$user = wp_signon($creds, false);
		if (is_wp_error($user)) {
			$_SESSION['login_error'] = $user->get_error_message();
			wp_redirect(wp_get_referer());
			exit;
		} else {
			// Check user role
			if (!in_array('administrator', (array) $user->roles)) {
				wp_redirect(home_url()); // or use learn_press_user_profile_link($user->ID)
				exit;
			}
		}
	}

	if (!isset($_POST['wp-submit']) && isset($_SESSION['login_error'])) {
		unset($_SESSION['login_error']);
	}
});

add_action( 'comment_post', function ( $comment_id ) {
	if ( isset( $_POST['rating'] ) ) {
		add_comment_meta( $comment_id, '_lpr_rating', intval($_POST['rating']) );
	}
});


function custom_comment_form_button_text($defaults) {
    $defaults['label_submit'] = 'Submit'; // Change text here
    return $defaults;
}
add_filter('comment_form_defaults', 'custom_comment_form_button_text');
// Add this to your functions.php or plugin file
add_action('wp_ajax_submit_instructor_review', 'handle_instructor_review_submission');
add_action('wp_ajax_nopriv_submit_instructor_review', 'handle_instructor_review_login_required');

function handle_instructor_review_submission() {
	if (!is_user_logged_in()) {
		wp_send_json_error('You must be logged in to leave a review.');
	}

	if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'submit_instructor_review')) {
		wp_send_json_error('Security check failed.');
	}

	$comment_content = isset($_POST['comment']) ? trim($_POST['comment']) : '';
	$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
	$course_id = isset($_POST['comment_post_ID']) ? (int)$_POST['comment_post_ID'] : 0;

	if (empty($comment_content) || $rating < 1 || $rating > 5 || !$course_id) {
		wp_send_json_error('Please fill all required fields.');
	}

	$user = wp_get_current_user();
	$comment_data = array(
		'comment_post_ID' => $course_id,
		'comment_author' => $user->display_name,
		'comment_author_email' => $user->user_email,
		'comment_content' => $comment_content,
		'comment_type' => 'review',
		'user_id' => $user->ID,
		'comment_approved' => 1,
	);
	add_comment_meta($comment_data, '_lpr_rating', $rating);
	$comment_id = wp_insert_comment($comment_data);

	if ($comment_id) {
		update_comment_meta($comment_id, '_lpr_rating', $rating);

        // Get the new review HTML
		$review = get_comment($comment_id);
		ob_start();
		?>
		<div class="review-item">
			<?php echo get_avatar($review, 48); ?>
			<div class="review-content">
				<div class="review-head">
					<strong><?php echo ucfirst(esc_html($review->comment_author)); ?></strong>
					<div class="stars">
						<?php echo number_format($rating, 1) . ' '; ?>
						<?php for ($i = 1; $i <= 5; $i++): ?>
							<?php if ($rating >= $i): ?>
								<i class="fas fa-star"></i>
							<?php elseif ($rating >= ($i - 0.5)): ?>
								<i class="fas fa-star-half-alt"></i>
							<?php else: ?>
								<i class="far fa-star"></i>
							<?php endif; ?>
						<?php endfor; ?>
					</div>
				</div>
				<p class="review-desc"><?php echo esc_html($review->comment_content); ?></p>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		wp_send_json_success(array(
			'message' => 'Review submitted successfully!',
			'html' => $html
		));
	} else {
		wp_send_json_error('Error submitting review.');
	}
}

function handle_instructor_review_login_required() {
	wp_send_json_error('You must be logged in to leave a review.');
}

function custom_add_loader_to_footer() {
	?>
	<div class="form-loader" style="display: none;">
		<img class="loader-images" src="<?php echo esc_url( get_home_url() ); ?>/wp-content/uploads/2025/04/pause-gIF-image-ezgif.com-gif-maker.gif" alt="Loading..." />
	</div>
	<?php
}
add_action( 'wp_footer', 'custom_add_loader_to_footer' );

add_action('wp_ajax_nopriv_check_user_email_exists', 'check_user_email_exists_callback');
add_action('wp_ajax_check_user_email_exists', 'check_user_email_exists_callback');

function check_user_email_exists_callback() {
	$user_input = sanitize_text_field($_POST['user_login']);
	$user = get_user_by('email', $user_input);
	if (!$user) {
		$user = get_user_by('login', $user_input);
	}

	wp_send_json([
		'exists' => $user ? true : false
	]);
}

add_filter( 'wp_nav_menu_objects', 'replace_login_with_avatar_and_name', 10, 2 );

function replace_login_with_avatar_and_name( $items, $args ) {
	foreach ( $items as &$item ) {
		if ( in_array( 'login-btn', $item->classes ) && is_user_logged_in() ) {
			$user = wp_get_current_user();

			if ( in_array( 'administrator', (array) $user->roles ) ) {
				continue;
			}

			$avatar = get_avatar( $user->ID, 32 );
			$name = esc_html( ucfirst( strtolower( $user->display_name ) ) );

			$profile_slug = $user->user_login;
			$overview_url = home_url( "/lp-profile/{$profile_slug}/" );

			$item->title = $avatar . ' ' . $name;
			$item->url = $overview_url;
			$item->classes[] = 'user-avatar-name';
		}
	}
	return $items;
}


add_action( 'wp_login', 'force_lp_profile_redirect_on_login', 10, 2 );

function force_lp_profile_redirect_on_login( $user_login, $user ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}
	if ( in_array( 'administrator', (array) $user->roles ) ) {
		return;
	}
	if ( function_exists( 'learn_press_user_profile_link' ) ) {
		$lp_profile_url = learn_press_user_profile_link( $user->ID );
		$overview_url = trailingslashit( $lp_profile_url ) . '/';
		wp_redirect( $overview_url );
		exit;
	}
}


function child_enqueue_logout_popup_script() {
	?>
	<!-- Logout Confirm Modal -->
	<div id="logout-confirm-modal" class="logoutmodal-cls-adding">
		<div class="logout-modal-content-cls-adding">
			<div class="modal-type-close-button-here-cls-adding">
				<button id="modal-close-button" aria-label="<?php esc_attr_e( 'Close'); ?>" class="logout-modalclose-ics">
					<span class="close-btn-new-cls-adding-here">×</span>
				</button>
			</div>
			<div class="logout-ics-cls-adding-here">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/Group 223.png" alt="<?php esc_attr_e( 'Logout Icon' ); ?>" class="logout-all-ics">
			</div>
			<div class="heading-confirm-type-here">
				<h2><?php _e( 'Confirm Logout' ); ?></h2>
			</div>
			<div class="paragraph-confirm-type-here">
				<p><?php _e( 'Are you sure you want to log out?' ); ?><br/>
					<?php _e( 'You will need to log in again to access your account.'); ?></p>
				</div>
				<div class="logout-cancel-cls-adding-here">
					<div class="cancel-popup-cls-adding-here">
						<button id="cancel-logout" class="samefor-btn-both-cls-adding"><?php _e( 'Cancel' ); ?></button>
					</div>
					<div class="confirm-popup-cls-adding-here">
						<button id="confirm-logout" class="samefor-btn-both-cls-adding"><?php _e( 'Logout' ); ?></button>
					</div>
				</div>
			</div>
		</div>

		<?php
	}
	add_action('wp_footer', 'child_enqueue_logout_popup_script', 100);

	add_action('wp_logout', 'custom_logout_redirect');
	function custom_logout_redirect() {
		wp_redirect(home_url());
		exit();
	}
	function current_course_category_shortcode() {
		if ( ! is_singular( 'lp_course' ) ) return '';

		$terms = get_the_terms( get_the_ID(), 'course_category' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$term = reset( $terms );
			return '<span class="current-course-category">' . esc_html( $term->name ) . '</span>';
		}
		return '<span class="current-course-category uncategorized">Uncategorized</span>';
	}
	add_shortcode( 'current_course_category', 'current_course_category_shortcode' );


// 14 - 04 - 2025
	add_filter('learn-press/profile-tabs', 'custom_lp_add_settings_section_first', 20);

	function custom_lp_add_settings_section_first($tabs) {
		if (isset($tabs['settings'])) {
			$profile_section = array(
				'profile' => array(
                'title'    => __('Profile', 'textdomain'), // Fixed typo here
                'slug'     => 'profile',
                'callback' => function () {
                	include get_stylesheet_directory() . '/learnpress/profile/tabs/settings/dashboard.php';
                },
                'priority' => 1,
            )
			);

			$notification_section = array(
				'notification-setting' => array(
					'title'    => __('Notification', 'textdomain'),
					'slug'     => 'notification-setting', 
					'callback' => function () {
						include get_stylesheet_directory() . '/learnpress/profile/tabs/settings/notification.php';
					},
					'priority' => 999,
				)
			);
			$tabs['settings']['sections'] = $profile_section + $tabs['settings']['sections'] + $notification_section;
		}
		return $tabs;
	}

	add_action('pre_get_posts', 'set_default_lp_assignment_order_desc');
	function set_default_lp_assignment_order_desc($query) {  
		if (is_admin() && $query->is_main_query()) {
			if (isset($_GET['post_type']) && $_GET['post_type'] === 'lp_assignment') {

				if (!isset($_GET['orderby']) || !isset($_GET['order'])) {
					$query->set('orderby', 'date');
					$query->set('order', 'DESC');  
				}
			}
		}
	}

add_action('wp_ajax_load_questions_page', 'load_questions_page_callback');
add_action('wp_ajax_nopriv_load_questions_page', 'load_questions_page_callback');

function load_questions_page_callback() {
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $per_page = 5;
    $current_user_id = get_current_user_id();

    // Get all questions for the current user
    $questions = get_posts([
        'post_type' => 'lp_question',
        'author' => $current_user_id,
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ]);

    $total_questions = count($questions);
    $total_pages = ceil($total_questions / $per_page);
    $offset = ($paged - 1) * $per_page;
    $paginated_questions = array_slice($questions, $offset, $per_page);

    ob_start();
    foreach ($paginated_questions as $question) {
        ?>
        <tr>
            <td><?php echo esc_html($question->post_title); ?></td>
            <td><?php echo esc_html(get_post_meta($question->ID, '_lp_type', true)); ?></td>
            <td><?php echo esc_html(get_the_author_meta('display_name', $question->post_author)); ?></td>
            <td>
                <?php
                $used_in = [];
                $quizzes = get_posts([
                    'post_type' => 'lp_quiz',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                ]);
                foreach ($quizzes as $quiz) {
                    $questions_data = get_post_meta($quiz->ID, '_lp_questions', true);
                    if (!empty($questions_data['questions'])) {
                        foreach ($questions_data['questions'] as $q) {
                            if ($q['question_id'] == $question->ID) {
                                $used_in[] = '<a href="' . get_permalink($quiz->ID) . '" target="_blank">' . esc_html($quiz->post_title) . '</a>';
                            }
                        }
                    }
                }
                echo !empty($used_in) ? implode(', ', $used_in) : '—';
                ?>
            </td>
            <td>
                <button class="edit-question-btn edit-assignment-btn bg-transparent-cls-adding-here-common" data-question-id="<?php echo $question->ID; ?>"></button>
                <button class="delete-question-btn delete-assignment bg-transparent-cls-adding-here-common" data-question-id="<?php echo $question->ID; ?>"></button>
            </td>
        </tr>
        <?php
    }
    $table_rows = ob_get_clean();

    // Output pagination links as HTML (optional)
    ob_start();
    if ($total_pages > 1) {
        echo '<div class="lp-pagination custom-pagination">';
        for ($i = 1; $i <= $total_pages; $i++) {
            $active_class = ($i == $paged) ? 'current' : '';
            echo '<a href="javascript:void(0);" class="pagination-link ajax-pagination-link ' . $active_class . '" data-page="' . $i . '">' . $i . '</a>';
        }
        echo '</div>';
    }
    $pagination_html = ob_get_clean();

    wp_send_json_success([
        'html' => $table_rows,
        'pagination' => $pagination_html,
    ]);
}

add_action('wp_ajax_load_quizzes_pagination', 'load_quizzes_pagination_callback');
add_action('wp_ajax_nopriv_load_quizzes_pagination', 'load_quizzes_pagination_callback');
function load_quizzes_pagination_callback() {
    $paged    = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 5;
    $user_id  = get_current_user_id();

    $all_quizzes = get_posts([
        'post_type'      => 'lp_quiz',
        'author'         => $user_id,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ]);

    $total_quizzes = count($all_quizzes);
    $total_pages = ceil($total_quizzes / $per_page);

    $offset  = ($paged - 1) * $per_page;
    $quizzes = array_slice($all_quizzes, $offset, $per_page);

    ob_start();
    foreach ($quizzes as $quiz): 
        $duration_meta = get_post_meta($quiz->ID, '_lp_duration', true);
        $duration_value = is_array($duration_meta) ? intval($duration_meta[0]) : intval($duration_meta);
        $duration_unit  = is_array($duration_meta) && isset($duration_meta[1]) ? ucfirst($duration_meta[1]) : 'Minute(s)';
        $duration_display = $duration_value > 0 ? "$duration_value $duration_unit" : 'No limit';

        $questions = get_post_meta($quiz->ID, '_lp_questions', true);
        $question_count = is_array($questions) ? count($questions) : 0;
        ?>
        <tr>
            <td><?= esc_html($quiz->post_title); ?></td>
            <td><?= esc_html($duration_display); ?></td>
            <td><?= esc_html($question_count); ?></td>
            <td class="removebg-for-allics-cls-adding-here">
                <button class="edit-quiz-btn edit-assignment-btn common-for-allics-cls-adding-here" data-quiz-id="<?= $quiz->ID ?>"></button>
                <button class="delete-quiz-btn delete-assignment common-for-allics-cls-adding-here" data-quiz-id="<?= $quiz->ID ?>"></button>
                <button class="duplicate-quiz-btn duplicate-submission-cls-adding common-for-allics-cls-adding-here" data-quiz-id="<?= $quiz->ID ?>"></button>
            </td>
        </tr>
    <?php endforeach;
    $table_rows = ob_get_clean();

    // Generate pagination HTML
    ob_start();
   

   if ($total_pages > 1): ?>
        <div class="lp-pagination custom-pagination">
           <?php for ( $i = 1; $i <= $total_pages; $i++ ) :
                $active_class = ( $i == $paged ) ? 'current' : '';
                ?>
                <a href="?paged=<?php echo $i; ?>" class="pagination-link quiz-pagination-link <?php echo $active_class; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; 
    $pagination_html = ob_get_clean();

    wp_send_json_success([
        'html' => $table_rows,
        'pagination' => $pagination_html,
    ]);
}

function display_all_tutors_info() {
		$args = array(
			'role'    => 'lp_teacher',  
			'orderby' => 'display_name',
			'order'   => 'DESC',
			'number'  => 18, 
			'include' => array(19, 17, 20, 129, 129, 130, 131),
		);
 
		$user_query = new WP_User_Query($args);
 
		if (!empty($user_query->results)) {
			ob_start();
 
			foreach ($user_query->results as $user) {
				$avatar = get_avatar($user->ID, 96);
 
				$name = $user->display_name;
				$user_description = get_user_meta($user->ID, 'description', true);
				$courses = get_posts(array(
					'post_type' => 'lp_course',
					'author'    => $user->ID,
					'posts_per_page' => -1,
				));
 
				$total_rating = 0;
				$total_reviews = 0;
 
				foreach ($courses as $course) {
 
					$comments = get_comments(array(
						'post_id' => $course->ID,  
						'status'  => 'approve',    
						'type'    => 'review',     
					));
 
					foreach ($comments as $comment) {
                	// var_dump($comment);
						$rating = get_comment_meta($comment->comment_ID, '_lpr_rating', true);
						if ($rating) {
							$total_rating += (int) $rating;
							$total_reviews++;
						}
					}
				}
 
				$average_rating = ($total_reviews > 0) ? round($total_rating / $total_reviews, 1) : 0;
 
				if ($average_rating == 0) {
					$average_rating = 0;
				}
 
            // Get the tutor's URL
				$tutor_slug = $user->user_nicename;
				$tutor_url = home_url("/instructor/{$tutor_slug}/");
 
				?>
					<div class="tutor-info new-tutor-stoke-info-cls-adding">
					<div class="tutor-avatar new-tutor-avatar-cls-adding">
					<?php echo $avatar; ?>
					<div class="tutor-details new-tutor-details-cls-adding">
					<h3 class="sub-heading-our-tutorstok"><a href="<?php echo esc_url($tutor_url); ?>" class="anchor-link-custom-our"><?php echo esc_html($name); ?></a></h3>
					<p><?php echo $user_description; ?></p>
					<div class="star-rating new-star-rating-custom">
						<span class="rating-number">(<?php echo esc_html($average_rating); ?>)</span>
					<?php
							$full_stars = floor($average_rating);
							$has_half_star = ($average_rating - $full_stars) >= 0.25 && ($average_rating - $full_stars) < 0.75;
							$empty_stars = 5 - $full_stars - ($has_half_star ? 1 : 0);
				
							for ($i = 0; $i < $full_stars; $i++) {
								echo '<span class="star full new-custom-star-rating">&#9733;</span>';
							}
 
							if ($has_half_star) {
								echo '<span class="star half new-custom-star-rating-half">&#9733;</span>';
							}
 
							for ($i = 0; $i < $empty_stars; $i++) {
								echo '<span class="star empty new-custom-star-rating-empty">&#9733;</span>';
							}
							?>

</div>
</div>
</div>

</div>
<?php
		}
 
		return ob_get_clean();
	} else {
 
		return '<p class="no-tutor-found-added">No tutors found.</p>';
	}
}
 
add_shortcode('all_tutors_info', 'display_all_tutors_info');


/**
 * Print breadcrumbs
 */
if ( ! function_exists( 'thim_print_breadcrumbs' ) ) {
	function thim_print_breadcrumbs() {
		?>
		<div class="breadcrumbs-wrapper new-bredcum-cls-adding-before-after-remove">
			<div class="container">
				<?php
				if ( is_page( 'lp-profile' ) && is_user_logged_in() ) {
					$profile = learn_press_get_profile();
					$user    = $profile ? $profile->get_user() : wp_get_current_user();
					$current_tab = learn_press_get_current_profile_tab();
					$current_subtab = learn_press_get_current_profile_tab( 'sub' );
					$breadcrumbs = array();
					if ( ! $current_tab || $current_tab === 'overview' ) {
						$breadcrumbs[] = __( 'Overview', 'learnpress' );
						$display_name = method_exists( $user, 'get_data' ) ? $user->get_data( 'display_name' ) : $user->display_name;

						$formatted_name = ucfirst( strtolower( $display_name ) );
						$breadcrumbs[] = esc_html( $formatted_name );
					} else {

						global $wp;
						$segments = explode( '/', trim( $wp->request, '/' ) );
						$username_slug = '';
						if ( isset( $segments[1] ) && $segments[0] === 'lp-profile' ) {
							$username_slug = $segments[1];
							$current_user = wp_get_current_user(); // logged-in user
							if ( in_array( 'parent', (array) $current_user->roles ) && $username_slug === $current_user->user_nicename ) {
								$profile_url = home_url( "/lp-profile/{$username_slug}/children/" );
							} else {
								$profile_url = home_url( "/lp-profile/{$username_slug}/" );
							}
							$breadcrumbs[] = '<a href="' . esc_url( $profile_url ) . '">' . __( 'Overview', 'learnpress' ) . '</a>';
						}
						if ( $current_tab ) {
							$breadcrumbs[] = '<span>' . esc_html( thim_format_tab_label( $current_tab ) ) . '</span>';
						}
						if ( $current_subtab && $current_subtab !== $current_tab ) {
							$breadcrumbs[] = '<span>' . esc_html( thim_format_tab_label( $current_subtab ) ) . '</span>';
						}
					}
					echo '<div id="breadcrumbs" class="bredcum-point-cls-adding-here" style="display: flex; flex-wrap: wrap; align-items: center;">';
					foreach ( $breadcrumbs as $index => $crumb ) {
						if ( $index === 0 ) {
							echo '<div class="breadcrumb-item-overview">' . $crumb . '</div>';
						} elseif ( $index === 1 ) {
							echo '<div class="breadcrumb-item-name">' . $crumb . '</div>';
						} elseif ( $index === 2 ) {
							echo '<div class="breadcrumb-item-subtab">' . $crumb . '</div>';
						} else {
							echo '<div class="breadcrumb-item">' . $crumb . '</div>';
						}

						if ( $index < count( $breadcrumbs ) - 1 ) {
							echo '<div class="breadcrumb-separator" style="margin: 0 5px;">/</div>';
						}
					}
					echo '</div>';
				} else {
					$wpseo = get_option( 'wpseo_titles' );
					if ( ( class_exists( 'WPSEO' ) || class_exists( 'WPSEO_Premium' ) ) && $wpseo['breadcrumbs-enable'] && function_exists( 'yoast_breadcrumb' ) ) {
						yoast_breadcrumb( '<div id="breadcrumbs">', '</div>' );
					} elseif ( function_exists( 'thim_use_bbpress' ) && thim_use_bbpress() ) {
						bbp_breadcrumb();
					} else {
						do_action( 'thim_breadcrumbs' );
					}
				}
				?>
			</div>
		</div>
		<?php
	}
}