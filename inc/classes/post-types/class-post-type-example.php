<?php
/**
 * Register Example post type.
 *
 * @package project-name-features
 */

namespace Project_Name\Features\Inc\Post_Types;

/**
 * Class Post_Type_Example
 */
class Post_Type_Example extends Base {

	/**
	 * Slug of post type.
	 *
	 * @var string
	 */
	const SLUG = 'post-type-slug';

	/**
	 * Icon of post type.
	 *
	 * @var string
	 */
	const ICON = 'dashicons-id';

	/**
	 * Post type label for internal uses.
	 *
	 * @var string
	 */
	const LABEL = 'Post Type Label';

	/**
	 * To get list of labels for post type.
	 *
	 * @return array
	 */
	public function get_labels() {

		return [
			'name'               => _x( 'Post_Type_Label', 'post type general name', 'project-name-features' ),
			'singular_name'      => _x( 'Post_Type_Label', 'post type singular name', 'project-name-features' ),
			'menu_name'          => _x( 'Post_Type_Label', 'admin menu', 'project-name-features' ),
			'name_admin_bar'     => _x( 'Post_Type_Label', 'add new on admin bar', 'project-name-features' ),
			'add_new'            => _x( 'Add New', 'post', 'project-name-features' ),
			'add_new_item'       => __( 'Add New Post_Type_Label', 'project-name-features' ),
			'new_item'           => __( 'New Post_Type_Label', 'project-name-features' ),
			'edit_item'          => __( 'Edit Post_Type_Label', 'project-name-features' ),
			'view_item'          => __( 'View Post_Type_Label', 'project-name-features' ),
			'all_items'          => __( 'All Post_Type_Label', 'project-name-features' ),
			'search_items'       => __( 'Search Post_Type_Label', 'project-name-features' ),
			'parent_item_colon'  => __( 'Parent Post_Type_Label:', 'project-name-features' ),
			'not_found'          => __( 'No Post_Type_Label found.', 'project-name-features' ),
			'not_found_in_trash' => __( 'No Post_Type_Label found in Trash.', 'project-name-features' ),
		];

	}
}
