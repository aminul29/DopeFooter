<?php
/**
 * Plugin Name:       DopeFooter
 * Plugin URI:        https://example.com/dope-footer
 * Description:       Configurable shortcode footer with CodeStar Framework settings and multiple layout presets.
 * Version:           1.0.1
 * Author:            DopeFooter Team
 * Text Domain:       dope-footer
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package DopeFooter
 */

defined( 'ABSPATH' ) || exit;

define( 'DOPE_FOOTER_VERSION', '1.0.1' );
define( 'DOPE_FOOTER_FILE', __FILE__ );
define( 'DOPE_FOOTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'DOPE_FOOTER_URL', plugin_dir_url( __FILE__ ) );
define( 'DOPE_FOOTER_BASENAME', plugin_basename( __FILE__ ) );

require_once DOPE_FOOTER_PATH . 'includes/class-dope-footer-plugin.php';
require_once DOPE_FOOTER_PATH . 'includes/class-dope-footer-options.php';

/**
 * Activation hook.
 *
 * @return void
 */
function dope_footer_activate() {
	$defaults = \DopeFooter\Options::get_defaults();

	if ( false === get_option( \DopeFooter\Options::OPTION_KEY, false ) ) {
		add_option( \DopeFooter\Options::OPTION_KEY, $defaults );
	}
}
register_activation_hook( __FILE__, 'dope_footer_activate' );

/**
 * Initialize plugin.
 *
 * @return void
 */
function dope_footer_init() {
	\DopeFooter\Plugin::instance();
}
add_action( 'plugins_loaded', 'dope_footer_init' );
