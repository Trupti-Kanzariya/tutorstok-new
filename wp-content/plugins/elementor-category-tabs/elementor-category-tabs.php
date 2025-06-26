<?php
/**
 * Plugin Name: Elementor Category Tabs
 * Description: Custom Elementor widget for filtering posts by category using tabs.
 * Version: 1.0
 * Author: CMARIX
 */

if ( ! defined('ABSPATH')) exit; // Exit if accessed directly

function register_elementor_category_tabs($widgets_manager) {
	require_once(__DIR__ . '/widgets/category-tabs-widget.php');
	$widgets_manager->register(new \Elementor_Category_Tabs_Widget());
}

add_action('elementor/widgets/register', 'register_elementor_category_tabs');

// Enqueue scripts for AJAX filtering
function category_tabs_scripts() {
	wp_enqueue_style('category-tabs-style', plugin_dir_url(__FILE__) . 'assets/category-tabs.css');
	wp_enqueue_script('category-tabs-ajax', plugin_dir_url(__FILE__) . 'assets/category-tabs.js', ['jquery'], null, true);
	wp_localize_script('category-tabs-ajax', 'ajax_params', ['ajax_url' => admin_url('admin-ajax.php')]);
	wp_localize_script('category-tabs-ajax', 'ajax_params', [
		'ajax_url' => admin_url('admin-ajax.php')
	]);
}

add_action('wp_enqueue_scripts', 'category_tabs_scripts');

function filter_posts_by_category() {
    $category_slug = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $posts_per_page = 4;

    // Get "Read More" text from Admin Settings (Update option name)
    $read_more_text = get_option('custom_read_more_text', 'Read More'); 

    $query = new WP_Query([
        'category_name'  => $category_slug,
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'post_status'    => 'publish',
    ]);

    ob_start(); // Start output buffering

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_date = get_the_date('d M Y'); 
            $read_time = calculate_read_time(get_the_content());

            ?>
            <li class="post-box">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                    </div>
                <?php endif; ?>
                <div class="post-content">
                    <p class="post-meta">
                       <?php 
                        $categories = get_the_category();
                        if (!empty($categories)) {
                            $category_links = array();
                            echo '<span class="post-category">';
                            $limited_categories = array_slice( $categories, 0, 2 );
                            foreach ($limited_categories as $category) {
                            // $category_links[] = esc_html($category->name);
                                echo '<a class="' . esc_attr( $category->slug ) . '" href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( $category->name ) . '">' . esc_html( $category->name ) . '</a>';
                            }
                            echo '</span>';
                            echo implode(' ', $category_links); 
                        }
                        ?>
                    </p>

                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                    <p class="post-meta">
                        <span class="post-date"><?php echo esc_html($post_date); ?></span> | 
                        <span class="post-read-time"><?php echo esc_html($read_time); ?></span>
                    </p>
                     <?php if ($read_more_text) { ?>
                    <a href="<?php the_permalink(); ?>" class="read-more"><?php echo esc_html($read_more_text); ?></a>
                    <?php } ?>
                </div>
            </li>
            <?php
        }
    }

    $html = ob_get_clean(); 

    $has_more = ($query->max_num_pages > $paged);

    wp_reset_postdata();

    echo json_encode([
        'html'      => $html,
        'has_more'  => $has_more,
        'next_page' => $paged + 1,
        'read_more_text' => esc_html($read_more_text) // Send Read More text dynamically
    ]);

    wp_die();
}

add_action('wp_ajax_filter_posts_by_category', 'filter_posts_by_category');
add_action('wp_ajax_nopriv_filter_posts_by_category', 'filter_posts_by_category');


// Function to Calculate Read Time
function calculate_read_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $read_time = ceil($word_count / 200);
    return sprintf(_n('%s min read', '%s mins read', $read_time, 'your-text-domain'), $read_time);
}


