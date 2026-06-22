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

// wp:example
use Project_Name\Features\Modules\REST\ExampleRESTController;
// wp:example:end
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
			// wp:example
			ExampleRESTController::class,
			// wp:example:end
		];
	}
}
