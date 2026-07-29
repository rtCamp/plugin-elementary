---
mode: agent
description: Set up this cloned features-plugin-skeleton into a named project, or manage its identity and capabilities later. During the pilot it can also run the full local bootstrap (sibling clones, local package refs, composer + npm install). Drives `npm run init`.
---

# /init

Copilot equivalent of the Claude `init` skill. Keep it in step with [`.claude/skills/init/SKILL.md`](../../.claude/skills/init/SKILL.md). Read [`AGENTS.md`](../../AGENTS.md) first for conventions and guardrails.

Two jobs: **bootstrap + setup** a fresh clone into a real plugin, or **manage** an already-set-up project.

## Be interactive (required for Copilot)

Copilot must gather inputs BEFORE acting. Do not assume any value.

1. Ask the developer every question the chosen path needs, in ONE message.
2. **Stop and wait** for the answers. Do not run commands, edit files, or proceed until they reply.
3. Echo the resolved/derived values back and get an explicit "yes" before any install, file rewrite, or destructive step.
4. If an answer is missing or ambiguous, ask again. Never guess.

## Use for
- First run: optional pilot bootstrap (deps), rename starter tokens, pick capabilities to keep.
- Later: rename / re-prefix, toggle features (Tailwind, HMR).

## Do not use for
- Adding a feature class → the `/scaffold` prompt.
- Bug fixes, refactors, hand-written features.
- Re-running init to change capabilities after first setup (removal is one-shot; a second run leaves dangling `Main::CLASSES` refs). Change the set later via `/scaffold` or by hand.

## Steps

### 1. Detect mode
Run: `test -f .wp-scaffold.json && echo manage || echo setup`. No file → **setup**. Present → **manage** (`npm run init -- --list` shows feature status).

### 2. Setup: offer the pilot bootstrap
The rtCamp packages are private/unpublished during the pilot. Ask: "Bootstrap local dependencies now? (clones siblings, points npm/Composer at them, installs). y/n". On **yes**, with consent, run in order (explain each step in <=30 words):

```bash
nvm use
git clone git@github.com:rtCamp/wp-tooling.git ../wp-tooling
( cd ../wp-tooling && git checkout release/v1.0.0 )
git clone git@github.com:rtCamp/wp-framework.git ../wp-framework
npm pkg set "devDependencies.@rtcamp/wp-tooling=file:../wp-tooling/node-packages/wp-tooling"
# Replace composer.json "repositories" with local path repos:
#   { "type": "path", "url": "../wp-framework", "options": { "symlink": false } },
#   { "type": "path", "url": "../wp-tooling/composer-packages/phpcs" },
#   { "type": "path", "url": "../wp-tooling/composer-packages/phpstan" }
composer update rtcamp/wp-framework rtcamp/wp-phpcs rtcamp/wp-phpstan
npm install                                                     # fallback: npm install --legacy-peer-deps
```
Add the `@rtcamp/tailwind-config` `file:` ref too only if Tailwind will be enabled. The `file:`/`path` edits are local-only: tell the developer to `git checkout package.json composer.json` before committing. On **no**, skip to step 3 and surface installs as developer actions.

### 3. Preconditions (verify; do not silently fix)
- `node_modules/@rtcamp/wp-tooling` exists. If missing and bootstrap declined, surface `npm install` as a developer action.
- Clean working tree (`git status`). Init rewrites files irreversibly; a clean tree is the only undo. If dirty, ask to commit/stash.
- Confirm this is a fresh clone meant to become a new plugin, not the skeleton repo itself.

### 4. Gather inputs
**Setup:** project name (required, e.g. `Acme Blog` → namespace `Acme_Blog\Features`, package `rtcamp/acme-blog-features`, text domain, prefixes, main file; show these back); version (default `1.0.0`); which capability sets to remove and which features to enable (defaults: keep all sets, hmr on, tailwind off).
**Manage:** which of identity / features to change.

### 5. Confirm
Show the exact resolved values and the exact command. Get explicit consent; init is destructive.

### 6. Run init (with consent)
`npm run init` also runs `npm run sync-ai`; expected.
```bash
npm run init -- --name="Acme Blog" --version=1.0.0 --yes --remove-examples=cron,rest --features=hmr,tailwind
# Manage:
npm run init -- --list
npm run init -- --enable=tailwind --yes
npm run init -- --features=hmr --yes        # exact enabled set (empty = none)
```
- `--keep-examples` keeps all; `--remove-examples` (no value) removes all; `--remove-examples=a,b` removes listed keys.
- `--features=a,b` sets the exact enabled set; `--enable`/`--disable` are deltas. `--yes` requires `--name`.

### 7. After init
- Tailwind enabled → it added `src/css/tailwind.css` + `postcss.config.js` and pinned `@rtcamp/tailwind-config`; developer runs `npm install` (re-apply the `file:` ref first during the pilot).
- Suggest `composer dump-autoload`.
- If you hand-edited PHP under `inc/`, run `composer format` → `composer lint` → `composer phpstan` and resolve every finding. Never silence a real issue; if unclear, STOP and ask.
- Refresh the knowledge graph: `/graphify . --update` (or, if graphify is a CLI here, `graphify update`). If not installed, say so in one line; do not block. See `AGENTS.md` (graphify).
- Report: new identity, capabilities removed/kept, features toggled, outstanding developer actions.

## Capability model
ONE keep-or-remove prompt; removing deletes the capability entirely (classes, `inc/Modules/<X>.php`, its `Main::CLASSES` line, coupled Core regions, feature deps).

| Category | Keys |
|---|---|
| Content | `post-types`, `taxonomies` |
| Editor & Frontend | `blocks`, `shortcodes`, `tailwind` (feature) |
| APIs & CLI | `rest`, `cli`, `cron` |
| Admin & Access | `settings`, `roles` |
| Dev & CI | `hmr` (feature) |

Sets kept by default; pass keys to `--remove-examples` to drop. Features: `hmr` on, `tailwind` off.

## Hard rules
- Package managers only with consent: `npm run init`, and the pilot bootstrap of step 2. Otherwise surface install commands as developer actions.
- Never run a destructive init without confirming resolved values, on a clean tree.
- Never commit, push, open PRs, or edit `.wp-scaffold.json` by hand.
- Tell the developer to revert local-only `file:`/`path` edits before commit.
- Never invent flags. Supported: `--name`, `--version`, `--yes`, `--keep-examples`, `--remove-examples[=...]`, `--features`, `--enable`, `--disable`, `--reinit`, `--list`, `--clean`, `--help`. Run `npm run init -- --help` if unsure.
