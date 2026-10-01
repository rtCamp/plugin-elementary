<?php
/**
 * Example dynamic block with server-side rendering.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules\Blocks;

use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractBlock;

/**
 * Class - ExampleDynamicBlock
 */
final class ExampleDynamicBlock extends AbstractBlock {

	/**
	 * {@inheritDoc}
	 */
	public static function get_name(): string {
		return 'elementary-plugin/example-block-dynamic';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return string Absolute path to this block's build directory.
	 */
	protected function get_block_dir(): string {
		return ELEMENTARY_PLUGIN_PATH . 'assets/build/blocks/example-block-dynamic';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param array     $attributes Block attributes.
	 * @param string    $content Block inner content.
	 * @param \WP_Block $block Full block instance.
	 *
	 * @return string Rendered block HTML.
	 */
	public function render( array $attributes, string $content, \WP_Block $block ): string {
		return \rtCamp\Plugin\Elementary\Helpers\Util::templates()->get(
			'block-templates/example-block-dynamic',
			null,
			$attributes
		);
	}
}
