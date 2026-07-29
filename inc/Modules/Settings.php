<?php
/**
 * Settings module.
 *
 * Groups all admin settings page classes.
 * To add a new settings page: create it in Settings/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

use Project_Name\Features\Modules\Settings\ExampleSettingsPage;
use rtCamp\WPFramework\Contracts\Abstracts\AbstractModule;

/**
 * Class - Settings
 */
final class Settings extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			ExampleSettingsPage::class,
		];
	}
}
