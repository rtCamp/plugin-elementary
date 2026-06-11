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
	const CLASSES = [
		// Core infrastructure.
		Core\Assets::class,
		Core\PluginSetup::class,
		Core\Components::class,
		Core\Templates::class,
		Core\Encryption::class,

		// CLI commands.
		Modules\CLI::class,

		// Feature modules — each module groups related Registrable classes.
		Modules\PostTypes::class,
		Modules\Taxonomies::class,
		Modules\Blocks::class,
		Modules\REST::class,
		Modules\Settings::class,
		Modules\Shortcodes::class,
		Modules\Roles::class,
		Modules\Cron::class,
	];

	/**
	 * Constructor.
	 */
	protected function __construct() {
		$this->load( self::CLASSES );
	}
}
