/**
 * Scaffold config for features-plugin-skeleton, consumed by bin/init.js and
 * handed to the shared scaffold engine in rtcamp/wp-framework.
 *
 * The `Project_Name` / `project-name` tokens already cover the composer package
 * and PHP namespace, so no explicit tokens are needed.
 *
 * Search tokens are embedded verbatim; safe because the engine never
 * search-replaces files under bin/.
 */

// The main plugin file (e.g. my-plugin-features.php) and its Tailwind enable
// constant, derived from the resolved identity. The constant mirrors the
// plugin's other constants (<CONSTANT_PREFIX>_FEATURES_*).
const tailwindEntry = ( api ) => `${ api.identity.package.split( '/' )[ 1 ] }.php`;
const tailwindConst = ( api ) => `${ api.identity.constantPrefix }_FEATURES_ENABLE_TAILWIND`;

module.exports = {
	kind: 'plugin',
	vendor: 'rtcamp',

	source: {
		name: 'Project Name',
		namespace: 'Project_Name\\Features',
		package: 'rtcamp/project-name-features',
	},

	// Derive the namespace and composer package from the chosen name.
	namespace: ( id ) => `${ id.pascalSnake }\\Features`,
	package: ( id ) => `rtcamp/${ id.kebab }-features`,

	version: '1.0.0',

	// Identity fields shown in the review table and offered in the editor.
	fields: [
		{ key: 'name', label: 'Plugin Name' },
		{ key: 'version', label: 'Version' },
		{ key: 'textDomain', label: 'Text Domain' },
		{ key: 'package', label: 'Package' },
		{ key: 'namespace', label: 'Namespace' },
		{ key: 'functionPrefix', label: 'Function Prefix' },
		{ key: 'constantPrefix', label: 'Constant Prefix' },
	],

	versionFiles: [
		{ path: ( target ) => `${ target.kebab }-features.php`, kind: 'php-header' },
		{ path: 'package.json', kind: 'json', key: 'version' },
	],

	steps: { composer: true, cleanup: false, git: true, hooks: true },

	// Optional features toggled in manage mode. Tailwind enqueue is gated on the
	// <CONSTANT_PREFIX>_FEATURES_ENABLE_TAILWIND constant in the main plugin file;
	// the feature flips it and adds/removes the entry CSS, PostCSS config and deps.
	featuresDir: 'bin/features',
	features: [
		{
			key: 'tailwind',
			label: 'Tailwind CSS',
			description: 'Tailwind v4 (opt-in). Adds the entry CSS, PostCSS config and deps, and flips the ENABLE_TAILWIND constant that gates the enqueue.',
			apply: {
				files: [
					{ from: 'tailwind/tailwind.css', to: 'src/css/tailwind.css' },
					{ from: 'tailwind/postcss.config.js', to: 'postcss.config.js' },
				],
				devDependencies: {
					'@rtcamp/tailwind-config': '^0.1.0',
					tailwindcss: '^4.3.0',
					'@tailwindcss/postcss': '^4.3.0',
				},
			},
			onEnable: ( api ) => api.setDefine( tailwindEntry( api ), tailwindConst( api ), true ),
			onDisable: ( api ) => api.setDefine( tailwindEntry( api ), tailwindConst( api ), false ),
			detect: ( api ) => true === api.readDefine( tailwindEntry( api ), tailwindConst( api ) ),
		},
		{
			key: 'hmr',
			label: 'HMR (BrowserSync live reload)',
			description: 'Live reload in watch mode. Toggling flips ENABLE_HMR in .env.local, which webpack (BrowserSync server) and PHP (client enqueue) both honour. Default on; deps stay installed.',
			// No files or deps: the code lives in webpack.config.js + Assets.php
			// permanently and is gated on the flag. detect reads the live flag,
			// defaulting on when .env.local (gitignored) has no ENABLE_HMR.
			onEnable: ( api ) => api.setEnv( '.env.local', 'ENABLE_HMR', 'true' ),
			onDisable: ( api ) => api.setEnv( '.env.local', 'ENABLE_HMR', 'false' ),
			detect: ( api ) => {
				const value = api.readEnv( '.env.local', 'ENABLE_HMR' );
				return null === value || ! [ 'false', '0', 'no', 'off' ].includes( value.toLowerCase() );
			},
		},
	],

	// First-run "which example sets to remove?" prompt. Each group's `strip`
	// files hold its wp:example markers (stripped either way; enclosed code kept
	// on keep, dropped on remove); `remove` globs are deleted when removed.
	examples: {
		marker: 'wp:example',
		groups: [
			{ key: 'post-types', label: 'Post types', strip: [ 'inc/Modules/PostTypes.php' ], remove: [ 'inc/Modules/PostTypes/Example*.php' ] },
			{ key: 'taxonomies', label: 'Taxonomies', strip: [ 'inc/Modules/Taxonomies.php' ], remove: [ 'inc/Modules/Taxonomies/Example*.php' ] },
			{ key: 'blocks', label: 'Blocks', strip: [ 'inc/Modules/Blocks.php', 'inc/Core/Assets.php' ], remove: [ 'inc/Modules/Blocks/Example*.php', 'src/blocks/example-*', 'templates/block-templates/example-*.php' ] },
			{ key: 'cron', label: 'Cron jobs', strip: [ 'inc/Modules/Cron.php' ], remove: [ 'inc/Modules/Cron/Example*.php' ] },
			{ key: 'rest', label: 'REST controllers', strip: [ 'inc/Modules/REST.php' ], remove: [ 'inc/Modules/REST/Example*.php' ] },
			{ key: 'roles', label: 'User roles', strip: [ 'inc/Modules/Roles.php' ], remove: [ 'inc/Modules/Roles/Example*.php' ] },
			{ key: 'settings', label: 'Settings pages', strip: [ 'inc/Modules/Settings.php' ], remove: [ 'inc/Modules/Settings/Example*.php' ] },
			{ key: 'shortcodes', label: 'Shortcodes', strip: [ 'inc/Modules/Shortcodes.php' ], remove: [ 'inc/Modules/Shortcodes/Example*.php' ] },
		],
	},

	docsUrl: 'https://github.com/rtCamp/features-plugin-skeleton/blob/master/README.md',
	repoUrl: 'https://github.com/rtCamp/features-plugin-skeleton',
};
