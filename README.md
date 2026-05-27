# Project_Name Features [![Project Status: Active – The project has reached a stable, usable state and is being actively developed.](https://www.repostatus.org/badges/latest/active.svg)](https://www.repostatus.org/#active)

Plugin for Project_Name. All backend functionality lives here — post types, taxonomies, blocks, REST endpoints, settings pages, shortcodes, custom user roles, WP-Cron jobs, and WP-CLI commands.

Reusable scaffolding (singleton, autoloader, asset loader, template loader, and a set of abstract base classes for the feature types above) ships separately as the `rtcamp/wp-framework` Composer package and is loaded from `vendor/`.

> **Working on this plugin?** See [DEVELOPMENT.md](DEVELOPMENT.md) for the architecture overview, the module pattern, and how to add new classes.

## Get Started
### Plugin Setup
- Clone the `features-plugin-skeleton` repository from [GitHub](https://github.com/rtCamp/features-plugin-skeleton/) with the desired directory name using the following command:
```bash
git clone git@github.com:rtCamp/features-plugin-skeleton.git <directory-name>
```
- Run `nvm use` to use the preferred Node.js version. It is highly recommended to use the Node version mentioned in the `.nvmrc` file to ensure building scripts run without failing.
- Run `npm install` to install all the dependencies.
    - After running the `npm install`, `npm run init` command will run the init script in interactive mode to initialize the plugin.
    - `npm:init` command is responsible for handling search-replace and Plugin cleanup.
    - If you don't want to run the init script, you can run `npm install --ignore-scripts` to install the dependencies without running the init script.
- Run `npm run init` to initialize the plugin anytime you want.

### Build Assets
- Use `npm start` to build the plugin assets in interactive mode.
- Use `npm run build:dev` or `npm run build:prod` to build the plugin assets in a non-interactive mode for Development and Production environments respectively.
- There are some additional commands available for building the assets separately:
    - `npm run start:blocks`: Builds the blocks assets only in interactive mode within `assets/src/blocks` directory.
    - `npm run start:js`: Builds the JavaScript assets only in interactive mode within `assets/src/js` directory.
    - `npm run build:blocks`: Builds the blocks assets only within `assets/src/blocks` directory.
    - `npm run build:js`: Builds the JavaScript assets only within `assets/src/js` directory.

### Run Linters and CS
- Use `npm run lint` to lint the plugin assets.
- Use `npm run lint:php`, `npm run lint:js` and `npm run lint:css` to lint the PHP, JavaScript and CSS files respectively.
- Use `npm run lint:php:fix`, `npm run lint:js:fix` and `npm run lint:php:fix` to lint the PHP, JavaScript or CSS files respectively and automatically fix the issues that are auto-fixable.
- There are linting commands available for `package.json` and `lint-staged` files:
    - `npm run lint:package-json`: Lints the package.json file.
    - `npm run lint:staged`: Lints the staged files.
- Used Linters and CS:
    - `stylelint`: Lints the CSS files.
    - `eslint`: Lints the JavaScript files.
    - `phpcs/phpcbf`: Lints the PHP files.

### Run Tests
- Use `npm run test` to run the plugin tests.
- WP PHPUnit tests are run inside the docker container using [wp-env](https://www.npmjs.com/package/@wordpress/env) package.
- wp env Usage:
    - `npm run wp-env start`: Starts the wp env docker container.
    - `npm run wp-env start -- --xdebug`: Starts the wp env docker container with Xdebug enabled.
    - `npm run wp-env start -- --xdebug=debug,coverage`: Starts the wp env docker container with Xdebug enabled in debug and coverage mode.
    - `npm run wp-env stop`: Stops the wp env docker container.
- Use `npm run test:js` and `npm run test:php` to run the JavaScript or PHP unit tests respectively.
- Use `npm run test:php:coverage` to run the PHP unit tests with coverage. Before using this command, make sure to run wp env with xdebug coverage mode using `npm run wp-env start -- --xdebug=coverage` command.
- Tests suites:
    - `js`: JavaScript unit tests using `jest`.
    - `php`: PHP unit tests using WP PHPUnit.

> **`composer test` vs `npm run test:php`** — both invoke the same `vendor/bin/phpunit -c phpunit.xml.dist`, but at different layers:
> - `npm run test:php` runs phpunit **inside** the wp-env container (which already has WordPress test infrastructure configured). This is the day-to-day developer command.
> - `composer test` runs phpunit **directly on the host**. It only works if WordPress test infrastructure is reachable from there — useful for CI runners or when you've shelled into the wp-env container manually.
>
> The two are not in conflict; the npm script is the host-side wrapper, the composer script is the low-level invocation.

### Working with PHPUnit Tests
This plugin is configured with WP PHPUnit tests out of the box. The tests are run inside the docker container using [wp-env](https://www.npmjs.com/package/@wordpress/env) package. There can be chances when you need to run test cases with the different matrix of PHP, WP, and WP PHPUnit versions. To do that, you can override the default `.wp-env.json` file with `.wp-env.override.json` which is not included in your version control.

For example, you can use the following configuration to run the tests with PHP 7.4, WP 5.6, and WP PHPUnit 5.7:
```json
{
    "core": "WordPress/WordPress#5.6",
    "phpVersion": "7.4",
    "mappings": {
        "../wordpress-develop": "WordPress/wordpress-develop#5.7"
    }
}
```
Since wp env supports xdebug coverage mode, you can generate the coverage report for the tests on your local using `npm run test:php:coverage`. To do that, you need to run wp env with xdebug coverage mode using `npm run wp-env start -- --xdebug=coverage` command. After running the tests, you can find the coverage report in `coverage` directory.

> While running for test coverage in CI we suggest using `pcov` driver instead of `xdebug` driver to generate reports in less time in comparison to `xdebug` driver.

### Pre-commit Hook
- Support for two types of pre-commit hooks has been added:
    - Husky-based: `husky` package is used to run the pre-commit hook.
    - Bash Script-based: A bash script is added in `.git/hooks/pre-commit` directory.
- Use `npm run install:husky` or `npm run remove:husky` to install or remove the Husky pre-commit hook.
- Use `npm run install:pre-commit-hook` or `npm run remove:pre-commit-hook` to install or remove the bash script-based pre-commit hook.
**Note:** If you are switching from husky to bash script-based pre-commit hook, you need to remove `hooksPath = .husky` from `.git/config` file.

### Extend Webpack Configuration
- Blocks are using [@wordpress/scripts](https://www.npmjs.com/package/@wordpress/scripts) Webpack configuration so no need to extend Webpack configuration for blocks.
- `assets/src/js` directory is using custom Webpack configuration extended from `@wordpress/scripts` Webpack configuration, so you need to specify entry point for the JavaScript files in `assets/src/js` directory.
- To add more JavaScript files to the Webpack configuration, you can use the following syntax:
```js
// If you have an example.js file in the `assets/src/js` directory, you can add it to the Webpack configuration like this:
const exampleJS = {
    ...sharedConfig,
    entry: {
        'example': path.resolve( process.cwd(), 'assets', 'src', 'js', 'example.js' ),
    },
};

// If you want to add a plugin for this specific JS file, you can use the following syntax:
const exampleJS = {
    ...sharedConfig,
    entry: {
        'example': path.resolve( process.cwd(), 'assets', 'src', 'js', 'example.js' ),
    },
    plugins: [
        ...sharedConfig.plugins,
        new Plugin(),
    ],
};

// Similarly you can modify the webpack configuration for a entry point.

// Now you need to export the webpack configuration like this:
module.exports = [
    // ... more webpack configurations ...
    exampleJS,
]
```

## Plugin Structure

High-level layout. See [DEVELOPMENT.md](DEVELOPMENT.md) for the full `inc/` tree and the architecture rules.

<details>
<summary>Expand Plugin Structure</summary>

```
features-plugin-skeleton/
├── .github/                              # CI workflows, issue / PR templates
├── assets/
│   └── src/
│       ├── blocks/
│       │   ├── example-block/            # Static block
│       │   ├── example-block-interactive/# Static block + Interactivity API
│       │   └── example-block-dynamic/    # Dynamic block (server-rendered)
│       ├── css/
│       └── js/
├── bin/                                  # Build, init, and pre-commit scripts
├── inc/                                  # All project-specific PHP — PSR-4 root
│   ├── Autoloader.php                    # Wraps vendor/autoload.php with graceful failure
│   ├── Main.php                          # Plugin bootstrap — loads modules
│   ├── Helpers/                          # Stateless static utility classes (final + private __construct)
│   │   └── Util.php                      # General-purpose helpers (get_data, is_production, is_mobile)
│   ├── Core/                             # Plugin-wide infrastructure
│   │   ├── Assets.php
│   │   └── Templates.php
│   └── Modules/                          # Feature areas; each *Module groups its classes
│       ├── BlocksModule.php
│       ├── Blocks/                       # Dynamic blocks (extend AbstractBlock)
│       ├── CLI.php                       # WP-CLI registrar (ConditionallyRegistrable)
│       ├── CLI/                          # Individual CLICommand implementations
│       ├── CronModule.php
│       ├── Cron/                         # WP-Cron jobs (implement Registrable)
│       ├── PostTypesModule.php
│       ├── PostTypes/                    # Extend AbstractPostType
│       ├── RESTModule.php
│       ├── REST/                         # Extend AbstractRESTController
│       ├── RolesModule.php
│       ├── Roles/                        # Extend AbstractUserRole
│       ├── SettingsModule.php
│       ├── Settings/                     # Extend AbstractSettingsPage
│       ├── ShortcodesModule.php
│       ├── Shortcodes/                   # Extend AbstractShortcode
│       ├── TaxonomiesModule.php
│       └── Taxonomies/                   # Extend AbstractTaxonomy
├── languages/
├── templates/                            # Theme-overridable templates
│   └── block-templates/
├── tests/
│   ├── php/                              # PHPUnit suite
│   └── js/                               # Jest suite
├── vendor/
│   └── rtcamp/wp-framework/              # Framework — do not modify (Composer-managed)
├── DEVELOPMENT.md
├── README.md
├── composer.json
├── package.json
├── phpcs.xml.dist
├── phpunit.xml.dist
├── project-name-features.php             # Plugin entry point
└── webpack.config.js
```

</details>

## Post types

| Label                | Slug                  | Public | Taxonomies          | Source                                       |
|----------------------|-----------------------|--------|---------------------|----------------------------------------------|
| Post (Default)       | `post`                | Yes    | Category, Tag       | WordPress core                               |
| Page (Default)       | `page`                | Yes    | —                   | WordPress core                               |
| Media (Default)      | `attachment`          | Yes    | —                   | WordPress core                               |
| Post Type Label      | `post-type-slug`      | Yes    | Taxonomy Label      | `Modules\PostTypes\ExamplePostType`          |
| Post Type Two Label  | `post-type-slug-two`  | Yes    | Taxonomy Two Label  | `Modules\PostTypes\ExamplePostTypeTwo`       |

## Taxonomies

| Label               | Slug                 | Public | Source                                       |
|---------------------|----------------------|--------|----------------------------------------------|
| Category (Default)  | `category`           | No     | WordPress core                               |
| Tag (Default)       | `post_tag`           | Yes    | WordPress core                               |
| Taxonomy Label      | `taxonomy-slug`      | Yes    | `Modules\Taxonomies\ExampleTaxonomy`         |
| Taxonomy Two Label  | `taxonomy-slug-two`  | Yes    | `Modules\Taxonomies\ExampleTaxonomyTwo`      |

## Blocks

| Label                     | Type    | Source                                                        |
|---------------------------|---------|---------------------------------------------------------------|
| Example Block             | Static  | `assets/src/blocks/example-block/`                            |
| Example Block Interactive | Static  | `assets/src/blocks/example-block-interactive/`                |
| Example Dynamic Block     | Dynamic | `Modules\Blocks\ExampleDynamicBlock` (extends `AbstractBlock`) |

## REST endpoints

| Route                                      | Source                                  |
|--------------------------------------------|-----------------------------------------|
| `/wp-json/project-name-features/v1/examples` | `Modules\REST\ExampleRESTController`  |

## Settings pages

| Page                | URL                                                  | Source                                  |
|---------------------|------------------------------------------------------|-----------------------------------------|
| Project Name Features | Settings → Project Name Features                   | `Modules\Settings\ExampleSettingsPage`  |

## Shortcodes

| Tag                       | Source                                  |
|---------------------------|-----------------------------------------|
| `[project_name_example]`  | `Modules\Shortcodes\ExampleShortcode`   |

## Custom user roles

| Role             | Slug                              | Source                              |
|------------------|-----------------------------------|-------------------------------------|
| Content Editor   | `project_name_content_editor`     | `Modules\Roles\ExampleUserRole`     |

## WP-Cron jobs

| Hook                                      | Recurrence | Source                              |
|-------------------------------------------|------------|-------------------------------------|
| `project_name_features_daily_cleanup`     | `daily`    | `Modules\Cron\ExampleCronJob`       |

## WP-CLI commands

| Command                                       | Source                              |
|-----------------------------------------------|-------------------------------------|
| `wp project-name-features health-check`       | `Modules\CLI\Healthcheck`           |

### Reporting a bug 🐞

Before creating a new issue, do browse through the [existing issues](https://github.com/rtCamp/features-plugin-skeleton/issues) for resolution or upcoming fixes. 

If you still need to [log an issue](https://github.com/rtCamp/features-plugin-skeleton/issues/new), making sure to include as much detail as you can, including clear steps to reproduce your issue if possible.

### Creating a pull request

Want to contribute a new feature? Start a conversation by logging an [issue](https://github.com/rtCamp/features-plugin-skeleton/issues).

Once you're ready to send a pull request, please run through the following checklist: 

1. Browse through the [existing issues](https://github.com/rtCamp/features-plugin-skeleton/issues) for anything related to what you want to work on. If you don't find any related issues, open a new one.

2. Create a branch from `develop` for each issue you'd like to address, commit and push your changes.

3. Open a pull request and that's it! We'll with feedback as soon as possible (Isn't collaboration a great thing? 😌)

4. Once your pull request has passed final code review and tests, it will be merged into `develop` and be in the pipeline for the next release. Props to you! 🎉


## Does this interest you?
<a href="https://rtcamp.com/"><img src="https://rtcamp.com/wp-content/uploads/sites/2/2019/04/github-banner@2x.png" alt="Join us at rtCamp, we specialize in providing high performance enterprise WordPress solutions"></a>
