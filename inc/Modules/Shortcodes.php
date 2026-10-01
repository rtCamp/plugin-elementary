<?php
/**
 * Shortcodes module.
 *
 * Groups all shortcode classes.
 * To add a new shortcode: create it in Shortcodes/ and add the class-string here.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules;

use rtCamp\Plugin\Elementary\Modules\Shortcodes\ExampleShortcode;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractModule;

/**
 * Class - Shortcodes
 */
final class Shortcodes extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			ExampleShortcode::class,
		];
	}
}
