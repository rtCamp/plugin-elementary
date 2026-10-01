<?php
/**
 * REST API module.
 *
 * Groups all REST controller classes.
 * To add a new endpoint: create it in REST/ and add the class-string here.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules;

use rtCamp\Plugin\Elementary\Modules\REST\ExampleRESTController;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractModule;

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
