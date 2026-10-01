<h1 align="center">Plugin Elementary</h1>

<p align="center">
  <a href="https://www.repostatus.org/#active"><img src="https://www.repostatus.org/badges/latest/active.svg" alt="Project Status: Active"></a>
  <a href="LICENSE.md"><img src="https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg" alt="License: GPL-2.0-or-later"></a>
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777bb4.svg" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Node-22-5fa04e.svg" alt="Node 22">
</p>

<p align="center">
  A starter plugin for the backend of a WordPress site: post types, taxonomies,
  blocks, REST endpoints, settings and admin pages, shortcodes, user roles,
  WP-Cron jobs and WP-CLI commands.
</p>

---

Plugin Elementary gives a new client plugin its structure, working examples, asset pipeline, checks and CI, so a project starts from a tested baseline instead of an empty folder.

It builds on two shared rtCamp libraries and uses a third for CI:

- [`rtcamp/wp-primitives`](https://github.com/rtCamp/wp-primitives) (Composer, runtime): the registration system, `Abstract*` base classes, loaders and utilities that every class in `inc/` extends.
- [`@rtcamp/wp-tooling`](https://github.com/rtCamp/wp-tooling) (npm, development): the `npm run init` setup wizard, the `npx wp-tooling add` feature scaffolder, the shared lint configs and release scripts.
- [`rtCamp/wp-shared-workflows`](https://github.com/rtCamp/wp-shared-workflows): the reusable lint, test and build CI that `.github/workflows/test-measure.yml` calls.

These guides cover what Plugin Elementary provides and how to build on it. The `wp-primitives` API and lifecycle are documented in [its own docs](https://github.com/rtCamp/wp-primitives/blob/v2.0.0/docs/index.md), and the guides link there where needed.

> This is a **template**. Its placeholder identity, **Elementary Plugin** (`elementary-plugin`, `ELEMENTARY_PLUGIN_*`, `rtCamp\Plugin\Elementary`), is renamed to your project by `npm run init`.

## Get started

Follow [Getting Started](docs/getting-started.md): prerequisites, cloning, installing, naming the plugin, and seeing it running in a local WordPress site. It takes about fifteen minutes.

## What is included

- **Infrastructure** that stays in every project: bootstrap and loader, asset registration, theme-overridable templates and components, a shared logger, an encryption service, activation and deactivation handling, and PHPUnit, PHPCS, PHPStan, ESLint and Stylelint setups.
- **Twelve example sets**, one per feature domain (post types, taxonomies, blocks, REST, WP-CLI, cron, settings pages, admin pages, user roles, shortcodes, cache, transients). Each is a working, tested reference you keep or remove at setup.
- **Optional development features**: HMR live reload (on by default), Tailwind CSS, and DevTools runtime telemetry over MCP.

See [Included features](docs/features.md) for where each one lives and how to see it working.

## Choose your next task

| I want to… | Read |
| --- | --- |
| Name the plugin and choose what ships | [Initialization](docs/initialization.md) |
| Run WordPress locally, check a change, build for delivery | [Local development](docs/local-development.md) |
| See what Plugin Elementary already provides | [Included features](docs/features.md) |
| Generate a feature with the CLI or AI | [Scaffolding](docs/scaffolding.md) |
| Add blocks, scripts or styles | [Blocks and assets](docs/blocks-and-assets.md) |
| Extend the plugin by hand | [Development guide](DEVELOPMENT.md) |
| Contribute to Plugin Elementary | [Contributing](CONTRIBUTING.md) |

The full documentation index is [docs/index.md](docs/index.md).

## AI tooling

Setup and feature scaffolding are also available through AI assistants, kept in step across tools:

- **Claude Code:** skills in [`.claude/skills/`](.claude/skills/) — `/init` (set up or manage the plugin) and `/scaffold` (add one feature, test first). `/setup` is a generic tooling bootstrapper and does not replace `/init`.
- **GitHub Copilot:** matching `/init` and `/scaffold` prompts in [`.github/prompts/`](.github/prompts/).
- **Any assistant:** [AGENTS.md](AGENTS.md) is the tool-agnostic source of conventions; `CLAUDE.md` and `.github/copilot-instructions.md` point to it.

A committed knowledge graph in [`graphify-out/`](graphify-out/) lets assistants query the code's structure instead of reading it all; see [docs/internal/knowledge-graph.md](docs/internal/knowledge-graph.md).

## Folder structure

```
elementary-plugin.php   # entry: constants → Autoloader::autoload() → Main::get_instance()
inc/                        # PSR-4 root: rtCamp\Plugin\Elementary\ → inc/
├── Autoloader.php          # loads vendor/autoload.php; admin notice instead of a fatal if missing
├── Main.php                # Main::CLASSES — the list of everything the plugin loads
├── Core/                   # always loaded: Assets, PluginSetup, Components, Templates, Encryption, Logger
├── Helpers/                # stateless static utilities (Util)
└── Modules/                # one module per feature domain, concrete classes in the matching subfolder
    ├── PostTypes/  Taxonomies/  Blocks/  Shortcodes/
    ├── REST/  CLI/  Cron/
    ├── Settings/  Admin/  Roles/
    └── Cache/  Transients/
src/{blocks,css,js}/        # editable sources → assets/build/ (generated)
templates/                  # theme-overridable PHP templates
tests/{php,js}/             # PHPUnit (mirrors inc/) and Jest
bin/                        # init wrapper, scaffold config, block scaffolder, helper scripts
docs/                       # these guides; docs/internal/ is for maintainers of this repository
.github/                    # CI caller, issue/PR templates, Copilot instructions and prompts
vendor/                     # Composer-managed, including rtcamp/wp-primitives (never edit)
```

Some of these folders hold examples that initialization can remove; see [Included features](docs/features.md#supplied-examples).

## License

[GPL-2.0-or-later](LICENSE.md)

<p align="center">
  <a href="https://rtcamp.com"><img src="https://n8e0ka87m9.gdcdn.us/kfnbt046p8/GitHub_Banner.webp" alt="rtCamp — high-performance enterprise WordPress" width="100%"></a>
</p>
