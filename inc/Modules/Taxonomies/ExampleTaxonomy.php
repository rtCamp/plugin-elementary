<?php
/**
 * Example taxonomy.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules\Taxonomies;

use Project_Name\Features\Modules\PostTypes\ExamplePostType;
use rtCamp\WPFramework\Contracts\Abstracts\AbstractTaxonomy;

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
		return __( 'Taxonomy Label', 'project-name-features' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_plural_label(): string {
		return __( 'Taxonomy Labels', 'project-name-features' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_hierarchical(): bool {
		return true;
	}
}
