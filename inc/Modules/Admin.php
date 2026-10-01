<?php
/**
 * Admin module.
 *
 * Groups custom admin screens built on AbstractAdminPage (plain menu/submenu
 * pages, as distinct from the Settings API pages in Settings/).
 * To add a new admin page: create it in Admin/ and add the class-string here.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules;

use rtCamp\Plugin\Elementary\Modules\Admin\ExampleAdminPage;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractModule;

/**
 * Class - Admin
 */
final class Admin extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			ExampleAdminPage::class,
		];
	}
}
