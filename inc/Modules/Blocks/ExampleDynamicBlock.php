<?php
/**
 * Example dynamic block with server-side rendering.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules\Blocks;

use rtCamp\WPFramework\Contracts\Abstracts\AbstractBlock;

/**
 * Class - ExampleDynamicBlock
 */
final class ExampleDynamicBlock extends AbstractBlock {

	/**
	 * {@inheritDoc}
	 */
	public static function get_name(): string {
		return 'project-name-features/example-block-dynamic';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_block_dir(): ?string {
		return PROJECT_NAME_FEATURES_PATH . 'assets/build/blocks/example-block-dynamic';
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
		$located = \Project_Name\Features\Core\Templates::get_template_part(
			'block-templates/example-block-dynamic',
			null,
			[ 'attributes' => $attributes ],
			false
		);

		if ( ! $located ) {
			return '';
		}

		ob_start();
		load_template( $located, false, [ 'attributes' => $attributes ] );
		return (string) ob_get_clean();
	}
}
