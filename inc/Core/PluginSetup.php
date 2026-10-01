<?php
/**
 * Plugin setup: activation, deactivation, and textdomain.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Core;

use rtCamp\Plugin\Elementary\Helpers\Util;
// wp:example:cron
use rtCamp\Plugin\Elementary\Modules\Cron\ExampleCronJob;
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
		register_activation_hook( ELEMENTARY_PLUGIN_FILE, [ $this, 'activate' ] );
		register_deactivation_hook( ELEMENTARY_PLUGIN_FILE, [ $this, 'deactivate' ] );

		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	/**
	 * Runs on successful plugin activation.
	 */
	public function activate(): void {
		update_option( 'elementary_plugin_version', ELEMENTARY_PLUGIN_VERSION );

		// The shared Logger is available anywhere via Util::logger() (silent unless WP_DEBUG).
		Util::logger()->info( 'Plugin activated', [ 'version' => ELEMENTARY_PLUGIN_VERSION ] );
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
			'elementary-plugin',
			false,
			dirname( plugin_basename( ELEMENTARY_PLUGIN_FILE ) ) . '/languages'
		);
	}
}
