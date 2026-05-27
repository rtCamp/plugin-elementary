<?php
/**
 * Handles efficient plugin template loading (and overriding from themes).
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Core;

use rtCamp\WPFramework\Contracts\Traits\Singleton;
use rtCamp\WPFramework\Contracts\Traits\TemplateLoaderTrait;

/**
 * Class - Templates
 */
final class Templates {
	use Singleton;
	use TemplateLoaderTrait {
		TemplateLoaderTrait::get_template_part as trait_get_template_part;
	}

	/**
	 * The hook prefix for all filters and actions in this trait.
	 */
	private const HOOK_PREFIX = 'project_name_features';

	/**
	 * The relative template dir (within the plugin root).
	 */
	private const TEMPLATE_DIR = 'templates';

	/**
	 * The theme dir name for template overrides.
	 * Themes can override templates by placing them in: theme/project-name-features/
	 */
	private const TEMPLATE_THEME_DIR = 'project-name-features';

	/**
	 * {@inheritDoc}
	 */
	protected function __construct() {
		$this->hook_prefix        = self::HOOK_PREFIX;
		$this->template_theme_dir = self::TEMPLATE_THEME_DIR;
		$this->template_dir       = PROJECT_NAME_FEATURES_PATH . self::TEMPLATE_DIR;
	}

	/**
	 * Retrieve and optionally load a template part.
	 *
	 * Provides a static interface for use anywhere in the plugin.
	 *
	 * @see TemplateLoaderTrait::get_template_part() for details.
	 *
	 * @param string               $slug The slug name for the generic template.
	 * @param string|null          $name The name of the specialized template.
	 * @param array<string, mixed> $args Optional. Arguments to pass to the template.
	 * @param bool                 $load Whether to load the template immediately or just return the path.
	 * @return string|false The template path if found, or false if not found.
	 */
	public static function get_template_part( string $slug, ?string $name = null, array $args = [], bool $load = true ): string|false {
		return self::get_instance()->trait_get_template_part( $slug, $name, $args, $load );
	}
}
