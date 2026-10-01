<?php
/**
 * Cron module.
 *
 * Groups all WP-Cron job classes.
 * To add a new cron job: create it in Cron/ and add the class-string here.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules;

use rtCamp\Plugin\Elementary\Modules\Cron\ExampleCronJob;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractModule;

/**
 * Class - Cron
 */
final class Cron extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			ExampleCronJob::class,
		];
	}
}
