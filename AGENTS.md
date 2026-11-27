# AGENTS.md: Plugin Elementary

Source of truth for this project's conventions, shared across all AI coding tools (Claude Code, GitHub Copilot, Codex, Cursor). [`CLAUDE.md`](CLAUDE.md) and [`.github/copilot-instructions.md`](.github/copilot-instructions.md) are thin pointers to this file. Path-scoped detail lives in [`.github/instructions/`](.github/instructions/).

A custom WordPress plugin built on `rtcamp/wp-primitives` (`rtCamp\WPPrimitives`, in the gitignored `vendor/`, not visible at review). This is the **skeleton/template**: names are placeholders (`Project Name` / `project-name` / `Project_Name`) that `npm run init` rewrites per project. Do not assume a specific project name, and never flag a placeholder as an error.

## Path-scoped rules (full detail)

- `.github/instructions/primitives-php.instructions.md`: framework architecture, security, testing, and the do/don't review flags. Shipped from `rtcamp/wp-primitives`, generated locally by `npm run sync-ai` (absent until then).
- `.github/instructions/structure.instructions.md`: plugin layout and wiring.

The review rules ARE the coding rules: the files Copilot reviews against are the ones you write to.

## Stack

- **PHP 8.2+**, Composer, PSR-4: `Project_Name\Features\` → `inc/`; tests `Project_Name\Features\Tests\` → `tests/php/` (namespace === directory, filename === class).
- **PHPUnit + `wp-phpunit`** in `tests/php/` (mirrors `inc/`), run under `wp-env`. **PHPCS** (the shared `rtCampWP` ruleset from `rtcamp/wp-phpcs`: WPCS + VIP + Docs + Slevomat) and **PHPStan** (level 5, the `rtcamp/wp-phpstan` baseline): zero errors before merge.
- **Node 24**, build via `@wordpress/scripts` + a custom `webpack.config.js`. Source in `src/{blocks,css,js}`, output in `assets/build/`.

## Structure

Entry `project-name-features.php` → `Autoloader::autoload()` → `Main::get_instance()`. `inc/Main.php` lists every loaded class in `Main::CLASSES` and boots them through the framework `Loader` trait.

- `inc/Core/`: always-loaded infrastructure (`Assets`, `Templates`, `Components`, `Encryption`, `Logger`, `PluginSetup`).
- `inc/Modules/<Domain>.php`: an `AbstractModule` per feature domain (`PostTypes`, `Taxonomies`, `Blocks`, `REST`, `Settings`, `Shortcodes`, `Roles`, `Cron`, `Admin`, `Cache`, `Transients`); its `get_classes()` returns the `Registrable` classes it owns. Exception: `CLI` is a `ConditionallyRegistrable` class (WP-CLI only) whose `CLICommand` classes are listed in `CLI::get_commands()`.
- `inc/Modules/<Domain>/`: the concrete classes for that domain (each extends the matching framework abstract).
- `inc/Helpers/`: stateless static utilities.

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

- PHP: `composer lint` (PHPCS) · `composer format` (phpcbf) · `composer phpstan`; the PHPUnit suite runs under `wp-env` via `npm run test:php`.
- JS/build: `npm run build:dev` · `npm run build:prod` · `npm run lint` · `npm run test`.
- Setup / features: `npm run init` (setup + manage wizard) · `npx wp-tooling add <category>/<slug>` (add a feature).

Human documentation: [README.md](README.md) → [docs/index.md](docs/index.md) (developer guides), [DEVELOPMENT.md](DEVELOPMENT.md) (extending by hand), [CONTRIBUTING.md](CONTRIBUTING.md) and [docs/internal/](docs/internal/README.md) (skeleton maintainers). Framework API detail is linked, not copied, from the `rtcamp/wp-primitives` docs at the locked tag.

## AI tooling

The two core tasks, **init** (set up / manage the project) and **scaffold** (add one feature, TDD-first), exist for both assistants and must stay in step:

- **Claude Code:** skills in [`.claude/skills/`](.claude/skills/) (`init`, `scaffold`). Index: [`.claude/skills/README.md`](.claude/skills/README.md).
- **GitHub Copilot:** prompt files in [`.github/prompts/`](.github/prompts/) (`init`, `scaffold`), invoked as `/init` and `/scaffold` in Copilot Chat.
- Both follow this file and the path-scoped rules. `scaffold` tracks `@rtcamp/wp-tooling`; framework instructions re-sync with `npm run sync-ai`.

## Code intelligence (Saguri)

[Saguri](docs/internal/saguri.md) (`sgr`) indexes this plugin, the locked `vendor/rtcamp/wp-primitives` and the installed `@rtcamp/wp-tooling` engine into the gitignored `.saguri/`. Only the `init`/`scaffold` agentic tools use it; Copilot review does not. Report each Saguri step in <=30 words.

Before opening files to find where something is hooked, registered or called, query the index. It locates code; read the file for anything you copy.

- `sgr hook <name>`: a hook's handlers and the sites that fire it.
- `sgr entity --kind <kind>`: registered `rest_route`, `block`, `setting`, `shortcode`, `cli_command`.
- `sgr refs <symbol> [--callees]` and `sgr trace <symbol> [--up]`: callers, callees, call tree.
- `sgr brief "<task>"`: when you do not know the names yet.

Run `sgr index .` before the first query and after any code change. `sgr path` can cross a shared hook (`init`, `rest_api_init`) into an unrelated handler, so confirm such paths with `sgr hook`. If `sgr` is missing, say so in one line, point to the install doc, and carry on.

Never commit `.saguri/` or run `sgr install`, `sgr uninstall` or `sgr razu`.

## Guardrails (all AI tools) - BASE, non-negotiable

- **Never run history- or remote-affecting `git`/`gh`.** No `commit`, `push`, `branch -D`, `reset --hard`, `rebase`, `tag`, `git add` for a commit, PR create/merge, issue/PR comment, `gh secret set`, or any write to a remote or to git history. Surface every one of those as a developer action: print the exact command for the developer to run. Read/setup git is allowed: `git clone`, `git checkout`, `git status`, `git diff` (e.g. the pilot bootstrap's sibling clones) may run with consent.
- **Never do a destructive operation outside this plugin directory.** Do not delete or overwrite existing files in sibling repos (`../wp-tooling`, `../wp-primitives`, ...) or anywhere else on disk. Cloning a NEW sibling that does not already exist is additive and allowed; modifying or removing existing out-of-repo content is not.
- Never run a package manager (`npm install`, `composer require/update`) or `npm run build` without explicit consent; print the command instead. The one consented exception is `npm run init` (the project's own setup script, on a clean tree). In-repo install steps and the pilot bootstrap (sibling clones + `file:`/`path` ref edits + installs) may run with consent.
- Never read, log, or transmit secret values.
- Never apply cross-file wiring without showing the diff and getting consent.
