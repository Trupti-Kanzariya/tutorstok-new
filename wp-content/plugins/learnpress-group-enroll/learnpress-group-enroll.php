<?php
/*
Plugin Name: LearnPress Group Purchase – Parent & Child
Description: Allows parent to buy a course and enroll a child user.
Version: 1.0
Author: Your Name
*/

add_action('plugins_loaded', function () {
    if (!function_exists('learn_press')) return;

    // ✅ 1. Add Fields to Checkout
    add_filter('learn-press/checkout-fields', function ($fields) {
        $fields['child_name'] = [
            'label' => __('Child Name', 'learnpress'),
            'required' => true,
            'type' => 'text',
        ];
        $fields['child_email'] = [
            'label' => __('Child Email', 'learnpress'),
            'required' => true,
            'type' => 'email',
        ];
        return $fields;
    });

    // ✅ 2. Save Fields to Order
    add_action('learn-press/before-checkout-order-processed', function ($checkout) {
        $child_name = sanitize_text_field($_POST['child_name'] ?? '');
        $child_email = sanitize_email($_POST['child_email'] ?? '');

        if (!empty($child_name)) $checkout->order->add_meta_data('_child_name', $child_name);
        if (!empty($child_email)) $checkout->order->add_meta_data('_child_email', $child_email);
    });

    // ✅ 3. Create Child User & Enroll After Order
    add_action('learn_press_order_status_completed', function ($order_id) {
        $order = learn_press_get_order($order_id);
        if (!$order) return;

        $child_name  = $order->get_meta('_child_name');
        $child_email = $order->get_meta('_child_email');

        if (!$child_email) return;

        // Create or get child user
        if (!email_exists($child_email)) {
            $password = wp_generate_password();
            $child_id = wp_create_user($child_email, $password, $child_email);
            if (!is_wp_error($child_id)) {
                wp_update_user(['ID' => $child_id, 'display_name' => $child_name]);
                wp_mail($child_email, 'Course Access', "Hi $child_name,\n\nYou now have access to a course.\n\nLogin: " . wp_login_url() . "\nUsername: $child_email\nPassword: $password");
            } else {
                return;
            }
        } else {
            $child_id = get_user_by('email', $child_email)->ID;
        }

        // Enroll child in each course in the order
        foreach ($order->get_items() as $item) {
            $course_id = $item->get_item_meta('course_id');
            if ($course_id && function_exists('learn_press_enroll_course')) {
                learn_press_enroll_course($course_id, $child_id);
            }
        }
    });
});
