# AI skills

Skills for AI assistants (Claude Code, Cursor, and any tool that reads the Claude Code skill convention) that drive this plugin's setup and the `@rtcamp/wp-tooling` scaffold engine. Each skill is a directory with a `SKILL.md` (frontmatter `name:` + `description:`, portable Markdown body).

| Skill | Invoke | What it does |
|---|---|---|
| [`init/`](init/SKILL.md) | `/init` | Turn this cloned skeleton into a named project and manage it afterwards: rename the starter tokens, keep/remove the example sets, toggle optional features (Tailwind, HMR). Drives `npm run init`. |
| [`scaffold/`](scaffold/SKILL.md) | `/scaffold` | Add one feature (CPT, taxonomy, block, REST controller, shortcode, cron, CLI, settings page, user role, service, CI workflow) via `npx wp-tooling add`, TDD-first. |

`init` is skeleton-specific. `scaffold` tracks `@rtcamp/wp-tooling` and introspects the project, so it stays correct as the layout evolves.

**Copilot parity:** `init` and `scaffold` also exist for GitHub Copilot as prompt files in [`.github/prompts/`](../../.github/prompts/) (`/init`, `/scaffold`), kept consistent with these skills. Shared conventions and the knowledge-graph (graphify) policy live in [`AGENTS.md`](../../AGENTS.md).

## Safety

These skills are opinionated about safety and never: run a package manager or `npm run build` without consent; read, log, or transmit secret values; apply cross-file wiring without showing the diff and getting consent; or commit, push, or open PRs without approval. `init` additionally never runs a destructive setup without confirming the resolved values against a clean working tree.

Two consented exceptions to the package-manager rule: `npm run init` (the project's own setup script), and the **pilot bootstrap** `init` runs on request (sibling clones + local `file:`/`path` refs + `composer update`/`npm install`). After any code change, the skills refresh the graph with `/graphify . --update`.
