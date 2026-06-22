<?php
/**
 * Blocks module.
 *
 * Groups all dynamic block classes (those with server-side rendering).
 * Static blocks (no render_callback) are registered via Assets::register_blocks().
 * To add a new dynamic block: create it in Blocks/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

// wp:example
use Project_Name\Features\Modules\Blocks\ExampleDynamicBlock;
// wp:example:end
use rtCamp\WPFramework\Contracts\Abstracts\AbstractModule;

/**
 * Class - Blocks
 */
final class Blocks extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			// wp:example
			ExampleDynamicBlock::class,
			// wp:example:end
		];
	}
}
