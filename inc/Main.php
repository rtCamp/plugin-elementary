<?php
/**
 * Plugin bootstrap file.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features;

use rtCamp\WPFramework\Contracts\Traits\Loader;
use rtCamp\WPFramework\Contracts\Traits\Singleton;

/**
 * Class - Main
 */
final class Main {

	use Singleton;
	use Loader;

	/*
	 * List of classes to load
	 */
	public const CLASSES = [
		// Core infrastructure - always loaded.
		Core\Assets::class,
		Core\PluginSetup::class,
		Core\Components::class,
		Core\Templates::class,
		Core\Encryption::class,
		Core\Logger::class,

		// Optional capabilities - each is keep/remove at init time. Per-capability
		// init markers (stripped on keep) let the engine drop a capability's line
		// from this list when it is deselected.
		// wp:example:cli
		Modules\CLI::class,
		// wp:example:cli:end
		// wp:example:post-types
		Modules\PostTypes::class,
		// wp:example:post-types:end
		// wp:example:taxonomies
		Modules\Taxonomies::class,
		// wp:example:taxonomies:end
		// wp:example:blocks
		Modules\Blocks::class,
		// wp:example:blocks:end
		// wp:example:rest
		Modules\REST::class,
		// wp:example:rest:end
		// wp:example:settings
		Modules\Settings::class,
		// wp:example:settings:end
		// wp:example:shortcodes
		Modules\Shortcodes::class,
		// wp:example:shortcodes:end
		// wp:example:roles
		Modules\Roles::class,
		// wp:example:roles:end
		// wp:example:admin
		Modules\Admin::class,
		// wp:example:admin:end
		// wp:example:cron
		Modules\Cron::class,
		// wp:example:cron:end

		// Framework service usage examples.
		// wp:example:cache
		Modules\Cache::class,
		// wp:example:cache:end
		// wp:example:transients
		Modules\Transients::class,
		// wp:example:transients:end
	];

	/**
	 * Constructor.
	 */
	protected function __construct() {
		$this->load( self::CLASSES );
	}
}
