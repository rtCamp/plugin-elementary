# WP DevTools end-to-end check

A repeatable check that the whole [wp-devtools](https://github.com/rtCamp/wp-devtools) loop still works in this project's wp-env — the package booting, the abilities registering, the MCP allow-list, a real tool call that returns telemetry, and the isolation guarantees that keep the tooling out of production. Run it after any wp-devtools change, so a regression surfaces here rather than halfway through a demo.

> The check only reads. It curls the site and makes read-only WP-CLI and MCP calls inside the container; it never installs anything, never writes a file, and never runs `git`. When a prerequisite is missing it prints the command for you to run rather than running it.

For a guided tour of what these tools actually tell you, see [dev-tools-demo.md](../dev-tools-demo.md).

## Prerequisites

- DevTools enabled on this project: `npm run init -- --enable=dev-tools`, then `composer update rtcamp/wp-devtools -W`.
- Docker running, and the environment up: `npm run wp-env start`.
- This plugin **active** in the environment. The package boots from the plugin's Composer autoloader, so an inactive plugin means nothing registers at all. Renaming the project (which `npm run init` does on a first run) renames the main plugin file and can leave WordPress pointing at the old one:
  ```bash
  npx wp-env run cli -- wp plugin list
  npx wp-env run cli -- wp plugin activate "$(basename "$PWD")"
  ```

## Run it

```bash
bash bin/dev-tools-e2e.sh
```

| Option | Purpose |
|---|---|
| `--url=URL` | Site to probe (default `http://localhost:8888`) |
| `--skip-cwv` | Skip the Core Web Vitals beacon check |
| `-h`, `--help` | Show usage |

| Exit code | Meaning |
|---|---|
| `0` | Every check passed |
| `1` | One or more checks failed |
| `2` | A prerequisite is missing — Docker down, environment not started, `npx` absent |

## What each check proves

| # | Check | Why it is here |
|---|---|---|
| 1 | Package booted, gate open | `RT_DEV_TOOLS_BOOTED` only proves the autoload ran inside WordPress — it is defined **even when the dev gate is shut**. `Support\Config::is_enabled()` is the one that proves both hard gates (`RT_DEV_TOOLS_DEV_MODE` truthy *and* `wp_get_environment_type() === 'local'`) passed. |
| 2 | Abilities registered | All seven abilities are present under the `wp-devtools/` prefix via the core Abilities API. Catches a registrar that silently stopped running. |
| 3 | Dedicated MCP route responds | `/wp-json/wp-devtools/mcp` exists. Only a `401` or `403` is a **pass** — the route is there and refusing an unauthenticated call. Everything else fails: `404` means the MCP Adapter is inactive or the gate is shut, `000` means the request timed out (10s) or never connected, and a `2xx` means the route accepted an anonymous call it should have refused. |
| 4 | STDIO `tools/list` | The exposed surface is *exactly* the allow-list in `Mcp\ServerRegistrar::ABILITIES` — no more, no fewer. Also records the negotiated MCP protocol version. Note the tool ids are **hyphenated** (`wp-devtools-get-telemetry`): MCP sanitises the ability id by replacing `/` with `-`. |
| 5 | Capture loop | The real chain end to end: two tagged HTTP requests (`?dev-tools-e2e=before`, then `?dev-tools-e2e=after`) → `list-requests`, picked out by URL and ordered by `captured_at` → `get-telemetry` → `compare-requests`, all over the same STDIO transport an agent uses. Also confirms container→host `file:line` translation is working. |
| 6 | CWV beacon enqueued | The web-vitals library and the beacon script are on the front end with their config object. This proves only that the beacon *ships* — real field data needs a browser (see the demo doc). The beacon is deliberately skipped in `wp-admin`. |
| 7 | Default adapter server isolation | The MCP Adapter's own default server exposes **none** of our tools. Abilities are never flagged `meta.mcp.public`, so the dedicated server's allow-list is the single reviewable place that decides what a client can see. This is the check that would catch that invariant being weakened. |

Two results are intentionally *not* failures:

- **`host_file: null` on core-owned frames is correct.** `Support\PathTranslator` only translates paths under `RT_DEV_TOOLS_TELEMETRY_CONTAINER_ROOT`, and returns `null` rather than fabricating a path. The check warns, rather than fails, when a capture contains no translated frame at all — that is expected if none of your own code ran a query on that request.
- **An empty `list-requests`** just means nothing has been requested yet. Capture runs on `shutdown`, and WP-CLI requests are never captured, so the script issues its own HTTP requests first.

## Recorded pass

Recorded 18 August 2026 against the reference consumer — an init-scaffolded clone of this repository with `--enable=dev-tools`. The run predates the package rename, so it prints `wp-dev-tools` where a current run prints `wp-devtools`.

| | |
|---|---|
| WordPress | 7.2-alpha-63315 (`WordPress/WordPress#master`) |
| PHP | 8.3.33 |
| `rtcamp/wp-dev-tools` | 0.2.0, locked at `dev-release/v1.0.0` (`770bbe5`) |
| Query Monitor | 4.0.7 |
| MCP Adapter | 0.5.0 |
| MCP protocol | 2025-06-18 |
| Tools exposed | 7 |

```text
==> wp-dev-tools end-to-end check against http://localhost:8888
==> 1. Package booted
 ok: RT_DEV_TOOLS_BOOTED is defined
 ok: the dev gate is open (Config::is_enabled)
==> 2. Abilities registered
 ok: 7 abilities registered under wp-dev-tools/
==> 3. Dedicated MCP route
 ok: /wp-json/wp-dev-tools/mcp responds (HTTP 401)
==> 4. STDIO tools/list
 ok: negotiated MCP protocol version 2025-06-18
 ok: tools/list is exactly the 7-tool allow-list
==> 5. Capture loop
 ok: list-requests returned both tagged captures
 ok: get-telemetry returned the normalised sections
 ok: host file:line translation is working
 ok: compare-requests returned deltas
==> 6. CWV beacon
 ok: the beacon script and its config are enqueued on the front end
==> 7. Default adapter server isolation
 ok: the default server exposes no wp-dev-tools-* tools
 ok: the default server is up (discover-abilities present), so the check is meaningful

==> All checks passed.
```

Exit code `0`.

## Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| `RT_DEV_TOOLS_BOOTED is not defined` | The plugin is inactive, or the package is not installed | `wp plugin activate "$(basename "$PWD")"`; `composer update rtcamp/wp-devtools -W` |
| Booted, but `the dev gate is closed` | `RT_DEV_TOOLS_DEV_MODE` or `WP_ENVIRONMENT_TYPE` missing from the environment | Check `.wp-env.override.json`, then `npm run wp-env start` — constants only reach `wp-config.php` on a restart |
| Every ability errors | Same as above: the gate is shut | As above |
| Route returns `404` | The MCP Adapter plugin is not active | Confirm it is in `.wp-env.override.json`'s `plugins`, then restart |
| Route returns `000` | The site hung or dropped the connection | `npm run wp-env start`; check `npx wp-env logs` for a fatal |
| `tools/list` is empty, or the session never starts | `--user=admin` missing | Every ability is `manage_options`-gated and fails anonymously; the `dev:connect` script already passes `--user=admin` |
| Every `host_file` is `null` | Wrong `CONTAINER_ROOT`/`HOST_ROOT` pair | Re-run `npm run init -- --enable=dev-tools`; it derives both from the current checkout |
| `list-requests` is missing the tagged captures | Nothing was captured, or Query Monitor is inactive | `curl` the site once and retry; confirm Query Monitor is active |

## Related

- [dev-tools-demo.md](../dev-tools-demo.md) — the seven-beat guided demo.
- [maintenance.md](maintenance.md#release-validation) — the full release validation this check belongs to.
