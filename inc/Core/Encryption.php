<?php
/**
 * Plugin encryption service.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Core;

use rtCamp\WPFramework\Contracts\Interfaces\Shareable;
use rtCamp\WPFramework\Utils\Encryptor;

/**
 * Class - Encryption
 *
 * The plugin's shared Encryptor. Extends the framework Encryptor and sources the
 * key via the key() seam from the PROJECT_NAME_FEATURES_ENCRYPTION_KEY constant —
 * define it in wp-config.php before using encryption. There is deliberately no
 * salt fallback: an auth salt should not double as an encryption key, and
 * rotating WordPress salts must not invalidate encrypted data. Shared through
 * the container; call it through Helpers\Util (Util::encrypt() / Util::decrypt()).
 */
final class Encryption extends Encryptor implements Shareable {

	/**
	 * Resolve the plugin's encryption key.
	 *
	 * @return string The encryption key.
	 *
	 * @throws \RuntimeException If PROJECT_NAME_FEATURES_ENCRYPTION_KEY is not defined or is empty.
	 */
	protected function key(): string {
		if ( defined( 'PROJECT_NAME_FEATURES_ENCRYPTION_KEY' ) && '' !== PROJECT_NAME_FEATURES_ENCRYPTION_KEY ) {
			return (string) PROJECT_NAME_FEATURES_ENCRYPTION_KEY;
		}

		return parent::key();
	}
}
