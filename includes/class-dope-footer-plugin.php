<?php
/**
 * Main plugin class.
 *
 * @package DopeFooter
 */

namespace DopeFooter;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 */
class Plugin {

	/**
	 * Instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Options instance.
	 *
	 * @var Options
	 */
	private $options;

	/**
	 * Public instance.
	 *
	 * @var Public_Frontend
	 */
	private $public;

	/**
	 * Shortcode instance.
	 *
	 * @var Shortcode
	 */
	private $shortcode;

	/**
	 * Admin instance.
	 *
	 * @var Admin|null
	 */
	private $admin = null;

	/**
	 * Elementor integration instance.
	 *
	 * @var Elementor_Integration|null
	 */
	private $elementor = null;

	/**
	 * Get instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load dependencies.
	 *
	 * @return void
	 */
	private function load_dependencies() {
		require_once DOPE_FOOTER_PATH . 'includes/class-dope-footer-options.php';
		require_once DOPE_FOOTER_PATH . 'includes/class-dope-footer-shortcode.php';
		require_once DOPE_FOOTER_PATH . 'public/class-dope-footer-public.php';

		$this->options   = new Options();
		$this->public    = new Public_Frontend( $this->options );
		$this->shortcode = new Shortcode( $this->public );

		if ( $this->is_elementor_active() ) {
			require_once DOPE_FOOTER_PATH . 'includes/class-dope-footer-elementor.php';
			$this->elementor = new Elementor_Integration( $this->public );
		} elseif ( is_admin() ) {
			require_once DOPE_FOOTER_PATH . 'admin/class-dope-footer-admin.php';
			$this->admin = new Admin( $this->options );
		}
	}

	/**
	 * Check whether Elementor is active.
	 *
	 * @return bool
	 */
	private function is_elementor_active() {
		return did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'dope-footer',
			false,
			dirname( DOPE_FOOTER_BASENAME ) . '/languages'
		);
	}

	/**
	 * Get options instance.
	 *
	 * @return Options
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Get public instance.
	 *
	 * @return Public_Frontend
	 */
	public function get_public() {
		return $this->public;
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 *
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'dope-footer' ), DOPE_FOOTER_VERSION );
	}
}
