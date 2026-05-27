<?php
/**
 * Tests for the Main plugin bootstrap class.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Tests;

use Project_Name\Features\Main;
use Project_Name\Features\Core\PluginSetup;

/**
 * Class MainTest
 */
final class MainTest extends TestCase {

	/**
	 * The bootstrap class autoloads under its FQN.
	 *
	 * Catches PSR-4 / namespace breakage at the cheapest possible level —
	 * if this fails, every other test in the suite is meaningless.
	 */
	public function test_main_class_exists(): void {
		$this->assertTrue( class_exists( Main::class ) );
	}

	/**
	 * get_instance() returns a Main and is a true singleton.
	 */
	public function test_get_instance_returns_singleton(): void {
		$first  = Main::get_instance();
		$second = Main::get_instance();

		$this->assertInstanceOf( Main::class, $first );
		$this->assertSame( $first, $second );
	}

	/**
	 * activate() persists the plugin version option.
	 */
	public function test_activate_persists_plugin_version(): void {
		delete_option( 'project_name_features_version' );

		$setup = new PluginSetup();
		$setup->activate();

		$this->assertSame(
			PROJECT_NAME_FEATURES_VERSION,
			get_option( 'project_name_features_version' )
		);
	}

	/**
	 * deactivate() unschedules the example cron event.
	 */
	public function test_deactivate_clears_scheduled_cron_event(): void {
		wp_schedule_event( time(), 'daily', \Project_Name\Features\Modules\Cron\ExampleCronJob::HOOK );
		$this->assertNotFalse( wp_next_scheduled( \Project_Name\Features\Modules\Cron\ExampleCronJob::HOOK ) );

		$setup = new PluginSetup();
		$setup->deactivate();

		$this->assertFalse( wp_next_scheduled( \Project_Name\Features\Modules\Cron\ExampleCronJob::HOOK ) );
	}

	/**
	 * Every module class-string referenced from Main::CLASSES actually exists.
	 *
	 * If someone renames a module file without updating Main, this catches it.
	 */
	public function test_every_loaded_class_exists(): void {
		foreach ( Main::CLASSES as $class ) {
			$this->assertTrue( class_exists( $class ), "Missing class: {$class}" );
		}
	}
}
