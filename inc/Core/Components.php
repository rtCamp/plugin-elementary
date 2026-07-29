<?php
/**
 * Plugin component loader.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Core;

use Project_Name\Features\Main;
use rtCamp\WPFramework\AssetLoader;
use rtCamp\WPFramework\ComponentLoader;
use rtCamp\WPFramework\Contracts\Interfaces\Shareable;

/**
 * Class Components
 *
 * The plugin's component loader. Its components live in the plugin but can be
 * overridden by the active theme — child theme over parent theme — following the
 * WordPress template hierarchy, which falls out of get_asset_loaders()
 * automatically (self == plugin).
 */
final class Components extends ComponentLoader implements Shareable {

	/**
	 * Context slug used to namespace the plugin's component asset handles.
	 */
	protected function get_context(): string {
		return 'project-name-features';
	}

	/**
	 * Resolve the plugin's shared asset loader (its Assets instance).
	 *
	 * @return AssetLoader Shared plugin asset loader.
	 */
	protected function get_asset_loader(): AssetLoader {
		/**
		 * Shared plugin asset loader.
		 *
		 * @var Assets $assets
		 */
		$assets = Main::get_instance()->get_shared( Assets::class );

		return $assets;
	}
}
