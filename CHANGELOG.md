# Changelog

All notable changes to this project are documented in this file. The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Added

- Framework-service example modules demonstrating the shared wp-primitives engine.
- Conventional-commit git hook and `release:bump` / `release:changelog` / `release:zip` scripts, provided by `@rtcamp/wp-tooling`.
- Developer documentation: Getting Started, Initialization, Local development, Included features, Scaffolding, Blocks and assets, and Tailwind guides, published through a Docusaurus workflow.
- `CONTRIBUTING.md` and maintainer guidance under `docs/internal/`.

### Changed

- **BREAKING:** renamed the repository from `features-plugin-skeleton` to `plugin-elementary`, to match `theme-elementary`. Titles, URLs and `package.json` metadata follow.
- **BREAKING:** the template identity follows theme-elementary. The placeholder plugin is `Elementary Plugin`: text domain and main file `elementary-plugin`, prefixes `elementary_plugin_` and `ELEMENTARY_PLUGIN_`, namespace `rtCamp\Plugin\Elementary` and package `rtcamp/elementary-plugin`. Init derives `rtCamp\Plugin\<Name>` and `rtcamp/<slug>` from the chosen name and no longer appends `Features` / `-features`, so **Acme Content** becomes `acme-content` (namespace `rtCamp\Plugin\Acme_Content`) rather than `acme-content-features`.
- **BREAKING:** moved from `rtcamp/wp-framework` `^1.0` to `rtcamp/wp-primitives` `^2.0` ([rtCamp/wp-primitives#98](https://github.com/rtCamp/wp-primitives/pull/98)). The namespace changes from `rtCamp\WPFramework\` to `rtCamp\WPPrimitives\` in every example module and test, and `npm run sync-ai` now writes `.github/instructions/primitives-php.instructions.md` in place of `framework-php.instructions.md`. Framework docs links are pinned to `v2.0.0`.
- **BREAKING:** the optional `dev-tools` feature installs `rtcamp/wp-devtools` in place of `rtcamp/wp-dev-tools` ([rtCamp/wp-devtools#76](https://github.com/rtCamp/wp-devtools/pull/76)). The MCP server ID, REST route (`/wp-json/wp-devtools/mcp`), ability prefix (`wp-devtools/*`) and tool prefix (`mcp__wp-devtools__*`) move with it, and `bin/dev-tools-e2e.sh` checks the new names. The feature key stays `dev-tools`. A project that enabled the feature before this change must rename the package in `composer.json` and the server in the `dev:connect` / `dev:disconnect` scripts, then run `composer update rtcamp/wp-dev-tools rtcamp/wp-devtools -W` and `claude mcp remove wp-dev-tools && npm run dev:connect`.
- Adopted the rtCamp shared coding standards from the common packages (PHPCS, PHPStan, ESLint, Stylelint).
- Rewrote `README.md` and `DEVELOPMENT.md` as entry points that link framework API detail to `rtcamp/wp-primitives`.

### Removed

- The interim quick start and internal-testing guides, superseded by the new guides and maintainer docs.
