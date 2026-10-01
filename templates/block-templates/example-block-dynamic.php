<?php
/**
 * Example block dynamic template.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div>
	<h2><?php esc_html_e( 'Example block dynamic', 'project-name-features' ); ?></h2>
	<p>
		<?php esc_html_e( 'This is an example block dynamic template.', 'project-name-features' ); ?>
	</p>
</div>
