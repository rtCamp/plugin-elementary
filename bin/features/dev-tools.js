/**
 * WP DevTools feature, declared for the @rtcamp/wp-tooling init engine.
 *
 * Writes committed manifest wiring (composer.json, package.json scripts) plus
 * local dev-environment state (.wp-env.override.json, gitignored). The
 * committed .wp-env.json is never touched, so enabling this never forces Query
 * Monitor and the MCP Adapter onto teammates.
 */

/**
 * External dependencies
 */
const path = require( 'path' );

const PACKAGE = 'rtcamp/wp-devtools';

const CONSTRAINT = 'dev-release/v1.0.0';
const REPOSITORY = {
	type: 'vcs',
	url: 'https://github.com/rtCamp/wp-devtools.git',
	'no-api': true,
};

// wp-env plugins, not Composer deps: Query Monitor collects, the MCP Adapter
// exposes. The adapter is pinned; Query Monitor tracks its latest release.
const PLUGINS = [
	'https://downloads.wordpress.org/plugin/query-monitor.zip',
	'https://github.com/WordPress/mcp-adapter/releases/download/v0.5.0/mcp-adapter.zip',
];

const COMPOSER_FILE = 'composer.json';
const GITIGNORE_FILE = '.gitignore';
const WP_ENV_FILE = '.wp-env.json';
const WP_ENV_OVERRIDE = '.wp-env.override.json';

const DEV_MODE_KEY = 'RT_DEV_TOOLS_DEV_MODE';

// `--user=admin`: every ability is capability-checked and fails anonymously.
const CONNECT_SCRIPT =
	'claude mcp add wp-devtools -- npx wp-env run cli -- wp mcp-adapter serve --server=wp-devtools --user=admin';
const DISCONNECT_SCRIPT = 'claude mcp remove wp-devtools';

// `{}` for a missing file, null for malformed -- callers decide which matters.
const readJson = ( api, rel ) => {
	try {
		return JSON.parse( api.read( rel ) || '{}' );
	} catch {
		return null;
	}
};

// So disable leaves no empty `"require-dev": {}` behind.
const deleteIfEmpty = ( parent, key ) => {
	if ( parent[ key ] && ! Object.keys( parent[ key ] ).length ) {
		delete parent[ key ];
	}
};

// wp-env mounts "." as the host folder name, not the text domain.
const containerRoot = ( api ) => {
	const bucket = 'theme' === api.identity.kind ? 'themes' : 'plugins';
	return `/var/www/html/wp-content/${ bucket }/${ path.basename(
		api.root
	) }`;
};

const isSingleEnv = ( api ) =>
	false === ( readJson( api, WP_ENV_FILE ) || {} ).testsEnvironment;

// The block wp-env applies to the development site. With the tests environment
// disabled, wp-env ignores `env` and reads the root.
const devBlock = ( api, json ) => {
	if ( isSingleEnv( api ) ) {
		return json;
	}
	json.env = json.env || {};
	json.env.development = json.env.development || {};
	return json.env.development;
};

// wp-env replaces the plugins array rather than merging it, so the override has
// to re-emit whatever .wp-env.json already lists.
const basePlugins = ( api ) => {
	const wpEnv = readJson( api, WP_ENV_FILE ) || {};
	const development =
		( ! isSingleEnv( api ) && wpEnv.env && wpEnv.env.development ) || {};
	if ( Array.isArray( development.plugins ) ) {
		return development.plugins;
	}
	if ( Array.isArray( wpEnv.plugins ) ) {
		return wpEnv.plugins;
	}
	return 'theme' === api.identity.kind ? [] : [ '.' ];
};

// Override entries that are neither committed nor ours: a developer's own.
const developerPlugins = ( api, development ) => {
	const base = basePlugins( api );
	const listed = Array.isArray( development.plugins )
		? development.plugins
		: [];
	return listed.filter(
		( url ) => ! base.includes( url ) && ! PLUGINS.includes( url )
	);
};

// SAVEQUERIES gives Query Monitor the backtraces behind file:line, and the
// container/host root pair maps those paths back to this checkout.
const overrideConfig = ( api ) => ( {
	WP_ENVIRONMENT_TYPE: 'local',
	SAVEQUERIES: true,
	[ DEV_MODE_KEY ]: true,
	RT_DEV_TOOLS_TELEMETRY_CONTAINER_ROOT: containerRoot( api ),
	RT_DEV_TOOLS_TELEMETRY_HOST_ROOT: api.root,
	RT_DEV_TOOLS_TELEMETRY_LOOPBACK_BASE: 'http://wordpress',
} );

// The override holds absolute host paths, so keep it ignored even if a fork
// trimmed its .gitignore.
const ensureGitignored = ( api ) => {
	const current = api.read( GITIGNORE_FILE );
	if ( null === current ) {
		api.write( GITIGNORE_FILE, `/${ WP_ENV_OVERRIDE }\n` );
		return;
	}
	const listed = current
		.split( '\n' )
		.some(
			( line ) => line.trim().replace( /^\//, '' ) === WP_ENV_OVERRIDE
		);
	if ( listed ) {
		return;
	}
	const prefix = current.endsWith( '\n' ) ? current : `${ current }\n`;
	api.write( GITIGNORE_FILE, `${ prefix }/${ WP_ENV_OVERRIDE }\n` );
};

const addComposerEntry = ( api ) => {
	api.editJson( COMPOSER_FILE, ( json ) => {
		const repositories = json.repositories || [];
		const hasRepository = repositories.some(
			( entry ) => entry.url === REPOSITORY.url
		);
		if ( ! hasRepository ) {
			repositories.push( { ...REPOSITORY } );
		}
		json.repositories = repositories;
		json[ 'require-dev' ] = {
			...json[ 'require-dev' ],
			[ PACKAGE ]: CONSTRAINT,
		};
	} );
};

const removeComposerEntry = ( api ) => {
	api.editJson( COMPOSER_FILE, ( json ) => {
		if ( json[ 'require-dev' ] ) {
			delete json[ 'require-dev' ][ PACKAGE ];
			deleteIfEmpty( json, 'require-dev' );
		}
		if ( Array.isArray( json.repositories ) ) {
			json.repositories = json.repositories.filter(
				( entry ) => entry.url !== REPOSITORY.url
			);
			deleteIfEmpty( json, 'repositories' );
		}
	} );
};

const writeOverride = ( api ) => {
	api.editJson(
		WP_ENV_OVERRIDE,
		( json ) => {
			const development = devBlock( api, json );
			// Rebuilt from the current committed list, so a .wp-env.json that
			// gained a plugin is picked up rather than masked.
			development.plugins = [
				...new Set( [
					...basePlugins( api ),
					...developerPlugins( api, development ),
					...PLUGINS,
				] ),
			];
			development.config = {
				...development.config,
				...overrideConfig( api ),
			};
		},
		{ create: true, indentFrom: WP_ENV_FILE }
	);
};

// A plugins list left here would replace the committed one, so drop it unless
// the developer added entries of their own (kept beside the base list).
const prunePlugins = ( api, development ) => {
	const kept = developerPlugins( api, development );
	if ( ! kept.length ) {
		delete development.plugins;
		return;
	}
	development.plugins = [ ...new Set( [ ...basePlugins( api ), ...kept ] ) ];
};

// Remove our entries, then the file if nothing else is left in it.
const pruneOverride = ( api ) => {
	if ( ! api.exists( WP_ENV_OVERRIDE ) ) {
		return;
	}

	api.editJson( WP_ENV_OVERRIDE, ( json ) => {
		const development = devBlock( api, json );
		if ( development.config ) {
			Object.keys( overrideConfig( api ) ).forEach(
				( key ) => delete development.config[ key ]
			);
			deleteIfEmpty( development, 'config' );
		}
		if ( Array.isArray( development.plugins ) ) {
			prunePlugins( api, development );
		}
		if ( json.env ) {
			deleteIfEmpty( json.env, 'development' );
			deleteIfEmpty( json, 'env' );
		}
	} );

	const remaining = readJson( api, WP_ENV_OVERRIDE ) || {};
	if ( ! Object.keys( remaining ).length ) {
		api.remove( WP_ENV_OVERRIDE );
	}
};

const onEnable = ( api ) => {
	addComposerEntry( api );
	writeOverride( api );
	ensureGitignored( api );

	// `update`, not `install`: the lock has no entry for a just-added package.
	api.note(
		`wp-devtools: run \`composer update ${ PACKAGE } -W\`, then \`npm run wp-env start && npm run dev:connect\`.`
	);
	api.note(
		`wp-devtools: ${ WP_ENV_OVERRIDE } carries this machine's paths and is gitignored -- teammates run \`npm run init -- --enable=dev-tools\` to wire their own.`
	);
};

const onDisable = ( api ) => {
	removeComposerEntry( api );
	api.note(
		`wp-devtools: run \`composer update ${ PACKAGE } -W\` to drop it from composer.lock and vendor/.`
	);
	api.note(
		`wp-devtools: if Claude Code is still connected, run \`${ DISCONNECT_SCRIPT }\`.`
	);
	pruneOverride( api );
};

// Needs both halves: a fresh clone has the composer entry but not the
// gitignored override, and nothing works there. Must not throw: `init --list`
// runs it too.
const detect = ( api ) => {
	const composer = readJson( api, COMPOSER_FILE );
	const override = readJson( api, WP_ENV_OVERRIDE );
	if ( ! composer || ! override ) {
		return false;
	}
	const development = devBlock( api, override );
	return (
		Boolean( ( composer[ 'require-dev' ] || {} )[ PACKAGE ] ) &&
		true === ( development.config || {} )[ DEV_MODE_KEY ]
	);
};

module.exports = {
	key: 'dev-tools',
	label: 'WP DevTools (runtime telemetry over MCP)',
	category: 'Developer Tooling',
	description:
		'Opt-in. Adds rtcamp/wp-devtools as a dev dependency and the dev:connect script, and writes the local wp-env override (Query Monitor + the MCP Adapter, the dev-mode gate, and the host path map). The committed .wp-env.json is left untouched.',
	apply: {
		scripts: {
			'dev:connect': CONNECT_SCRIPT,
			'dev:disconnect': DISCONNECT_SCRIPT,
		},
	},
	onEnable,
	onDisable,
	detect,
};
