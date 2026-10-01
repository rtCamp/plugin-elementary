<?php
/**
 * Example taxonomy.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules\Taxonomies;

use rtCamp\Plugin\Elementary\Modules\PostTypes\ExamplePostType;
use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractTaxonomy;

/**
 * Class - ExampleTaxonomy
 */
final class ExampleTaxonomy extends AbstractTaxonomy {

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug(): string {
		return 'taxonomy-slug';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_object_types(): array {
		return [ ExamplePostType::get_slug() ];
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_singular_label(): string {
		return __( 'Taxonomy Label', 'elementary-plugin' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_plural_label(): string {
		return __( 'Taxonomy Labels', 'elementary-plugin' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_hierarchical(): bool {
		return true;
	}
}
