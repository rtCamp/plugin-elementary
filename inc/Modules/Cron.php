<?php
/**
 * Cron module.
 *
 * Groups all WP-Cron job classes.
 * To add a new cron job: create it in Cron/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

// wp:example
use Project_Name\Features\Modules\Cron\ExampleCronJob;
// wp:example:end
use rtCamp\WPFramework\Contracts\Abstracts\AbstractModule;

/**
 * Class - Cron
 */
final class Cron extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			// wp:example
			ExampleCronJob::class,
			// wp:example:end
		];
	}
}
