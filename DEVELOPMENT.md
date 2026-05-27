# Development Guide

## Architecture overview

The plugin is split into two layers:

- **`vendor/rtcamp/wp-framework/`** — The upstream framework, installed as a Composer dependency. Provides reusable scaffolding (`Singleton`, `Loader`, `Container`, `AssetLoaderTrait`, `TemplateLoaderTrait`, `Encryptor`) and abstract base classes (`AbstractPostType`, `AbstractTaxonomy`, `AbstractSettingsPage`, `AbstractRESTController`, `AbstractShortcode`, `AbstractUserRole`, `AbstractBlock`, `AbstractAdminPage`, `AbstractModule`). **Do not modify.** Changes belong in the framework repository.
- **`inc/`** — All project-specific code. Extends framework abstracts, registers feature modules, and bootstraps the plugin.

The `vendor/` boundary enforces the rule by convention: editing files there gets blown away on every `composer install`. To override framework behavior, extend the abstract in `inc/` and override the method there.

## PSR-4 namespace convention

Single PSR-4 root, declared in `composer.json`:

```json
"autoload": {
    "psr-4": {
        "Project_Name\\Features\\": "inc/"
    }
}
```

Directory segments map 1:1 to namespace segments. Files are PascalCase.

| Namespace                                                  | File                                            |
|------------------------------------------------------------|-------------------------------------------------|
| `Project_Name\Features\Main`                               | `inc/Main.php`                                  |
| `Project_Name\Features\Autoloader`                         | `inc/Autoloader.php`                            |
| `Project_Name\Features\Helpers\Util`                       | `inc/Helpers/Util.php`                          |
| `Project_Name\Features\Core\Assets`                        | `inc/Core/Assets.php`                           |
| `Project_Name\Features\Modules\PostTypes`                  | `inc/Modules/PostTypes.php`                     |
| `Project_Name\Features\Modules\PostTypes\ExamplePostType`  | `inc/Modules/PostTypes/ExamplePostType.php`     |

> `Project_Name\Features\` is a placeholder. Replace it with the real vendor namespace when spinning up a project from this skeleton.

`inc/Helpers/` is the home for stateless utility classes — `final`, `private __construct()`, static methods only. Today it holds one class, `Util`, exposing `Util::get_data()`, `Util::is_production()`, `Util::is_mobile()`. Add siblings (e.g. `Str`, `Cache`, `Url`) as cross-cutting helpers accumulate, rather than letting `Util` grow into a grab-bag.

## Directory layout

```
inc/
├── Autoloader.php              # Wraps vendor/autoload.php with graceful failure
├── Main.php                    # Plugin bootstrap — loads modules
├── Helpers/                    # Stateless static utility classes (final, private __construct)
│   └── Util.php                # General-purpose helpers (get_data, is_production, is_mobile)
├── Core/                       # Plugin-wide infrastructure
│   ├── Assets.php              # Static block + asset registration (uses AssetLoaderTrait)
│   └── Templates.php           # Theme-overridable template loader (uses TemplateLoaderTrait)
└── Modules/                    # Feature areas — each module groups its Registrable classes
    ├── PostTypes.php
    ├── PostTypes/
    │   ├── ExamplePostType.php
    │   └── ExamplePostTypeTwo.php
    ├── Taxonomies.php
    ├── Taxonomies/
    │   ├── ExampleTaxonomy.php
    │   └── ExampleTaxonomyTwo.php
    ├── Blocks.php
    ├── Blocks/
    │   └── ExampleDynamicBlock.php
    ├── REST.php
    ├── REST/
    │   └── ExampleRESTController.php
    ├── Settings.php
    ├── Settings/
    │   └── ExampleSettingsPage.php
    ├── Shortcodes.php
    ├── Shortcodes/
    │   └── ExampleShortcode.php
    ├── Roles.php
    ├── Roles/
    │   └── ExampleUserRole.php
    ├── Cron.php
    ├── Cron/
    │   └── ExampleCronJob.php
    ├── CLI.php                 # ConditionallyRegistrable — only runs under WP-CLI
    └── CLI/
        └── Healthcheck.php
```

## Module pattern

Each feature area has a module class that groups its `Registrable` classes:

```php
// inc/Modules/PostTypes.php
final class PostTypes extends AbstractModule {
    protected function get_classes(): array {
        return [
            ExamplePostType::class,
            ExamplePostTypeTwo::class,
        ];
    }
}
```

`Main` only loads modules — it never sees individual feature classes:

```php
// inc/Main.php
const CLASSES = [
    Core\Assets::class,
    Modules\CLI::class,
    Modules\PostTypes::class,
    Modules\Taxonomies::class,
    // ...
];
```

To add another post type: drop a class in `inc/Modules/PostTypes/` and add it to `PostTypes::get_classes()`. `Main.php` is untouched.

## Picking a base

| Feature                                  | Extends / implements                |
|------------------------------------------|-------------------------------------|
| Custom post type                         | `AbstractPostType`                  |
| Taxonomy                                 | `AbstractTaxonomy`                  |
| Settings page                            | `AbstractSettingsPage`              |
| Admin (non-settings) page                | `AbstractAdminPage`                 |
| Dynamic block (server-side render)       | `AbstractBlock`                     |
| REST controller                          | `AbstractRESTController`            |
| Shortcode                                | `AbstractShortcode`                 |
| Custom user role                         | `AbstractUserRole`                  |
| WP-CLI command                           | `CLICommand` interface              |
| Anything else that just wires hooks      | `Registrable` interface             |
| Same, but registration is conditional    | `ConditionallyRegistrable` interface|

## Adding a new class

1. Pick the right abstract or interface from the table above.
2. Drop the class in the matching `inc/Modules/<Area>/` directory.
3. Add its class-string to the corresponding module's `get_classes()` method.
4. Run `composer dump-autoload`.

If it doesn't fit any existing module, create a new one: `inc/Modules/<NewArea>.php` extending `AbstractModule`, then add it to `Main::CLASSES`.

## Conditional registration

A class can opt out of registration at runtime by implementing `ConditionallyRegistrable` instead of `Registrable`:

```php
final class CLI implements ConditionallyRegistrable {
    public function can_register(): bool {
        return defined( 'WP_CLI' ) && WP_CLI;
    }

    public function register_hooks(): void {
        // Wire commands here.
    }
}
```

The `Loader` calls `can_register()` first and skips `register_hooks()` when it returns false.

## Running Composer

```bash
# First-time setup
composer install

# After adding, renaming, or moving a class
composer dump-autoload
```

If `vendor/autoload.php` is missing at runtime, the plugin shows an admin notice instead of fataling — see `inc/Autoloader.php` and `AutoloaderTrait` in the framework.
