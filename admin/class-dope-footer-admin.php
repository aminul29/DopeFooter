<?php
/**
 * Admin class.
 *
 * @package DopeFooter
 */

namespace DopeFooter;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Settings page slug.
	 */
	const PAGE_SLUG = 'dope-footer-settings';

	/**
	 * Hub page slug.
	 */
	const HUB_SLUG = 'dope-footer-hub';

	/**
	 * Footer post type.
	 */
	const FOOTER_CPT = 'dope_footer_item';

	/**
	 * Tools page slug.
	 */
	const TOOLS_SLUG = 'dope-footer-tools';

	/**
	 * Help page slug.
	 */
	const HELP_SLUG = 'dope-footer-help';

	/**
	 * Options instance.
	 *
	 * @var Options
	 */
	private $options;

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
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'register_footer_post_type' ), 1 );
		add_action( 'init', array( $this, 'register_settings_page' ), 5 );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'reorder_admin_submenus' ), 999 );
		add_filter( 'manage_' . self::FOOTER_CPT . '_posts_columns', array( $this, 'register_footer_list_columns' ) );
		add_action( 'manage_' . self::FOOTER_CPT . '_posts_custom_column', array( $this, 'render_footer_list_column' ), 10, 2 );
		add_filter( 'csf_' . Options::OPTION_KEY . '_save', array( $this, 'sanitize_saved_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'admin_body_class', array( $this, 'add_admin_body_class' ) );
	}

	/**
	 * Register footer post type for future multi-footer management.
	 *
	 * @return void
	 */
	public function register_footer_post_type() {
		$labels = array(
			'name'               => __( 'Footers', 'dope-footer' ),
			'singular_name'      => __( 'Footer', 'dope-footer' ),
			'add_new'            => __( 'Add new footer', 'dope-footer' ),
			'add_new_item'       => __( 'Add New Footer', 'dope-footer' ),
			'edit_item'          => __( 'Edit Footer', 'dope-footer' ),
			'new_item'           => __( 'New Footer', 'dope-footer' ),
			'view_item'          => __( 'View Footer', 'dope-footer' ),
			'search_items'       => __( 'Search Footers', 'dope-footer' ),
			'not_found'          => __( 'No footers found', 'dope-footer' ),
			'not_found_in_trash' => __( 'No footers found in Trash', 'dope-footer' ),
			'all_items'          => __( 'All Footers', 'dope-footer' ),
		);

		register_post_type(
			self::FOOTER_CPT,
			array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'supports'            => array( 'title' ),
				'map_meta_cap'        => true,
				'capability_type'     => 'post',
				'menu_position'       => null,
			)
		);
	}

	/**
	 * Register plugin admin menu and submenus.
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		add_menu_page(
			__( 'DopeFooter', 'dope-footer' ),
			__( 'DopeFooter', 'dope-footer' ),
			'manage_options',
			self::HUB_SLUG,
			array( $this, 'render_manage_page' ),
			'dashicons-editor-kitchensink',
			58
		);

		add_submenu_page(
			self::HUB_SLUG,
			__( 'Manage Footer', 'dope-footer' ),
			__( 'Manage Footer', 'dope-footer' ),
			'manage_options',
			self::HUB_SLUG,
			array( $this, 'render_manage_page' )
		);

		add_submenu_page(
			self::HUB_SLUG,
			__( 'All Footers', 'dope-footer' ),
			__( 'All Footers', 'dope-footer' ),
			'edit_posts',
			'edit.php?post_type=' . self::FOOTER_CPT
		);

		add_submenu_page(
			self::HUB_SLUG,
			__( 'Add new footer', 'dope-footer' ),
			__( 'Add new footer', 'dope-footer' ),
			'edit_posts',
			'post-new.php?post_type=' . self::FOOTER_CPT
		);

		add_submenu_page(
			self::HUB_SLUG,
			__( 'Tools', 'dope-footer' ),
			__( 'Tools', 'dope-footer' ),
			'manage_options',
			self::TOOLS_SLUG,
			array( $this, 'render_tools_page' )
		);

		add_submenu_page(
			self::HUB_SLUG,
			__( 'Help', 'dope-footer' ),
			__( 'Help', 'dope-footer' ),
			'manage_options',
			self::HELP_SLUG,
			array( $this, 'render_help_page' )
		);
	}

	/**
	 * Ensure submenu order matches product requirement.
	 *
	 * @return void
	 */
	public function reorder_admin_submenus() {
		global $submenu;

		if ( ! isset( $submenu[ self::HUB_SLUG ] ) || ! is_array( $submenu[ self::HUB_SLUG ] ) ) {
			return;
		}

		$current = $submenu[ self::HUB_SLUG ];
		$map     = array();
		foreach ( $current as $item ) {
			if ( isset( $item[2] ) ) {
				$map[ $item[2] ] = $item;
			}
		}

		$ordered_keys = array(
			self::HUB_SLUG,
			'edit.php?post_type=' . self::FOOTER_CPT,
			'post-new.php?post_type=' . self::FOOTER_CPT,
			self::TOOLS_SLUG,
			self::PAGE_SLUG,
			self::HELP_SLUG,
		);

		$ordered = array();
		foreach ( $ordered_keys as $slug ) {
			if ( isset( $map[ $slug ] ) ) {
				$ordered[] = $map[ $slug ];
				unset( $map[ $slug ] );
			}
		}

		foreach ( $map as $item ) {
			$ordered[] = $item;
		}

		$submenu[ self::HUB_SLUG ] = $ordered;
	}

	/**
	 * Register custom columns in footer list table.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function register_footer_list_columns( $columns ) {
		$columns  = is_array( $columns ) ? $columns : array();
		$updated  = array();
		$inserted = false;

		foreach ( $columns as $column_key => $column_label ) {
			$updated[ $column_key ] = $column_label;

			if ( 'title' === $column_key ) {
				$updated['dope_footer_shortcode'] = __( 'Shortcode', 'dope-footer' );
				$inserted                         = true;
			}
		}

		if ( ! $inserted ) {
			$updated['dope_footer_shortcode'] = __( 'Shortcode', 'dope-footer' );
		}

		return $updated;
	}

	/**
	 * Render custom column value in footer list table.
	 *
	 * @param string $column  Current column key.
	 * @param int    $post_id Current post ID.
	 * @return void
	 */
	public function render_footer_list_column( $column, $post_id ) {
		if ( 'dope_footer_shortcode' !== $column ) {
			return;
		}

		$post_id     = absint( $post_id );
		$shortcode   = $this->build_footer_shortcode( $post_id );
		$feedback_id = 'dope-footer-copy-feedback-' . $post_id;
		?>
		<div class="dope-footer-list-shortcode">
			<code><?php echo esc_html( $shortcode ); ?></code>
			<button
				type="button"
				class="button button-small dope-footer-list-copy"
				data-shortcode="<?php echo esc_attr( $shortcode ); ?>"
				aria-describedby="<?php echo esc_attr( $feedback_id ); ?>"
			>
				<span class="dashicons dashicons-clipboard" aria-hidden="true"></span>
				<span class="dope-footer-copy-label"><?php esc_html_e( 'Copy', 'dope-footer' ); ?></span>
			</button>
			<span id="<?php echo esc_attr( $feedback_id ); ?>" class="dope-footer-list-copy-feedback" aria-live="polite"></span>
		</div>
		<?php
	}

	/**
	 * Register settings page via CodeStar.
	 *
	 * @return void
	 */
	public function register_settings_page() {
		static $registered = false;

		if ( $registered ) {
			return;
		}

		$csf_file = DOPE_FOOTER_PATH . 'vendor/codestar-framework/codestar-framework.php';
		if ( file_exists( $csf_file ) && ! class_exists( 'CSF' ) ) {
			require_once $csf_file;
		}

		if ( ! class_exists( 'CSF' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_codestar_notice' ) );
			return;
		}

		$defaults            = Options::get_defaults();
		$social_networks     = Options::get_social_networks();
		$social_select_items = array();

		foreach ( $social_networks as $network_key => $network ) {
			$social_select_items[ $network_key ] = $network['label'];
		}

		\CSF::createOptions(
			Options::OPTION_KEY,
			array(
				'menu_title'     => __( 'Settings', 'dope-footer' ),
				'menu_slug'      => self::PAGE_SLUG,
				'menu_type'      => 'submenu',
				'menu_parent'    => self::HUB_SLUG,
				'framework_title'=> __( 'DopeFooter Settings', 'dope-footer' ),
				'class'          => 'dope-footer-settings-options dope-footer-has-sidebar',
				'nav'            => 'inline',
				'show_search'    => false,
				'show_reset_all' => false,
				'show_footer'    => false,
				'show_bar_menu'  => false,
				'theme'          => 'dark',
				'footer_after'   => $this->get_settings_sidebar_markup(),
			)
		);

		\CSF::createSection(
			Options::OPTION_KEY,
			array(
				'title'  => __( 'Layout Presets', 'dope-footer' ),
				'icon'   => 'fas fa-th-large',
				'fields' => array(
					array(
						'id'      => 'layout_preset',
						'type'    => 'image_select',
						'title'   => __( 'Choose Layout Preset', 'dope-footer' ),
						'options' => array(
							'preset_signature' => DOPE_FOOTER_URL . 'assets/images/preset-signature.svg',
							'preset_split'     => DOPE_FOOTER_URL . 'assets/images/preset-split.svg',
							'preset_minimal'   => DOPE_FOOTER_URL . 'assets/images/preset-minimal.svg',
						),
						'default' => $defaults['layout_preset'],
						'inline'  => true,
					),
				),
			)
		);

		\CSF::createSection(
			Options::OPTION_KEY,
			array(
				'title'  => __( 'General Settings', 'dope-footer' ),
				'icon'   => 'fas fa-cog',
				'fields' => array(
					array(
						'id'      => 'brand_name',
						'type'    => 'text',
						'title'   => __( 'Brand Name', 'dope-footer' ),
						'default' => $defaults['brand_name'],
					),
					array(
						'id'      => 'brand_tagline',
						'type'    => 'textarea',
						'title'   => __( 'Brand Tagline', 'dope-footer' ),
						'default' => $defaults['brand_tagline'],
					),
					array(
						'id'           => 'quick_links',
						'type'         => 'group',
						'title'        => __( 'Quick Links', 'dope-footer' ),
						'button_title' => __( 'Add Quick Link', 'dope-footer' ),
						'fields'       => array(
							array(
								'id'    => 'label',
								'type'  => 'text',
								'title' => __( 'Label', 'dope-footer' ),
							),
							array(
								'id'         => 'url',
								'type'       => 'text',
								'title'      => __( 'URL', 'dope-footer' ),
								'attributes' => array(
									'type'        => 'url',
									'placeholder' => 'https://example.com',
								),
							),
						),
						'default'      => $defaults['quick_links'],
					),
					array(
						'id'           => 'other_links',
						'type'         => 'group',
						'title'        => __( 'Other Links', 'dope-footer' ),
						'button_title' => __( 'Add Other Link', 'dope-footer' ),
						'fields'       => array(
							array(
								'id'    => 'label',
								'type'  => 'text',
								'title' => __( 'Label', 'dope-footer' ),
							),
							array(
								'id'         => 'url',
								'type'       => 'text',
								'title'      => __( 'URL', 'dope-footer' ),
								'attributes' => array(
									'type'        => 'url',
									'placeholder' => 'https://example.com',
								),
							),
						),
						'default'      => $defaults['other_links'],
					),
					array(
						'id'      => 'contact_text',
						'type'    => 'textarea',
						'title'   => __( 'Contact Text', 'dope-footer' ),
						'default' => $defaults['contact_text'],
					),
					array(
						'id'      => 'contact_link_label',
						'type'    => 'text',
						'title'   => __( 'Contact Link Label', 'dope-footer' ),
						'default' => $defaults['contact_link_label'],
					),
					array(
						'id'         => 'contact_link_url',
						'type'       => 'text',
						'title'      => __( 'Contact Link URL', 'dope-footer' ),
						'default'    => $defaults['contact_link_url'],
						'attributes' => array(
							'type'        => 'url',
							'placeholder' => 'https://example.com/contact',
						),
					),
					array(
						'id'           => 'social_links',
						'type'         => 'group',
						'title'        => __( 'Social Links', 'dope-footer' ),
						'button_title' => __( 'Add Social Link', 'dope-footer' ),
						'fields'       => array(
							array(
								'id'      => 'network',
								'type'    => 'select',
								'title'   => __( 'Network', 'dope-footer' ),
								'options' => $social_select_items,
							),
							array(
								'id'         => 'url',
								'type'       => 'text',
								'title'      => __( 'URL', 'dope-footer' ),
								'attributes' => array(
									'type'        => 'url',
									'placeholder' => 'https://example.com',
								),
							),
							array(
								'id'    => 'custom_label',
								'type'  => 'text',
								'title' => __( 'Custom Label', 'dope-footer' ),
							),
						),
						'default'      => $defaults['social_links'],
					),
					array(
						'id'          => 'copyright_text',
						'type'        => 'text',
						'title'       => __( 'Copyright Text', 'dope-footer' ),
						'subtitle'    => __( 'Use %year% to inject current year dynamically.', 'dope-footer' ),
						'default'     => $defaults['copyright_text'],
					),
				),
			)
		);

		\CSF::createSection(
			Options::OPTION_KEY,
			array(
				'title'  => __( 'Display Settings', 'dope-footer' ),
				'icon'   => 'fas fa-desktop',
				'fields' => array(
					array(
						'id'      => 'show_social',
						'type'    => 'switcher',
						'title'   => __( 'Show Social Icons', 'dope-footer' ),
						'default' => $defaults['show_social'],
					),
					array(
						'id'      => 'show_quick_links',
						'type'    => 'switcher',
						'title'   => __( 'Show Quick Links', 'dope-footer' ),
						'default' => $defaults['show_quick_links'],
					),
					array(
						'id'      => 'show_other_links',
						'type'    => 'switcher',
						'title'   => __( 'Show Other Links', 'dope-footer' ),
						'default' => $defaults['show_other_links'],
					),
					array(
						'id'      => 'show_contact',
						'type'    => 'switcher',
						'title'   => __( 'Show Contact Block', 'dope-footer' ),
						'default' => $defaults['show_contact'],
					),
					array(
						'id'      => 'show_back_to_top',
						'type'    => 'switcher',
						'title'   => __( 'Show Back To Top Button', 'dope-footer' ),
						'default' => $defaults['show_back_to_top'],
					),
					array(
						'id'      => 'background_color',
						'type'    => 'color',
						'title'   => __( 'Background Color', 'dope-footer' ),
						'default' => $defaults['background_color'],
					),
					array(
						'id'      => 'text_color',
						'type'    => 'color',
						'title'   => __( 'Text Color', 'dope-footer' ),
						'default' => $defaults['text_color'],
					),
					array(
						'id'      => 'heading_color',
						'type'    => 'color',
						'title'   => __( 'Heading Color', 'dope-footer' ),
						'default' => $defaults['heading_color'],
					),
					array(
						'id'      => 'accent_color',
						'type'    => 'color',
						'title'   => __( 'Accent Color', 'dope-footer' ),
						'default' => $defaults['accent_color'],
					),
					array(
						'id'      => 'divider_color',
						'type'    => 'color',
						'title'   => __( 'Divider Color', 'dope-footer' ),
						'default' => $defaults['divider_color'],
					),
					array(
						'id'         => 'max_width',
						'type'       => 'number',
						'title'      => __( 'Max Width (px)', 'dope-footer' ),
						'default'    => $defaults['max_width'],
						'attributes' => array(
							'min'  => 720,
							'max'  => 1800,
							'step' => 10,
						),
					),
					array(
						'id'         => 'section_padding_top',
						'type'       => 'number',
						'title'      => __( 'Section Padding Top (px)', 'dope-footer' ),
						'default'    => $defaults['section_padding_top'],
						'attributes' => array(
							'min'  => 20,
							'max'  => 240,
							'step' => 2,
						),
					),
					array(
						'id'         => 'section_padding_bottom',
						'type'       => 'number',
						'title'      => __( 'Section Padding Bottom (px)', 'dope-footer' ),
						'default'    => $defaults['section_padding_bottom'],
						'attributes' => array(
							'min'  => 20,
							'max'  => 240,
							'step' => 2,
						),
					),
					array(
						'id'      => 'open_links_new_tab',
						'type'    => 'switcher',
						'title'   => __( 'Open Links In New Tab', 'dope-footer' ),
						'default' => $defaults['open_links_new_tab'],
					),
				),
			)
		);

		\CSF::createSection(
			Options::OPTION_KEY,
			array(
				'title'  => __( 'Typography', 'dope-footer' ),
				'icon'   => 'fas fa-font',
				'fields' => array(
					array(
						'id'      => 'font_family_base',
						'type'    => 'select',
						'title'   => __( 'Base Font Family', 'dope-footer' ),
						'options' => array(
							'"Poppins", "Segoe UI", sans-serif'             => 'Poppins / Segoe UI',
							'"Outfit", "Poppins", "Segoe UI", sans-serif'   => 'Outfit / Poppins',
							'"Lora", "Times New Roman", serif'              => 'Lora / Serif',
							'system-ui, -apple-system, "Segoe UI", sans-serif' => 'System UI',
						),
						'default' => $defaults['font_family_base'],
					),
					array(
						'id'      => 'font_family_heading',
						'type'    => 'select',
						'title'   => __( 'Heading Font Family', 'dope-footer' ),
						'options' => array(
							'"Outfit", "Poppins", "Segoe UI", sans-serif'   => 'Outfit / Poppins',
							'"Poppins", "Segoe UI", sans-serif'             => 'Poppins / Segoe UI',
							'"Lora", "Times New Roman", serif'              => 'Lora / Serif',
							'system-ui, -apple-system, "Segoe UI", sans-serif' => 'System UI',
						),
						'default' => $defaults['font_family_heading'],
					),
					array(
						'id'         => 'brand_name_size_desktop',
						'type'       => 'number',
						'title'      => __( 'Brand Name Size Desktop (px)', 'dope-footer' ),
						'default'    => $defaults['brand_name_size_desktop'],
						'attributes' => array(
							'min'  => 24,
							'max'  => 96,
							'step' => 1,
						),
					),
					array(
						'id'         => 'brand_name_size_mobile',
						'type'       => 'number',
						'title'      => __( 'Brand Name Size Mobile (px)', 'dope-footer' ),
						'default'    => $defaults['brand_name_size_mobile'],
						'attributes' => array(
							'min'  => 18,
							'max'  => 72,
							'step' => 1,
						),
					),
					array(
						'id'         => 'column_heading_size',
						'type'       => 'number',
						'title'      => __( 'Column Heading Size (px)', 'dope-footer' ),
						'default'    => $defaults['column_heading_size'],
						'attributes' => array(
							'min'  => 16,
							'max'  => 40,
							'step' => 1,
						),
					),
					array(
						'id'         => 'body_text_size',
						'type'       => 'number',
						'title'      => __( 'Body Text Size (px)', 'dope-footer' ),
						'default'    => $defaults['body_text_size'],
						'attributes' => array(
							'min'  => 12,
							'max'  => 30,
							'step' => 1,
						),
					),
					array(
						'id'         => 'link_text_size',
						'type'       => 'number',
						'title'      => __( 'Link Text Size (px)', 'dope-footer' ),
						'default'    => $defaults['link_text_size'],
						'attributes' => array(
							'min'  => 12,
							'max'  => 40,
							'step' => 1,
						),
					),
					array(
						'id'      => 'brand_weight',
						'type'    => 'select',
						'title'   => __( 'Brand Weight', 'dope-footer' ),
						'options' => array(
							'400' => '400',
							'500' => '500',
							'600' => '600',
							'700' => '700',
							'800' => '800',
							'900' => '900',
						),
						'default' => (string) $defaults['brand_weight'],
					),
					array(
						'id'      => 'heading_weight',
						'type'    => 'select',
						'title'   => __( 'Heading Weight', 'dope-footer' ),
						'options' => array(
							'400' => '400',
							'500' => '500',
							'600' => '600',
							'700' => '700',
							'800' => '800',
							'900' => '900',
						),
						'default' => (string) $defaults['heading_weight'],
					),
					array(
						'id'      => 'body_weight',
						'type'    => 'select',
						'title'   => __( 'Body Weight', 'dope-footer' ),
						'options' => array(
							'300' => '300',
							'400' => '400',
							'500' => '500',
							'600' => '600',
							'700' => '700',
						),
						'default' => (string) $defaults['body_weight'],
					),
					array(
						'id'         => 'line_height',
						'type'       => 'number',
						'title'      => __( 'Line Height', 'dope-footer' ),
						'default'    => $defaults['line_height'],
						'attributes' => array(
							'min'  => 1,
							'max'  => 2.4,
							'step' => 0.1,
						),
					),
				),
			)
		);

		$registered = true;
	}

	/**
	 * Sanitize values before saving.
	 *
	 * @param mixed $data Raw settings data.
	 * @return array
	 */
	public function sanitize_saved_options( $data ) {
		return $this->options->sanitize_for_storage( $data );
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin hook.
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		if ( ! $this->is_dope_footer_screen( $hook ) ) {
			return;
		}

		$admin_css_path = DOPE_FOOTER_PATH . 'assets/css/admin.css';
		$admin_js_path  = DOPE_FOOTER_PATH . 'assets/js/admin.js';
		$admin_css_ver  = file_exists( $admin_css_path ) ? (string) filemtime( $admin_css_path ) : DOPE_FOOTER_VERSION;
		$admin_js_ver   = file_exists( $admin_js_path ) ? (string) filemtime( $admin_js_path ) : DOPE_FOOTER_VERSION;

		wp_enqueue_style(
			'dope-footer-admin',
			DOPE_FOOTER_URL . 'assets/css/admin.css',
			array(),
			$admin_css_ver
		);

		wp_enqueue_script(
			'dope-footer-admin',
			DOPE_FOOTER_URL . 'assets/js/admin.js',
			array(),
			$admin_js_ver,
			true
		);

		wp_localize_script(
			'dope-footer-admin',
			'dopeFooterAdmin',
			array(
				'copyLabel'   => __( 'Copy', 'dope-footer' ),
				'copiedLabel' => __( 'Copied', 'dope-footer' ),
				'copySuccess' => __( 'Shortcode copied to clipboard.', 'dope-footer' ),
				'copyError'   => __( 'Unable to copy shortcode.', 'dope-footer' ),
				'savingLabel' => __( 'Saving...', 'dope-footer' ),
				'savedLabel'  => __( 'Draft', 'dope-footer' ),
			)
		);
	}

	/**
	 * Add admin body class on settings page.
	 *
	 * @param string $classes Existing classes.
	 * @return string
	 */
	public function add_admin_body_class( $classes ) {
		if ( $this->is_dope_footer_screen() ) {
			$classes .= ' dope-footer-admin-page';
		}

		return $classes;
	}

	/**
	 * Get settings sidebar HTML.
	 *
	 * @return string
	 */
	private function get_settings_sidebar_markup() {
		ob_start();
		?>
		<div id="dope-footer-sidebar">
			<div id="dope-footer-publish" class="postbox dope-footer-metabox">
				<div class="postbox-header">
					<h2 class="hndle"><?php esc_html_e( 'Publish', 'dope-footer' ); ?></h2>
				</div>
				<div class="inside">
					<p class="dope-footer-publish-top-action">
						<button type="button" class="button" id="dope-footer-save-draft"><?php esc_html_e( 'Save Draft', 'dope-footer' ); ?></button>
					</p>

					<div class="dope-footer-publish-row">
						<span class="dashicons dashicons-admin-post"></span>
						<span><?php esc_html_e( 'Status:', 'dope-footer' ); ?> <strong id="dope-footer-status-value"><?php esc_html_e( 'Draft', 'dope-footer' ); ?></strong> <a href="#" class="dope-footer-inline-edit"><?php esc_html_e( 'Edit', 'dope-footer' ); ?></a></span>
					</div>
					<div class="dope-footer-publish-row">
						<span class="dashicons dashicons-visibility"></span>
						<span><?php esc_html_e( 'Visibility:', 'dope-footer' ); ?> <strong><?php esc_html_e( 'Public', 'dope-footer' ); ?></strong> <a href="#" class="dope-footer-inline-edit"><?php esc_html_e( 'Edit', 'dope-footer' ); ?></a></span>
					</div>
					<div class="dope-footer-publish-row">
						<span class="dashicons dashicons-calendar-alt"></span>
						<span><?php esc_html_e( 'Publish immediately', 'dope-footer' ); ?> <a href="#" class="dope-footer-inline-edit"><?php esc_html_e( 'Edit', 'dope-footer' ); ?></a></span>
					</div>

					<div class="dope-footer-publish-actions">
						<button type="button" class="button button-primary" id="dope-footer-publish-now"><?php esc_html_e( 'Publish', 'dope-footer' ); ?></button>
					</div>
				</div>
			</div>

			<div id="dope-footer-howto" class="postbox dope-footer-metabox">
				<div class="postbox-header">
					<h2 class="hndle"><?php esc_html_e( 'How To Use', 'dope-footer' ); ?></h2>
				</div>
				<div class="inside">
					<p><?php esc_html_e( 'To display the footer, copy and paste this shortcode into your post, page, widget, or block editor.', 'dope-footer' ); ?> <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::HELP_SLUG ) ); ?>"><?php esc_html_e( 'Learn how', 'dope-footer' ); ?></a>.</p>
					<label for="dope-footer-shortcode-field" class="screen-reader-text"><?php esc_html_e( 'Dope Footer shortcode', 'dope-footer' ); ?></label>
					<div class="dope-footer-shortcode-copy">
						<span class="dashicons dashicons-shortcode" aria-hidden="true"></span>
						<input
							type="text"
							id="dope-footer-shortcode-field"
							class="regular-text"
							value="<?php echo esc_attr( $this->build_footer_shortcode() ); ?>"
							readonly
						/>
						<button type="button" class="button" id="dope-footer-copy-shortcode">
							<span class="dope-footer-copy-label"><?php esc_html_e( 'Copy', 'dope-footer' ); ?></span>
						</button>
					</div>
					<p class="dope-footer-copy-feedback" id="dope-footer-copy-feedback" aria-live="polite"></p>
				</div>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Build shortcode string.
	 *
	 * @param int $post_id Footer item ID.
	 * @return string
	 */
	private function build_footer_shortcode( $post_id = 0 ) {
		$post_id = absint( $post_id );

		if ( $post_id > 0 ) {
			return sprintf( '[dope_footer id="%d"]', $post_id );
		}

		return '[dope_footer]';
	}

	/**
	 * Show notice when CodeStar is unavailable.
	 *
	 * @return void
	 */
	public function missing_codestar_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'DopeFooter: CodeStar Framework could not be loaded. Please verify plugin files under vendor/codestar-framework.', 'dope-footer' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Check if current request is plugin settings page.
	 *
	 * @return bool
	 */
	private function is_settings_page() {
		if ( ! is_admin() ) {
			return false;
		}

		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		return self::PAGE_SLUG === $page;
	}

	/**
	 * Check if current page is Manage Footer page.
	 *
	 * @return bool
	 */
	private function is_manage_page() {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		return self::HUB_SLUG === $page;
	}

	/**
	 * Check whether current admin screen belongs to DopeFooter.
	 *
	 * @param string $hook Current admin hook.
	 * @return bool
	 */
	private function is_dope_footer_screen( $hook = '' ) {
		if ( ! is_admin() ) {
			return false;
		}

		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		if ( in_array( $page, array( self::HUB_SLUG, self::PAGE_SLUG, self::TOOLS_SLUG, self::HELP_SLUG ), true ) ) {
			return true;
		}

		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen && self::FOOTER_CPT === $screen->post_type ) {
				return true;
			}
		}

		return ( false !== strpos( (string) $hook, 'dope-footer' ) );
	}

	/**
	 * Render manage page.
	 *
	 * @return void
	 */
	public function render_manage_page() {
		$normalized    = $this->options->get_normalized();
		$footers_count = wp_count_posts( self::FOOTER_CPT );
		$total_footers = isset( $footers_count->publish ) ? (int) $footers_count->publish : 0;
		?>
		<div class="wrap dope-footer-hub">
			<h1><?php esc_html_e( 'DopeFooter Manager', 'dope-footer' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Manage your footer library, open settings, and copy shortcode quickly.', 'dope-footer' ); ?></p>

			<div class="dope-footer-hub-grid">
				<div class="dope-footer-hub-card">
					<h2><?php esc_html_e( 'Quick Actions', 'dope-footer' ); ?></h2>
					<p>
						<a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . self::FOOTER_CPT ) ); ?>">
							<?php esc_html_e( 'Add new footer', 'dope-footer' ); ?>
						</a>
						<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . self::FOOTER_CPT ) ); ?>">
							<?php esc_html_e( 'All Footers', 'dope-footer' ); ?>
						</a>
						<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) ); ?>">
							<?php esc_html_e( 'Open Settings', 'dope-footer' ); ?>
						</a>
					</p>
				</div>

				<div class="dope-footer-hub-card">
					<h2><?php esc_html_e( 'Status', 'dope-footer' ); ?></h2>
					<ul>
						<li><strong><?php esc_html_e( 'Saved Preset:', 'dope-footer' ); ?></strong> <?php echo esc_html( $normalized['layout_preset'] ); ?></li>
						<li><strong><?php esc_html_e( 'Published Footers:', 'dope-footer' ); ?></strong> <?php echo esc_html( (string) $total_footers ); ?></li>
						<li><strong><?php esc_html_e( 'Default Shortcode:', 'dope-footer' ); ?></strong> <code>[dope_footer]</code></li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render tools page.
	 *
	 * @return void
	 */
	public function render_tools_page() {
		?>
		<div class="wrap dope-footer-hub">
			<h1><?php esc_html_e( 'DopeFooter Tools', 'dope-footer' ); ?></h1>
			<div class="dope-footer-hub-card">
				<h2><?php esc_html_e( 'Available Tools', 'dope-footer' ); ?></h2>
				<ul>
					<li><?php esc_html_e( 'Use Settings to configure global footer defaults.', 'dope-footer' ); ?></li>
					<li><?php esc_html_e( 'Use All Footers to maintain footer items.', 'dope-footer' ); ?></li>
					<li><?php esc_html_e( 'Use shortcode [dope_footer] in any page/post.', 'dope-footer' ); ?></li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Render help page.
	 *
	 * @return void
	 */
	public function render_help_page() {
		?>
		<div class="wrap dope-footer-hub">
			<h1><?php esc_html_e( 'DopeFooter Help', 'dope-footer' ); ?></h1>
			<div class="dope-footer-hub-card">
				<h2><?php esc_html_e( 'How To Use', 'dope-footer' ); ?></h2>
				<ol>
					<li><?php esc_html_e( 'Go to DopeFooter > Settings and configure your layout.', 'dope-footer' ); ?></li>
					<li><?php esc_html_e( 'Insert shortcode [dope_footer] in your content.', 'dope-footer' ); ?></li>
					<li><?php esc_html_e( 'Optionally target a specific footer: [dope_footer id="123"]', 'dope-footer' ); ?></li>
					<li><?php esc_html_e( 'Optionally override preset: [dope_footer preset="preset_minimal"]', 'dope-footer' ); ?></li>
				</ol>
			</div>
		</div>
		<?php
	}
}
