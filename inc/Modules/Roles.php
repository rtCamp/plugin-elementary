<?php
/**
 * Roles module.
 *
 * Groups all custom user role classes.
 * To add a new role: create it in Roles/ and add the class-string here.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules;

use rtCamp\Plugin\Elementary\Modules\Roles\ExampleUserRole;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractModule;

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
