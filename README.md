<h1 align="center">Features Plugin Skeleton</h1>

<p align="center">
  <a href="https://www.repostatus.org/#active"><img src="https://www.repostatus.org/badges/latest/active.svg" alt="Project Status: Active"></a>
  <a href="LICENSE.md"><img src="https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg" alt="License: GPL-2.0-or-later"></a>
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777bb4.svg" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Node-22-5fa04e.svg" alt="Node 22">
</p>

<p align="center">
  The starting template for a WordPress <b>feature plugin</b> — all backend
  functionality (post types, taxonomies, blocks, REST endpoints, settings pages,
  shortcodes, user roles, WP-Cron, WP-CLI) lives here. The reusable framework
  ships as the <a href="https://github.com/rtCamp/wp-framework"><code>rtcamp/wp-framework</code></a>
  Composer package in <code>vendor/</code>.
</p>

---

> This is a **template**. `Project Name` / `project-name` / `Project_Name` are
> placeholders — run `npm run init` to turn it into a named project.

## What it offers

- **Capability-based setup.** One command names the plugin and ships only the
  capabilities you need; everything else is removed cleanly, leaving a
  gate-clean starting point.
- **AI that writes consistent, tested code.** The `/init` and `/scaffold` skills
  build on `rtcamp/wp-framework` and the scaffold engine, so they produce the
  same structure no matter who prompts them, write the test first, and run the
  `phpcs`/`phpstan` gates by default.
- **Shared framework and standards.** Base classes and a registration layer from
  `rtcamp/wp-framework`, plus the rtCamp PHPCS ruleset, PHPStan, ESLint and
  Stylelint, so every plugin reads the same.
- **Reusable CI.** Lint, test and build run through
  [`rtCamp/wp-shared-workflows`](https://github.com/rtCamp/wp-shared-workflows),
  called from a single thin workflow file; every job is gated on detected
  changes, so a docs-only PR runs almost nothing.

## Quick start

Requires Node 22 (`nvm use`), PHP 8.2+, and access to rtCamp's private packages
(see [Dependencies](#dependencies)).

```bash
nvm use
composer install
npm install
npm run init        # rename the starter tokens, keep/remove examples, toggle features
npm start           # build assets (watch); npm run build:prod for production
```

Prefer to drive it with AI? In Claude Code, run `/init` and describe the project.

## Working on the plugin

Conventions are written once and shared across every tool:

- **[AGENTS.md](AGENTS.md)** — the source of truth: stack, structure, TDD,
  framework patterns, security, guardrails. `CLAUDE.md` and
  `.github/copilot-instructions.md` are thin pointers to it; path-scoped detail
  lives in `.github/instructions/`.
- **[DEVELOPMENT.md](DEVELOPMENT.md)** — architecture overview, the module
  pattern, and how to add classes by hand.
- **`.claude/skills/`** — built-in AI skills: `/init`, `/scaffold`, `/setup`.

## Commands

| Command | What it does |
|---|---|
| `npm run init` | Setup + manage wizard: rename, keep/remove examples, toggle features. |
| `npm start` / `npm run build:dev` / `npm run build:prod` | Build `src/{blocks,css,js}` into `assets/build/` (watch / dev / prod). |
| `npm run lint` · `npm run test` | Lint (PHP/JS/CSS) · run JS + PHP test suites. |
| `composer test` · `composer lint` · `composer phpstan` | PHPUnit · PHPCS · PHPStan, on the host. |
| `npx wp-tooling add <category>/<slug>` | Scaffold a new feature (see below). |

See the build/test detail (wp-env, coverage, matrix overrides, extending webpack)
in [DEVELOPMENT.md](DEVELOPMENT.md).

With the optional dev-tools feature enabled (`npm run init -- --enable=dev-tools`),
`npm run dev:connect` gives a coding agent live runtime telemetry over MCP — see the
[seven-beat demo](docs/dev-tools-demo.md) and the [end-to-end check](docs/dev-tools-e2e.md).

## Adding a feature

Use the scaffold engine instead of hand-writing — it writes the class, wires it
into the right module, and emits a test stub:

```bash
npx wp-tooling add wp/cli --name=health-check    # add a WP-CLI command
npx wp-tooling list --json                        # see the full catalogue
```

Or run `/scaffold` in Claude Code and describe the feature. By hand: create the
concrete class extending the right framework abstract, register its `::class` in
the owning module's `get_classes()`, and add a whole new domain to `Main::CLASSES`.

## Project structure

```
project-name-features.php   # entry: Autoloader::autoload() → Main::get_instance()
inc/                        # PSR-4 root — Project_Name\Features\ → inc/
├── Autoloader.php
├── Main.php                # Main::CLASSES boots Core + Modules via the framework Loader
├── Core/                   # always-loaded infra: Assets, Templates, Components, Encryption, PluginSetup
├── Helpers/                # stateless static utilities
└── Modules/                # one AbstractModule per domain; concrete classes in the matching subdir
    ├── PostTypes.php  + PostTypes/      Taxonomies.php  + Taxonomies/
    ├── Blocks.php     + Blocks/         REST.php        + REST/
    ├── Settings.php   + Settings/       Shortcodes.php  + Shortcodes/
    ├── Roles.php      + Roles/          Cron.php        + Cron/
    └── CLI.php        + CLI/
src/{blocks,css,js}/        # build sources → assets/build/
bin/                        # init.js (npm run init), scaffold.config.js, sync-ai.js, build helpers
tests/php/                  # PHPUnit suite — Project_Name\Features\Tests\ → tests/php/
templates/                  # theme-overridable templates
.claude/skills/             # AI skills: init, scaffold, setup
.github/                    # CI, issue/PR templates, copilot-instructions, instructions/
```

## What ships as examples

Each feature domain ships a working `Example*` class so you can see the pattern.
`npm run init` offers to keep or remove each set; what you remove is deleted and
its registration stripped.

| Set | Source | Set | Source |
|---|---|---|---|
| Post types | `Modules\PostTypes\Example*` | Settings | `Modules\Settings\ExampleSettingsPage` |
| Taxonomies | `Modules\Taxonomies\Example*` | Shortcodes | `Modules\Shortcodes\ExampleShortcode` |
| Blocks | `Modules\Blocks\ExampleDynamicBlock` + `src/blocks/example-*` | User roles | `Modules\Roles\ExampleUserRole` |
| REST | `Modules\REST\ExampleRESTController` | Cron | `Modules\Cron\ExampleCronJob` |
| WP-CLI | `Modules\CLI\Healthcheck` | | |

## Dependencies

`rtcamp/wp-framework` is a Composer package resolved from its public GitHub
repository (see the `repositories` entry in `composer.json`) — no token needed.

The npm-side rtCamp packages (`@rtcamp/wp-tooling` and the shared ESLint and
Stylelint configs) are still unpublished during the pilot and are mapped to
GitHub Packages via `.npmrc`. Until they ship, installing needs a token with
`read:packages`:

```bash
export GITHUB_TOKEN=<token-with-read:packages>   # CI uses a secret
npm install
```

## Contributing

1. Open or find an [issue](https://github.com/rtCamp/features-plugin-skeleton/issues)
   describing the change.
2. Branch from the active release branch, commit, and push.
3. Open a pull request using the template. CI (lint + tests) must pass.

## License

[GPL-2.0-or-later](LICENSE.md)

<p align="center">
  <a href="https://rtcamp.com"><img src="https://n8e0ka87m9.gdcdn.us/kfnbt046p8/GitHub_Banner.webp" alt="rtCamp — high-performance enterprise WordPress" width="100%"></a>
</p>
