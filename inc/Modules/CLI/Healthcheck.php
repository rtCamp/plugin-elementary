<?php
/**
 * Healthcheck CLI command for Project Name Features.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules\CLI;

use rtCamp\WPFramework\Contracts\Interfaces\CLICommand;

/**
 * Class - Healthcheck
 *
 * Implements the `project-name-features health-check` WP-CLI command.
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
		return __( 'Run a health check on the plugin.', 'project-name-features' );
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
			'plugin_active'     => class_exists( 'Project_Name\Features\Main' ),
			'constants_defined' => defined( 'PROJECT_NAME_FEATURES_PATH' ) && defined( 'PROJECT_NAME_FEATURES_URL' ),
			'composer_autoload' => class_exists( 'Composer\Autoload\ClassLoader' ),
		];

		\WP_CLI::log( 'Project Name Features Health Check' );
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
