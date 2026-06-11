<?php
/**
 * Tests for the plugin Encryption service.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Tests;

use Project_Name\Features\Core\Encryption;
use Project_Name\Features\Helpers\Util;
use Project_Name\Features\Main;
use rtCamp\WPFramework\Utils\Encryptor;

/**
 * Class EncryptionTest
 */
final class EncryptionTest extends TestCase {

	/**
	 * The class exists and extends the framework Encryptor.
	 */
	public function test_extends_framework_encryptor(): void {
		$this->assertInstanceOf( Encryptor::class, new Encryption() );
	}

	/**
	 * It is shareable, registered in Main, and resolvable from the container.
	 */
	public function test_registered_and_shared_in_main(): void {
		$this->assertContains( Encryption::class, Main::CLASSES );
		$this->assertInstanceOf( Encryption::class, Main::get_instance()->get_shared( Encryption::class ) );
	}

	/**
	 * A keyless framework Encryptor refuses to encrypt — the invariant the
	 * service falls through to when PROJECT_NAME_FEATURES_ENCRYPTION_KEY is missing.
	 */
	public function test_missing_key_throws(): void {
		$this->expectException( \RuntimeException::class );

		( new Encryptor() )->encrypt( 'no key configured' );
	}

	/**
	 * With the key constant defined, Util::encrypt() / Util::decrypt() roundtrip.
	 */
	public function test_encrypt_decrypt_roundtrips_with_key_constant(): void {
		if ( ! defined( 'PROJECT_NAME_FEATURES_ENCRYPTION_KEY' ) ) {
			define( 'PROJECT_NAME_FEATURES_ENCRYPTION_KEY', str_repeat( 'k', 32 ) );
		}

		$encrypted = Util::encrypt( 'sensitive-value' );

		$this->assertIsString( $encrypted );
		$this->assertNotSame( 'sensitive-value', $encrypted );
		$this->assertSame( 'sensitive-value', Util::decrypt( $encrypted ) );
	}

	/**
	 * Tampered ciphertext fails authentication and returns false (no throw).
	 */
	public function test_decrypt_returns_false_for_tampered_value(): void {
		if ( ! defined( 'PROJECT_NAME_FEATURES_ENCRYPTION_KEY' ) ) {
			define( 'PROJECT_NAME_FEATURES_ENCRYPTION_KEY', str_repeat( 'k', 32 ) );
		}

		$encrypted = Util::encrypt( 'secret' );
		$this->assertIsString( $encrypted );

		$decoded     = base64_decode( $encrypted, true );
		$decoded[20] = 'A' === $decoded[20] ? 'B' : 'A';

		$this->assertFalse( Util::decrypt( base64_encode( $decoded ) ) );
	}
}
