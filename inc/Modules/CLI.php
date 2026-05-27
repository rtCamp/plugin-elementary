<?php
/**
 * WP-CLI commands for Project Name Features.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules;

use rtCamp\WPFramework\Contracts\Interfaces\ConditionallyRegistrable;

/**
 * Class - CLI
 *
 * Registers WP-CLI commands for the plugin.
 */
final class CLI implements ConditionallyRegistrable {

	/**
	 * {@inheritDoc}
	 *
	 * Skip registration entirely when not running under WP-CLI.
	 */
	public function can_register(): bool {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @uses \WP_CLI::add_command() instead of a WordPress hook.
	 */
	public function register_hooks(): void {
		foreach ( $this->get_commands() as $name => $command ) {
			\WP_CLI::add_command(
				"project-name-features {$name}",
				$command['callback'],
				[
					'shortdesc' => $command['description'],
				]
			);
		}
	}

	/**
	 * Get available CLI commands.
	 *
	 * @return array<string, array{
	 *   callback: callable( array<int, mixed>, array<string, mixed> ): void,
	 *   description: string
	 * }>
	 */
	private function get_commands(): array {
		$commands = [
			CLI\Healthcheck::class,
		];

		return array_reduce(
			$commands,
			static function ( array $carry, string $command_class ): array {
				$carry[ $command_class::get_name() ] = [
					'callback'    => [ $command_class, 'run' ],
					'description' => $command_class::get_description(),
				];
				return $carry;
			},
			[]
		);
	}
}
