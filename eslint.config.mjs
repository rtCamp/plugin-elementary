/**
 * rtCamp shared ESLint flat config.
 *
 * Extends @rtcamp/wp-tooling/eslint-config (which bundles @wordpress/eslint-plugin,
 * eslint-comments, and the jest config for test files). Only the
 * project-specific ignores are layered on top.
 */
import rtCampConfig from '@rtcamp/wp-tooling/eslint-config';

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
