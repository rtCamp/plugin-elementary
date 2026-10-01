<?php
/**
 * Cache module.
 *
 * Groups examples that demonstrate the rtcamp/wp-primitives Cache utility.
 * To cache in your own code, instantiate {@see \rtCamp\WPPrimitives\Utils\Cache}
 * directly; this module only ships a usage example.
 * To add another example: create it in Cache/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

use Project_Name\Features\Modules\Cache\ExampleCache;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractModule;

/**
 * Class - Cache
 */
final class Cache extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			ExampleCache::class,
		];
	}
}
