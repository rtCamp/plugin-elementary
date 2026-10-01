# Changelog

All notable changes to this project are documented in this file. The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## 1.0.0 - 2026-10-01

### Added

- Framework-service example modules demonstrating the shared wp-primitives engine.
- Conventional-commit git hook and `release:bump` / `release:changelog` / `release:zip` scripts, provided by `@rtcamp/wp-tooling`.
- Developer documentation: Getting Started, Initialization, Local development, Included features, Scaffolding, Blocks and assets, and Tailwind guides, published through a Docusaurus workflow.
- `CONTRIBUTING.md` and maintainer guidance under `docs/internal/`.
- A `.distignore`, so `release:zip` packs only the files the plugin ships.

### Changed

- **Renamed from `features-plugin-skeleton` to `plugin-elementary`** and moved to the
  `rtCamp/plugin-elementary` repository, aligning it with the sibling `theme-elementary`.
  The scaffold placeholder tokens (`Project_Name`, `project-name`) are unchanged. The
  Composer package is now `rtcamp/plugin-elementary`, matching the repository, and
  `npm run init` still replaces it with `rtcamp/<slug>-features`.
- Updated the shared engine dependency from `rtcamp/wp-framework` to `rtcamp/wp-primitives ^2.1`.
- Adopted the rtCamp shared coding standards from the common packages (PHPCS, PHPStan, ESLint, Stylelint).
- Rewrote `README.md` and `DEVELOPMENT.md` as entry points that link framework API detail to `rtcamp/wp-primitives`.
- Installs `@rtcamp/eslint-config`, `@rtcamp/stylelint-config` and `@rtcamp/wp-tooling` from the npm registry, and `rtcamp/wp-primitives`, `rtcamp/wp-phpcs` and `rtcamp/wp-phpstan` from Packagist, instead of git branches and VCS repositories.
- `@wordpress/scripts` 36. JS tests run through `wp-scripts test-unit-jest`, with `jest` and `jest-environment-jsdom` as direct dev dependencies.
- PHP tests run in their own wp-env environment (`.wp-env.tests.json`), so they never touch the development site.
- CI calls [rtCamp/wp-shared-workflows](https://github.com/rtCamp/wp-shared-workflows) at `@v1`.

### Removed

- The pilot-era quick start and internal-testing guides (superseded by the new guides and maintainer docs).

### Fixed

- `composer phpstan` works on a checkout without `node_modules`.

### Security

- Fixes every open npm and Composer security advisory in the plugin's dependencies.
