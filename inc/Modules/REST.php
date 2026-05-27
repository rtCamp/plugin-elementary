<?php
/**
 * REST API module.
 *
 * Groups all REST controller classes.
 * To add a new endpoint: create it in REST/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

use Project_Name\Features\Modules\REST\ExampleRESTController;
use rtCamp\WPFramework\Contracts\Abstracts\AbstractModule;

/**
 * Class - REST
 */
final class REST extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			ExampleRESTController::class,
		];
	}
}
