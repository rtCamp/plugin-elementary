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

/**
 * Class - Assets
 *
 * Extends the framework's AssetLoader to register the plugin's own assets.
 */
final class Assets extends AssetLoader implements Registrable {

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
