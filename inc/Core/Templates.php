<?php
/**
 * Handles efficient plugin template loading (and overriding from themes).
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Core;

use rtCamp\WPFramework\Contracts\Interfaces\Shareable;
use rtCamp\WPFramework\TemplateLoader;

/**
 * Class - Templates
 *
 * The plugin's template loader: ships template parts the active theme can
 * override (child theme > parent theme > plugin). Extends the framework
 * TemplateLoader and is shared through the container, mirroring the Assets
 * loader. Access it from anywhere via Helpers\Util::templates(), e.g.
 * Util::templates()->render( $slug ) or Util::templates()->get( $slug ).
 */
final class Templates extends TemplateLoader implements Shareable {

	/**
	 * Hook prefix for all of this loader's filters and actions.
	 */
	private const HOOK_PREFIX = 'project_name_features';

	/**
	 * Template directory, relative to the plugin root.
	 */
	private const TEMPLATE_DIR = 'templates';

	/**
	 * Directory name themes override templates into: theme/project-name-features/.
	 */
	private const TEMPLATE_THEME_DIR = 'project-name-features';

	/**
	 * Configure the loader with the plugin's template paths.
	 */
	public function __construct() {
		parent::__construct(
			self::HOOK_PREFIX,
			PROJECT_NAME_FEATURES_PATH . self::TEMPLATE_DIR,
			self::TEMPLATE_THEME_DIR
		);
	}
}
