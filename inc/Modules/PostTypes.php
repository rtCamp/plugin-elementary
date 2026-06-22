<?php
/**
 * Post Types module.
 *
 * Groups all custom post type classes so Main is decoupled from individual registrations.
 * To add a new post type: create it in PostTypes/ and add the class-string here.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

// wp:example
use Project_Name\Features\Modules\PostTypes\ExamplePostType;
use Project_Name\Features\Modules\PostTypes\ExamplePostTypeTwo;
// wp:example:end
use rtCamp\WPFramework\Contracts\Abstracts\AbstractModule;

/**
 * Class - PostTypes
 */
final class PostTypes extends AbstractModule {

	/**
	 * {@inheritDoc}
	 */
	protected function get_classes(): array {
		return [
			// wp:example
			ExamplePostType::class,
			ExamplePostTypeTwo::class,
			// wp:example:end
		];
	}
}
