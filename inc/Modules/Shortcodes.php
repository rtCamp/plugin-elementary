<?php
/**
 * Shortcodes module.
 *
 * Groups all shortcode classes.
 * To add a new shortcode: create it in Shortcodes/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

use Project_Name\Features\Modules\Shortcodes\ExampleShortcode;
use rtCamp\WPFramework\Contracts\Abstracts\AbstractModule;

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
