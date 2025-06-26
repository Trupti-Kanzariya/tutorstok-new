<?php
/**
 * Plugin Name: LearnPress - Upsell
 * Plugin URI: http://thimpress.com/learnpress
 * Description: Add upsell feature to LearnPress
 * Author: ThimPress
 * Version: 4.0.6
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author URI: http://thimpress.com
 * Tags: learnpress
 * Text Domain: learnpress-upsell
 * Domain Path: /languages/
 * Require_LP_Version: 4.2.7.7
 */

use LearnPress\Upsell\Package\Order;
use LearnPress\Upsell\TemplateHooks\ArchivePackage;
use LearnPress\Upsell\TemplateHooks\ListCouponsTemplate;
use LearnPress\Upsell\TemplateHooks\SingleCoursePackage;
use LearnPress\Upsell\TemplateHooks\SinglePackage;

defined( 'ABSPATH' ) || exit();

const LP_ADDON_UPSELL_FILE = __FILE__;
define( 'LP_ADDON_UPSELL_PATH', plugin_dir_path( __FILE__ ) );
define( 'LP_ADDON_UPSELL_URL', plugin_dir_url( __FILE__ ) );
define( 'LP_ADDON_UPSELL_BASENAME', plugin_basename( __FILE__ ) );
const LP_PACKAGE_CPT = 'learnpress_package';
const LP_COUPON_CPT  = 'learnpress_coupon';

class LP_Addon_Upsell_Preload {
	/**
	 * @var array
	 */
	public static $addon_info = array();
	/**
	 * @var LP_Addon_Course_Review $addon
	 */
	public static $addon;

	public function __construct() {

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		self::$addon_info = get_file_data(
			LP_ADDON_UPSELL_FILE,
			array(
				'Name'               => 'Plugin Name',
				'Require_LP_Version' => 'Require_LP_Version',
				'Version'            => 'Version',
			)
		);

		define( 'LP_ADDON_UPSELL_VER', self::$addon_info['Version'] );
		define( 'LP_ADDON_UPSELL_REQUIRE_VER', self::$addon_info['Require_LP_Version'] );
		define( 'LP_ADDON_UPSELL_PACKAGE_PATH', LP_ADDON_UPSELL_PATH . 'inc/Package/' );

		if ( ! is_plugin_active( 'learnpress/learnpress.php' ) ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_lp' ) );

			deactivate_plugins( LP_ADDON_UPSELL_BASENAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return;
		}

		// Set priority 9 to load before addons payments like: Stripe, Qpay,...
		add_action( 'learn-press/ready', array( $this, 'load' ), 9 );
	}

	public function load() {
		include_once 'vendor/autoload.php';

		include_once LP_ADDON_UPSELL_PATH . 'inc/load.php';
		self::$addon = LP_Addon_Upsell::instance();
		Order::init();
		SinglePackage::instance();
		ArchivePackage::instance();
		SingleCoursePackage::instance();
		ListCouponsTemplate::instance();
	}

	public function show_note_errors_require_lp() {
		?>
		<div class="notice notice-error">
			<p><?php echo( 'Please active <strong>LP version ' . LP_ADDON_UPSELL_REQUIRE_VER . ' or later</strong> before active <strong>' . self::$addon_info['Name'] . '</strong>' ); ?></p>
		</div>
		<?php
	}
}

new LP_Addon_Upsell_Preload();
