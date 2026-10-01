<?php
/**
 * Rewrite class.
 *
 * @package project-name-features
 */

namespace Project_Name\Features\Inc;

use Project_Name\Features\Inc\Traits\Singleton;

/**
 * Class Rewrite
 */
class Rewrite {

	use Singleton;

	/**
	 * Construct method.
	 */
	protected function __construct() {

		$this->setup_hooks();

	}

	/**
	 * To setup action/filter.
	 *
	 * @return void
	 */
	protected function setup_hooks() {}
}
