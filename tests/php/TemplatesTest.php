<?php
/**
 * Tests for the plugin Templates loader.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Tests;

use Project_Name\Features\Core\Templates;
use Project_Name\Features\Main;

/**
 * Class TemplatesTest
 */
final class TemplatesTest extends TestCase {

	/**
	 * The class exists and extends the framework TemplateLoader.
	 */
	public function test_extends_framework_template_loader(): void {
		$this->assertInstanceOf( 'rtCamp\WPFramework\TemplateLoader', new Templates() );
	}

	/**
	 * It is shareable, so the container hands out a single instance.
	 */
	public function test_is_shareable(): void {
		$this->assertInstanceOf( 'rtCamp\WPFramework\Contracts\Interfaces\Shareable', new Templates() );
	}

	/**
	 * It is registered in Main and resolvable from the container.
	 */
	public function test_registered_and_shared_in_main(): void {
		$this->assertContains( Templates::class, Main::CLASSES );
		$this->assertInstanceOf( Templates::class, Main::get_instance()->get_shared( Templates::class ) );
	}

	/**
	 * It resolves the plugin's own templates (e.g. the example block template).
	 */
	public function test_resolves_a_plugin_template(): void {
		$located = ( new Templates() )->locate( 'block-templates/example-block-dynamic' );

		$this->assertIsString( $located );
		$this->assertStringEndsWith( 'block-templates/example-block-dynamic.php', (string) $located );
	}

	/**
	 * A missing template resolves to false rather than erroring.
	 */
	public function test_missing_template_returns_false(): void {
		$this->assertFalse( ( new Templates() )->locate( 'no-such-template' ) );
	}
}
