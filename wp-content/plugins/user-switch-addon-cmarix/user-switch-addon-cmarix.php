<?php
/*
Plugin Name: User Switch Addon - CMARIX
Description: Allows parent users (a custom role inheriting from 'customer') to switch into child accounts and back using shortcodes.
Version: 1.5
Author: Your Name
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Start PHP session on init to avoid header issues.
 */
function pcs_init_session() {
    if ( ! session_id() ) {
        session_start();
    }
}
add_action( 'init', 'pcs_init_session', 1 );

/**
 * Ensure 'parent_user' role exists on init, inheriting 'customer' capabilities.
 */
function pcs_add_parent_role() {
    $customer = get_role( 'customer' );
    if ( $customer && ! get_role( 'parent_user' ) ) {
        add_role( 'parent_user', 'Parent', $customer->capabilities );
    }
}
add_action( 'init', 'pcs_add_parent_role' );

/**
 * Remove 'parent_user' role on deactivation.
 */
function pcs_remove_parent_role() {
    remove_role( 'parent_user' );
}
register_deactivation_hook( __FILE__, 'pcs_remove_parent_role' );

/**
 * Shortcode: [switch_to_child child_id="123"]
 */
function pcs_switch_to_child_shortcode( $atts ) {
    if ( ! is_user_logged_in() ) {
        return '';
    }

    $current = wp_get_current_user();
    if ( ! in_array( 'parent_user', (array) $current->roles, true ) ) {
        return '<p>Only parents can switch accounts.</p>';
    }

    $atts = shortcode_atts( array(
        'child_id' => '',
    ), $atts, 'switch_to_child' );

    $child_id = intval( $atts['child_id'] );
    if ( ! $child_id ) {
        return '<p>No child ID provided.</p>';
    }

    if ( ! pcs_is_child_of_parent( $current->ID, $child_id ) ) {
        return '<p>Unauthorized access to this child account.</p>';
    }

    $switch_url = wp_nonce_url(
        add_query_arg( array(
            'pcs_action' => 'switch',
            'child_id'   => $child_id,
        ), site_url() ),
        'pcs_switch_' . $child_id
    );

    return '<a style="color:white" href="' . esc_url( $switch_url ) . '" class="button">Switch to Child Account</a>';
}
add_shortcode( 'switch_to_child', 'pcs_switch_to_child_shortcode' );

/**
 * Shortcode: [switch_back_to_parent]
 */
function pcs_switch_back_shortcode() {
    if ( ! is_user_logged_in() || ! pcs_get_parent_id_from_session() ) {
        return '';
    }

    $back_url = wp_nonce_url(
        add_query_arg( 'pcs_action', 'switch_back', site_url() ),
        'pcs_switch_back'
    );

    return '<a style="color:white" href="' . esc_url( $back_url ) . '" class="button">Switch Back to Parent Account</a>';
}
add_shortcode( 'switch_back_to_parent', 'pcs_switch_back_shortcode' );

/**
 * Handle switch and switch-back actions.
 */
function pcs_handle_switch_actions() {
    if ( ! is_user_logged_in() ) {
        return;
    }

    // Switch to child
    if ( isset( $_GET['pcs_action'], $_GET['child_id'] ) && 'switch' === $_GET['pcs_action'] ) {
        $child_id  = intval( $_GET['child_id'] );
        $parent_id = get_current_user_id();

        if ( ! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'pcs_switch_' . $child_id ) ) {
            wp_die( 'Security check failed.' );
        }

        if ( ! pcs_is_child_of_parent( $parent_id, $child_id ) ) {
            wp_die( 'Unauthorized switch attempt.' );
        }

        pcs_set_switch_session( $parent_id );
        wp_set_current_user( $child_id );
        wp_set_auth_cookie( $child_id );
        wp_redirect( site_url( '/my-account/' ) ); // Update to child landing URL
        exit;
    }

    // Switch back to parent
    if ( isset( $_GET['pcs_action'] ) && 'switch_back' === $_GET['pcs_action'] ) {
        $parent_id = pcs_get_parent_id_from_session();
        if ( ! $parent_id ) {
            wp_die( 'Session expired.' );
        }

        pcs_clear_switch_session();
        wp_set_current_user( $parent_id );
        wp_set_auth_cookie( $parent_id );
        wp_redirect( site_url( '/my-account/' ) ); // Update to parent landing URL
        exit;
    }
}
add_action( 'template_redirect', 'pcs_handle_switch_actions' );

/**
 * Check parent-child relationship.
 */
function pcs_is_child_of_parent( $parent_id, $child_id ) {
    return intval( get_user_meta( $child_id, 'parent_user_id', true ) ) === intval( $parent_id );
}

/**
 * Manage parent switch context in session.
 */
function pcs_set_switch_session( $parent_id ) {
    $_SESSION['pcs_parent_id'] = $parent_id;
}
function pcs_get_parent_id_from_session() {
    return $_SESSION['pcs_parent_id'] ?? false;
}
function pcs_clear_switch_session() {
    unset( $_SESSION['pcs_parent_id'] );
}

include_once __DIR__ . '/learnpress-quiz-manual-evaluation.php';