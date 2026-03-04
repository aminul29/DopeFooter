<?php
/**
 * Options and normalization class.
 *
 * @package DopeFooter
 */

namespace DopeFooter;

defined( 'ABSPATH' ) || exit;

/**
 * Options class.
 */
class Options {

	/**
	 * Option key.
	 */
	const OPTION_KEY = 'dope_footer_options';

	/**
	 * Get default option values.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'layout_preset'           => 'preset_signature',
			'brand_name'              => 'Bassel Daher, Ph.D.',
			'brand_tagline'           => 'Building Bridges. Simplexifying Sustainability. Transforming Systems',
			'quick_links'             => array(
				array(
					'label' => 'Home',
					'url'   => home_url( '/' ),
				),
				array(
					'label' => 'About Me',
					'url'   => home_url( '/about' ),
				),
				array(
					'label' => 'Research Areas',
					'url'   => home_url( '/research' ),
				),
				array(
					'label' => 'Publications',
					'url'   => home_url( '/publications' ),
				),
				array(
					'label' => 'Projects',
					'url'   => home_url( '/projects' ),
				),
			),
			'other_links'             => array(
				array(
					'label' => 'Global Reach',
					'url'   => home_url( '/global-reach' ),
				),
				array(
					'label' => 'Consulting',
					'url'   => home_url( '/consulting' ),
				),
				array(
					'label' => 'Talks & Engagements',
					'url'   => home_url( '/talks-engagements' ),
				),
				array(
					'label' => 'Leadership & Awards',
					'url'   => home_url( '/leadership-awards' ),
				),
			),
			'contact_text'            => 'Interested in collaboration or speaking engagements?',
			'contact_link_label'      => 'Get in touch',
			'contact_link_url'        => home_url( '/contact' ),
			'social_links'            => array(
				array(
					'network'      => 'linkedin',
					'url'          => 'https://www.linkedin.com/',
					'custom_label' => 'LinkedIn',
				),
				array(
					'network'      => 'x',
					'url'          => 'https://x.com/',
					'custom_label' => 'X',
				),
				array(
					'network'      => 'email',
					'url'          => 'mailto:hello@example.com',
					'custom_label' => 'Email',
				),
			),
			'copyright_text'          => '© %year% Bassel Daher. All rights reserved.',
			'show_social'             => true,
			'show_quick_links'        => true,
			'show_other_links'        => true,
			'show_contact'            => true,
			'show_back_to_top'        => true,
			'background_color'        => '#1d232d',
			'text_color'              => '#dce3ef',
			'heading_color'           => '#ffffff',
			'accent_color'            => '#0f5edb',
			'divider_color'           => '#485369',
			'max_width'               => 1200,
			'section_padding_top'     => 72,
			'section_padding_bottom'  => 36,
			'open_links_new_tab'      => false,
			'font_family_base'        => '"Poppins", "Segoe UI", sans-serif',
			'font_family_heading'     => '"Outfit", "Poppins", "Segoe UI", sans-serif',
			'brand_name_size_desktop' => 52,
			'brand_name_size_mobile'  => 36,
			'column_heading_size'     => 24,
			'body_text_size'          => 18,
			'link_text_size'          => 16,
			'brand_weight'            => 700,
			'heading_weight'          => 600,
			'body_weight'             => 400,
			'line_height'             => 1.6,
		);
	}

	/**
	 * Get available social networks.
	 *
	 * @return array
	 */
	public static function get_social_networks() {
		return array(
			'linkedin'  => array(
				'label' => 'LinkedIn',
				'icon'  => 'dashicons-linkedin',
			),
			'x'         => array(
				'label' => 'X',
				'icon'  => 'dashicons-twitter',
			),
			'email'     => array(
				'label' => 'Email',
				'icon'  => 'dashicons-email-alt',
			),
			'facebook'  => array(
				'label' => 'Facebook',
				'icon'  => 'dashicons-facebook',
			),
			'instagram' => array(
				'label' => 'Instagram',
				'icon'  => 'dashicons-instagram',
			),
			'youtube'   => array(
				'label' => 'YouTube',
				'icon'  => 'dashicons-youtube',
			),
			'github'    => array(
				'label' => 'GitHub',
				'icon'  => 'dashicons-editor-code',
			),
			'website'   => array(
				'label' => 'Website',
				'icon'  => 'dashicons-admin-site-alt3',
			),
		);
	}

	/**
	 * Get allowed layout presets.
	 *
	 * @return string[]
	 */
	public static function get_allowed_presets() {
		return array(
			'preset_signature',
			'preset_split',
			'preset_minimal',
		);
	}

	/**
	 * Get saved options merged with defaults.
	 *
	 * @return array
	 */
	public function get_options() {
		$saved = get_option( self::OPTION_KEY, array() );
		$saved = is_array( $saved ) ? $saved : array();

		return wp_parse_args( $saved, self::get_defaults() );
	}

	/**
	 * Sanitize options for storage.
	 *
	 * @param mixed $raw Raw options.
	 * @return array
	 */
	public function sanitize_for_storage( $raw ) {
		$defaults = self::get_defaults();
		$raw      = is_array( $raw ) ? $raw : array();
		$data     = wp_parse_args( $raw, $defaults );

		$allowed_presets = self::get_allowed_presets();
		$layout_preset   = sanitize_key( $data['layout_preset'] );
		if ( ! in_array( $layout_preset, $allowed_presets, true ) ) {
			$layout_preset = $defaults['layout_preset'];
		}

		$sanitized = array(
			'layout_preset'           => $layout_preset,
			'brand_name'              => sanitize_text_field( $data['brand_name'] ),
			'brand_tagline'           => sanitize_textarea_field( $data['brand_tagline'] ),
			'quick_links'             => $this->sanitize_link_group( $data['quick_links'] ),
			'other_links'             => $this->sanitize_link_group( $data['other_links'] ),
			'contact_text'            => sanitize_textarea_field( $data['contact_text'] ),
			'contact_link_label'      => sanitize_text_field( $data['contact_link_label'] ),
			'contact_link_url'        => esc_url_raw( $data['contact_link_url'] ),
			'social_links'            => $this->sanitize_social_group( $data['social_links'] ),
			'copyright_text'          => sanitize_text_field( $data['copyright_text'] ),
			'show_social'             => $this->to_bool( $data['show_social'] ),
			'show_quick_links'        => $this->to_bool( $data['show_quick_links'] ),
			'show_other_links'        => $this->to_bool( $data['show_other_links'] ),
			'show_contact'            => $this->to_bool( $data['show_contact'] ),
			'show_back_to_top'        => $this->to_bool( $data['show_back_to_top'] ),
			'background_color'        => $this->sanitize_color( $data['background_color'], $defaults['background_color'] ),
			'text_color'              => $this->sanitize_color( $data['text_color'], $defaults['text_color'] ),
			'heading_color'           => $this->sanitize_color( $data['heading_color'], $defaults['heading_color'] ),
			'accent_color'            => $this->sanitize_color( $data['accent_color'], $defaults['accent_color'] ),
			'divider_color'           => $this->sanitize_color( $data['divider_color'], $defaults['divider_color'] ),
			'max_width'               => $this->clamp_int( $data['max_width'], 720, 1800, $defaults['max_width'] ),
			'section_padding_top'     => $this->clamp_int( $data['section_padding_top'], 20, 240, $defaults['section_padding_top'] ),
			'section_padding_bottom'  => $this->clamp_int( $data['section_padding_bottom'], 20, 240, $defaults['section_padding_bottom'] ),
			'open_links_new_tab'      => $this->to_bool( $data['open_links_new_tab'] ),
			'font_family_base'        => sanitize_text_field( $data['font_family_base'] ),
			'font_family_heading'     => sanitize_text_field( $data['font_family_heading'] ),
			'brand_name_size_desktop' => $this->clamp_int( $data['brand_name_size_desktop'], 24, 96, $defaults['brand_name_size_desktop'] ),
			'brand_name_size_mobile'  => $this->clamp_int( $data['brand_name_size_mobile'], 18, 72, $defaults['brand_name_size_mobile'] ),
			'column_heading_size'     => $this->clamp_int( $data['column_heading_size'], 16, 40, $defaults['column_heading_size'] ),
			'body_text_size'          => $this->clamp_int( $data['body_text_size'], 12, 30, $defaults['body_text_size'] ),
			'link_text_size'          => $this->clamp_int( $data['link_text_size'], 12, 40, $defaults['link_text_size'] ),
			'brand_weight'            => $this->clamp_int( $data['brand_weight'], 100, 900, $defaults['brand_weight'] ),
			'heading_weight'          => $this->clamp_int( $data['heading_weight'], 100, 900, $defaults['heading_weight'] ),
			'body_weight'             => $this->clamp_int( $data['body_weight'], 100, 900, $defaults['body_weight'] ),
			'line_height'             => $this->clamp_float( $data['line_height'], 1.0, 2.4, $defaults['line_height'] ),
		);

		return $sanitized;
	}

	/**
	 * Build normalized render data.
	 *
	 * @param array $overrides Shortcode overrides.
	 * @return array
	 */
	public function get_normalized( $overrides = array() ) {
		$overrides = is_array( $overrides ) ? $overrides : array();
		$data      = $this->sanitize_for_storage( $this->get_options() );

		if ( ! empty( $overrides['preset'] ) ) {
			$preset = sanitize_key( $overrides['preset'] );
			if ( in_array( $preset, self::get_allowed_presets(), true ) ) {
				$data['layout_preset'] = $preset;
			}
		}

		$wrapper_class = '';
		if ( ! empty( $overrides['class'] ) ) {
			$wrapper_class = $this->sanitize_css_class_list( $overrides['class'] );
		}

		$current_year = wp_date( 'Y' );
		$copyright    = str_replace( '%year%', $current_year, $data['copyright_text'] );

		$normalized = array(
			'layout_preset'   => $data['layout_preset'],
			'wrapper_class'   => $wrapper_class,
			'brand'           => array(
				'name'    => $data['brand_name'],
				'tagline' => $data['brand_tagline'],
			),
			'quick_links'     => $data['quick_links'],
			'other_links'     => $data['other_links'],
			'contact'         => array(
				'text'       => $data['contact_text'],
				'link_label' => $data['contact_link_label'],
				'link_url'   => $data['contact_link_url'],
			),
			'social_links'    => $data['social_links'],
			'copyright_text'  => $copyright,
			'display'         => array(
				'show_social'        => $data['show_social'],
				'show_quick_links'   => $data['show_quick_links'],
				'show_other_links'   => $data['show_other_links'],
				'show_contact'       => $data['show_contact'],
				'show_back_to_top'   => $data['show_back_to_top'],
				'open_links_new_tab' => $data['open_links_new_tab'],
			),
			'styles'          => array(
				'background_color'       => $data['background_color'],
				'background_alt_color'   => $this->mix_with_black( $data['background_color'], 0.18 ),
				'text_color'             => $data['text_color'],
				'heading_color'          => $data['heading_color'],
				'accent_color'           => $data['accent_color'],
				'divider_color'          => $data['divider_color'],
				'max_width'              => $data['max_width'],
				'section_padding_top'    => $data['section_padding_top'],
				'section_padding_bottom' => $data['section_padding_bottom'],
			),
			'typography'      => array(
				'font_family_base'        => $data['font_family_base'],
				'font_family_heading'     => $data['font_family_heading'],
				'brand_name_size_desktop' => $data['brand_name_size_desktop'],
				'brand_name_size_mobile'  => $data['brand_name_size_mobile'],
				'column_heading_size'     => $data['column_heading_size'],
				'body_text_size'          => $data['body_text_size'],
				'link_text_size'          => $data['link_text_size'],
				'brand_weight'            => $data['brand_weight'],
				'heading_weight'          => $data['heading_weight'],
				'body_weight'             => $data['body_weight'],
				'line_height'             => $data['line_height'],
			),
		);

		$css_vars                      = $this->build_css_vars( $normalized );
		$normalized['css_vars']        = $css_vars;
		$normalized['css_vars_inline'] = $this->build_css_vars_inline( $css_vars );

		return apply_filters( 'dope_footer_normalized_data', $normalized, $data, $overrides );
	}

	/**
	 * Sanitize link group items.
	 *
	 * @param mixed $items Raw items.
	 * @return array
	 */
	private function sanitize_link_group( $items ) {
		$sanitized = array();
		$items     = is_array( $items ) ? $items : array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$label = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
			$url   = isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '';

			if ( '' === $label || '' === $url ) {
				continue;
			}

			$sanitized[] = array(
				'label' => $label,
				'url'   => $url,
			);
		}

		return $sanitized;
	}

	/**
	 * Sanitize social group items.
	 *
	 * @param mixed $items Raw social items.
	 * @return array
	 */
	private function sanitize_social_group( $items ) {
		$items            = is_array( $items ) ? $items : array();
		$allowed_networks = self::get_social_networks();
		$sanitized        = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$network = isset( $item['network'] ) ? sanitize_key( $item['network'] ) : '';
			$url     = isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '';
			$label   = isset( $item['custom_label'] ) ? sanitize_text_field( $item['custom_label'] ) : '';

			if ( ! isset( $allowed_networks[ $network ] ) || '' === $url ) {
				continue;
			}

			if ( '' === $label ) {
				$label = $allowed_networks[ $network ]['label'];
			}

			$sanitized[] = array(
				'network' => $network,
				'url'     => $url,
				'label'   => $label,
				'icon'    => $allowed_networks[ $network ]['icon'],
			);
		}

		return $sanitized;
	}

	/**
	 * Convert a value to boolean.
	 *
	 * @param mixed $value Raw value.
	 * @return bool
	 */
	private function to_bool( $value ) {
		return ! empty( $value ) && 'false' !== $value && '0' !== (string) $value;
	}

	/**
	 * Clamp integer.
	 *
	 * @param mixed $value   Input value.
	 * @param int   $min     Min.
	 * @param int   $max     Max.
	 * @param int   $default Default.
	 * @return int
	 */
	private function clamp_int( $value, $min, $max, $default ) {
		if ( ! is_numeric( $value ) ) {
			return (int) $default;
		}

		$value = (int) $value;
		$value = max( $min, $value );
		$value = min( $max, $value );

		return $value;
	}

	/**
	 * Clamp float.
	 *
	 * @param mixed $value   Input value.
	 * @param float $min     Min.
	 * @param float $max     Max.
	 * @param float $default Default.
	 * @return float
	 */
	private function clamp_float( $value, $min, $max, $default ) {
		if ( ! is_numeric( $value ) ) {
			return (float) $default;
		}

		$value = (float) $value;
		$value = max( $min, $value );
		$value = min( $max, $value );

		return round( $value, 2 );
	}

	/**
	 * Sanitize color with fallback.
	 *
	 * @param mixed  $value    Raw color.
	 * @param string $fallback Fallback color.
	 * @return string
	 */
	private function sanitize_color( $value, $fallback ) {
		$color = sanitize_hex_color( (string) $value );
		return $color ? $color : $fallback;
	}

	/**
	 * Sanitize class list.
	 *
	 * @param string $class_string Raw classes.
	 * @return string
	 */
	private function sanitize_css_class_list( $class_string ) {
		$class_string = is_string( $class_string ) ? $class_string : '';
		$class_parts  = preg_split( '/\s+/', trim( $class_string ) );
		$class_parts  = is_array( $class_parts ) ? $class_parts : array();
		$sanitized    = array();

		foreach ( $class_parts as $class_name ) {
			$class_name = sanitize_html_class( $class_name );
			if ( '' !== $class_name ) {
				$sanitized[] = $class_name;
			}
		}

		return implode( ' ', array_unique( $sanitized ) );
	}

	/**
	 * Build CSS variable array.
	 *
	 * @param array $data Normalized data.
	 * @return array
	 */
	private function build_css_vars( $data ) {
		return array(
			'--df-bg'                 => $data['styles']['background_color'],
			'--df-bg-alt'             => $data['styles']['background_alt_color'],
			'--df-text'               => $data['styles']['text_color'],
			'--df-heading'            => $data['styles']['heading_color'],
			'--df-accent'             => $data['styles']['accent_color'],
			'--df-divider'            => $data['styles']['divider_color'],
			'--df-max-width'          => $data['styles']['max_width'] . 'px',
			'--df-pad-top'            => $data['styles']['section_padding_top'] . 'px',
			'--df-pad-bottom'         => $data['styles']['section_padding_bottom'] . 'px',
			'--df-font-base'          => $data['typography']['font_family_base'],
			'--df-font-heading'       => $data['typography']['font_family_heading'],
			'--df-brand-size-desktop' => $data['typography']['brand_name_size_desktop'] . 'px',
			'--df-brand-size-mobile'  => $data['typography']['brand_name_size_mobile'] . 'px',
			'--df-col-heading-size'   => $data['typography']['column_heading_size'] . 'px',
			'--df-body-size'          => $data['typography']['body_text_size'] . 'px',
			'--df-link-size'          => $data['typography']['link_text_size'] . 'px',
			'--df-brand-weight'       => (string) $data['typography']['brand_weight'],
			'--df-heading-weight'     => (string) $data['typography']['heading_weight'],
			'--df-body-weight'        => (string) $data['typography']['body_weight'],
			'--df-line-height'        => (string) $data['typography']['line_height'],
		);
	}

	/**
	 * Build inline CSS variable string.
	 *
	 * @param array $css_vars CSS vars.
	 * @return string
	 */
	private function build_css_vars_inline( $css_vars ) {
		$parts = array();

		foreach ( $css_vars as $var => $value ) {
			$parts[] = $var . ':' . $value;
		}

		return implode( ';', $parts );
	}

	/**
	 * Darken a color by mixing with black.
	 *
	 * @param string $hex_color Base color.
	 * @param float  $ratio     0..1 amount mixed with black.
	 * @return string
	 */
	private function mix_with_black( $hex_color, $ratio ) {
		$hex_color = ltrim( $hex_color, '#' );
		$ratio     = max( 0, min( 1, (float) $ratio ) );

		if ( 6 !== strlen( $hex_color ) ) {
			return '#111827';
		}

		$r = hexdec( substr( $hex_color, 0, 2 ) );
		$g = hexdec( substr( $hex_color, 2, 2 ) );
		$b = hexdec( substr( $hex_color, 4, 2 ) );

		$r = (int) round( $r * ( 1 - $ratio ) );
		$g = (int) round( $g * ( 1 - $ratio ) );
		$b = (int) round( $b * ( 1 - $ratio ) );

		return sprintf( '#%02x%02x%02x', $r, $g, $b );
	}
}
