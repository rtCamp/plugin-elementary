import react from '@vitejs/plugin-react';
import { configDefaults, defineConfig } from 'vitest/config';

const EXT = '{js,jsx,ts,tsx}';
const TESTS = [
	`{src,tests/js}/**/__tests__/**/*.${ EXT }`,
	`{src,tests/js}/**/test/*.${ EXT }`,
	`{src,tests/js}/**/?(*.)test.${ EXT }`,
];
const DOM_TESTS = `{src,tests/js}/**/*.jsdom.test.${ EXT }`;

// Vite parses JSX only in .jsx and .tsx files.
export default defineConfig( {
	plugins: [ react() ],
	test: {
		// Init can remove all bundled tests.
		passWithNoTests: true,
		restoreMocks: true,
		// WordPress packages import JSON without the attribute Node requires.
		server: { deps: { inline: [ /@wordpress\// ] } },
		coverage: {
			// Untested files report 0% instead of being left out.
			include: [ `src/**/*.${ EXT }` ],
			reporter: [ 'lcov', 'text-summary' ],
			reportsDirectory: 'tests/logs',
		},
		projects: [
			{
				extends: true,
				test: {
					name: 'node',
					include: TESTS,
					exclude: [ ...configDefaults.exclude, DOM_TESTS ],
				},
			},
			{
				extends: true,
				test: {
					name: 'jsdom',
					include: [ DOM_TESTS ],
					environment: 'jsdom',
					setupFiles: [ './tests/js/setup-jsdom.js' ],
				},
			},
		],
	},
} );
