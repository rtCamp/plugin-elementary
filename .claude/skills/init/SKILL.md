---
name: init
description: Set up this cloned features-plugin-skeleton into a named project, or manage its identity and capabilities later. During the pilot it can also run the full local bootstrap (sibling clones, local package refs, composer + npm install). Drives `npm run init`. Always confirms before destructive or install steps; expects a clean working tree.
---

# init

Two jobs: **bootstrap + setup** a fresh clone into a real plugin, or **manage** an already-set-up project. Always interactive: gather inputs first, confirm the resolved plan, then act.

## Use for
- First run: optional pilot bootstrap (deps), rename starter tokens, pick capabilities to keep.
- First run WITH a feature brief: after rename + capabilities, implement the described features by chaining to the **scaffold** skill (TDD), replacing the matching `Example*` placeholders (step 8).
- Later: rename / re-prefix, toggle features (Tailwind, HMR).

## Do not use for
- Adding a feature class → the **scaffold** skill (`npx wp-tooling add`).
- Bug fixes, refactors, hand-written features.
- Re-running init to change capabilities after first setup. Removal is one-shot (markers are consumed); a second run leaves dangling `Main::CLASSES` refs. Set the set ONCE; change it later via scaffold.

## Be interactive (required)
Ask the developer for every input the chosen path needs, in ONE batched message, before acting. Never assume a project name. Show derived values back. Wait for explicit consent before any install, file rewrite, or destructive step. If an answer is missing, ask; do not guess.

## Plan and announce (required) - developer experience first
Keep the developer oriented at every moment; great DX is the goal even through an AI skill.

1. **State the plan up front** in one short message: the mode (setup / manage) and the ordered steps you will run for THIS request. For a setup-with-brief, that is: detect mode -> (offer) bootstrap deps -> gather + confirm identity and capabilities -> run init (which also empties the briefed capabilities' examples) -> hand each feature to the scaffold skill.
2. **Maintain a live TODO list** on the host's task surface (`TodoWrite` in Claude Code): one entry per planned step, exactly one `in_progress` at a time, marked `completed` the moment it is done. Add entries as the work reveals them (a missing dep, each feature to scaffold).
3. **Announce each step with a short title** as you start it (e.g. "Running init", "Deleting the example post type", "Handing the Testimonial CPT to scaffold") and report its outcome in one line. Never go silent during a long step.
4. **Confirm before anything destructive or installing** (the init rewrite, dependency installs), showing the exact resolved values.

## Steps

### 1. Detect mode
```bash
test -f .wp-scaffold.json && echo manage || echo setup
```
No `.wp-scaffold.json` → **setup**. Present → **manage** (read it for current identity; `npm run init -- --list` shows feature status).

### 2. Setup: offer the pilot bootstrap
The rtCamp packages are private/unpublished during the pilot. Ask: "Bootstrap local dependencies now? (clones siblings, points npm/Composer at them, installs). y/n". On **yes**, with consent, run in order (tell the developer each step in <=30 words):

```bash
nvm use                                                            # Node 22

# Sibling clones (skip any that already exist):
git clone git@github.com:rtCamp/wp-tooling.git ../wp-tooling
( cd ../wp-tooling && git checkout release/v1.0.0 )               # init engine landed here (wp-tooling #32)
git clone git@github.com:rtCamp/wp-framework.git ../wp-framework

# Local-only npm refs: link EVERY @rtcamp/* devDependency, not just wp-tooling.
# Each @rtcamp/<name> maps to ../wp-tooling/node-packages/<name>; the project also needs
# @rtcamp/eslint-config and @rtcamp/stylelint-config (and tailwind-config once Tailwind is
# enabled), or `npm install` 404s on the unpublished packages. This loop covers all of them:
node -e "const d=require('./package.json').devDependencies||{};for(const k of Object.keys(d))if(k.startsWith('@rtcamp/'))console.log(k+'=file:../wp-tooling/node-packages/'+k.split('/')[1])" \
  | while IFS= read -r ref; do npm pkg set "devDependencies.$ref"; done

# Local-only Composer path repos: replace composer.json "repositories" with these.
# "symlink": false (copy) on ALL THREE is required - PHPStan's wp-phpstan baseline
# does a relative includes: of szepeviktor, which a symlinked package resolves
# outside vendor/ (PHPStan then fails to find the extension).
#   { "type": "path", "url": "../wp-framework", "options": { "symlink": false } },
#   { "type": "path", "url": "../wp-tooling/composer-packages/phpcs", "options": { "symlink": false } },
#   { "type": "path", "url": "../wp-tooling/composer-packages/phpstan", "options": { "symlink": false } }

composer update rtcamp/wp-framework rtcamp/wp-phpcs rtcamp/wp-phpstan
# --install-links is REQUIRED: it copies the file: @rtcamp packages into node_modules
# instead of symlinking, so their peer deps (e.g. @wordpress/eslint-plugin) resolve from
# the consumer. Plain `npm install` symlinks them and `npm run lint:js` then fails with
# "Cannot find module '@wordpress/eslint-plugin'". Add --legacy-peer-deps on ERESOLVE.
npm install --install-links
```
The `file:`/`path` edits are local-only. Note: init ALSO writes identity changes (name, namespace, pot path) into `package.json`/`composer.json`, so a blanket `git checkout package.json composer.json` would discard the rename. Before committing, revert ONLY the dependency-source lines: the `@rtcamp/*` `file:` refs in `package.json`, the `repositories` block in `composer.json`, and `composer.lock` - keep every identity change. On **no**, skip to step 3 and surface installs as developer actions instead.

### 3. Preconditions (verify; do not silently fix)
- `node_modules/@rtcamp/wp-tooling` exists (the engine needs it). If missing and bootstrap was declined, surface `npm install` as a developer action.
- Clean working tree (`git status`). Init rewrites files irreversibly; a clean tree is the only undo. If dirty, ask to commit/stash.
- Confirm this is a fresh clone meant to become a new plugin, not the skeleton repo itself.

### 4. Gather inputs
**Setup:** project name (required, e.g. `Acme Blog` → namespace `Acme_Blog\Features`, package `rtcamp/acme-blog-features`, text domain, prefixes, main file; show these back); version (default `1.0.0`); which capability sets to remove and which features to enable (defaults: keep all sets, hmr on, tailwind off). The engine derives the tokens itself; do not read the engine source (`identity.js` etc.) to work them out - the mapping above is the contract, and the graph answers any deeper question (see the graphify policy in `AGENTS.md`).
**Manage:** which of identity / features to change.

### 5. Confirm
Show the exact resolved values and the exact command. Get explicit consent; init is destructive.

### 6. Run init (with consent)
`npm run init` also runs `npm run sync-ai`; that is expected.
```bash
# Setup:
npm run init -- --name="Acme Blog" --version=1.0.0 --yes \
  --remove-examples=cron,rest --features=hmr,tailwind
# Manage:
npm run init -- --list
npm run init -- --enable=tailwind --yes
npm run init -- --features=hmr --yes        # exact enabled set (empty = none)
```
- `--keep-examples` keeps all; `--remove-examples` (no value) removes all; `--remove-examples=a,b` removes listed keys.
- `--features=a,b` sets the exact enabled set; `--enable`/`--disable` are deltas.
- `--yes` requires `--name`. For the guided wizard, run bare `npm run init` (space toggles, enter confirms).

**Setup WITH a feature brief - clear the briefed examples in THIS step (do not make it a separate round-trip):** keep the capabilities the brief needs (do NOT list them in `--remove-examples`); remove every other capability. Then, batched with the init run, strip the kept-briefed capabilities of their demo code so scaffold can fill them: for each, delete the `Example*` class file(s) under `inc/Modules/<Kind>/`, delete `tests/php/<Cap>Test.php`, and set that module's `get_classes()` to `return [];` (drop the example `use` imports). The module stays wired in `Main::CLASSES` - only its example contents go, leaving an empty, already-wired module ready for scaffold. Example: brief = Testimonial CPT + Industry taxonomy + draft REST -> keep `post-types`/`taxonomies`/`rest`, `--remove-examples=blocks,shortcodes,cli,cron,settings,roles,cache,transients,admin`, then empty the PostTypes/Taxonomies/REST modules.

### 7. After init
- Tailwind enabled → it added `src/css/tailwind.css` + `postcss.config.js` and pinned `@rtcamp/tailwind-config`; developer runs `npm install` (re-apply the `file:` ref first during the pilot).
- `composer dump-autoload` (engine runs it when `composer.json` is present).
- Trust the engine's own output to verify (it reports the removed sets, drops their `Main::CLASSES` lines and tests, and regenerates the autoloader). To confirm a symbol or reference, run a single `graphify query`/`affected` against the graph - never grep `Main.php`/`inc/` to check removal.
- If you hand-edited PHP and are NOT handing off to scaffold (e.g. a manage-mode dangling `Main::CLASSES` cleanup), run `composer lint` on the change and fix it. When a brief follows (step 8), leave the lint/PHPStan/test gates to the scaffold skill - init does not run them.
- **Refresh the LOCAL graph** with `graphify update .` (plugin slice, tree-sitter, no API, seconds). **Never commit or push `graphify-out/graph.json`** - the committed file is a maintainer-maintained cross-repo baseline (rebuilt and pushed when this or a shared repo changes); your local refresh stays local. Do NOT run the cross-repo `/graphify . --update` subagent or `merge-graphs` (expensive; clobbers the baseline). If graphify is not installed, say so in one line; do not block.
- Report: new identity, capabilities removed/kept, features toggled, outstanding developer actions.

### 8. Implement the brief by handing off to scaffold (setup only, when features were described)
By this point step 6 has already emptied the briefed capabilities (their `Example*` classes/tests are deleted and the modules are empty but still wired in `Main::CLASSES`). init's remaining job is purely to **hand each described feature to the scaffold skill** - it does NOT build the class, write or run tests, set up the test env (`npm run wp-env start`), or run the lint/PHPStan gates. The **scaffold skill takes over** and owns all of that.

For each described feature, **invoke the scaffold skill**, passing the brief (e.g. a `testimonial` CPT; an `industry` taxonomy attached to `testimonial`; a REST controller that creates testimonials in `draft`). The scaffold skill owns conventions, the `wp-tooling add` call, wiring into the already-present module, the TDD loop, test execution, and the gates - report its result; do not duplicate that work here.

**Pass your findings to scaffold (speeds it up).** init has already resolved the project identity while renaming, so hand those values to the scaffold skill in the same message instead of making it re-derive them. State explicitly: the resolved **PHP namespace root** (e.g. `Acme\Testimonials`), **base_path** (`inc`), **tests_namespace** (e.g. `Acme\Testimonials\Tests`), **tests_path** (`tests/php`), **text_domain** and **slug** (e.g. `testimonial-features`), the **plugin display name**, and **which capabilities step 6 kept-and-emptied** (their `inc/Modules/<Kind>/` folders already exist and are wired in `Main::CLASSES`, so scaffold wires into them rather than creating a module). With these provided, the scaffold skill skips its own `composer.json` / plugin-header / `Main.php` discovery reads. It still reads source files when it needs exact code (the graph and a handoff are for orientation, not a substitute for reading).

For a kept capability the brief did NOT mention, leave its `Example*` but tell the developer it is demo-only, not for deployment, and offer to scaffold a real class or drop the capability.

End state: init has renamed, trimmed capabilities, and emptied the briefed examples (step 6); the scaffold skill has implemented the real features.

## Capability model
ONE "Select the capabilities to include" prompt. Each is keep-or-remove; removing deletes it entirely (concrete classes, `inc/Modules/<X>.php`, its `Main::CLASSES` line, coupled Core regions, feature deps).

| Category | Keys |
|---|---|
| Content | `post-types`, `taxonomies` |
| Editor & Frontend | `blocks`, `shortcodes`, `tailwind` (feature) |
| APIs & CLI | `rest`, `cli`, `cron` |
| Admin & Access | `settings`, `roles` |
| Dev & CI | `hmr` (feature) |

Sets are kept by default; pass keys to `--remove-examples` to drop. Features: `hmr` on, `tailwind` off; toggle via `--features`/`--enable`/`--disable`.

### Changing capabilities after setup
Add later → scaffold skill / `npx wp-tooling add <category>/<slug>` (writes the class, wires the module). Remove later → delete its `inc/Modules/<X>/` classes + `Main::CLASSES` line by hand. Do not re-run init to change the set.

## Hard rules
- **BASE (see AGENTS.md guardrails):** never run history/remote `git`/`gh` (commit, push, `branch -D`, `reset --hard`, PR, issue comment, `gh secret set`) - print them as developer actions. `git clone`/`checkout` for setup are fine. Never do a destructive operation outside this plugin directory; cloning a NEW sibling is additive and OK, deleting/overwriting existing out-of-repo files is not.
- Never commit or push `graphify-out/graph.json`; local graph refreshes stay local.
- Package managers only with consent: `npm run init`, and the pilot bootstrap of step 2 (sibling clones + local refs + `composer update`/`npm install`). Outside those, surface install commands as developer actions.
- Never run a destructive init without confirming resolved values, on a clean tree.
- Never commit, push, open PRs, or edit `.wp-scaffold.json` by hand.
- Never leave local-only `file:`/`path` edits uncommunicated: tell the developer to revert ONLY the dependency-source lines before commit (the `@rtcamp/*` `file:` refs, the path `repositories` block, `composer.lock`), keeping all identity changes init wrote into those files.
- Never invent flags. Supported: `--name`, `--version`, `--yes`, `--keep-examples`, `--remove-examples[=...]`, `--features`, `--enable`, `--disable`, `--reinit`, `--list`, `--clean`, `--help`. Run `npm run init -- --help` if unsure.

## Reference
- Engine: `@rtcamp/wp-tooling/init` (via `bin/init.js`). Capability map: `bin/scaffold.config.js`.
- Conventions renamed into: `AGENTS.md`, `.github/instructions/structure.instructions.md`.
- Pilot bootstrap detail: `docs/quick-start-guide.md` (Local setup), `docs/internal-testing.md`.
- Graphify policy: `AGENTS.md`.
