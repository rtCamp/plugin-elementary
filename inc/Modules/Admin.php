<?php
/**
 * Admin module.
 *
 * Groups custom admin screens built on AbstractAdminPage (plain menu/submenu
 * pages, as distinct from the Settings API pages in Settings/).
 * To add a new admin page: create it in Admin/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

use Project_Name\Features\Modules\Admin\ExampleAdminPage;
use rtCamp\WPFramework\Contracts\Abstracts\AbstractModule;

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
