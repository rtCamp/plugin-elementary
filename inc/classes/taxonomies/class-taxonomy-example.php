<?php
/**
 * To register custom taxonomy.
 *
 * @package project-name-features
 */

namespace Project_Name\Features\Inc\Taxonomies;

/**
 * Class Taxonomy_Example
 */
class Taxonomy_Example extends Base {

	/**
	 * Slug of taxonomy.
	 *
	 * @var string
	 */
	const SLUG = 'taxonomy-slug';

	/**
	 * Labels for taxonomy.
	 *
	 * @return array
	 */
	public function get_labels() {

		return [
			'name'                       => _x( 'Taxonomy_Example', 'taxonomy general name', 'project-name-features' ),
			'singular_name'              => _x( 'Taxonomy_Example', 'taxonomy singular name', 'project-name-features' ),
			'search_items'               => __( 'Search Taxonomy_Example', 'project-name-features' ),
			'popular_items'              => __( 'Popular Taxonomy_Example', 'project-name-features' ),
			'all_items'                  => __( 'All Taxonomy_Example', 'project-name-features' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Taxonomy_Example', 'project-name-features' ),
			'update_item'                => __( 'Update Taxonomy_Example', 'project-name-features' ),
			'add_new_item'               => __( 'Add New Taxonomy_Example', 'project-name-features' ),
			'new_item_name'              => __( 'New Taxonomy_Example Name', 'project-name-features' ),
			'separate_items_with_commas' => __( 'Separate Taxonomy_Example with commas', 'project-name-features' ),
			'add_or_remove_items'        => __( 'Add or remove Taxonomy_Example', 'project-name-features' ),
			'choose_from_most_used'      => __( 'Choose from the most used Taxonomy_Example', 'project-name-features' ),
			'not_found'                  => __( 'No Taxonomy_Example found.', 'project-name-features' ),
			'menu_name'                  => __( 'Taxonomy_Example', 'project-name-features' ),
		];

	}

	/**
	 * List of post types for taxonomy.
	 *
	 * @return array
	 */
	public function get_post_types() {

		return [
			'post',
		];

	}
}
