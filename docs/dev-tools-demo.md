# WP Dev Tools demo — seven beats

A copy-paste tour of [wp-dev-tools](https://github.com/rtCamp/wp-devtools) on this project: seven prompts that walk the whole loop, from "what can you even see?" to an agent diagnosing a slow page from live runtime data and pointing at the line of code responsible. It takes about ten minutes on a fresh environment.

The point of the package is that it gives a coding agent things it *cannot* read from source: which queries actually ran, how long they actually took, what the browser actually measured. Every beat below is a prompt you type into Claude Code, not a command you run — the shell lines are only there to generate traffic.

> Beats 1–5 and 7 need nothing but the setup below. **Beat 6 needs a real browser visit** — `curl` cannot produce Core Web Vitals. Read that beat before you start if you want the browser visit to line up with the rest.

## Setup

```bash
# 1. Enable the feature. Writes composer.json, the dev:connect scripts, and the
#    gitignored .wp-env.override.json carrying this machine's paths.
npm run init -- --enable=dev-tools

# 2. Resolve the package. `update`, not `install`: the lock has no entry yet.
composer update rtcamp/wp-dev-tools -W

# 3. Boot. wp-env installs Query Monitor and the MCP Adapter from the override.
npm run wp-env start

# 4. Register the MCP server with Claude Code. Local scope; nothing is committed.
npm run dev:connect
```

`/mcp` inside Claude Code should now show `wp-dev-tools` connected with **7 tools**. `npm run dev:disconnect` undoes step 4.

Confirm the plugin itself is active — the package boots from its Composer autoloader, so an inactive plugin means nothing registers:

```bash
npx wp-env run cli -- wp plugin activate "$(basename "$PWD")"
```

Three of the settings step 1 writes are worth knowing, because each one has a distinctive failure:

| Setting | What it buys you | If it is wrong |
|---|---|---|
| `SAVEQUERIES` | Query Monitor's query backtraces | telemetry has no `file`/`line` at all |
| `CONTAINER_ROOT` / `HOST_ROOT` | the prefix map turning `/var/www/html/…` into a path your editor can open | every `host_file` comes back `null` |
| `LOOPBACK_BASE` | an in-container base URL, since `localhost:8888` is unreachable from inside | `profile-url` fails to capture |

### Give it something to find

The demo is more convincing with a real problem in it, so add one. Paste this at the bottom of your main plugin file — it is deliberately throwaway, and deleting it *is* beat 5:

```php
// --- TEMPORARY: wp-dev-tools demo. Delete this block in beat 5. ---
add_action(
	'wp',
	static function (): void {
		if ( is_admin() ) {
			return;
		}
		global $wpdb;
		for ( $i = 0; $i < 25; $i++ ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->get_var( 'SELECT SLEEP(0.01)' );
		}
	}
);
```

It lives in your own code on purpose. Telemetry only translates container paths that sit under `CONTAINER_ROOT` — your plugin directory — so a slow query raised from *your* file is one the agent can point you straight at. The same block dropped into `wp-content/mu-plugins/` would report `host_file: null` and the best part of the demo would be lost. The `phpcs:ignore` line keeps `npm run lint:php` clean while the block is in place, so nothing else in your workflow trips over it.

## Beat 1 — What can you see?

```
What wp-dev-tools tools do you have available? List them with one line each on what data they surface.
```

Expect all seven: `list-requests`, `get-telemetry`, `compare-requests`, `profile-url`, `get-web-vitals`, `get-runtime-health`, `audit-rest-permissions`. If the list is empty, `/mcp` will show whether the server is actually connected.

## Beat 2 — What just happened?

Generate some traffic first — capture runs on `shutdown`, and WP-CLI requests are never captured:

```bash
curl -s -o /dev/null http://localhost:8888/
```

```
Use list-requests to show the last few captured requests with their headline metrics.
```

You get one row per request, with `id`, `url`, `type`, `status`, `captured_at` and the headline numbers. The slow block is already visible in the aggregate — 50 queries instead of 25, and two orders of magnitude more time in the database:

```text
captured_at                type      status   total    queries   query time   url
2026-08-18T12:12:03+00:00  frontend  200     539.00ms       50     365.23ms   http://localhost:8888/
2026-08-18T12:09:59+00:00  frontend  200      64.50ms       25       3.14ms   http://localhost:8888/
```

## Beat 3 — Why was it slow?

```
Take the most recent capture and show me its slowest database query — the SQL, how long it took, and the host file:line I can open.
```

This is the beat that justifies the whole package. The answer names a real file on your machine:

```text
16.19ms   SELECT SLEEP(0.01)
          caller     Acme_Analytics\Features\{closure}()
          host_file  /path/to/acme-analytics/acme-analytics-features.php:89
```

In that capture, 25 of the 50 query frames carried a translated `host_file` and 25 came back `null`. **That is correct, not a bug** — the null ones are core-owned frames outside `CONTAINER_ROOT`, and the translator returns `null` rather than inventing a path.

`get-telemetry` returns far more than queries: `db_queries`, `http`, `php_errors`, `assets`, `doing_it_wrong`, `cache`, `hooks` and a `metrics` summary. Worth a follow-up prompt:

```
Anything else in that capture worth worrying about? Check the cache hit rate, the doing_it_wrong notices and the PHP errors.
```

## Beat 4 — Is it actually consistent?

One capture could be a cold cache or a noisy neighbour. `profile-url` issues its own loopback requests and reports medians, so it works even with zero prior traffic:

```
Profile http://localhost:8888/ with 5 samples and give me the median timings.
```

```text
batch     8ff46e21-d503-4648-8629-f4c5d72b892e
samples   5 × HTTP 200
medians   total 470.60ms · queries 50 · query time 355.48ms
```

Five for five, so it is the code, not the weather.

## Beat 5 — Prove the fix

Now delete the temporary block you added during setup, and capture the same URL again:

```bash
curl -s -o /dev/null http://localhost:8888/
```

```
Compare the capture from before I removed that block with the one after, and tell me exactly what improved.
```

```text
total_time_ms     467.5  ->   104.1      -363.4   -77.7%
query_count          50  ->      25         -25     -50%
query_time_ms    312.74  ->    4.57     -308.17   -98.5%
peak_memory_mb      7.8  ->     7.7        -0.1    -1.3%
php_error_count       0  ->       0           0     null
http_call_count       0  ->       0           0     null
```

That is the loop closing: a change was made, and the runtime data proves what it did. (`delta_pct` is `null` where the before value was zero — there is no percentage change from nothing.)

## Beat 6 — What did the browser see?

Server timings say nothing about what a user experiences. Field Core Web Vitals come from a real browser, and only from a real browser:

1. Open `http://localhost:8888/` in Chrome.
2. Leave the tab **focused for about seven seconds** — the beacon's idle flush fires at six.
3. Switch to another tab or close it. That triggers the authoritative flush that finalises CLS and INP.

Click something on the page while you are there, or INP will have nothing to report.

```
Show the Core Web Vitals for the homepage, and correlate them with the server telemetry for the same request.
```

You get the field metrics with their attribution (what element was the LCP, what shifted, what the input delay was), a p75 aggregate across recent samples, and a correlation block tying them back to the server-side capture — duplicate queries, slow queries, error counts.

Skip any of the three steps and the tool tells you so rather than inventing numbers:

```text
No Core Web Vitals captured for http://localhost:8888/ yet. Open it in a real
browser (Chrome); field metrics are beaconed when the tab is hidden or after a
short idle. Then retry.
```

If you want the vitals and the server telemetry to describe the same regression, do this browser visit **before** you delete the block in beat 5.

## Beat 7 — The finale

Everything so far named a tool. This one does not:

```
The homepage feels slow. Find out why from runtime data, and point me at the code.
```

A connected agent should work it out on its own: list the recent captures, pull the telemetry for the slowest, sort the queries, and come back with the file and line. It may reach for `profile-url` to confirm the pattern repeats, or `get-runtime-health` to rule out overdue cron and heavy autoloaded options. Put the temporary block back first if you want it to have something to find.

This is the whole pitch in one prompt: no tool names, no dashboard, no guessing from source — the agent reads what actually happened.

## Hygiene notes

- **`list-requests` came back empty.** Nothing has been requested yet. Capture runs on `shutdown`, and WP-CLI requests are never captured — `curl` the site once and retry.
- **Every ability errors.** The dev gate is shut. Check `RT_DEV_TOOLS_DEV_MODE` and `WP_ENVIRONMENT_TYPE` in `.wp-env.override.json`, then `npm run wp-env start` — constants only reach `wp-config.php` on a restart.
- **Every `host_file` is `null`.** The `CONTAINER_ROOT`/`HOST_ROOT` pair is wrong. Re-run `npm run init -- --enable=dev-tools`; it derives both from the current checkout.
- **Some `host_file`s are `null`.** Expected. Only paths under `CONTAINER_ROOT` translate.
- **Old captures vanish.** By design. The store is a ring buffer, pruned on write and hourly, against both a count cap (200 captures) and a TTL (7 days).
- **`curl` produced no Core Web Vitals.** It never will, and neither will `profile-url`. Beat 6 needs a browser.
- **Nothing is exposed in production.** The package is a `require-dev` dependency, and it registers nothing unless `RT_DEV_TOOLS_DEV_MODE` is truthy *and* the environment type is `local`.

## Related

- [dev-tools-e2e.md](dev-tools-e2e.md) — the repeatable check that all of this still works.
- [internal-testing.md](internal-testing.md) — testing `npm run init` itself.
