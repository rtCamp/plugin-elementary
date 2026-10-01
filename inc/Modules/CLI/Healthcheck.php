<?php
/**
 * Healthcheck CLI command for Elementary Plugin.
 *
 * @package rtCamp\Plugin\Elementary
 */

declare( strict_types = 1 );

namespace rtCamp\Plugin\Elementary\Modules\CLI;

use rtCamp\WPPrimitives\Contracts\Interfaces\CLICommand;

/**
 * Class - Healthcheck
 *
 * Implements the `elementary-plugin health-check` WP-CLI command.
 */
final class Healthcheck implements CLICommand {

	/**
	 * {@inheritDoc}
	 */
	public static function get_name(): string {
		return 'health-check';
	}

	/**
	 * {@inheritDoc}
	 */
	public static function get_description(): string {
		return __( 'Run a health check on the plugin.', 'elementary-plugin' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param array $args Positional arguments passed to the command.
	 * @param array $assoc_args Associative arguments passed to the command.
	 *
	 * @return void
	 */
	public static function run( array $args = [], array $assoc_args = [] ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$checks = [
			'plugin_active'     => class_exists( 'rtCamp\Plugin\Elementary\Main' ),
			'constants_defined' => defined( 'ELEMENTARY_PLUGIN_PATH' ) && defined( 'ELEMENTARY_PLUGIN_URL' ),
			'composer_autoload' => class_exists( 'Composer\Autoload\ClassLoader' ),
		];

		\WP_CLI::log( 'Elementary Plugin Health Check' );
		\WP_CLI::log( '====================================' );

		$all_passed = true;
		foreach ( $checks as $check_name => $passed ) {
			$status = $passed ? '✓ PASS' : '✗ FAIL';
			\WP_CLI::log( "  {$check_name}: {$status}" );

			if ( ! $passed ) {
				$all_passed = false;
			}
		}

		if ( ! $all_passed ) {
			\WP_CLI::error( 'One or more health checks failed. Please investigate the issues above.' );
		}

		\WP_CLI::success( 'All health checks passed!' );
	}
}
