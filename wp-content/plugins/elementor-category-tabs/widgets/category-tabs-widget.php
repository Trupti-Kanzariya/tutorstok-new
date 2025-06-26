<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Elementor_Category_Tabs_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'category_tabs_widget';
	}

	public function get_title() {
		return __('Category Tabs', 'elementor-category-tabs');
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return ['general'];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Settings', 'elementor-category-tabs'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        // Post Type Selection
		$this->add_control(
			'post_type',
			[
				'label' => __('Select Post Type', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'post' => __('Posts', 'elementor-category-tabs'),
					'custom_post_type' => __('Custom Post Type', 'elementor-category-tabs'),
				],
				'default' => 'post',
			]
		);

        // Category Selection
		$categories = get_categories();
		$category_options = [];
		foreach ($categories as $category) {
			$category_options[$category->slug] = $category->name;
		}

		$this->add_control(
			'selected_categories',
			[
				'label' => __('Select Categories', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $category_options,
				'multiple' => true,
				'label_block' => true,
			]
		);

        // Number of Posts
		$this->add_control(
			'posts_per_page',
			[
				'label' => __('Number of Posts', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 5,
			]
		);

        // Read More Button Text
		$this->add_control(
			'read_more_text',
			[
				'label' => __('Read More Text', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __('Read More', 'elementor-category-tabs'),
			]
		);

        // Description Length
		$this->add_control(
			'description_length',
			[
				'label' => __('Description Length (words)', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 20,
			]
		);

		$this->end_controls_section();

        // Styling Options
		$this->start_controls_section(
			'style_section',
			[
				'label' => __('Style Settings', 'elementor-category-tabs'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

        // Tab Background Color (Non-Active)
		$this->add_control(
			'tab_bg_color',
			[
				'label' => __('Tab Background Color', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .category-tab' => 'background-color: {{VALUE}};',
				],
			]
		);

        // Tab Text Color (Non-Active)
		$this->add_control(
			'tab_text_color',
			[
				'label' => __('Tab Text Color', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .category-tab' => 'color: {{VALUE}};',
				],
			]
		);

        // Tab Active Background Color
		$this->add_control(
			'tab_active_bg_color',
			[
				'label' => __('Active Tab Background Color', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .category-tab.active' => 'background-color: {{VALUE}};',
				],
			]
		);

        // Tab Active Text Color
		$this->add_control(
			'tab_active_text_color',
			[
				'label' => __('Active Tab Text Color', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .category-tab.active' => 'color: {{VALUE}};',
				],
			]
		);


        // Tabs Background Color
		$this->add_control(
			'tabs_background_color',
			[
				'label' => __('Tabs Background Color', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .category-tabs' => 'background-color: {{VALUE}};',
				],
			]
		);

        // Tab Text Color
		$this->add_control(
			'tab_text_color',
			[
				'label' => __('Tab Text Color', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .category-tab' => 'color: {{VALUE}};',
				],
			]
		);

        // Post Title Color
		$this->add_control(
			'post_title_color',
			[
				'label' => __('Post Title Color', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-content h3 a' => 'color: {{VALUE}};',
				],
			]
		);

        // Post Content Color
		$this->add_control(
			'post_content_color',
			[
				'label' => __('Post Content Color', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-content p' => 'color: {{VALUE}};',
				],
			]
		);

        // Read More Button Colors
		$this->add_control(
			'read_more_text_color',
			[
				'label' => __('Read More Button Text Color', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .read-more' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_bg_color',
			[
				'label' => __('Read More Button Background', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .read-more' => 'background-color: {{VALUE}};',
				],
			]
		);
        // Load More Button Text Color
		$this->add_control(
			'load_more_text_color',
			[
				'label' => __('Load More Button Text Color', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .load-more' => 'color: {{VALUE}};',
				],
			]
		);

// Load More Button Background Color
		$this->add_control(
			'load_more_bg_color',
			[
				'label' => __('Load More Button Background', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .load-more' => 'background-color: {{VALUE}};',
				],
			]
		);

// Load More Button Hover Color
		$this->add_control(
			'load_more_hover_bg_color',
			[
				'label' => __('Load More Button Hover Background', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .load-more:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

        // Tab Alignment
		$this->add_control(
			'tab_alignment',
			[
				'label' => __('Tab Alignment', 'elementor-category-tabs'),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'elementor-category-tabs'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'elementor-category-tabs'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'elementor-category-tabs'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .category-tab-links' => 'text-align: {{VALUE}};',
				],
			]
		);

        // Tab Typography
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'tab_typography',
				'label' => __('Tab Typography', 'elementor-category-tabs'),
				'selector' => '{{WRAPPER}} .category-tab',
			]
		);


		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$categories = !empty($settings['selected_categories']) ? $settings['selected_categories'] : [];
		$post_type = $settings['post_type'];

		if (!empty($categories)) {
			echo '<div class="category-tabs">';
			echo '<ul class="category-tab-links">';

			foreach ($categories as $index => $category_slug) {
				$category = get_category_by_slug($category_slug);
				if ($category) {
					$post_count = get_category($category->term_id)->count;
					if ($post_count > 0) {
						echo '<li><a href="#" class="category-tab ' . ($index === 0 ? 'active' : '') . '" data-category="' . esc_attr($category_slug) . '">' . esc_html($category->name) . '</a></li>';
					}
				}
			}

			echo '</ul>';
			echo '<div class="category-tab-content">';
			
			echo '<div class="loader" style="display: none;">
			
			<img class="loader-images" src="'. get_home_url() .'/wp-content/uploads/2025/04/pause-gIF-image-ezgif.com-gif-maker.gif" alt="Loading..." />
			</div>';


			echo '<ul id="post-list" class="post-list">';
			$this->load_category_posts($categories[0], $settings, $post_type);
			echo '</ul>'; 
			echo '<div class="load-more-container"></div>';
			echo '</div>';
			echo '</div>';
		}
	}


	public function load_category_posts($category_slug, $settings, $post_type) {
		$posts_per_page = !empty($settings['posts_per_page']) ? intval($settings['posts_per_page']) : 4;
		$query = new WP_Query([
			'post_type'      => $post_type,
			'category_name'  => $category_slug,
			'posts_per_page' => $posts_per_page,
			'post_status'    => 'publish',
			'paged'          => 1,
		]);

		if ($query->have_posts()) {
			// echo '<ul class="post-list">';

			while ($query->have_posts()) {
				$query->the_post();
				$categories = get_the_category();
				$category_name = !empty($categories) ? esc_html($categories[0]->name) : ''; 
            $post_date = get_the_date('d M Y'); // Formats date as "08 Jan 2025"
            $read_time = $this->calculate_read_time(get_the_content()); // Calculate read time

            ?>
            <li class="post-box">
            	<?php if (has_post_thumbnail()) : ?>
            		<div class="post-thumbnail">
            			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
            		</div>
            	<?php endif; ?>
            	<div class="post-content">
            		<!-- Display Meta Information -->
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
            		<p><?php echo wp_trim_words(get_the_excerpt(), $settings['description_length']); ?></p>
            		<p class="post-meta">

            			<span class="post-date"><?php echo esc_html($post_date); ?></span> | 
            			<span class="post-read-time"><?php echo esc_html($read_time); ?></span>
            		</p>
            		<?php if ($settings['read_more_text']) { ?>
            			<a href="<?php the_permalink(); ?>" class="read-more"><?php echo esc_html($settings['read_more_text']); ?></a>
            		<?php } ?>
            	</div>
            </li>
            <?php
        }
        // echo '</ul>';
        echo '<div class="load-more-container">';
        if ($query->max_num_pages > 1) {
        	echo '<button class="load-more" data-category="' . esc_attr($category_slug) . '" data-page="2">Load More</button>';
        }
        echo '</div>';
    } else {
    	echo '<p>No posts found.</p>';
    }
    wp_reset_postdata();
}

private function calculate_read_time($content) {
	$word_count = str_word_count(strip_tags($content));
    $read_time = ceil($word_count / 200); // Assuming 200 words per minute
    return sprintf(_n('%s min read', '%s mins read', $read_time, 'your-text-domain'), $read_time);
}


} ?>

