<?php
/**
 * Template for displaying user profile content.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/profile/content.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.2
 */

defined( 'ABSPATH' ) || exit();

/**
 * @var LP_Profile_Tab $profile_tab
 */
if ( ! isset( $user ) || ! isset( $tab_key ) || ! isset( $profile ) || ! isset( $profile_tab ) ) {
	return;
}
?>

<article id="profile-content dwedrwefrger" class="lp-profile-content">
	<div id="profile-content-<?php echo esc_attr( $tab_key ); ?>">
		<?php
			$user_id = $user->get_id();
			$avatar = get_avatar( $user_id, 96 ); // You can change the size if needed
			$display_name = $user->get_display_name();
			$username = $user->user_login;

			$user_login = urlencode($user->get_data('user_login'));
		    $settings_url = home_url("/lp-profile/{$user_login}/settings/basic-information/");
			//$profile_url = learn_press_user_profile_link($user_id); // current user's profile base URL
			//$settings_url = trailingslashit($profile_url) . esc_html($username) . 'settings';
		?>
		<div class="lp-profile-header" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
			<a href="<?php echo esc_url($settings_url); ?>" class="custom-lp-profile-link">
				<div class="custom-lp-profile-avatar"><?php echo $avatar; ?></div>
			</a>
			<div class="custom-lp-profile-content">
				<a href="<?php echo esc_url($settings_url); ?>" class="custom-lp-profile-link">
					<div class="custom-lp-profile-username">Hi, <span><?php echo esc_html( $display_name ); ?></span></div>
					<div class="custom-lp-profile-description">Making great progress!!</div>
				</a>
			</div>
		</div>

		<?php do_action( 'learn-press/before-profile-content', $tab_key, $profile_tab, $user ); ?>

		<?php
		if ( empty( $profile_tab->get( 'sections' ) ) ) {
			if ( $profile_tab->get( 'callback' ) && is_callable( $profile_tab->get( 'callback' ) ) ) {
				echo call_user_func_array(
					$profile_tab->get( 'callback' ),
					[
						$tab_key,
						$profile_tab,
						$user,
					]
				);
			} else {
				do_action( 'learn-press/profile-content', $tab_key, $profile_tab, $user );
			}
		} else {
			foreach ( $profile_tab->get( 'sections' ) as $key => $section ) {
				if ( $profile->get_current_section( '', false, false ) === $section['slug'] ) {
					if ( isset( $section['callback'] ) && is_callable( $section['callback'] ) ) {
						echo call_user_func_array( $section['callback'], array( $key, $section, $user ) );
					} else {
						do_action( 'learn-press/profile-section-content', $key, $section, $user );
						
					}
				}
			}
		}
		?>

		<?php do_action( 'learn-press/after-profile-content' ); ?>
	</div>
</article>