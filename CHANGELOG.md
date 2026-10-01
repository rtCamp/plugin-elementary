# Changelog

All notable changes to this project are documented in this file. The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Added

- Framework-service example modules demonstrating the shared wp-primitives engine.
- Conventional-commit git hook and `release:bump` / `release:changelog` / `release:zip` scripts, provided by `@rtcamp/wp-tooling`.
- Developer documentation: Getting Started, Initialization, Local development, Included features, Scaffolding, Blocks and assets, and Tailwind guides, published through a Docusaurus workflow.
- `CONTRIBUTING.md` and maintainer guidance under `docs/internal/`.

### Changed

- **Renamed from `features-plugin-skeleton` to `plugin-elementary`** and moved to the
  `rtCamp/plugin-elementary` repository, aligning it with the sibling `theme-elementary`.
  The scaffold placeholder tokens (`Project_Name`, `project-name`,
  `rtcamp/project-name-features`) are unchanged; only the repository identity, URLs and
  documentation moved.
- Updated the shared engine dependency from `rtcamp/wp-framework` to `rtcamp/wp-primitives ^2.0`.
- Adopted the rtCamp shared coding standards from the common packages (PHPCS, PHPStan, ESLint, Stylelint).
- Rewrote `README.md` and `DEVELOPMENT.md` as entry points that link framework API detail to `rtcamp/wp-primitives`.

### Removed

- The pilot-era quick start and internal-testing guides (superseded by the new guides and maintainer docs).
