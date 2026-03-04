<?php
/**
 * Split preset template.
 *
 * @package DopeFooter
 */

defined( 'ABSPATH' ) || exit;

$wrapper_classes = 'dope-footer dope-footer--split';
if ( ! empty( $data['wrapper_class'] ) ) {
	$wrapper_classes .= ' ' . $data['wrapper_class'];
}
?>
<footer class="<?php echo esc_attr( $wrapper_classes ); ?>" style="<?php echo esc_attr( $data['css_vars_inline'] ); ?>">
	<div class="dope-footer__container">
		<div class="dope-footer__split-top">
			<div class="dope-footer__brand-col">
				<h2 class="dope-footer__brand-name"><?php echo esc_html( $data['brand']['name'] ); ?></h2>
				<?php if ( '' !== trim( $data['brand']['tagline'] ) ) : ?>
					<p class="dope-footer__brand-tagline"><?php echo esc_html( $data['brand']['tagline'] ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $data['display']['show_contact'] ) : ?>
				<div class="dope-footer__contact-col">
					<h3 class="dope-footer__col-title"><?php esc_html_e( 'Contact', 'dope-footer' ); ?></h3>
					<?php if ( '' !== trim( $data['contact']['text'] ) ) : ?>
						<p class="dope-footer__contact-text"><?php echo esc_html( $data['contact']['text'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== trim( $data['contact']['link_url'] ) && '' !== trim( $data['contact']['link_label'] ) ) : ?>
						<a class="dope-footer__contact-link" href="<?php echo esc_url( $data['contact']['link_url'] ); ?>"
							<?php if ( $data['display']['open_links_new_tab'] ) : ?>
								target="_blank" rel="noopener noreferrer"
							<?php endif; ?>
						><?php echo esc_html( $data['contact']['link_label'] ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="dope-footer__split-bottom">
			<?php if ( $data['display']['show_quick_links'] && ! empty( $data['quick_links'] ) ) : ?>
				<nav class="dope-footer__links-col" aria-label="<?php esc_attr_e( 'Quick Links', 'dope-footer' ); ?>">
					<h3 class="dope-footer__col-title"><?php esc_html_e( 'Quick Links', 'dope-footer' ); ?></h3>
					<ul class="dope-footer__link-list dope-footer__link-list--inline">
						<?php foreach ( $data['quick_links'] as $item ) : ?>
							<li>
								<a href="<?php echo esc_url( $item['url'] ); ?>"
									<?php if ( $data['display']['open_links_new_tab'] ) : ?>
										target="_blank" rel="noopener noreferrer"
									<?php endif; ?>
								><?php echo esc_html( $item['label'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>

			<?php if ( $data['display']['show_other_links'] && ! empty( $data['other_links'] ) ) : ?>
				<nav class="dope-footer__links-col" aria-label="<?php esc_attr_e( 'Other Links', 'dope-footer' ); ?>">
					<h3 class="dope-footer__col-title"><?php esc_html_e( 'Other Links', 'dope-footer' ); ?></h3>
					<ul class="dope-footer__link-list dope-footer__link-list--inline">
						<?php foreach ( $data['other_links'] as $item ) : ?>
							<li>
								<a href="<?php echo esc_url( $item['url'] ); ?>"
									<?php if ( $data['display']['open_links_new_tab'] ) : ?>
										target="_blank" rel="noopener noreferrer"
									<?php endif; ?>
								><?php echo esc_html( $item['label'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>

			<?php if ( $data['display']['show_social'] && ! empty( $data['social_links'] ) ) : ?>
				<div class="dope-footer__social" aria-label="<?php esc_attr_e( 'Social links', 'dope-footer' ); ?>">
					<?php foreach ( $data['social_links'] as $item ) : ?>
						<a
							class="dope-footer__social-link"
							href="<?php echo esc_url( $item['url'] ); ?>"
							aria-label="<?php echo esc_attr( $item['label'] ); ?>"
							<?php if ( $data['display']['open_links_new_tab'] ) : ?>
								target="_blank" rel="noopener noreferrer"
							<?php endif; ?>
						>
							<span class="dashicons <?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></span>
							<span class="screen-reader-text"><?php echo esc_html( $item['label'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<hr class="dope-footer__divider" />
		<p class="dope-footer__copyright"><?php echo esc_html( $data['copyright_text'] ); ?></p>
	</div>

	<?php if ( $data['display']['show_back_to_top'] ) : ?>
		<button type="button" class="dope-footer__backtotop" data-df-backtotop aria-label="<?php esc_attr_e( 'Back to top', 'dope-footer' ); ?>">
			<span aria-hidden="true">&uarr;</span>
		</button>
	<?php endif; ?>
</footer>

