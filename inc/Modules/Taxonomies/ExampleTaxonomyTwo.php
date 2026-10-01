<?php
/**
 * Example Taxonomy Two.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules\Taxonomies;

use rtCamp\Plugin\Elementary\Modules\PostTypes\ExamplePostTypeTwo;
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
		return __( 'Taxonomy Two Label', 'elementary-plugin' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_plural_label(): string {
		return __( 'Taxonomy Two Labels', 'elementary-plugin' );
	}
}
