---
name: init
description: Turn this cloned features-plugin-skeleton into a named project, and later manage its identity and optional features. Drives `npm run init` (the @rtcamp/wp-tooling init engine): rename the starter tokens, keep or remove the shipped example sets, and toggle optional features (Tailwind, HMR). Never runs a package manager; init rewrites files in place, so it always confirms first and expects a clean working tree.
---

# init

Drive `npm run init` (the `@rtcamp/wp-tooling` init engine) to set up or reconfigure this plugin. Two modes, decided by whether the project is already set up.

## Use for

- First-run **setup**: rename the `Project Name` / `project-name` / `Project_Name` starter tokens to a real project (name, namespace, package, text domain, constants, main file), choose which example sets to keep, and pick optional features.
- Later **manage**: rename or re-prefix the project, toggle optional features (Tailwind, HMR), or remove example sets you no longer want.

## Do not use for

- Adding a new feature class (CPT, block, REST controller, CLI command, ...) — that is the **`scaffold`** skill (`npx wp-tooling add`).
- Bug fixes, refactors, or hand-written features.

## Preconditions (verify, do not fix silently)

- `node_modules/@rtcamp/wp-tooling` exists. If not, the engine cannot load — surface `npm install` as a developer action; **do not run it**.
- The working tree is clean (`git status`). Init rewrites files irreversibly in place; a clean tree is the only undo. If dirty, ask the developer to commit/stash first.
- Setup mode renames the whole project. Confirm the developer is in a fresh clone they intend to turn into a new plugin, not the skeleton repo itself.

## Workflow

### 0. Plan and announce

Write a short TODO (host task surface) before acting: detect mode → gather inputs → confirm → run init → report. Keep exactly one entry in progress.

### 1. Detect the mode

```bash
test -f .wp-scaffold.json && echo manage || echo setup
```

- No `.wp-scaffold.json` → **setup** (first run).
- Present → **manage** (already set up). Read it for the current identity, and run `npm run init -- --list` to see feature status.

### 2. Gather inputs from the developer

Ask only for what the chosen mode needs, all at once, never drip.

**Setup:**
- Project name (required), e.g. `Acme Blog`. The engine derives the namespace (`Acme_Blog\Features`), package (`rtcamp/acme-blog-features`), text domain, constant/function prefixes, and the main file name from it. Show the derived values back for confirmation.
- Version (default `1.0.0`).
- Example sets to **keep vs remove**. The sets are: `post-types`, `taxonomies`, `blocks`, `cron`, `rest`, `roles`, `settings`, `shortcodes`. Removing a set deletes its `inc/Modules/<Set>/Example*.php` files and strips its registrations; kept sets stay as working references.
- Optional features: `tailwind`, `hmr`.

**Manage:** which of identity / features / examples the developer wants to change.

### 3. Confirm the plan

Show the exact resolved values and the exact command you intend to run before running anything. Get explicit consent — init is destructive.

### 4. Run init (non-interactive, with consent)

Use flags so the run is deterministic. `npm run init` also runs `npm run sync-ai` (regenerates the AI instruction files); that is expected.

**Setup:**
```bash
npm run init -- --name="Acme Blog" --version=1.0.0 --yes --remove-examples=cron,rest
```
- `--keep-examples` keeps all; `--remove-examples` (no value) removes all; `--remove-examples=a,b` removes the listed sets.
- `--yes` requires `--name`.

**Manage — features** (after setup):
```bash
npm run init -- --list                 # show feature status
npm run init -- --enable=tailwind,hmr --yes
npm run init -- --disable=tailwind --yes
npm run init -- --features=hmr --yes   # set the exact enabled set (empty = none)
```

**Manage — identity / examples:** re-run with `--name=` / `--version=` / `--remove-examples=` as needed. Use `--reinit` only if the developer explicitly wants a fresh scaffold over an already-set-up project.

If the developer prefers to drive it themselves, hand them the exact command instead of running it. If they want the guided experience, tell them to run bare `npm run init` for the interactive wizard.

### 5. After init

- Tailwind enabled → remind: it added `src/css/tailwind.css` + `postcss.config.js` and pinned `@rtcamp/tailwind-config` + Tailwind deps; the developer runs `npm install` to pull them. **Do not run it.**
- Suggest `composer dump-autoload` if classes were removed.
- Report: new identity, example sets removed/kept, features toggled, and the developer actions outstanding (`npm install`, `composer dump-autoload`).

## Hard rules — never violate

- Never run `npm install`, `composer install/require`, or any package manager. `npm run init` (the project's own setup script) is allowed, but only after explicit consent and a clean tree.
- Never run a destructive init without confirming the resolved values first.
- Never run init against a dirty working tree without the developer's OK.
- Never commit, push, open PRs, or edit `.wp-scaffold.json` by hand.
- Never invent flags — the supported set is `--name`, `--version`, `--yes`, `--keep-examples`, `--remove-examples[=...]`, `--reinit`, `--list`, `--features`, `--enable`, `--disable`, `--clean`, `--help`. Run `npm run init -- --help` if unsure.

## Reference

- Engine: `@rtcamp/wp-tooling/init` (invoked by `bin/init.js`).
- Conventions this project renames into: `AGENTS.md`, `.github/instructions/structure.instructions.md`.
- Adding features after setup: the `scaffold` skill.
