<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class LP_Course_Material_Widget extends Widget_Base {

    public function get_name() {
        return 'lp_course_materials';
    }

    public function get_title() {
        return __('LP Course Materials', 'text-domain');
    }

    public function get_icon() {
        return 'eicon-download-bold';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'text-domain'),
            ]
        );

        $this->add_control(
            'course_id',
            [
                'label' => __('Course ID', 'text-domain'),
                'type' => Controls_Manager::NUMBER,
                'description' => __('Enter the ID of the LearnPress course', 'text-domain'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
    $settings = $this->get_settings_for_display();
    $course_id = !empty($settings['course_id']) ? $settings['course_id'] : get_the_ID();

    echo '<p><strong>Debug:</strong> Course ID: ' . esc_html($course_id) . '</p>';

    if (!function_exists('learn_press_get_course')) {
        echo '<p>LearnPress is not active.</p>';
        return;
    }

    $course = learn_press_get_course($course_id);
    if (!$course) {
        echo '<p>Invalid Course ID.</p>';
        return;
    }

    $materials = get_post_meta($course_id, '_lp_materials', true);

    echo '<pre><strong>Raw _lp_materials data:</strong><br>';
    print_r($materials);
    echo '</pre>';

    if (empty($materials) || !is_array($materials)) {
        echo '<p>No downloadable materials found for this course.</p>';
        return;
    }

    // Continue with your table output as before...
}

}
