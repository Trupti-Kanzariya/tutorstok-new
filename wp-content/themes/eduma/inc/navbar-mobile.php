<?php
function thim_eduma_custom_action_footer() {
	// add action for login_popup_footer;
	do_action( 'thim_login_popup_footer' );
 	// Nav Footer
	thim_nav_bar_mobile_footer();
 }

add_action( 'wp_footer', 'thim_eduma_custom_action_footer', 15 );
// Add Mobile Navbar Mobile
function thim_nav_bar_mobile_footer() {
	// show form login popup in single course
	if ( get_theme_mod( 'thim_learnpress_single_popup', true ) && ! get_theme_mod( 'navbar_mobile_show', false ) ) {
		if ( ! has_action( 'thim_login_popup_footer' ) && is_single() && get_post_type() == 'lp_course' ) {
			echo '<div class="thim-login-popup thim-link-login"><a class="login js-show-popup" href="#" style="display: none"></a>';
			thim_form_login_popup();
			echo '</div>';
		}
	} else {
		if ( ! get_theme_mod( 'navbar_mobile_show', false ) ) {
			return;
		}
		$nav_mobile_items = get_theme_mod( 'nav_mobile_item', [ 'home', 'course', 'search', 'account' ] );
		$active           = '';
		$has_account      = false;
		if ( ! empty( $nav_mobile_items ) ) {
			echo '<div class="navbar-mobile-button">';
			foreach ( $nav_mobile_items as $nav_item ) {
				$nav_item = apply_filters( 'thim_navbar_mobile_button', $nav_item );
				switch ( $nav_item ) {
					case 'home':
						$active = ( is_home() || is_front_page() ) ? ' active' : '';
						echo '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" class="item-menubar' . $active . '"><i class="' . eduma_font_icon( 'home' ) . '"></i><span>' . esc_html__( 'Home', 'eduma' ) . '</span></a>';
						break;
					case 'course':
						if ( class_exists( 'LearnPress' ) ) {
							$active         = thim_check_learnpress() ? ' active' : '';
							$course_page_id = learn_press_get_page_id( 'courses' );
							echo '<a href="' . get_the_permalink( $course_page_id ) . '" title="' . esc_html__( 'Courses', 'learnpress' ) . '" class="item-menubar' . $active . '"><i class="lp-icon-book-open"></i><span>' . esc_html__( 'Courses', 'learnpress' ) . '</span></a>';
						}
						break;
					case 'search':
						if ( class_exists( 'LearnPress' ) ) {
							$placeholder = esc_html__( 'What do you want to learn today?', 'eduma' );
							echo '<div class="item-menubar thim-course-search-overlay"><div class="search-toggle flex-center"><i class="' . eduma_font_icon( 'search' ) . '"></i><span>' . esc_html__( 'Search', 'eduma' ) . '</span></div>';
							wp_enqueue_script( 'search-course-widget' );
							thim_form_search_popup( $placeholder, true );
							echo '</div>';
						}
						break;
					case 'account':
						if ( is_user_logged_in() && class_exists( 'LearnPress' ) ) {
							$link_account = learn_press_user_profile_link();
						} else {
							$link_account = thim_get_login_page_url();
						}
						$active = ( $link_account == get_the_permalink( get_the_ID() ) ) ? ' active' : '';
						echo '<div class="item-menubar thim-login-popup thim-link-login' . $active . '"><a class="login js-show-popup flex-center" href="' . esc_url( $link_account ) . '" title="' . esc_html__( 'Account', 'eduma' ) . '"><i class="' . eduma_font_icon( 'user' ) . '"></i><span>' . esc_html__( 'Account', 'eduma' ) . '</span></a></div>';
						$has_account = true;
						break;
					case 'cart':
						if ( class_exists( 'WooCommerce' ) ) {
							$active = is_cart() ? ' active' : '';
							echo '<a href="' . wc_get_cart_url() . '" title="' . esc_html__( 'Cart', 'eduma' ) . '" class="item-menubar' . $active . '"><i class="' . eduma_font_icon( 'shopping-cart' ) . '"></i><span>' . esc_html__( 'Cart', 'eduma' ) . '</span></a>';
						}
						break;
				}
			}
			echo '</div>';
			if ( ! has_action( 'thim_login_popup_footer' ) && $has_account ) {
				$setting            = [];
				$setting['captcha'] = get_theme_mod( 'captcha_form_login', false );
				$setting['term']    = get_theme_mod( 'terms_form_login', '' );
				thim_form_login_popup( $setting );
			}
		}
	}
}

