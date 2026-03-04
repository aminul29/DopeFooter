<?php
/**
 * Elementor integration for DopeFooter.
 *
 * @package DopeFooter
 */

namespace DopeFooter;

defined( 'ABSPATH' ) || exit;

/**
 * Elementor integration class.
 */
class Elementor_Integration {

	/**
	 * Public renderer instance.
	 *
	 * @var Public_Frontend
	 */
	private $public;

	/**
	 * Constructor.
	 *
	 * @param Public_Frontend $public  Public renderer instance.
	 */
	public function __construct( Public_Frontend $public ) {
		$this->public = $public;
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		if ( did_action( 'elementor/init' ) ) {
			$this->register_elementor_hooks();
			return;
		}

		add_action( 'elementor/init', array( $this, 'register_elementor_hooks' ) );
	}

	/**
	 * Register Elementor hooks.
	 *
	 * @return void
	 */
	public function register_elementor_hooks() {
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'ensure_assets_registered' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'ensure_assets_registered' ) );

		$this->ensure_assets_registered();
	}

	/**
	 * Ensure DopeFooter public assets are registered.
	 *
	 * @return void
	 */
	public function ensure_assets_registered() {
		$this->public->ensure_assets_registered();
	}

	/**
	 * Register Elementor widget category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 * @return void
	 */
	public function register_category( $elements_manager ) {
		if ( ! method_exists( $elements_manager, 'add_category' ) ) {
			return;
		}

		$elements_manager->add_category(
			'dope-footer',
			array(
				'title' => esc_html__( 'DopeFooter', 'dope-footer' ),
				'icon'  => 'eicon-footer',
			)
		);
	}

	/**
	 * Register Elementor widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		if ( ! method_exists( $widgets_manager, 'register' ) ) {
			return;
		}

		require_once DOPE_FOOTER_PATH . 'includes/elementor/class-dope-footer-widget.php';

		$widgets_manager->register( new Elementor\Dope_Footer_Widget() );
	}
}
