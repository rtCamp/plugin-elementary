<?php
/**
 * Plugin setup: activation, deactivation, and textdomain.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Core;

use Project_Name\Features\Helpers\Util;
// wp:example:cron
use Project_Name\Features\Modules\Cron\ExampleCronJob;
// wp:example:cron:end
use rtCamp\WPPrimitives\Contracts\Interfaces\Registrable;

/**
 * Class PluginSetup
 */
class PluginSetup implements Registrable {

	/**
	 * Register hooks.
	 */
	public function register_hooks(): void {
		register_activation_hook( PROJECT_NAME_FEATURES_FILE, [ $this, 'activate' ] );
		register_deactivation_hook( PROJECT_NAME_FEATURES_FILE, [ $this, 'deactivate' ] );

		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	/**
	 * Runs on successful plugin activation.
	 */
	public function activate(): void {
		update_option( 'project_name_features_version', PROJECT_NAME_FEATURES_VERSION );

		// The shared Logger is available anywhere via Util::logger() (silent unless WP_DEBUG).
		Util::logger()->info( 'Plugin activated', [ 'version' => PROJECT_NAME_FEATURES_VERSION ] );
	}

	/**
	 * Runs on successful plugin deactivation.
	 *
	 * For uninstall-time cleanup (option/table removal), use the root
	 * `uninstall.php` file instead — that runs only on actual uninstall.
	 */
	public function deactivate(): void {
		// wp:example:cron
		ExampleCronJob::unschedule();
		// wp:example:cron:end
	}

	/**
	 * Load plugin textdomain.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'project-name-features',
			false,
			dirname( plugin_basename( PROJECT_NAME_FEATURES_FILE ) ) . '/languages'
		);
	}
}
