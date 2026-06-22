<?php
/**
 * Roles module.
 *
 * Groups all custom user role classes.
 * To add a new role: create it in Roles/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

// wp:example
use Project_Name\Features\Modules\Roles\ExampleUserRole;
// wp:example:end
use rtCamp\WPFramework\Contracts\Abstracts\AbstractModule;

/**
 * Class - Roles
 */
final class Roles extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			// wp:example
			ExampleUserRole::class,
			// wp:example:end
		];
	}
}
