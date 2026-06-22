# AGENTS.md — Features Plugin

Source of truth for this project's conventions, shared across all AI coding tools (Claude Code, GitHub Copilot, Codex, Cursor). [`CLAUDE.md`](CLAUDE.md) and [`.github/copilot-instructions.md`](.github/copilot-instructions.md) are thin pointers to this file. Path-scoped detail lives in [`.github/instructions/`](.github/instructions/).

A custom WordPress plugin built on `rtcamp/wp-framework` (`rtCamp\WPFramework`, in the gitignored `vendor/`, not visible at review). This is the **skeleton/template**: names are placeholders (`Project Name` / `project-name` / `Project_Name`) that `npm run init` rewrites per project. Do not assume a specific project name, and never flag a placeholder as an error.

## Path-scoped rules (full detail)

- `.github/instructions/framework-php.instructions.md` — framework architecture, security, testing, and the do/don't review flags. Shipped from `rtcamp/wp-framework`, generated locally by `npm run sync-ai` (absent until then).
- `.github/instructions/structure.instructions.md` — plugin layout and wiring.

The review rules ARE the coding rules: the files Copilot reviews against are the ones you write to.

## Stack

- **PHP 8.2+**, Composer, PSR-4: `Project_Name\Features\` → `inc/`; tests `Project_Name\Features\Tests\` → `tests/php/` (namespace === directory, filename === class).
- **PHPUnit + `wp-phpunit`** in `tests/php/` (mirrors `inc/`). **PHPCS** (WordPress-Core/Extra/Docs + VIPCS) and **PHPStan**: zero errors before merge.
- **Node 22**, build via `@wordpress/scripts` + a custom `webpack.config.js`. Source in `src/{blocks,css,js}`, output in `assets/build/`.

## Structure

Entry `project-name-features.php` → `Autoloader::autoload()` → `Main::get_instance()`. `inc/Main.php` lists every loaded class in `Main::CLASSES` and boots them through the framework `Loader` trait.

- `inc/Core/` — always-loaded infrastructure (`Assets`, `Templates`, `Components`, `Encryption`, `PluginSetup`).
- `inc/Modules/<Domain>.php` — an `AbstractModule` per feature domain (`PostTypes`, `Taxonomies`, `Blocks`, `REST`, `Settings`, `Shortcodes`, `Roles`, `Cron`, `CLI`); its `get_classes()` returns the `Registrable` classes it owns.
- `inc/Modules/<Domain>/` — the concrete classes for that domain (each extends the matching framework abstract).
- `inc/Helpers/` — stateless static utilities.

**Examples are deletable.** `Example*` classes (and any unneeded module) are demos that show the pattern; `npm run init` can strip them per set. Do not assume a specific `Example*` file exists, and never flag one as "missing".

To add a feature: write the test, create the concrete class extending the right abstract, register its `::class` in the owning module's `get_classes()` (a whole new domain → add the module to `Main::CLASSES`). Prefer the **`scaffold`** skill / `npx wp-tooling add` over hand-writing.

## Key principles

- **TDD:** write the failing PHPUnit test in `tests/php/` first, then the implementation. Never ship code without a test.
- **Don't default to Singleton.** Hook WordPress via the framework `Loader` + `Registrable`; use `Shareable` only when an instance must be retrieved later via `get_shared()`. `Singleton` is for `Main` only.
- **Extend the framework abstracts** (`AbstractPostType`, `AbstractRESTController`, `AbstractSettingsPage`, …) instead of hand-rolling `register_post_type` / `add_menu_page` / `register_rest_route`.
- **WordPress security:** escape on output, sanitize on input, verify nonce + `current_user_can()` before mutations, `$wpdb->prepare()`, a real REST `permission_callback`.
- `declare( strict_types = 1 );`, full types, `@package` / `@since`, `static::` not `self::`, PSR-4 (namespace === dir).
- Prefer official WordPress / `@wordpress/*` APIs over custom code. Never modify WordPress core or anything under `vendor/`; extend via actions and filters.
- British English in prose; American English in code (`color`, `optimize`).

## Commands

- PHP: `composer test` · `composer lint` · `composer format` · `composer phpstan`.
- JS/build: `npm run build:dev` · `npm run build:prod` · `npm run lint` · `npm run test`.
- Setup / features: `npm run init` (setup + manage wizard) · `npx wp-tooling add <category>/<slug>` (add a feature).

## AI tooling

- **Skills** in [`.claude/skills/`](.claude/skills/): `init` (project setup/manage), `scaffold` (add one feature, TDD-first), `setup` (bootstrap from a brief). Index: [`.claude/skills/README.md`](.claude/skills/README.md).
- `scaffold` and `setup` are synced from `@rtcamp/wp-tooling`; re-sync with `npm run sync-ai`.

## Guardrails (all AI tools)

- Never run a package manager (`npm install`, `composer require`), `npm run build`, or `gh secret set` without explicit consent — print the command instead. (`npm run init` is allowed with consent, on a clean tree, since it is the project's own setup script.)
- Never read, log, or transmit secret values.
- Never apply cross-file wiring without showing the diff and getting consent.
- Never commit, push, open PRs, or comment on issues without explicit approval.
