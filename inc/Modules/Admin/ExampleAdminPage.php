<?php
/**
 * Example Admin Page: a plain Tools subpage.
 *
 * Demonstrates AbstractAdminPage: a menu or submenu screen with custom HTML
 * and NO Settings API wiring (reach for AbstractSettingsPage when you need
 * registered options). Here it is a read-only panel registered as a submenu
 * under Tools.
 *
 * Copy this class as a starting point for any custom admin screen.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Modules\Admin;

use rtCamp\WPPrimitives\Contracts\Abstracts\AbstractAdminPage;

/**
 * Class ExampleAdminPage
 */
final class ExampleAdminPage extends AbstractAdminPage {

	/**
	 * {@inheritDoc}
	 */
	public static function get_slug(): string {
		return 'project-name-features-tools';
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_page_title(): string {
		return __( 'Project Name Features Tools', 'project-name-features' );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_menu_title(): string {
		return __( 'Features Tools', 'project-name-features' );
	}

	/**
	 * Register as a submenu under Tools rather than a top-level menu.
	 *
	 * @return string Parent menu slug.
	 */
	protected function get_parent_slug(): string {
		return 'tools.php';
	}

	/**
	 * {@inheritDoc}
	 */
	public function render(): void {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( $this->get_page_title() ); ?></h1>
			<p>
				<?php esc_html_e( 'A plain admin page rendered by the plugin. Replace this with your own tooling UI.', 'project-name-features' ); ?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: plugin version. */
					esc_html__( 'Plugin version: %s', 'project-name-features' ),
					esc_html( PROJECT_NAME_FEATURES_VERSION )
				);
				?>
			</p>
		</div>
		<?php
	}
}
