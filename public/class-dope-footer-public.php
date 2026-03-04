<?php
/**
 * Public frontend class.
 *
 * @package DopeFooter
 */

namespace DopeFooter;

defined( 'ABSPATH' ) || exit;

/**
 * Public renderer class.
 */
class Public_Frontend {

	/**
	 * Footer post type.
	 */
	const FOOTER_CPT = 'dope_footer_item';

	/**
	 * Options instance.
	 *
	 * @var Options
	 */
	private $options;

	/**
	 * Asset registration flag.
	 *
	 * @var bool
	 */
	private $assets_registered = false;

	/**
	 * Asset enqueue flag.
	 *
	 * @var bool
	 */
	private $assets_enqueued = false;

	/**
	 * Constructor.
	 *
	 * @param Options $options Options manager.
	 */
	public function __construct( Options $options ) {
		$this->options = $options;
		$this->init_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ) );
	}

	/**
	 * Conditionally enqueue public assets.
	 *
	 * @return void
	 */
	public function maybe_enqueue_assets() {
		$this->enqueue_assets( false );
	}

	/**
	 * Enqueue assets.
	 *
	 * @param bool $force Force enqueue.
	 * @return void
	 */
	public function enqueue_assets( $force = false ) {
		$this->register_assets();

		if ( $this->assets_enqueued ) {
			return;
		}

		if ( ! $force && ! $this->should_enqueue_assets() ) {
			return;
		}

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'dope-footer-public' );
		wp_enqueue_script( 'dope-footer-public' );

		$this->assets_enqueued = true;
	}

	/**
	 * Render footer from shortcode.
	 *
	 * @param array $atts Shortcode attrs.
	 * @return string
	 */
	public function render_footer( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'id'     => 0,
				'preset' => '',
				'class'  => '',
			),
			(array) $atts,
			'dope_footer'
		);

		$footer_id = absint( $atts['id'] );
		if ( $footer_id > 0 ) {
			$footer_post = get_post( $footer_id );
			if ( ! ( $footer_post instanceof \WP_Post ) || self::FOOTER_CPT !== $footer_post->post_type ) {
				$footer_id = 0;
			}
		}
		$atts['id'] = $footer_id;

		$this->enqueue_assets( true );

		$data = $this->options->get_normalized( $atts );
		$data['footer_id'] = $footer_id;

		$template_map = array(
			'preset_signature' => DOPE_FOOTER_PATH . 'public/views/footer-preset-signature.php',
			'preset_split'     => DOPE_FOOTER_PATH . 'public/views/footer-preset-split.php',
			'preset_minimal'   => DOPE_FOOTER_PATH . 'public/views/footer-preset-minimal.php',
		);

		$template = isset( $template_map[ $data['layout_preset'] ] ) ? $template_map[ $data['layout_preset'] ] : $template_map['preset_signature'];

		if ( ! file_exists( $template ) ) {
			return '';
		}

		ob_start();
		include $template;
		$html = ob_get_clean();

		return apply_filters( 'dope_footer_rendered_html', $html, $data, $atts );
	}

	/**
	 * Register scripts and styles.
	 *
	 * @return void
	 */
	private function register_assets() {
		if ( $this->assets_registered ) {
			return;
		}

		wp_register_style(
			'dope-footer-public',
			DOPE_FOOTER_URL . 'assets/css/frontend.css',
			array(),
			DOPE_FOOTER_VERSION
		);

		wp_register_script(
			'dope-footer-public',
			DOPE_FOOTER_URL . 'assets/js/frontend.js',
			array(),
			DOPE_FOOTER_VERSION,
			true
		);

		$this->assets_registered = true;
	}

	/**
	 * Determine whether assets should be enqueued.
	 *
	 * @return bool
	 */
	private function should_enqueue_assets() {
		if ( is_admin() ) {
			return false;
		}

		global $post;

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'dope_footer' ) ) {
			return true;
		}

		if ( is_a( $post, 'WP_Post' ) && function_exists( 'has_block' ) && has_block( 'core/shortcode', $post ) ) {
			if ( false !== strpos( $post->post_content, '[dope_footer' ) ) {
				return true;
			}
		}

		return false;
	}
}
