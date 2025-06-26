<?php
/**
 * Plugin Name: LearnPress - Co-Instructors
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Building courses with other instructors.
 * Author: ThimPress
 * Version: 4.0.6
 * Author URI: http://thimpress.com
 * Tags: learnpress, lms, add-on, co-instructor
 * Text Domain: learnpress-co-instructor
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.8
 *
 * @package learnpress-co-instructors
 */

use LP_Addon_Co_Instructor\CourseCoInstructorTemplate;
use LP_Addon_Co_Instructor\Hook;

defined( 'ABSPATH' ) || exit;

const LP_ADDON_CO_INSTRUCTOR_FILE = __FILE__;
define( 'LP_ADDON_CO_INSTRUCTOR_PATH', dirname( LP_ADDON_CO_INSTRUCTOR_FILE ) );

if ( ! class_exists( 'LP_Co_Instructor_Preload' ) ) {

	/**
	 * Class LP_Co_Instructor_Preload
	 */
	class LP_Co_Instructor_Preload {
		/**
		 * @var array
		 */
		public static $addon_info = array();
		/**
		 * @var LP_Addon_Co_Instructor $addon
		 */
		public static $addon;

		/**
		 * Singleton.
		 */
		public static function instance() {
			static $instance;
			if ( ! $instance ) {
				$instance = new self();
			}

			return $instance;
		}

		/**
		 * LP_Co_Instructor_Preload constructor.
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {
			$can_load = true;
			// Set Base name plugin.
			define( 'LP_ADDON_CO_INSTRUCTOR_BASENAME', plugin_basename( LP_ADDON_CO_INSTRUCTOR_FILE ) );

			// Set version addon for LP check .
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			self::$addon_info = get_file_data(
				LP_ADDON_CO_INSTRUCTOR_FILE,
				array(
					'Name'               => 'Plugin Name',
					'Require_LP_Version' => 'Require_LP_Version',
					'Version'            => 'Version',
				)
			);

			define( 'LP_ADDON_CO_INSTRUCTOR_VER', self::$addon_info['Version'] );
			define( 'LP_ADDON_CO_INSTRUCTOR_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );

			// Check LP activated .
			if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
				$can_load = false;
			} elseif ( version_compare( LP_ADDON_CO_INSTRUCTOR_VER, get_option( 'learnpress_version', '3.0.0' ), '>' ) ) {
				$can_load = false;
			}

			if ( ! $can_load ) {
				add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );
				deactivate_plugins( LP_ADDON_CO_INSTRUCTOR_BASENAME );

				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}

				return;
			}

			add_filter( 'learn-press/email-actions', [ $this, 'hooks_notify_email' ] );

			// Sure LP loaded.
			add_action( 'learn-press/ready', array( $this, 'load' ) );
		}

		/**
		 * Load plugin main class.
		 *
		 * @since 3.0.0
		 */
		public function load() {
			include_once 'vendor/autoload.php';
			include_once 'inc/load.php';
			self::$addon = LP_Addon_Co_Instructor::instance();
			Hook::instance();
			CourseCoInstructorTemplate::instance();
		}

		public function show_note_errors_require_lp() {
			?>
			<div class="notice notice-error">
				<p><?php echo( 'Please active <strong>LearnPress version ' . LP_ADDON_CO_INSTRUCTOR_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Email hooks notify
		 *
		 * @param array $email_hooks
		 *
		 * @return array
		 */
		public function hooks_notify_email( array $email_hooks ): array {
			$email_hooks['learnpress/user/course-enrolled'][ LP_Email_Enrolled_Course_Co_Instructor::class ]  = LP_ADDON_CO_INSTRUCTOR_PATH . 'inc/emails/class-lp-co-instructor-email-enrolled-course.php';
			$email_hooks['learn-press/user-course-finished'][ LP_Email_Finished_Course_Co_Instructor::class ] = LP_ADDON_CO_INSTRUCTOR_PATH . 'inc/emails/class-lp-co-instructor-email-finished-course.php';

			return $email_hooks;
		}
	}

	LP_Co_Instructor_Preload::instance();
}
