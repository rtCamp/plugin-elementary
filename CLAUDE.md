# CLAUDE.md

Read **[AGENTS.md](AGENTS.md)**: it is the source of truth for this project's conventions, shared across all AI tools (Claude Code, Copilot, Codex). The detailed, path-scoped rules it points to live in [`.github/instructions/`](.github/instructions/).

Claude Code skills live in [`.claude/skills/`](.claude/skills/):
- `/init` — turn this cloned skeleton into a named project, and manage its identity, features, and example sets (drives `npm run init`).
- `/scaffold` — add one feature (CPT, block, REST controller, CLI command, ...) via `npx wp-tooling add`, TDD-first.
- `/setup` — bootstrap tooling + a sequence of feature scaffolds from one brief.

Claude-specific notes:
- _(none currently; keep Claude overrides here if they ever diverge from AGENTS.md)_
