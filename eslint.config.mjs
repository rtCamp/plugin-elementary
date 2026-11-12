/**
 * rtCamp shared ESLint flat config.
 *
 * Extends the Jest entry of `@rtcamp/eslint-config` (which bundles
 * `@wordpress/eslint-plugin`, eslint-comments, and the Jest rules for test
 * files, which run on `wp-scripts test-unit-jest`). Only the
 * project-specific ignores are layered on top.
 */
import rtCampConfig from '@rtcamp/eslint-config/jest';

export default [
	...rtCampConfig,
	{
		ignores: [
			'**/*.min.js',
			'**/node_modules/**',
			'**/vendor/**',
			'assets/build/**',
		],
	},
];
