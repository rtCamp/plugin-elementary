<?php
/**
 * Blocks module.
 *
 * Groups all dynamic block classes (those with server-side rendering).
 * Static blocks (no render_callback) are registered via Assets::register_blocks().
 * To add a new dynamic block: create it in Blocks/ and add the class-string here.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules;

use rtCamp\Plugin\Elementary\Modules\Blocks\ExampleDynamicBlock;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractModule;

/**
 * Class - Blocks
 */
final class Blocks extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			ExampleDynamicBlock::class,
		];
	}
}
