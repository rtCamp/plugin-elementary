<?php
/**
 * Plugin manifest class.
 *
 * @package project-name-features
 */

namespace Project_Name\Features\Tests\Inc;

use Project_Name\Features\Tests\TestCase;

/**
 * Class Test_Plugin
 *
 * @since 1.0.0
 */
class Test_Plugin extends TestCase {

	/**
	 * Test that the plugin class exists.
	 *
	 * @since 1.0.0
	 */
	public function test_plugin_class_exists() {
		$this->assertTrue( class_exists( 'Project_Name\Features\Inc\Plugin' ) );
	}
}
