<?php
/**
 * Settings module.
 *
 * Groups all admin settings page classes.
 * To add a new settings page: create it in Settings/ and add the class-string here.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules;

use rtCamp\Plugin\Elementary\Modules\Settings\ExampleSettingsPage;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractModule;

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
