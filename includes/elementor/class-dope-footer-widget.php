<?php
/**
 * Elementor widget for DopeFooter.
 *
 * @package DopeFooter
 */

namespace DopeFooter\Elementor;

use DopeFooter\Options;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * DopeFooter Elementor widget.
 */
class Dope_Footer_Widget extends Widget_Base {

	/**
	 * Widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dope_footer_widget';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Dope Footer', 'dope-footer' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-footer';
	}

	/**
	 * Widget categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'dope-footer' );
	}

	/**
	 * Widget keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'footer', 'dope', 'dopefooter', 'links', 'contact' );
	}

	/**
	 * Style dependencies.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'dope-footer-public' );
	}

	/**
	 * Script dependencies.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'dope-footer-public' );
	}

	/**
	 * Register controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$defaults      = Options::get_defaults();
		$link_defaults = $this->get_default_link_repeaters( $defaults );
		$social_repeat = $this->get_default_social_repeaters( $defaults );

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout & Behavior', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'open_links_new_tab',
			array(
				'label'        => esc_html__( 'Open All Links In New Tab', 'dope-footer' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => ! empty( $defaults['open_links_new_tab'] ) ? 'yes' : '',
			)
		);

		$this->add_control(
			'show_back_to_top',
			array(
				'label'        => esc_html__( 'Show Back To Top Button', 'dope-footer' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => ! empty( $defaults['show_back_to_top'] ) ? 'yes' : '',
			)
		);

		$this->add_control(
			'copyright_text',
			array(
				'label'       => esc_html__( 'Copyright Text', 'dope-footer' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '(c) %year% Bassel Daher. All rights reserved.',
				'description' => esc_html__( 'Use %year% for dynamic year.', 'dope-footer' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_brand',
			array(
				'label' => esc_html__( 'Brand', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'brand_name',
			array(
				'label'   => esc_html__( 'Brand Name', 'dope-footer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $defaults['brand_name'],
			)
		);

		$this->add_control(
			'brand_tagline',
			array(
				'label'   => esc_html__( 'Tagline', 'dope-footer' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => $defaults['brand_tagline'],
			)
		);

		$this->end_controls_section();

		$link_repeater = new Repeater();
		$link_repeater->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Label', 'dope-footer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Link', 'dope-footer' ),
			)
		);
		$link_repeater->add_control(
			'url',
			array(
				'label'         => esc_html__( 'URL', 'dope-footer' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://example.com',
				'show_external' => true,
				'default'       => array(
					'url'         => home_url( '/' ),
					'is_external' => false,
					'nofollow'    => false,
				),
			)
		);

		$this->start_controls_section(
			'section_quick_links',
			array(
				'label' => esc_html__( 'Quick Links', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'show_quick_links',
			array(
				'label'        => esc_html__( 'Show Quick Links', 'dope-footer' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => ! empty( $defaults['show_quick_links'] ) ? 'yes' : '',
			)
		);
		$this->add_control(
			'quick_links_title',
			array(
				'label'     => esc_html__( 'Heading', 'dope-footer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Quick Links', 'dope-footer' ),
				'condition' => array( 'show_quick_links' => 'yes' ),
			)
		);
		$this->add_control(
			'quick_links',
			array(
				'label'       => esc_html__( 'Items', 'dope-footer' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $link_repeater->get_controls(),
				'default'     => $link_defaults['quick_links'],
				'title_field' => '{{{ label }}}',
				'condition'   => array( 'show_quick_links' => 'yes' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_other_links',
			array(
				'label' => esc_html__( 'Other Links', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'show_other_links',
			array(
				'label'        => esc_html__( 'Show Other Links', 'dope-footer' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => ! empty( $defaults['show_other_links'] ) ? 'yes' : '',
			)
		);
		$this->add_control(
			'other_links_title',
			array(
				'label'     => esc_html__( 'Heading', 'dope-footer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Other Links', 'dope-footer' ),
				'condition' => array( 'show_other_links' => 'yes' ),
			)
		);
		$this->add_control(
			'other_links',
			array(
				'label'       => esc_html__( 'Items', 'dope-footer' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $link_repeater->get_controls(),
				'default'     => $link_defaults['other_links'],
				'title_field' => '{{{ label }}}',
				'condition'   => array( 'show_other_links' => 'yes' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_contact',
			array(
				'label' => esc_html__( 'Contact', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'show_contact',
			array(
				'label'        => esc_html__( 'Show Contact', 'dope-footer' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => ! empty( $defaults['show_contact'] ) ? 'yes' : '',
			)
		);
		$this->add_control(
			'contact_title',
			array(
				'label'     => esc_html__( 'Heading', 'dope-footer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Contact', 'dope-footer' ),
				'condition' => array( 'show_contact' => 'yes' ),
			)
		);
		$this->add_control(
			'contact_text',
			array(
				'label'     => esc_html__( 'Text', 'dope-footer' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'default'   => $defaults['contact_text'],
				'condition' => array( 'show_contact' => 'yes' ),
			)
		);
		$this->add_control(
			'contact_link_label',
			array(
				'label'     => esc_html__( 'Link Label', 'dope-footer' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => $defaults['contact_link_label'],
				'condition' => array( 'show_contact' => 'yes' ),
			)
		);
		$this->add_control(
			'contact_link_url',
			array(
				'label'         => esc_html__( 'Link URL', 'dope-footer' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://example.com/contact',
				'show_external' => true,
				'default'       => array(
					'url'         => $defaults['contact_link_url'],
					'is_external' => false,
					'nofollow'    => false,
				),
				'condition'     => array( 'show_contact' => 'yes' ),
			)
		);
		$this->end_controls_section();

		$social_repeater = new Repeater();
		$social_repeater->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Label', 'dope-footer' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Social', 'dope-footer' ),
			)
		);
		$social_repeater->add_control(
			'url',
			array(
				'label'         => esc_html__( 'URL', 'dope-footer' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://example.com',
				'show_external' => true,
				'default'       => array(
					'url'         => home_url( '/' ),
					'is_external' => true,
					'nofollow'    => false,
				),
			)
		);
		$social_repeater->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'dope-footer' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-link',
					'library' => 'fa-solid',
				),
			)
		);

		$this->start_controls_section(
			'section_social',
			array(
				'label' => esc_html__( 'Social', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'show_social',
			array(
				'label'        => esc_html__( 'Show Social', 'dope-footer' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => ! empty( $defaults['show_social'] ) ? 'yes' : '',
			)
		);
		$this->add_control(
			'social_links',
			array(
				'label'       => esc_html__( 'Items', 'dope-footer' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $social_repeater->get_controls(),
				'default'     => $social_repeat,
				'title_field' => '{{{ label }}}',
				'condition'   => array( 'show_social' => 'yes' ),
			)
		);
		$this->end_controls_section();

		$this->register_style_controls();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$brand_name    = isset( $settings['brand_name'] ) ? sanitize_text_field( $settings['brand_name'] ) : '';
		$brand_tagline = isset( $settings['brand_tagline'] ) ? sanitize_textarea_field( $settings['brand_tagline'] ) : '';

		$quick_links = $this->sanitize_link_repeater( isset( $settings['quick_links'] ) ? $settings['quick_links'] : array() );
		$other_links = $this->sanitize_link_repeater( isset( $settings['other_links'] ) ? $settings['other_links'] : array() );
		$social      = $this->sanitize_social_repeater( isset( $settings['social_links'] ) ? $settings['social_links'] : array() );

		$show_quick_links = ! empty( $settings['show_quick_links'] ) && 'yes' === $settings['show_quick_links'];
		$show_other_links = ! empty( $settings['show_other_links'] ) && 'yes' === $settings['show_other_links'];
		$show_contact     = ! empty( $settings['show_contact'] ) && 'yes' === $settings['show_contact'];
		$show_social      = ! empty( $settings['show_social'] ) && 'yes' === $settings['show_social'];
		$show_backtotop   = ! empty( $settings['show_back_to_top'] ) && 'yes' === $settings['show_back_to_top'];
		$open_new_tab     = ! empty( $settings['open_links_new_tab'] ) && 'yes' === $settings['open_links_new_tab'];

		$quick_title   = isset( $settings['quick_links_title'] ) ? sanitize_text_field( $settings['quick_links_title'] ) : esc_html__( 'Quick Links', 'dope-footer' );
		$other_title   = isset( $settings['other_links_title'] ) ? sanitize_text_field( $settings['other_links_title'] ) : esc_html__( 'Other Links', 'dope-footer' );
		$contact_title = isset( $settings['contact_title'] ) ? sanitize_text_field( $settings['contact_title'] ) : esc_html__( 'Contact', 'dope-footer' );
		$contact_text  = isset( $settings['contact_text'] ) ? sanitize_textarea_field( $settings['contact_text'] ) : '';

		$contact_link_label = isset( $settings['contact_link_label'] ) ? sanitize_text_field( $settings['contact_link_label'] ) : '';
		$contact_link_url   = $this->extract_url_value( isset( $settings['contact_link_url'] ) ? $settings['contact_link_url'] : array() );
		$contact_link_attrs = $this->build_link_attrs( isset( $settings['contact_link_url'] ) ? $settings['contact_link_url'] : array(), $open_new_tab );

		$copyright = isset( $settings['copyright_text'] ) ? sanitize_text_field( $settings['copyright_text'] ) : '';
		$copyright = str_replace( '%year%', wp_date( 'Y' ), $copyright );

		$css_vars        = $this->build_css_vars( $settings );
		$css_vars_inline = $this->build_css_vars_inline( $css_vars );
		?>
		<footer class="dope-footer dope-footer--signature dope-footer--elementor" style="<?php echo esc_attr( $css_vars_inline ); ?>">
			<div class="dope-footer__container">
				<div class="dope-footer__grid dope-footer__grid--signature">
					<div class="dope-footer__brand-col">
						<h2 class="dope-footer__brand-name"><?php echo esc_html( $brand_name ); ?></h2>
						<?php if ( '' !== trim( $brand_tagline ) ) : ?>
							<p class="dope-footer__brand-tagline"><?php echo esc_html( $brand_tagline ); ?></p>
						<?php endif; ?>
						<?php if ( $show_social && ! empty( $social ) ) : ?>
							<div class="dope-footer__social" aria-label="<?php esc_attr_e( 'Social links', 'dope-footer' ); ?>">
								<?php foreach ( $social as $item ) : ?>
									<a class="dope-footer__social-link" href="<?php echo esc_url( $item['url'] ); ?>" aria-label="<?php echo esc_attr( $item['label'] ); ?>" <?php echo $this->build_link_attrs( $item['url_control'], $open_new_tab ); ?>>
										<?php Icons_Manager::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ); ?>
										<span class="screen-reader-text"><?php echo esc_html( $item['label'] ); ?></span>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $show_quick_links && ! empty( $quick_links ) ) : ?>
						<nav class="dope-footer__links-col dope-footer__links-col--quick" aria-label="<?php echo esc_attr( $quick_title ); ?>">
							<h3 class="dope-footer__col-title"><?php echo esc_html( $quick_title ); ?></h3>
							<ul class="dope-footer__link-list">
								<?php foreach ( $quick_links as $item ) : ?>
									<li>
										<a href="<?php echo esc_url( $item['url'] ); ?>" <?php echo $this->build_link_attrs( $item['url_control'], $open_new_tab ); ?>>
											<?php echo esc_html( $item['label'] ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endif; ?>

					<?php if ( $show_other_links && ! empty( $other_links ) ) : ?>
						<nav class="dope-footer__links-col dope-footer__links-col--other" aria-label="<?php echo esc_attr( $other_title ); ?>">
							<h3 class="dope-footer__col-title"><?php echo esc_html( $other_title ); ?></h3>
							<ul class="dope-footer__link-list">
								<?php foreach ( $other_links as $item ) : ?>
									<li>
										<a href="<?php echo esc_url( $item['url'] ); ?>" <?php echo $this->build_link_attrs( $item['url_control'], $open_new_tab ); ?>>
											<?php echo esc_html( $item['label'] ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endif; ?>

					<?php if ( $show_contact ) : ?>
						<div class="dope-footer__contact-col">
							<h3 class="dope-footer__col-title"><?php echo esc_html( $contact_title ); ?></h3>
							<?php if ( '' !== trim( $contact_text ) ) : ?>
								<p class="dope-footer__contact-text"><?php echo esc_html( $contact_text ); ?></p>
							<?php endif; ?>
							<?php if ( '' !== trim( $contact_link_url ) && '' !== trim( $contact_link_label ) ) : ?>
								<a class="dope-footer__contact-link" href="<?php echo esc_url( $contact_link_url ); ?>" <?php echo $contact_link_attrs; ?>>
									<?php echo esc_html( $contact_link_label ); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<hr class="dope-footer__divider" />
				<p class="dope-footer__copyright"><?php echo esc_html( $copyright ); ?></p>
			</div>

			<?php if ( $show_backtotop ) : ?>
				<button type="button" class="dope-footer__backtotop" data-df-backtotop aria-label="<?php esc_attr_e( 'Back to top', 'dope-footer' ); ?>">
					<span aria-hidden="true">&uarr;</span>
				</button>
			<?php endif; ?>
		</footer>
		<?php
	}

	/**
	 * Register style controls.
	 *
	 * @return void
	 */
	private function register_style_controls() {
		$this->start_controls_section(
			'section_style_background',
			array(
				'label' => esc_html__( 'Background', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'footer_background',
				'label'    => esc_html__( 'Background', 'dope-footer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .dope-footer',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'footer_background_overlay',
				'label'    => esc_html__( 'Overlay', 'dope-footer' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .dope-footer::before',
			)
		);

		$this->add_control(
			'footer_overlay_opacity',
			array(
				'label'     => esc_html__( 'Overlay Opacity', 'dope-footer' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 1,
				'step'      => 0.01,
				'default'   => 0,
				'selectors' => array(
					'{{WRAPPER}} .dope-footer::before' => 'opacity: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_colors',
			array(
				'label' => esc_html__( 'Colors', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$color_controls = array(
			'background_color'     => array( 'Background Start', '#2b2d31', '{{WRAPPER}} .dope-footer', '--df-bg: {{VALUE}};' ),
			'background_alt_color' => array( 'Background End', '#222428', '{{WRAPPER}} .dope-footer', '--df-bg-alt: {{VALUE}};' ),
			'text_color'           => array( 'Body Text', '#dce3ef', '{{WRAPPER}} .dope-footer', '--df-text: {{VALUE}};' ),
			'heading_color'        => array( 'Heading Text', '#ffffff', '{{WRAPPER}} .dope-footer', '--df-heading: {{VALUE}};' ),
			'accent_color'         => array( 'Accent', '#0f5edb', '{{WRAPPER}} .dope-footer', '--df-accent: {{VALUE}};' ),
			'divider_color'        => array( 'Divider', '#8b95aa', '{{WRAPPER}} .dope-footer', '--df-divider: {{VALUE}};' ),
			'social_bg_color'      => array( 'Social Background', '#ffffff', '{{WRAPPER}} .dope-footer__social-link', 'background: {{VALUE}};' ),
			'social_icon_color'    => array( 'Social Icon', '#0f5edb', '{{WRAPPER}} .dope-footer__social-link', 'color: {{VALUE}};' ),
			'backtotop_bg_color'   => array( 'Back To Top Background', '#ffffff', '{{WRAPPER}} .dope-footer__backtotop', 'background: {{VALUE}};' ),
			'backtotop_icon_color' => array( 'Back To Top Icon', '#1f2838', '{{WRAPPER}} .dope-footer__backtotop', 'color: {{VALUE}};' ),
		);

		foreach ( $color_controls as $id => $control ) {
			$this->add_control(
				$id,
				array(
					'label'     => esc_html( $control[0] ),
					'type'      => Controls_Manager::COLOR,
					'default'   => $control[1],
					'selectors' => array(
						$control[2] => $control[3],
					),
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_typography',
			array(
				'label' => esc_html__( 'Typography', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'font_family_base',
			array(
				'label'     => esc_html__( 'Base Font Family', 'dope-footer' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'"Poppins", "Segoe UI", sans-serif'                => 'Poppins / Segoe UI',
					'"Outfit", "Poppins", "Segoe UI", sans-serif'      => 'Outfit / Poppins',
					'"Lora", "Times New Roman", serif'                 => 'Lora / Serif',
					'system-ui, -apple-system, "Segoe UI", sans-serif' => 'System UI',
				),
				'default'   => '"Poppins", "Segoe UI", sans-serif',
				'selectors' => array(
					'{{WRAPPER}} .dope-footer' => '--df-font-base: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'font_family_heading',
			array(
				'label'     => esc_html__( 'Heading Font Family', 'dope-footer' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'"Outfit", "Poppins", "Segoe UI", sans-serif'      => 'Outfit / Poppins',
					'"Poppins", "Segoe UI", sans-serif'                => 'Poppins / Segoe UI',
					'"Lora", "Times New Roman", serif'                 => 'Lora / Serif',
					'system-ui, -apple-system, "Segoe UI", sans-serif' => 'System UI',
				),
				'default'   => '"Outfit", "Poppins", "Segoe UI", sans-serif',
				'selectors' => array(
					'{{WRAPPER}} .dope-footer' => '--df-font-heading: {{VALUE}};',
				),
			)
		);

		$numeric_vars = array(
			'brand_name_size_desktop' => array( 'Brand Size Desktop (px)', 52, 20, 100, '--df-brand-size-desktop: {{VALUE}}px;' ),
			'brand_name_size_mobile'  => array( 'Brand Size Mobile (px)', 36, 16, 80, '--df-brand-size-mobile: {{VALUE}}px;' ),
			'column_heading_size'     => array( 'Column Heading Size (px)', 24, 14, 48, '--df-col-heading-size: {{VALUE}}px;' ),
			'body_text_size'          => array( 'Body Text Size (px)', 18, 12, 36, '--df-body-size: {{VALUE}}px;' ),
			'link_text_size'          => array( 'Link Text Size (px)', 16, 12, 40, '--df-link-size: {{VALUE}}px;' ),
			'line_height'             => array( 'Line Height', 1.6, 1, 2.4, '--df-line-height: {{VALUE}};' ),
		);
		foreach ( $numeric_vars as $id => $args ) {
			$this->add_control(
				$id,
				array(
					'label'     => esc_html( $args[0] ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => $args[1],
					'min'       => $args[2],
					'max'       => $args[3],
					'step'      => 'line_height' === $id ? 0.1 : 1,
					'selectors' => array(
						'{{WRAPPER}} .dope-footer' => $args[4],
					),
				)
			);
		}

		$weight_options = array(
			'300' => '300',
			'400' => '400',
			'500' => '500',
			'600' => '600',
			'700' => '700',
			'800' => '800',
			'900' => '900',
		);
		$this->add_control(
			'brand_weight',
			array(
				'label'     => esc_html__( 'Brand Weight', 'dope-footer' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $weight_options,
				'default'   => '700',
				'selectors' => array(
					'{{WRAPPER}} .dope-footer' => '--df-brand-weight: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'heading_weight',
			array(
				'label'     => esc_html__( 'Heading Weight', 'dope-footer' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $weight_options,
				'default'   => '600',
				'selectors' => array(
					'{{WRAPPER}} .dope-footer' => '--df-heading-weight: {{VALUE}};',
				),
			)
		);
		$this->add_control(
			'body_weight',
			array(
				'label'     => esc_html__( 'Body Weight', 'dope-footer' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $weight_options,
				'default'   => '400',
				'selectors' => array(
					'{{WRAPPER}} .dope-footer' => '--df-body-weight: {{VALUE}};',
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_layout',
			array(
				'label' => esc_html__( 'Layout', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'max_width',
			array(
				'label'     => esc_html__( 'Max Width (px)', 'dope-footer' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1200,
				'min'       => 720,
				'max'       => 1800,
				'step'      => 10,
				'selectors' => array(
					'{{WRAPPER}} .dope-footer' => '--df-max-width: {{VALUE}}px;',
				),
			)
		);
		$this->add_control(
			'section_padding_top',
			array(
				'label'     => esc_html__( 'Padding Top (px)', 'dope-footer' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 72,
				'min'       => 20,
				'max'       => 240,
				'step'      => 2,
				'selectors' => array(
					'{{WRAPPER}} .dope-footer' => '--df-pad-top: {{VALUE}}px;',
				),
			)
		);
		$this->add_control(
			'section_padding_bottom',
			array(
				'label'     => esc_html__( 'Padding Bottom (px)', 'dope-footer' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 36,
				'min'       => 20,
				'max'       => 240,
				'step'      => 2,
				'selectors' => array(
					'{{WRAPPER}} .dope-footer' => '--df-pad-bottom: {{VALUE}}px;',
				),
			)
		);
		$this->add_control(
			'column_gap',
			array(
				'label'     => esc_html__( 'Column Gap (px)', 'dope-footer' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 28,
				'min'       => 0,
				'max'       => 120,
				'step'      => 1,
				'selectors' => array(
					'{{WRAPPER}} .dope-footer__grid' => 'gap: {{VALUE}}px;',
				),
			)
		);
		$this->add_control(
			'social_size',
			array(
				'label'     => esc_html__( 'Social Circle Size (px)', 'dope-footer' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 44,
				'min'       => 24,
				'max'       => 96,
				'step'      => 1,
				'selectors' => array(
					'{{WRAPPER}} .dope-footer__social-link' => 'width: {{VALUE}}px; height: {{VALUE}}px;',
				),
			)
		);
		$this->add_control(
			'social_icon_size',
			array(
				'label'     => esc_html__( 'Social Icon Size (px)', 'dope-footer' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 20,
				'min'       => 12,
				'max'       => 52,
				'step'      => 1,
				'selectors' => array(
					'{{WRAPPER}} .dope-footer__social-link .dashicons' => 'font-size: {{VALUE}}px; width: {{VALUE}}px; height: {{VALUE}}px;',
					'{{WRAPPER}} .dope-footer__social-link i'           => 'font-size: {{VALUE}}px;',
					'{{WRAPPER}} .dope-footer__social-link svg'         => 'width: {{VALUE}}px; height: {{VALUE}}px;',
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_columns',
			array(
				'label' => esc_html__( 'Columns (Responsive)', 'dope-footer' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'brand_column_width',
			array(
				'label'          => esc_html__( 'Brand Column Width', 'dope-footer' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( '%' ),
				'range'          => array(
					'%' => array(
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'        => array(
					'size' => 37,
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'      => array(
					'{{WRAPPER}} .dope-footer__grid--signature' => 'display:flex; flex-wrap:wrap; align-items:flex-start;',
					'{{WRAPPER}} .dope-footer__brand-col'       => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'quick_column_width',
			array(
				'label'          => esc_html__( 'Quick Links Column Width', 'dope-footer' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( '%' ),
				'range'          => array(
					'%' => array(
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'        => array(
					'size' => 21,
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'      => array(
					'{{WRAPPER}} .dope-footer__grid--signature'        => 'display:flex; flex-wrap:wrap; align-items:flex-start;',
					'{{WRAPPER}} .dope-footer__links-col--quick'       => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'other_column_width',
			array(
				'label'          => esc_html__( 'Other Links Column Width', 'dope-footer' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( '%' ),
				'range'          => array(
					'%' => array(
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'        => array(
					'size' => 22,
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'      => array(
					'{{WRAPPER}} .dope-footer__grid--signature'        => 'display:flex; flex-wrap:wrap; align-items:flex-start;',
					'{{WRAPPER}} .dope-footer__links-col--other'       => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'contact_column_width',
			array(
				'label'          => esc_html__( 'Contact Column Width', 'dope-footer' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( '%' ),
				'range'          => array(
					'%' => array(
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					),
				),
				'default'        => array(
					'size' => 20,
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'      => array(
					'{{WRAPPER}} .dope-footer__grid--signature' => 'display:flex; flex-wrap:wrap; align-items:flex-start;',
					'{{WRAPPER}} .dope-footer__contact-col'     => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get default link repeater settings.
	 *
	 * @param array $defaults Defaults.
	 * @return array
	 */
	private function get_default_link_repeaters( $defaults ) {
		return array(
			'quick_links' => $this->map_links_to_repeater( isset( $defaults['quick_links'] ) ? $defaults['quick_links'] : array() ),
			'other_links' => $this->map_links_to_repeater( isset( $defaults['other_links'] ) ? $defaults['other_links'] : array() ),
		);
	}

	/**
	 * Convert link arrays to repeater defaults.
	 *
	 * @param array $items Link items.
	 * @return array
	 */
	private function map_links_to_repeater( $items ) {
		$mapped = array();
		$items  = is_array( $items ) ? $items : array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$mapped[] = array(
				'label' => isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '',
				'url'   => array(
					'url'         => isset( $item['url'] ) ? esc_url( $item['url'] ) : '',
					'is_external' => false,
					'nofollow'    => false,
				),
			);
		}

		return $mapped;
	}

	/**
	 * Default social repeater values.
	 *
	 * @param array $defaults Defaults.
	 * @return array
	 */
	private function get_default_social_repeaters( $defaults ) {
		$network_icons = array(
			'linkedin' => array(
				'value'   => 'fab fa-linkedin-in',
				'library' => 'fa-brands',
			),
			'x'        => array(
				'value'   => 'fab fa-x-twitter',
				'library' => 'fa-brands',
			),
			'email'    => array(
				'value'   => 'fas fa-envelope',
				'library' => 'fa-solid',
			),
		);

		$items  = isset( $defaults['social_links'] ) && is_array( $defaults['social_links'] ) ? $defaults['social_links'] : array();
		$mapped = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$network = isset( $item['network'] ) ? sanitize_key( $item['network'] ) : '';
			$icon    = isset( $network_icons[ $network ] ) ? $network_icons[ $network ] : array(
				'value'   => 'fas fa-link',
				'library' => 'fa-solid',
			);

			$mapped[] = array(
				'label' => isset( $item['custom_label'] ) ? sanitize_text_field( $item['custom_label'] ) : esc_html__( 'Social', 'dope-footer' ),
				'url'   => array(
					'url'         => isset( $item['url'] ) ? esc_url( $item['url'] ) : '',
					'is_external' => true,
					'nofollow'    => false,
				),
				'icon'  => $icon,
			);
		}

		return $mapped;
	}

	/**
	 * Build CSS variable array from settings.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	private function build_css_vars( $settings ) {
		return array(
			'--df-bg'                 => $this->sanitize_color( isset( $settings['background_color'] ) ? $settings['background_color'] : '', '#2b2d31' ),
			'--df-bg-alt'             => $this->sanitize_color( isset( $settings['background_alt_color'] ) ? $settings['background_alt_color'] : '', '#222428' ),
			'--df-text'               => $this->sanitize_color( isset( $settings['text_color'] ) ? $settings['text_color'] : '', '#dce3ef' ),
			'--df-heading'            => $this->sanitize_color( isset( $settings['heading_color'] ) ? $settings['heading_color'] : '', '#ffffff' ),
			'--df-accent'             => $this->sanitize_color( isset( $settings['accent_color'] ) ? $settings['accent_color'] : '', '#0f5edb' ),
			'--df-divider'            => $this->sanitize_color( isset( $settings['divider_color'] ) ? $settings['divider_color'] : '', '#8b95aa' ),
			'--df-max-width'          => $this->sanitize_px( isset( $settings['max_width'] ) ? $settings['max_width'] : 1200, 720, 1800, 1200 ),
			'--df-pad-top'            => $this->sanitize_px( isset( $settings['section_padding_top'] ) ? $settings['section_padding_top'] : 72, 20, 240, 72 ),
			'--df-pad-bottom'         => $this->sanitize_px( isset( $settings['section_padding_bottom'] ) ? $settings['section_padding_bottom'] : 36, 20, 240, 36 ),
			'--df-font-base'          => $this->sanitize_font_stack( isset( $settings['font_family_base'] ) ? $settings['font_family_base'] : '"Poppins", "Segoe UI", sans-serif' ),
			'--df-font-heading'       => $this->sanitize_font_stack( isset( $settings['font_family_heading'] ) ? $settings['font_family_heading'] : '"Outfit", "Poppins", "Segoe UI", sans-serif' ),
			'--df-brand-size-desktop' => $this->sanitize_px( isset( $settings['brand_name_size_desktop'] ) ? $settings['brand_name_size_desktop'] : 52, 20, 100, 52 ),
			'--df-brand-size-mobile'  => $this->sanitize_px( isset( $settings['brand_name_size_mobile'] ) ? $settings['brand_name_size_mobile'] : 36, 16, 80, 36 ),
			'--df-col-heading-size'   => $this->sanitize_px( isset( $settings['column_heading_size'] ) ? $settings['column_heading_size'] : 24, 14, 48, 24 ),
			'--df-body-size'          => $this->sanitize_px( isset( $settings['body_text_size'] ) ? $settings['body_text_size'] : 18, 12, 36, 18 ),
			'--df-link-size'          => $this->sanitize_px( isset( $settings['link_text_size'] ) ? $settings['link_text_size'] : 16, 12, 40, 16 ),
			'--df-brand-weight'       => $this->sanitize_weight( isset( $settings['brand_weight'] ) ? $settings['brand_weight'] : '700', '700' ),
			'--df-heading-weight'     => $this->sanitize_weight( isset( $settings['heading_weight'] ) ? $settings['heading_weight'] : '600', '600' ),
			'--df-body-weight'        => $this->sanitize_weight( isset( $settings['body_weight'] ) ? $settings['body_weight'] : '400', '400' ),
			'--df-line-height'        => $this->sanitize_line_height( isset( $settings['line_height'] ) ? $settings['line_height'] : 1.6, 1.6 ),
		);
	}

	/**
	 * Build CSS inline var string.
	 *
	 * @param array $css_vars CSS vars.
	 * @return string
	 */
	private function build_css_vars_inline( $css_vars ) {
		$chunks = array();

		foreach ( $css_vars as $name => $value ) {
			$chunks[] = $name . ':' . $value;
		}

		return implode( ';', $chunks );
	}

	/**
	 * Sanitize link repeater settings.
	 *
	 * @param array $items Repeater values.
	 * @return array
	 */
	private function sanitize_link_repeater( $items ) {
		$items     = is_array( $items ) ? $items : array();
		$sanitized = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$label = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
			$url   = $this->extract_url_value( isset( $item['url'] ) ? $item['url'] : array() );

			if ( '' === $label || '' === $url ) {
				continue;
			}

			$sanitized[] = array(
				'label'       => $label,
				'url'         => $url,
				'url_control' => isset( $item['url'] ) ? $item['url'] : array(),
			);
		}

		return $sanitized;
	}

	/**
	 * Sanitize social repeater settings.
	 *
	 * @param array $items Repeater values.
	 * @return array
	 */
	private function sanitize_social_repeater( $items ) {
		$items     = is_array( $items ) ? $items : array();
		$sanitized = array();

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$label = isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : '';
			$url   = $this->extract_url_value( isset( $item['url'] ) ? $item['url'] : array() );
			$icon  = isset( $item['icon'] ) && is_array( $item['icon'] ) ? $item['icon'] : array();

			if ( '' === $label || '' === $url ) {
				continue;
			}

			if ( empty( $icon['value'] ) ) {
				$icon = array(
					'value'   => 'fas fa-link',
					'library' => 'fa-solid',
				);
			}

			$sanitized[] = array(
				'label'       => $label,
				'url'         => $url,
				'icon'        => $icon,
				'url_control' => isset( $item['url'] ) ? $item['url'] : array(),
			);
		}

		return $sanitized;
	}

	/**
	 * Extract URL value from Elementor URL control output.
	 *
	 * @param mixed $url_control URL control data.
	 * @return string
	 */
	private function extract_url_value( $url_control ) {
		if ( is_array( $url_control ) && ! empty( $url_control['url'] ) ) {
			return esc_url( $url_control['url'] );
		}

		if ( is_string( $url_control ) ) {
			return esc_url( $url_control );
		}

		return '';
	}

	/**
	 * Build target/rel attributes for links.
	 *
	 * @param mixed $url_control   URL control value.
	 * @param bool  $force_new_tab Force opening in new tab.
	 * @return string
	 */
	private function build_link_attrs( $url_control, $force_new_tab ) {
		$attributes = array();
		$rels       = array();
		$external   = false;
		$nofollow   = false;

		if ( is_array( $url_control ) ) {
			$external = ! empty( $url_control['is_external'] );
			$nofollow = ! empty( $url_control['nofollow'] );
		}

		if ( $force_new_tab || $external ) {
			$attributes['target'] = '_blank';
			$rels[]               = 'noopener';
			$rels[]               = 'noreferrer';
		}

		if ( $nofollow ) {
			$rels[] = 'nofollow';
		}

		if ( ! empty( $rels ) ) {
			$attributes['rel'] = implode( ' ', array_unique( $rels ) );
		}

		if ( empty( $attributes ) ) {
			return '';
		}

		$chunks = array();
		foreach ( $attributes as $name => $value ) {
			$chunks[] = sprintf( '%s="%s"', $name, esc_attr( $value ) );
		}

		return implode( ' ', $chunks );
	}

	/**
	 * Sanitize a hex color.
	 *
	 * @param string $value    Color value.
	 * @param string $fallback Fallback color.
	 * @return string
	 */
	private function sanitize_color( $value, $fallback ) {
		$color = sanitize_hex_color( (string) $value );

		return $color ? $color : $fallback;
	}

	/**
	 * Sanitize a px value.
	 *
	 * @param mixed $value   Value.
	 * @param int   $min     Min.
	 * @param int   $max     Max.
	 * @param int   $default Default.
	 * @return string
	 */
	private function sanitize_px( $value, $min, $max, $default ) {
		if ( ! is_numeric( $value ) ) {
			$value = $default;
		}

		$value = (int) $value;
		$value = max( $min, $value );
		$value = min( $max, $value );

		return $value . 'px';
	}

	/**
	 * Sanitize font family stack.
	 *
	 * @param string $value Font stack.
	 * @return string
	 */
	private function sanitize_font_stack( $value ) {
		$value = sanitize_text_field( (string) $value );

		return '' !== $value ? $value : '"Poppins", "Segoe UI", sans-serif';
	}

	/**
	 * Sanitize font weight.
	 *
	 * @param mixed  $value    Weight.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	private function sanitize_weight( $value, $fallback ) {
		$weight = is_numeric( $value ) ? (int) $value : (int) $fallback;
		$weight = max( 100, min( 900, $weight ) );

		return (string) $weight;
	}

	/**
	 * Sanitize line-height.
	 *
	 * @param mixed $value    Value.
	 * @param float $fallback Fallback.
	 * @return string
	 */
	private function sanitize_line_height( $value, $fallback ) {
		$line_height = is_numeric( $value ) ? (float) $value : (float) $fallback;
		$line_height = max( 1.0, min( 2.4, $line_height ) );

		return (string) round( $line_height, 2 );
	}
}
