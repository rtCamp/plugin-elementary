# AI skills

Skills for AI assistants (Claude Code, Cursor, and any tool that reads the Claude Code skill convention) that drive this plugin's setup and the `@rtcamp/wp-tooling` scaffold engine. Each skill is a directory with a `SKILL.md` (frontmatter `name:` + `description:`, portable Markdown body).

| Skill | Invoke | What it does |
|---|---|---|
| [`init/`](init/SKILL.md) | `/init` | Turn this cloned skeleton into a named project and manage it afterwards: rename the starter tokens, keep/remove the example sets, toggle optional features (Tailwind, HMR). Drives `npm run init`. |
| [`scaffold/`](scaffold/SKILL.md) | `/scaffold` | Add one feature (CPT, taxonomy, block, REST controller, shortcode, cron, CLI, settings page, user role, service, CI workflow) via `npx wp-tooling add`, TDD-first. |
| [`setup/`](setup/SKILL.md) | `/setup` | Bootstrap tooling + a sequence of feature scaffolds from one natural-language brief. |

`init` is skeleton-specific. `scaffold` and `setup` are synced from `@rtcamp/wp-tooling` (`node_modules/@rtcamp/wp-tooling/skills/`) and introspect the project, so they stay correct as the layout evolves; re-sync them with `npm run sync-ai`.

## Safety

These skills are opinionated about safety and never: run a package manager (`npm install`, `composer require`) or `npm run build` without consent; read, log, or transmit secret values; apply cross-file wiring without showing the diff and getting consent; or commit, push, or open PRs without approval. `init` additionally never runs a destructive setup without confirming the resolved values against a clean working tree.
