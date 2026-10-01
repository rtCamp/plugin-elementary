<?php
/**
 * Example Taxonomy Two.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules\Taxonomies;

use Project_Name\Features\Modules\PostTypes\ExamplePostTypeTwo;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractTaxonomy;

/**
 * Class - ExampleTaxonomyTwo
 */
final class ExampleTaxonomyTwo extends AbstractTaxonomy {

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug(): string {
		return 'taxonomy-slug-two';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_object_types(): array {
		return [ ExamplePostTypeTwo::get_slug() ];
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_singular_label(): string {
		return __( 'Taxonomy Two Label', 'project-name-features' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_plural_label(): string {
		return __( 'Taxonomy Two Labels', 'project-name-features' );
	}
}
