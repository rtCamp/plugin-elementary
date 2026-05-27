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

use Project_Name\Features\Modules\Roles\ExampleUserRole;
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
			ExampleUserRole::class,
		];
	}
}
