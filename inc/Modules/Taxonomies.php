<?php
/**
 * Taxonomies module.
 *
 * Groups all custom taxonomy classes so Main is decoupled from individual registrations.
 * To add a new taxonomy: create it in Taxonomies/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

use Project_Name\Features\Modules\Taxonomies\ExampleTaxonomy;
use Project_Name\Features\Modules\Taxonomies\ExampleTaxonomyTwo;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractModule;

/**
 * Class - Taxonomies
 */
final class Taxonomies extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			ExampleTaxonomy::class,
			ExampleTaxonomyTwo::class,
		];
	}
}
