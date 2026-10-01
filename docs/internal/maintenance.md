# Maintain the skeleton

The maintainer reference for changing the skeleton itself: what it owns versus its dependencies, how the init engine reads it, release validation, dependency updates, AI tooling parity and documentation publishing.

Use a disposable clone, outside the maintained checkout, for every init test, so its generated files cannot leak into this repository's tooling. Never personalize the maintained skeleton.

## What this repository owns

The skeleton is a consumer of three shared repositories. Fix a problem where it lives: a framework bug belongs in `wp-primitives`, an engine bug in `wp-tooling`, and a CI job bug in `wp-shared-workflows`. Missing upstream documentation belongs upstream too; do not grow a duplicate manual here.

| Concern | Owner | In this repo |
| --- | --- | --- |
| Registration system, `Abstract*` classes, loaders, utilities | [`rtcamp/wp-primitives`](https://github.com/rtCamp/wp-primitives) | Consumed through `vendor/`. `inc/Core/*` are thin subclasses. |
| PHP review rules (`framework-php.instructions.md`) | `wp-primitives` | Generated into `.github/instructions/` by `npm run sync-ai`. Never edit the generated copy. |
| Plugin structure rules (`structure.instructions.md`), `AGENTS.md` | This repo | Edit here. |
| Init engine, scaffold catalogue, release scripts, Git hooks | [`@rtcamp/wp-tooling`](https://github.com/rtCamp/wp-tooling) | `bin/init.js` wraps the engine; `bin/scaffold.config.js` configures it. |
| PHPCS / PHPStan / ESLint / Stylelint rules | `rtcamp/wp-phpcs`, `rtcamp/wp-phpstan`, `@rtcamp/eslint-config`, `@rtcamp/stylelint-config` (all from wp-tooling) | `phpcs.xml.dist`, `phpstan.neon.dist`, `eslint.config.mjs`, `.stylelintrc.json` extend them. |
| CI jobs | [`rtCamp/wp-shared-workflows`](https://github.com/rtCamp/wp-shared-workflows) | `.github/workflows/test-measure.yml` is a thin caller pinned to a ref. |
| Documentation site builder | [`rtCamp/action-docusaurus-build`](https://github.com/rtCamp/action-docusaurus-build) | `.github/workflows/documentation.yml` calls it. |
| Examples, modules, asset pipeline, tests, docs | This repo | Everything else. |

## How init reads this repository

[`bin/scaffold.config.js`](../../bin/scaffold.config.js) is the contract between the skeleton and the init engine. It declares:

- **`source`**: the placeholder identity (`Project Name`, `Project_Name\Features`, `rtcamp/project-name-features`) the engine search-replaces. The engine never rewrites files under `bin/`, so placeholders there are safe.
- **`versionFiles`**: where the version is written (main-file header, `package.json`).
- **`examples.groups`**: one entry per example set, built with `capability( key, label, category, { module, strip, remove, tests } )`, plus one `workflow()` entry for the CI caller.
- **`features`**: the toggleable features (`tailwind`, `hmr`, and `dev-tools` from `bin/features/dev-tools.js`).
- **`cleanup.targets`**: paths deleted after setup (currently only `tests/js/scaffold-config.test.js`).

### Markers

A removable region is wrapped in a marker pair in any file listed in that capability's `strip` array (`inc/Main.php` is always included):

```php
// wp:example:cron
Modules\Cron::class,
// wp:example:cron:end
```

On removal the engine deletes the region; on keep it deletes only the two marker lines. Current marked files are `inc/Main.php` (every set), `inc/Core/Assets.php` (`blocks`: the `STATIC_BLOCKS` entries) and `inc/Core/PluginSetup.php` (`cron`: the `use` import and the unschedule call). CI workflows use `wp:ci:<key>`.

Rules that keep init working for every downstream project:

- Keep marker pairs balanced and on their own lines. A broken pair breaks removal for everyone.
- A capability's footprint must be complete: module file, class folder, test file, and every coupled region. Anything left behind references deleted classes.
- Markers are consumed by the first run, so removal is one-shot by design.
- [`tests/js/scaffold-config.test.js`](../../tests/js/scaffold-config.test.js) asserts the config's shape; run `npm run test:js` after editing it.

### Add an example set

1. Add the module (`inc/Modules/<Module>.php`, extending `AbstractModule`) and its example class(es) in `inc/Modules/<Module>/`.
2. Add `tests/php/<Module>Test.php`.
3. Add the module to `Main::CLASSES` inside a `// wp:example:<key>` pair, and wrap any other coupled code in the same pair.
4. Add `capability( '<key>', '<Label>', '<Category>', { module: '<Module>', strip: [ ... ] } )` to `examples.groups`.
5. Update [features.md](../features.md#supplied-examples), [initialization.md](../initialization.md#capability-keys), the README summary, the capability table in `.claude/skills/init/SKILL.md`, and `.github/prompts/init.prompt.md`.
6. Validate removal and keep in a disposable clone (see below).

### Add an optional feature

Declare it in `features` (inline, or as a module in `bin/features/` for anything large). Use `apply.files` for files copied from `bin/features/`, `apply.devDependencies` / `apply.scripts` for manifest changes, and paired `onEnable` / `onDisable` hooks plus `detect` for anything else. Disabling must reverse enabling exactly. Document it in [features.md](../features.md#optional-development-features) and [initialization.md](../initialization.md#optional-dependencies).

## Release validation

Record `git rev-parse HEAD`, Node and PHP versions, the framework revision in `composer.lock`, and the tooling revision in `package-lock.json`.

1. Follow [Getting Started](../getting-started.md) in a fresh clone with a fresh dependency install from the declared sources. Do not substitute a local engine.
2. Run interactive init and a separate non-interactive setup. Check identity, kept sets, `.wp-scaffold.json`, and the Git decision.
3. Exercise each example-set removal in a fresh clone. Check deleted files and the remaining registration, then run `composer dump-autoload` and `php -l` on touched files before loading WordPress. The engine's own output (renamed, removed, toggled) is the primary signal; do not diff `inc/` by hand.
4. Enable and disable each optional feature. Install the changed declarations before checking Tailwind output; run the [Dev Tools check](dev-tools-e2e.md) for `dev-tools`.
5. Start WordPress, activate the plugin, build, and confirm a source edit on the frontend and in the editor.
6. Follow the [scaffolding example](../scaffolding.md) and the [manual examples](../../DEVELOPMENT.md). Verify paths, registration, behaviour and focused tests.
7. Run the [checks](#checks) and build the documentation (see [Documentation publishing](#documentation-publishing)).

If a route cannot be exercised, report the missing prerequisite and the affected step. Do not describe an untested path as verified.

### Checks

Use the explicit commands in [Local development](../local-development.md#check-a-change), not `npm test` or `npm run lint` (see [Known gaps](#known-gaps)). Record pre-existing failures separately from regressions.

### Record validation results

Put journey and link-check results in the pull request description or release hand-off: the date, skeleton commit (and any uncommitted changes), dependency revisions, Node/PHP versions, environment and local overrides. Record one row per route, init mode, removal or feature selection, scaffold or manual example, build and check: the command, the expected result, the actual result, and **pass / fail / blocked** with evidence.

## Dependency updates

| Dependency | Where it is declared | When bumping |
| --- | --- | --- |
| `rtcamp/wp-primitives` | `composer.json` (`^1.0`), `composer.lock` | Run `composer update rtcamp/wp-primitives -W`, then `npm run sync-ai` to refresh the generated instructions. Update every pinned docs link (`grep -rn "wp-primitives/blob/v" README.md DEVELOPMENT.md docs`) to the new tag, and review the framework's changelog for anything the examples or docs must follow. |
| `@rtcamp/wp-tooling`, lint configs | `package.json` (`github:` / `git+https:` refs to `npm/*` branches), `package-lock.json` | Run `npm update @rtcamp/wp-tooling` (and the configs) to move the lock, then re-run init and scaffold validation. |
| Coding standards | `composer.json` (`rtcamp/wp-phpcs`, `rtcamp/wp-phpstan`) | `composer update rtcamp/wp-phpcs rtcamp/wp-phpstan`; fix or baseline new findings in a separate commit. |
| Shared CI | `.github/workflows/test-measure.yml` (`@release/v1.0.0`) | Move the ref once a stable tag exists; check the input names against the new `wp-ci.yml`. |
| Docs builder | `.github/workflows/documentation.yml` (pinned SHA) | See [Documentation publishing](#documentation-publishing). |

Also keep the plugin header's `Tested up to` in step with the newest WordPress version in the CI matrix.

## Local dependency development

When deliberately testing an unreleased change to a shared package, use a separate disposable clone of the skeleton and a separate dependency checkout. Install the clone normally first and note the dependency entries you will override.

```bash
git clone https://github.com/rtCamp/wp-tooling.git '/absolute/path/to/wp-tooling'
git clone https://github.com/rtCamp/wp-primitives.git '/absolute/path/to/wp-primitives'
```

Check out and record the revision under test. The tooling override needs the source monorepo's `node-packages/` layout, not the `npm/*` distribution branches. Follow the [wp-tooling](https://github.com/rtCamp/wp-tooling) or [wp-primitives contributing guide](https://github.com/rtCamp/wp-primitives/blob/v1.0.1/CONTRIBUTING.md#development-setup) for that package's own setup and checks.

From the disposable clone, point only the declarations you need at the checkout:

```bash
npm pkg set 'devDependencies.@rtcamp/wp-tooling=file:/absolute/path/to/wp-tooling/node-packages/wp-tooling'
npm install --install-links     # copies the package so its peer dependencies resolve
```

For framework work, replace the framework entry in `composer.json`'s `repositories` with a `path` repository (`"url": "/absolute/path/to/wp-primitives", "options": { "symlink": false }`) and run `composer update rtcamp/wp-primitives -W`. The checkout's version must satisfy `^1.0`. Use the same pattern for `wp-tooling/composer-packages/phpcs` and `phpstan`; `symlink: false` is required for PHPStan to resolve its baseline.

When done, restore only the dependency source lines and regenerate the affected lockfile. Keep identity changes made by init. Never commit absolute paths or `file:` / `path` overrides, and recheck a clean install from the declared sources.

## Keeping AI tooling in step

- [`AGENTS.md`](../../AGENTS.md) is the canonical convention file. `CLAUDE.md` and `.github/copilot-instructions.md` only point to it.
- `init` and `scaffold` exist twice, as Claude skills (`.claude/skills/<name>/SKILL.md`) and Copilot prompts (`.github/prompts/<name>.prompt.md`). A behaviour change to one must land in the other in the same pull request.
- `.claude/skills/setup/` is a generic bootstrap skill shipped with `wp-tooling`; it has no Copilot counterpart and is not a personalization path.
- Skill eval cases live in `.claude/skills/<name>/evals/evals.json`. Update the expectations when a skill's behaviour changes.
- Skills must follow the base guardrails in `AGENTS.md` (no history- or remote-affecting Git, package managers only with consent, local graph refresh only).

## Documentation publishing

Markdown under `docs/` is rendered by the shared [Docusaurus action](https://github.com/rtCamp/action-docusaurus-build) through [`.github/workflows/documentation.yml`](../../.github/workflows/documentation.yml). `README.md`, `DEVELOPMENT.md` and `CONTRIBUTING.md` stay GitHub pages linked from the site. No Docusaurus dependencies or generated site files belong in this repository.

- The workflow's `sidebar` input sets the main navigation order; pages need no front matter. `docs/internal/` is reached through Contributing and stays out of the sidebar.
- Pull requests against `feature-plugin-skeleton-v2` build only. Pushes and manual runs on that branch also deploy to GitHub Pages. Maintainers set **Settings → Pages → Source** to **GitHub Actions** and allow the branch in the `github-pages` environment; no custom token is needed.
- Keep the action pinned to a reviewed SHA, and test the site with a new SHA before moving it. Change the branch name and its triggers together when the supported branch changes.

To build locally, clone the builder separately and run its CLI:

```bash
node /path/to/action-docusaurus-build/cli.mjs build \
  --source /path/to/plugin-elementary \
  --repository rtCamp/plugin-elementary \
  --out-dir /tmp/plugin-elementary-docs
```

The builder fails on broken internal links and missing repository files. Also check external links, anchors and the root Markdown pages, which it does not fully cover. Update incoming links whenever a page moves.

## Knowledge graph

See [knowledge-graph.md](knowledge-graph.md). In short: contributors refresh their local copy with `graphify update .` and never commit it; the committed cross-repo baseline is rebuilt by a maintainer.

## Temporary procedures

Status recorded on **2026-09-25** for skeleton `e49d4f3`, framework `v1.0.1` (`87774ee`) and tooling `18d003c`. Recheck against the revisions being validated.

| Active procedure | Why it is needed | Retire when |
| --- | --- | --- |
| Run the explicit check commands, not `npm test` / `npm run lint`. | The aggregates' wildcards include watch, coverage and fix scripts. | Both aggregates terminate and only check. |
| Set `BLOCKS_DEV_SERVER_PORT` when using `wp-env`. | The block dev server and the `wp-env` site both default to 8888. | The default port no longer clashes. |
| Point `@rtcamp/tailwind-config` at `github:rtCamp/wp-tooling#npm/tailwind-config` after enabling Tailwind. | `scaffold.config.js` declares `^0.1.0`, which is not on npm. | The config declares an installable spec (the starter theme already does). |

## Known gaps

Keep implementation follow-ups out of documentation pull requests; record each with a reproduction and revision when it is picked up.

- **Aggregate scripts:** `npm test` runs `test:*` (including `test:js:watch` and `test:php:coverage`); `npm run lint` runs `lint:*` (including every `:fix` script and `lint:staged`).
- **Port clash:** `BLOCKS_DEV_SERVER_PORT` defaults to 8888 in `webpack.blocks.config.js` and `.env.local.example`, the same as `wp-env`.
- **Tailwind dependency:** see the temporary procedure above.
- **Release zip:** no `.distignore` ships, so `release:zip` includes `src/`, `docs/`, `graphify-out/`, AI files and dotfiles.
- **Scaffold wiring targets:** `wp/rest` and `wp/cli` suggest `inc/Modules/Rest.php` and `inc/Modules/Cli.php`, but this skeleton's modules are `REST.php` and `CLI.php`, which are different files on case-sensitive filesystems. Keep the uppercase names (they match WordPress acronym style and the framework's `AbstractRESTController` / `CLICommand`); the skill adapts, and CLI users edit the existing file. Fix upstream in wp-tooling.
- **Block scaffolder:** `npm run create:block` writes meta blocks to `src/blocks/meta-blocks/`, which `Assets::STATIC_BLOCKS` cannot register.
- **Dangling references:** `PluginSetup::deactivate()` mentions a root `uninstall.php` and `Util::get_data()` reads `inc/data/`; neither ships.
- **Stale wording:** `.npmrc` still calls `wp-tooling` a private repository, and `bin/init.js` suggests the pilot `npm install --install-links`.
- **Lockfile URLs:** `package-lock.json` resolves `@rtcamp/*` over `git+ssh://`. Confirm a clean `npm ci` works for a developer without a GitHub SSH key.
- **Plugin header:** `Tested up to: 6.8` while CI tests up to WordPress 7.0.
- **CI trigger:** `test-measure.yml` runs on pushes to `master` only; client projects on `main` or `develop` must edit it.
- **Downstream docs workflow:** `documentation.yml` ships into client projects. It is inert there (it only triggers for `feature-plugin-skeleton-v2`), but decide whether init cleanup should remove it.
- **Multisite tests:** `composer test-multisite` has no `wp-env` wrapper.
- **Tailwind lint:** with Tailwind on, Stylelint rejects `@source` in `src/css/tailwind.css` (`scss/at-rule-no-unknown`).
