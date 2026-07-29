<?php
/**
 * Transients module.
 *
 * Groups examples that demonstrate the rtcamp/wp-framework Transients utility.
 * To cache in your own code, instantiate {@see \rtCamp\WPFramework\Utils\Transients}
 * directly; this module only ships a usage example.
 * To add another example: create it in Transients/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

use Project_Name\Features\Modules\Transients\ExampleTransients;
use rtCamp\WPFramework\Contracts\Abstracts\AbstractModule;

/**
 * Class - Transients
 */
final class Transients extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			ExampleTransients::class,
		];
	}
}
