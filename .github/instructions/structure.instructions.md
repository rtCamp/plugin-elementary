---
applyTo: "inc/**"
description: "Plugin structure. Merges with framework-php.instructions.md and copilot-instructions.md."
---

# Plugin structure

Namespace `Project_Name\Features\` → `inc/`; tests `Project_Name\Features\Tests\` → `tests/php/`. Text domain `project-name-features`. Constants `PROJECT_NAME_FEATURES_{FILE,VERSION,PATH,URL}`. Entry: `project-name-features.php` → `Autoloader::autoload()` → `Main::get_instance()`. (These `project-name` / `Project_Name` placeholders are renamed per project.)

Feature domains are grouped by an `AbstractModule` in `inc/Modules/` (`PostTypes`, `Taxonomies`, `Blocks`, `REST`, `Settings`, `Shortcodes`, `Roles`, `Cron`, `CLI`). Concrete classes live in the matching subdir (e.g. `inc/Modules/PostTypes/`). `inc/Core/` holds always-loaded infra.

**Scaffolding:** classes named `Example*` (and any module not needed) are demos shipped to show the pattern. A real project deletes unused ones and adds its own per requirements; do not assume a specific `Example*` / module file exists, and do not flag one as "missing". The framework abstracts always exist in `vendor/`; which you extend is requirement-driven.

To add a feature: create the concrete class extending the right abstract, then register its `::class` in the owning module's `get_classes()` (a whole new domain → add the module to `Main::CLASSES`).

## Flag

- 🚩 New concrete class not registered in its module's `get_classes()` → it never loads.
- 🚩 New domain not added to `Main::CLASSES`.
- 🚩 WP-CLI command not implementing `CLICommand`.
- 🚩 Plugin entry / directly-accessible PHP file without `defined( 'ABSPATH' ) || exit;`.
