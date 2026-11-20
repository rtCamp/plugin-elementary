<?php
/**
 * Tests for JS translation loading in the Assets class.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Tests;

use Project_Name\Features\Core\Assets;

/**
 * Class AssetsTranslationsTest
 *
 * CI runs PHPUnit without a build, so each test writes a built js/editor fixture
 * and a translation file, then removes them. The starter has no editor entry, so
 * the fixture does not collide with a local build.
 *
 * @since 1.0.2
 */
final class AssetsTranslationsTest extends TestCase {

	/**
	 * Locale the translation fixture is written for.
	 */
	private const LOCALE = 'zz_ZZ';

	/**
	 * Files and directories this test created, in creation order.
	 *
	 * @var array<int, string>
	 */
	private array $created = [];

	/**
	 * Start from a fresh script registry in the fixture locale.
	 */
	public function set_up(): void {
		parent::set_up();

		$GLOBALS['wp_scripts'] = null;

		add_filter( 'determine_locale', static fn (): string => self::LOCALE );
	}

	/**
	 * Remove the fixtures and the script registry this test created.
	 */
	public function tear_down(): void {
		foreach ( array_reverse( $this->created ) as $path ) {
			if ( is_dir( $path ) ) {
				rmdir( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir -- Test fixture cleanup.
			} elseif ( file_exists( $path ) ) {
				unlink( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- Test fixture cleanup.
			}
		}

		$this->created         = [];
		$GLOBALS['wp_scripts'] = null;

		parent::tear_down();
	}

	/**
	 * A script built against @wordpress/i18n gets its strings from the plugin's languages/ directory.
	 */
	public function test_script_using_i18n_loads_plugin_translations(): void {
		$this->create_fixtures( [ 'wp-i18n' ] );

		$assets = new Assets();
		$assets->register_editor_assets();

		$translations = wp_scripts()->print_translations( $assets->handle( 'editor' ), false );

		$this->assertIsString( $translations );
		$this->assertStringContainsString( 'Translated example string', $translations );
	}

	/**
	 * A script without i18n strings gets no translations, so wp-i18n stays off the page.
	 */
	public function test_script_without_i18n_skips_translations(): void {
		$this->create_fixtures( [] );

		$assets = new Assets();
		$assets->register_editor_assets();

		$handle = $assets->handle( 'editor' );
		$script = wp_scripts()->query( $handle );

		$this->assertInstanceOf( \_WP_Dependency::class, $script );
		$this->assertNotContains( 'wp-i18n', $script->deps );
		$this->assertFalse( wp_scripts()->print_translations( $handle, false ) );
	}

	/**
	 * Write assets/build/js/editor.js, its .asset.php manifest, and a translation
	 * file named the way `wp i18n make-json` names it for that script.
	 *
	 * @param array<int, string> $dependencies Dependencies the manifest lists.
	 */
	private function create_fixtures( array $dependencies ): void {
		$build_dir = PROJECT_NAME_FEATURES_PATH . 'assets/build/js';
		$json_file = sprintf(
			'%slanguages/project-name-features-%s-%s.json',
			PROJECT_NAME_FEATURES_PATH,
			self::LOCALE,
			md5( 'assets/build/js/editor.js' )
		);

		$manifest = sprintf(
			"<?php return array( 'dependencies' => %s, 'version' => 'test' );\n",
			var_export( $dependencies, true ) // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export -- Writes a fixture manifest.
		);

		$translations = (string) wp_json_encode(
			[
				'domain'      => 'messages',
				'locale_data' => [
					'messages' => [
						''               => [
							'domain' => 'messages',
							'lang'   => self::LOCALE,
						],
						'Example string' => [ 'Translated example string' ],
					],
				],
			]
		);

		$files = [
			"{$build_dir}/editor.js"        => '',
			"{$build_dir}/editor.asset.php" => $manifest,
			$json_file                      => $translations,
		];

		foreach ( array_keys( $files ) as $file ) {
			if ( file_exists( $file ) ) {
				$this->markTestSkipped( "{$file} already exists; this test writes its own fixture there." );
			}
		}

		$missing = [];
		$path    = $build_dir;
		while ( ! is_dir( $path ) ) {
			$missing[] = $path;
			$path      = dirname( $path );
		}
		foreach ( array_reverse( $missing ) as $new_dir ) {
			mkdir( $new_dir ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir -- Test fixture.
			$this->created[] = $new_dir;
		}

		foreach ( $files as $file => $contents ) {
			file_put_contents( $file, $contents ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- Test fixture.
			$this->created[] = $file;
		}
	}
}
