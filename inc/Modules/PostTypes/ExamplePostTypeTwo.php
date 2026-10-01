<?php
/**
 * Example Post Type Two.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules\PostTypes;

use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractPostType;

/**
 * Class - ExamplePostTypeTwo
 */
final class ExamplePostTypeTwo extends AbstractPostType {

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug(): string {
		return 'post-type-slug-two';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_singular_label(): string {
		return __( 'Post Type Two Label', 'project-name-features' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_plural_label(): string {
		return __( 'Post Type Two Labels', 'project-name-features' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_menu_icon(): string {
		return 'dashicons-id-alt';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_editor_supports(): array {
		return array_merge( parent::get_editor_supports(), [ 'custom-fields' ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_supported_taxonomies(): array {
		return [ 'taxonomy-slug-two' ];
	}
}
