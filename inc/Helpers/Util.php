<?php
/**
 * General-purpose plugin utility helpers.
 *
 * Stateless utility class — pure functions wrapped in a namespace.
 * Final + private constructor: must be used statically, never instantiated.
 *
 * Future helper classes (string, cache, url, …) should be siblings of this
 * one under `inc/Helpers/`.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Helpers;

use Project_Name\Features\Core\Components;
use Project_Name\Features\Main;

/**
 * Class - Util
 */
final class Util {

	/**
	 * Disallow instantiation — this class only exposes static helpers.
	 */
	private function __construct() {}

	/**
	 * Render a component by name.
	 *
	 * @param string               $name    Component name.
	 * @param array<string, mixed> $args    Arguments to pass to the component.
	 * @param array<string, mixed> $options Optional. Resolution options. See ComponentLoader::render().
	 *
	 * @return void
	 */
	public static function component( string $name, array $args = [], array $options = [] ): void {
		self::component_loader()->render( $name, $args, $options );
	}

	/**
	 * Get the rendered HTML of a component as a string.
	 *
	 * @param string               $name    Component name.
	 * @param array<string, mixed> $args    Arguments to pass to the component.
	 * @param array<string, mixed> $options Optional. Resolution options. See ComponentLoader::get().
	 *
	 * @return string Rendered component HTML.
	 */
	public static function get_component( string $name, array $args = [], array $options = [] ): string {
		return self::component_loader()->get( $name, $args, $options );
	}

	/**
	 * Get the shared plugin component loader.
	 *
	 * @return Components Shared component loader.
	 */
	private static function component_loader(): Components {
		/**
		 * Shared component loader.
		 *
		 * @var Components $loader
		 */
		$loader = Main::get_instance()->get_shared( Components::class );

		return $loader;
	}

	/**
	 * Load a plugin data file from the /inc/data/ directory.
	 *
	 * @param string $slug          File slug (no .php extension).
	 * @param mixed  $default_value Default value if the file is not found.
	 *
	 * @return mixed File contents or $default_value.
	 */
	public static function get_data( string $slug, mixed $default_value = [] ): mixed {
		$data_file = sprintf( PROJECT_NAME_FEATURES_PATH . 'inc/data/%s.php', $slug );

		if ( file_exists( $data_file ) ) {
			return require $data_file; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
		}

		return $default_value;
	}

	/**
	 * Determine whether the current environment is production.
	 *
	 * @see https://make.wordpress.org/core/2020/07/24/new-wp_get_environment_type-function-in-wordpress-5-5/
	 */
	public static function is_production(): bool {
		return 'production' === wp_get_environment_type();
	}

	/**
	 * Determine if the current User Agent matches the given kind.
	 *
	 * Delegates to Jetpack's jetpack_is_mobile() when available.
	 *
	 * @param string $kind                 Category of mobile device: 'any', 'dumb', or 'smart'.
	 * @param bool   $return_matched_agent Return the matched UA string instead of a boolean.
	 *
	 * @return bool|string
	 */
	public static function is_mobile( string $kind = 'any', bool $return_matched_agent = false ): bool|string {
		if ( function_exists( 'jetpack_is_mobile' ) ) {
			return jetpack_is_mobile( $kind, $return_matched_agent );
		}

		return false;
	}
}
