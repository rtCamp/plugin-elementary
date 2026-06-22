# Features Plugin Skeleton [![Project Status: Active](https://www.repostatus.org/badges/latest/active.svg)](https://www.repostatus.org/#active)

The starting template for an rtCamp client WordPress **feature plugin**: all backend functionality (post types, taxonomies, blocks, REST endpoints, settings pages, shortcodes, user roles, WP-Cron, WP-CLI) lives here. The reusable framework (autoloader, asset/template loaders, abstract base classes) ships as the `rtcamp/wp-framework` Composer package in `vendor/`.

> This is a **template**. `Project Name` / `project-name` / `Project_Name` are placeholders. Use this template, then run `npm run init` to turn it into a named project.

## Quick start

1. **Use this template** on GitHub (or clone) to create your project's repo.
2. Install dependencies (needs Node 22 — run `nvm use` — and access to the private rtCamp packages, see [Private packages](#private-packages)):
   ```bash
   nvm use
   composer install
   npm install
   ```
3. **Initialize the plugin** — the setup wizard renames the starter tokens to your project, lets you keep or remove the shipped example sets, and toggle optional features (Tailwind, HMR):
   ```bash
   npm run init
   ```
   > Prefer to drive it with AI? In Claude Code, run `/init` and describe the project.
4. **Build assets:** `npm start` (watch) or `npm run build:prod`.

## Working on the plugin (humans and AI)

Conventions are written once and shared across every AI tool:

- **[AGENTS.md](AGENTS.md)** — the source of truth: stack, structure, TDD, framework patterns, security, guardrails. [`CLAUDE.md`](CLAUDE.md) and [`.github/copilot-instructions.md`](.github/copilot-instructions.md) are thin pointers to it; path-scoped detail lives in [`.github/instructions/`](.github/instructions/).
- **[`.claude/skills/`](.claude/skills/)** — built-in AI skills: `/init` (set up / manage the project), `/scaffold` (add one feature), `/setup` (bootstrap from a brief).
- **[DEVELOPMENT.md](DEVELOPMENT.md)** — the architecture overview, the module pattern, and how to add classes by hand.

## Commands

| Command | What it does |
|---|---|
| `npm run init` | Setup + manage wizard: rename, keep/remove examples, toggle features. |
| `npm start` / `npm run build:dev` / `npm run build:prod` | Build `src/{blocks,css,js}` into `assets/build/` (watch / dev / prod). |
| `npm run lint` · `npm run test` | Lint (PHP/JS/CSS) · run JS + PHP test suites. |
| `composer test` · `composer lint` · `composer phpstan` | PHPUnit · PHPCS · PHPStan, on the host. |
| `npx wp-tooling add <category>/<slug>` | Scaffold a new feature (see below). |

See the build / test detail (wp-env, coverage, matrix overrides, extending webpack) in [DEVELOPMENT.md](DEVELOPMENT.md).

## Adding a feature

Use the scaffold engine instead of hand-writing — it writes the class, wires it into the right module, and emits a test stub:

```bash
npx wp-tooling add wp/cli --name=health-check    # add a WP-CLI command
npx wp-tooling list --json                        # see the full catalogue
```

Or run `/scaffold` in Claude Code and describe the feature. By hand: create the concrete class extending the right framework abstract, register its `::class` in the owning module's `get_classes()`, and add a whole new domain to `Main::CLASSES`.

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

Each feature domain ships a working `Example*` class so you can see the pattern. `npm run init` offers to keep or remove each set; what you remove is deleted and its registration stripped.

| Set | Source | Set | Source |
|---|---|---|---|
| Post types | `Modules\PostTypes\Example*` | Settings | `Modules\Settings\ExampleSettingsPage` |
| Taxonomies | `Modules\Taxonomies\Example*` | Shortcodes | `Modules\Shortcodes\ExampleShortcode` |
| Blocks | `Modules\Blocks\ExampleDynamicBlock` + `src/blocks/example-*` | User roles | `Modules\Roles\ExampleUserRole` |
| REST | `Modules\REST\ExampleRESTController` | Cron | `Modules\Cron\ExampleCronJob` |
| WP-CLI | `Modules\CLI\Healthcheck` | | |

## Private packages

During the pilot, `@rtcamp/wp-tooling` and `@rtcamp/tailwind-config` are served from **GitHub Packages** (private to the rtCamp org), mapped via `.npmrc`. To install them you need a GitHub token with `read:packages`:

```bash
export GITHUB_TOKEN=ghp_your_token   # org users; CI uses a secret
```

For local end-to-end testing before the packages are published, see [docs/internal-testing.md](docs/internal-testing.md).

## Contributing

1. Open or find an [issue](https://github.com/rtCamp/features-plugin-skeleton/issues) describing the change.
2. Branch from the active release branch (not `master`), commit, and push.
3. Open a pull request using the template. CI (lint + tests) must pass.

Found a bug? Browse [existing issues](https://github.com/rtCamp/features-plugin-skeleton/issues) first, then [log a new one](https://github.com/rtCamp/features-plugin-skeleton/issues/new) with clear reproduction steps.

## Does this interest you?

<a href="https://rtcamp.com/"><img src="https://rtcamp.com/wp-content/uploads/sites/2/2019/04/github-banner@2x.png" alt="Join us at rtCamp, we specialize in providing high performance enterprise WordPress solutions"></a>
