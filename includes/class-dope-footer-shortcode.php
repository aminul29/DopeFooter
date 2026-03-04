<?php
/**
 * Shortcode class.
 *
 * @package DopeFooter
 */

namespace DopeFooter;

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode class.
 */
class Shortcode {

	/**
	 * Public frontend instance.
	 *
	 * @var Public_Frontend
	 */
	private $public;

	/**
	 * Constructor.
	 *
	 * @param Public_Frontend $public Public renderer.
	 */
	public function __construct( Public_Frontend $public ) {
		$this->public = $public;
		$this->init_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_shortcode( 'dope_footer', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Render shortcode.
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'     => 0,
				'preset' => '',
				'class'  => '',
			),
			(array) $atts,
			'dope_footer'
		);

		return $this->public->render_footer( $atts );
	}
}
