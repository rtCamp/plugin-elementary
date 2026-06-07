# AGENTS.md — Features Plugin

Tool-agnostic brief for AI coding agents (Claude Code, Copilot coding agent, Codex). A custom WordPress plugin built on `rtcamp/wp-framework` (in `vendor/`). Names are placeholders (`Project_Name` / `project-name`) until `npm run init` sets them.

## Authoritative rules

The review rules ARE the coding rules: the same files Copilot reviews against. Follow them when writing code; they hold the full detail:

- `.github/instructions/framework-php.instructions.md`: framework architecture, security, testing, and the do/don't flags. Shipped from `rtcamp/wp-framework`, generated locally by `npm run sync-ai` (absent until then).
- `.github/instructions/structure.instructions.md`: plugin layout and wiring.
- `.github/copilot-instructions.md`: overview + conventions.

## Key principles (full detail in the files above)

- **TDD**: write the failing PHPUnit test first (`tests/php/` mirrors `inc/`), then code.
- **Don't default to Singleton.** Hook WordPress via the framework `Loader` + `Registrable`; use `Shareable` only when an instance must be retrieved later via `get_shared()`. `Singleton` is for `Main` only.
- **Extend the framework abstracts** (`AbstractPostType`, `AbstractRESTController`, `AbstractAdminPage`, …) instead of hand-rolling `register_post_type`/`add_menu_page`/`register_rest_route`.
- **WordPress security**: escape on output, sanitize on input, verify nonce + `current_user_can()` before mutations, `$wpdb->prepare()`, a real REST `permission_callback`.
- `declare( strict_types = 1 );`, full types, `@package`/`@since`, `static::` not `self::`, PSR-4 (namespace === dir).

## Structure

`inc/` (PSR-4 `Project_Name\Features\`): `Main.php` (boot), `Core/` (always-loaded infra), `Modules/` (each `extends AbstractModule`, concrete classes in matching subdir). `tests/php/` mirrors `inc/`. `Example*` classes are scaffolding; delete unused, add your own per requirements.

To add a feature: write the test, extend the right abstract, register its `::class` in the module's `get_classes()` (new domain → add the module to `Main::CLASSES`).
