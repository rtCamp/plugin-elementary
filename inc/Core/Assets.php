<?php
/**
 * Enqueue plugin assets: styles, scripts, and blocks.
 *
 * @package Project_Name\Features
 */

declare( strict_types = 1 );

namespace Project_Name\Features\Core;

use rtCamp\WPFramework\AssetLoader;
use rtCamp\WPFramework\Contracts\Interfaces\Registrable;
use rtCamp\WPFramework\Contracts\Interfaces\Shareable;

/**
 * Class - Assets
 *
 * Extends the framework's AssetLoader to register the plugin's own assets.
 */
final class Assets extends AssetLoader implements Registrable, Shareable {

	/**
	 * Prefix for all asset handles.
	 */
	private const PREFIX = 'project-name-features-';

	/**
	 * Asset handles.
	 */
	public const FRONTEND_HANDLE = self::PREFIX . 'frontend';
	public const ADMIN_HANDLE    = self::PREFIX . 'admin';
	public const EDITOR_HANDLE   = self::PREFIX . 'editor';

	/**
	 * Static blocks to register (build dir names, no render_callback needed).
	 *
	 * Dynamic blocks are registered via their own AbstractBlock subclasses.
	 */
	private const STATIC_BLOCKS = [
		'example-block',
		'example-block-interactive',
	];

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			(string) PROJECT_NAME_FEATURES_PATH,
			(string) PROJECT_NAME_FEATURES_URL,
			'assets/build'
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function register_hooks(): void {
		add_action( 'init', [ $this, 'register_blocks' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_assets' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'register_editor_assets' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_module_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_browser_sync' ] );
	}

	/**
	 * Enqueue the BrowserSync client script for local live reload.
	 *
	 * Only runs in the `local` environment and when not disabled. The client URL
	 * is derived from the site URL and the BrowserSync port (BS_PORT in
	 * .env.local, default 3000), or taken verbatim from the
	 * PROJECT_NAME_FEATURES_BROWSER_SYNC_URL constant when defined (for custom
	 * ports or remote/proxied setups).
	 */
	public function enqueue_browser_sync(): void {
		if ( 'local' !== wp_get_environment_type() || PROJECT_NAME_FEATURES_DISABLE_BROWSER_SYNC ) {
			return;
		}

		if ( defined( 'PROJECT_NAME_FEATURES_BROWSER_SYNC_URL' ) ) {
			$bs_url = PROJECT_NAME_FEATURES_BROWSER_SYNC_URL;
		} else {
			$scheme = is_ssl() ? 'https' : 'http';
			$host   = wp_parse_url( home_url(), PHP_URL_HOST );
			$host   = $host ? $host : 'localhost';
			$port   = $this->get_browser_sync_port();
			$bs_url = "{$scheme}://{$host}:{$port}/browser-sync/browser-sync-client.js";
		}

		wp_enqueue_script( self::PREFIX . 'browser-sync', $bs_url, [], PROJECT_NAME_FEATURES_VERSION, true );
	}

	/**
	 * Read the BrowserSync port from .env.local (BS_PORT), defaulting to 3000.
	 *
	 * Keeps the enqueued client URL in sync with the port webpack/BrowserSync
	 * actually bind to, which is read from the same .env.local on the build side.
	 * Falls back to the default when BS_PORT is absent or not a valid TCP port
	 * (1–65535).
	 *
	 * THIS METHOD IS INTENDED FOR LOCAL DEVELOPMENT ENVIRONMENTS ONLY.
	 * 
	 * @return int BrowserSync port.
	 */
	private function get_browser_sync_port(): int {
		$default  = 3000;
		$env_file = PROJECT_NAME_FEATURES_PATH . '.env.local';

		if ( ! is_readable( $env_file ) ) {
			return $default;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local dev only; reading a small project file, not remote.
		$contents = file_get_contents( $env_file );

		if ( false === $contents ) {
			return $default;
		}

		if ( preg_match( '/^\s*BS_PORT\s*=\s*(\d+)/m', $contents, $matches ) ) {
			$port = (int) $matches[1];

			if ( $port >= 1 && $port <= 65535 ) {
				return $port;
			}
		}

		return $default;
	}

	/**
	 * Register static blocks.
	 *
	 * Dynamic blocks self-register via their AbstractBlock subclass.
	 */
	public function register_blocks(): void {
		$blocks_dir = $this->base_dir . 'assets/build/blocks/';

		foreach ( self::STATIC_BLOCKS as $block ) {
			$dir = $blocks_dir . $block;
			if ( is_dir( $dir ) ) {
				register_block_type( $dir );
			}
		}
	}

	/**
	 * Register assets for the frontend.
	 *
	 * Assets are registered once centrally and enqueued in modules that need them.
	 */
	public function register_assets(): void {
		$this->register_script( self::FRONTEND_HANDLE, 'js/main' );
		$this->register_style( self::FRONTEND_HANDLE, 'css/main' );

		wp_enqueue_script( self::FRONTEND_HANDLE );
		wp_enqueue_style( self::FRONTEND_HANDLE );
	}

	/**
	 * Register assets for the admin.
	 */
	public function register_admin_assets(): void {
		$this->register_script( self::ADMIN_HANDLE, 'js/admin' );
		$this->register_style( self::ADMIN_HANDLE, 'css/admin' );
	}

	/**
	 * Register assets for the block editor.
	 */
	public function register_editor_assets(): void {
		$this->register_script( self::EDITOR_HANDLE, 'js/editor' );
		$this->register_style( self::EDITOR_HANDLE, 'css/editor' );
	}

	/**
	 * Register interactive block module scripts.
	 */
	public function register_module_scripts(): void {
		if ( $this->register_script_module( '@project-name-features/module', 'js/modules/module', [ '@wordpress/interactivity' ] ) ) {
			wp_enqueue_script_module( '@project-name-features/module' );
		}
	}
}
