<?php
/**
 * Example custom user role.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules\Roles;

use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractUserRole;

/**
 * Class - ExampleUserRole
 *
 * Defines a custom "Content Editor" role with limited capabilities.
 * Increment {@see get_version()} whenever capabilities change — the role
 * will be re-registered automatically on the next admin request.
 */
final class ExampleUserRole extends AbstractUserRole {

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug(): string {
		return 'project_name_content_editor';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_display_name(): string {
		return __( 'Content Editor', 'project-name-features' );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_capabilities(): array {
		return [
			'read'                   => true,
			'edit_posts'             => true,
			'edit_published_posts'   => true,
			'publish_posts'          => true,
			'delete_posts'           => false,
			'delete_published_posts' => false,
			'upload_files'           => true,
		];
	}

	/**
	 * {@inheritDoc}
	 *
	 * Increment this when configuration changes for role.
	 */
	protected function get_version(): int {
		return 1;
	}
}
