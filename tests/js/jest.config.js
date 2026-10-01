module.exports = {
	rootDir: '../../',
	testEnvironment: 'node',
	testMatch: [ '<rootDir>/tests/js/**/*.test.js' ],
	setupFiles: [ '<rootDir>/tests/js/setup-globals' ],
	testPathIgnorePatterns: [
		'<rootDir>/.git',
		'<rootDir>/node_modules',
		'<rootDir>/assets/build',
		'<rootDir>/vendor',
		// Add more specific patterns here if needed.
	],
	coveragePathIgnorePatterns: [
		'<rootDir>/node_modules',
		'<rootDir>/assets/build/',
		// Add more specific patterns here if needed.
	],
	modulePathIgnorePatterns: [
		// Add more specific patterns here if needed.
	],
	coverageReporters: [ 'lcov' ],
	coverageDirectory: '<rootDir>/tests/logs',
	reporters: [
		[ 'jest-silent-reporter', { useDots: true } ],
		'github-actions',
	],
};
