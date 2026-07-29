# Quick Start Guide

Turn this skeleton into a named plugin and add features. Two tracks: **AI skills**
(guided, recommended) or the **raw CLI** (manual).

Worked example throughout: a **Bookshelf** plugin with a `book` CPT, a `genre`
taxonomy attached to it, and a REST endpoint that ingests books in draft from a
publisher feed.

## At a glance

| Task | AI skill (recommended) | Raw CLI |
|------|------------------------|---------|
| Set up the plugin | `/init` + brief | `npm run init` |
| Add a feature | `/scaffold` + description | `npx wp-tooling add wp/<kind>` |

- **init:** names the plugin and selects which capabilities ship. Once per repo.
- **scaffold:** adds one feature (CPT, taxonomy, REST controller, block, CLI, ...).

## Prerequisites

- Node 22, PHP 8.2+, and Docker (Docker powers the `wp-env` test site).
- For the AI track: [Claude Code](https://claude.com/claude-code) with the repo open.

> **Using a different AI assistant?** This guide's AI track shows Claude Code's
> `/init` and `/scaffold` skills, but the project ships shared instructions for
> GitHub Copilot and any other AI too: [AGENTS.md](../AGENTS.md) is the source of
> truth, with [`CLAUDE.md`](../CLAUDE.md) and
> [`.github/copilot-instructions.md`](../.github/copilot-instructions.md) as thin
> pointers to it. The slash-command skills are Claude Code specific; with another tool,
> describe the task and use the [raw CLI track](#track-b-raw-cli-no-ai).

## Local setup (pilot)

The rtCamp packages this plugin needs are **private and unpublished**, so setup
points the package managers at **sibling clones** on disk.

- **AI track:** do step 1 only. `/init` ([Track A](#track-a-ai-skills-recommended)) runs steps 2 to 5 for you.
- **Manual track:** do all five steps, then use [Track B](#track-b-raw-cli-no-ai).

```bash
# 1. Clone the skeleton's pilot branch, then enter it.
git clone -b v2/feat/pilot-foundation git@github.com:rtCamp/features-plugin-skeleton.git bookshelf && cd bookshelf
nvm use                                         # Node 22 (from .nvmrc)
```

> **On the AI track, stop here**, open the repo in Claude Code, and go to [Track A](#track-a-ai-skills-recommended).

```bash
# 2. Clone the rtCamp packages as siblings (next to your plugin).
git clone git@github.com:rtCamp/wp-tooling.git ../wp-tooling
( cd ../wp-tooling && git checkout release/v1.0.0 )
git clone git@github.com:rtCamp/wp-framework.git ../wp-framework

# 3. Point npm at the local tooling (local-only).
npm pkg set "devDependencies.@rtcamp/wp-tooling=file:../wp-tooling/node-packages/wp-tooling"
npm pkg set "devDependencies.@rtcamp/eslint-config=file:../wp-tooling/node-packages/eslint-config"
npm pkg set "devDependencies.@rtcamp/stylelint-config=file:../wp-tooling/node-packages/stylelint-config"

# 4. Point Composer at the local framework + standards. Replace the
#    "repositories" array in composer.json with these three path repos
#    (symlink false on all three: required so PHPStan resolves its baseline):
#      { "type": "path", "url": "../wp-framework",                       "options": { "symlink": false } }
#      { "type": "path", "url": "../wp-tooling/composer-packages/phpcs",   "options": { "symlink": false } }
#      { "type": "path", "url": "../wp-tooling/composer-packages/phpstan", "options": { "symlink": false } }

# 5. Install (--install-links copies the @rtcamp packages so peer deps resolve).
composer update rtcamp/wp-framework rtcamp/wp-phpcs rtcamp/wp-phpstan
npm install --install-links                     # add --legacy-peer-deps on ERESOLVE
```

> **Reverting before commit:** the step 3 and 4 edits are local-only, but `init`
> also writes your plugin's identity (name, namespace) into
> [`package.json`](../package.json) and [`composer.json`](../composer.json). Do not
> blanket-revert those files. Revert only the dependency-source lines: the
> `@rtcamp/*` `file:` refs, the `repositories` block, and
> [`composer.lock`](../composer.lock).

## Track A: AI skills (recommended)

### 1. Set up the plugin

Open the repo in Claude Code. Run `/init` with the plugin name and a one-line brief:

```
/init Set up a "Bookshelf" plugin: a book CPT, a genre taxonomy attached to it,
and a REST endpoint that creates books in draft for a publisher import feed.
```

The skill, confirming with you at each step:

1. Runs the local bootstrap: clones the rtCamp packages as siblings, points npm
   and Composer at them, and installs (setup steps 2 to 5).
2. Renames the skeleton to Bookshelf (namespace, text domain, file headers).
3. Keeps only the capabilities the brief needs (post types, taxonomies, REST);
   removes the rest.
4. Empties those examples, then hands each feature to scaffold, which writes the
   test first, generates the class, wires it in, and runs the gates.

Result from one sentence: a `book` CPT, a `genre` taxonomy on it, and a
draft-creating REST endpoint, each tested and passing `phpcs` / `phpstan` /
`phpunit`.

### 2. Add a feature later

Run `/scaffold` with the feature in plain words:

```
/scaffold Add a "New releases" block that lists the six most recently published books.
```

The skill picks the right kind (a dynamic block), writes the test, scaffolds and
wires the class, and runs the gates before reporting back.

## Track B: Raw CLI (no AI)

### 1. Initialize

```bash
npm run init                                    # guided wizard
# or non-interactive:
npm run init -- --name="Bookshelf" --version=1.0.0 --yes \
  --remove-examples=blocks,shortcodes,cli,cron,settings,roles,cache,transients,admin
npm run init -- --list                          # capability status (add --json for one machine-readable line)
```

The wizard renames the plugin and trims capabilities; implementing the features
comes next, by hand or via scaffold.

### 2. Add the book features (in dependency order)

Scaffold the CPT first, then the taxonomy that attaches to it, then the REST
controller that creates books. Append `--dry-run` to preview a command and to
discover a kind's required inputs.

```bash
# Book CPT
npx wp-tooling add wp/cpt --non-interactive --json \
  --namespace='Bookshelf\Features\Modules\PostTypes' --base_path=inc/Modules/PostTypes \
  --tests_namespace='Bookshelf\Features\Tests' --tests_path=tests/php \
  --text_domain=bookshelf-features --slug=book --singular=Book --plural=Books --class=Book

# Genre taxonomy, attached to the book CPT (--object_type)
npx wp-tooling add wp/taxonomy --non-interactive --json \
  --namespace='Bookshelf\Features\Modules\Taxonomies' --base_path=inc/Modules/Taxonomies \
  --tests_namespace='Bookshelf\Features\Tests' --tests_path=tests/php \
  --text_domain=bookshelf-features --slug=genre --object_type=book \
  --singular=Genre --plural=Genres --class=Genre

# REST controller (creates books in draft)
npx wp-tooling add wp/rest --non-interactive --json \
  --namespace='Bookshelf\Features\Modules\REST' --base_path=inc/Modules/REST \
  --tests_namespace='Bookshelf\Features\Tests' --tests_path=tests/php \
  --name=BookImport --rest_namespace=bookshelf-features/v1 --class=BookImport
```

The CLI generates each class and wires it in, along with a test stub. You add the
business logic and assertions, then run the gates when you are ready.

## AI skills vs raw CLI

The example above shows the difference: [Track A](#track-a-ai-skills-recommended)
built all three features from one sentence, while
[Track B](#track-b-raw-cli-no-ai) reached the same result through the manual
bootstrap, three precise commands, and hands-on tests, wiring, and gates. Both
are valid; they trade convenience for control.

| Aspect | AI skills (`/init`, `/scaffold`) | Raw CLI (`npm run init`, `wp-tooling add`) |
|---|---|---|
| Local bootstrap | Handled for you (siblings, refs, installs) | Run manually, with full control over each step |
| Capability selection (init) | Chosen from your brief | Chosen directly in the wizard or via flags |
| Conventions and inputs | Inferred (namespace, paths, text domain) | Specified explicitly, so each value is exactly as intended |
| Multi-feature order | Sequenced for you (CPT before its taxonomy and REST) | Ordered explicitly, in the sequence you prefer |
| Code written | Implemented from your brief and the codebase context | Scaffolded with wiring; the business logic is yours to write |
| Tests | Written first (TDD) and run for you | Provided as a stub to complete |
| Quality gates (`phpcs` / `phpstan`) | Run and fixed for you | Run at your discretion, on your own schedule |
| Speed | A little slower on the first run, faster after | Near-instant to invoke |
| Output | Generated from your intent, so worth a quick review | Deterministic and predictable, exactly as specified |
| Cost | A small, metered AI spend, modest next to the engineer time it saves | No AI spend; uses engineer time instead |

Rule of thumb: use the CLI for a single artifact you can fully specify; use the
skills for multi-feature setup, unfamiliar conventions, or when you want the
tests and gates handled for you.

## Code consistency and standards

Consistency is a primary requirement: code should read the same no matter who, or
what, writes it. The AI track is built around that.

- **The same rtCamp standards, enforced every run.** Generated code is checked
  and fixed against the project's standards, the exact gates a developer runs
  locally:
  - PHP style: [`phpcs.xml.dist`](../phpcs.xml.dist), extending the shared
    `rtCampWP` ruleset (WPCS + VIP + Docs + Slevomat).
  - PHP static analysis: [`phpstan.neon.dist`](../phpstan.neon.dist), extending
    the `rtcamp/wp-phpstan` level-5 baseline.
  - JS: [`eslint.config.mjs`](../eslint.config.mjs) (`@rtcamp/eslint-config`).
  - CSS: [`.stylelintrc.json`](../.stylelintrc.json) (`@rtcamp/stylelint-config`).
- **Verified, not just formatted.** The skill writes the test first and runs it,
  then `phpcbf` -> `phpcs` -> `phpstan`, fixing what it can. A feature ships
  passing the gates, not merely looking right.
- **No drift between developers.** Registration, wiring, and lifecycle come from
  the shared [`rtcamp/wp-framework`](https://github.com/rtCamp/wp-framework)
  abstracts, and the same tooling and shared repos built for this initiative back
  every project. The structure is identical whoever generates it, so the output
  does not vary developer to developer.
- **Where the model shows.** An AI model can shape the business logic inside a
  method. The framework, conventions, and gates around it do not change, so the
  core stays consistent across people and models, and review focuses on the logic.

The conventions these gates enforce are documented in [AGENTS.md](../AGENTS.md).

## Command reference

| Action | Command |
|--------|---------|
| Set up (guided) | `/init <name + brief>` |
| Set up (CLI) | `npm run init` |
| Capability status | `npm run init -- --list` (`--json` for machine output) |
| Add feature (guided) | `/scaffold <description>` |
| Add feature (CLI) | `npx wp-tooling add wp/<kind> ... --dry-run` |
| Build assets | `npm start` (watch) / `npm run build:prod` |
| Run PHP tests | `npm run test:php` |
| Lint + analyse | `composer lint && composer phpstan` |
