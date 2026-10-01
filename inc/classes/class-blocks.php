<?php
/**
 * Registers all custom gutenberg blocks.
 *
 * @package project-name-features
 */

namespace Project_Name\Features\Inc;

use Project_Name\Features\Inc\Traits\Singleton;

/**
 * Class Blocks
 */
class Blocks {

	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		$this->setup_hooks();

	}

	/**
	 * Setup hooks.
	 *
	 * @return void
	 */
	public function setup_hooks() {
		add_action( 'init', [ $this, 'register_blocks' ] );
	}

	/**
	 * Register all custom gutenberg blocks.
	 *
	 * @return void
	 */
	public function register_blocks() {

		// Register example-block Block.
		register_block_type(
			PROJECT_NAME_FEATURES_PATH . '/assets/build/blocks/example-block/'
		);

		// Register example-block Block.
		register_block_type(
			PROJECT_NAME_FEATURES_PATH . '/assets/build/blocks/example-block-interactive/'
		);

		// Register example-block-dynamic Block.
		register_block_type(
			PROJECT_NAME_FEATURES_PATH . '/assets/build/blocks/example-block-dynamic/',
			[
				'render_callback' => [ $this, 'render_example_block_dynamic' ],
			]
		);

	}

	/**
	 * Render example-block-dynamic Block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string Rendered HTML.
	 */
	public function render_example_block_dynamic( $attributes = [] ) {

		$attributes = wp_parse_args( $attributes, [] );

		return project_name_features_template(
			'block-templates/example-block-dynamic',
			[
				'attributes' => $attributes,
			]
		);

	}
}
