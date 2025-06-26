<?php
/**
 * Plugin Name: Elementor Accordion Tabs
 * Description: Adds a custom accordion widget.
 * Version: 1.0
 * Author: CMARIX
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

function register_custom_accordion_widget($widgets_manager) {
    require_once(__DIR__ . '/widgets/category-accordion-widget.php');
    $widgets_manager->register(new \Elementor_Custom_Accordion_Widget());
}

add_action('elementor/widgets/register', 'register_custom_accordion_widget');

function accordion_tabs_scripts() {
    if (is_page('career')) { // You can also use is_page(123) for ID
        wp_enqueue_style('accordion-tabs-style', plugin_dir_url(__FILE__) . 'assets/style.css');
        wp_enqueue_script('jquery');
        wp_enqueue_script('accordion-tabs-ajax', plugin_dir_url(__FILE__) . 'assets/script.js', ['jquery'], null, true);

        wp_localize_script('accordion-tabs-ajax', 'ajax_params', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'accordion_tabs_scripts');
