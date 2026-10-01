<?php
/**
 * Example shortcode.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules\Shortcodes;

use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractShortcode;

/**
 * Class - ExampleShortcode
 *
 * Usage: [elementary_plugin_example title="Hello" count="5"]
 */
final class ExampleShortcode extends AbstractShortcode {

	/**
	 * {@inheritDoc}
	 */
	public static function get_tag(): string {
		return 'elementary_plugin_example';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function default_atts(): array {
		return [
			'title' => '',
			'count' => 5,
		];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param array       $atts Shortcode attributes.
	 * @param string|null $content Shortcode inner content.
	 *
	 * @return string Rendered shortcode output.
	 */
	protected function render( array $atts, ?string $content ): string {
		$title = sanitize_text_field( (string) $atts['title'] );
		$count = absint( $atts['count'] );

		ob_start();
		?>
		<div class="elementary-plugin-example-shortcode">
			<?php if ( $title ) : ?>
				<h2><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d: number of items */
						_n( 'Showing %d example item.', 'Showing %d example items.', $count, 'elementary-plugin' ),
						$count
					)
				);
				?>
			</p>
			<?php if ( $content ) : ?>
				<div class="elementary-plugin-example-shortcode__content">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}
}
