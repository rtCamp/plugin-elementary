#!/usr/bin/env bash
#
# dev-tools-e2e.sh - prove the whole wp-dev-tools loop against this project's
# wp-env, from package boot through to a real MCP tool call.
#
# Exit codes:
#   0  every check passed
#   1  one or more checks failed
#   2  a prerequisite is missing (Docker down, wp-env not started, dev-tools
#      not wired into this project)
#
# Run it after any wp-dev-tools change, and after `composer update
# rtcamp/wp-dev-tools -W`, to catch a regression before it reaches a demo.
#
# This script only reads. It curls the site and makes read-only WP-CLI and MCP
# calls inside the container. It never installs anything, never writes a file
# and never runs git -- a missing prerequisite is printed as the command for
# you to run, not run for you.
#
set -uo pipefail

# ---- configuration -----------------------------------------------------------

SITE_URL="http://localhost:8888"
SKIP_CWV=0

# The allow-list, mirrored from Mcp\ServerRegistrar::ABILITIES. MCP sanitises
# the ability id by replacing "/" with "-", so these are the tool ids a client
# sees, not the ability ids.
EXPECTED_TOOLS="wp-dev-tools-audit-rest-permissions
wp-dev-tools-compare-requests
wp-dev-tools-get-runtime-health
wp-dev-tools-get-telemetry
wp-dev-tools-get-web-vitals
wp-dev-tools-list-requests
wp-dev-tools-profile-url"

EXPECTED_COUNT=7

# ---- output helpers ----------------------------------------------------------

log()  { printf '\033[1;34m==>\033[0m %s\n' "$*"; }
ok()   { printf '\033[1;32m ok:\033[0m %s\n' "$*"; }
warn() { printf '\033[1;33mwarn:\033[0m %s\n' "$*" >&2; }
err()  { printf '\033[1;31merror:\033[0m %s\n' "$*" >&2; }

have() { command -v "$1" >/dev/null 2>&1; }

# `printf ... | grep -q` is a trap here: grep exits on the first match, the
# producer takes SIGPIPE, and `pipefail` then reports the whole pipeline as
# failed even though the match succeeded. A herestring keeps it one command.
contains() { grep -q "$1" <<<"$2"; }

FAILURES=0
fail() { err "$*"; FAILURES=$(( FAILURES + 1 )); }

usage() {
  awk '/^# dev-tools-e2e/,/^set -uo/ { if ($0 !~ /^set -uo/) { sub(/^#[ ]?/, ""); print } }' "$0"
  cat <<'USAGE'
Usage: bash bin/dev-tools-e2e.sh [options]

  --url=URL    Site URL to probe (default http://localhost:8888)
  --skip-cwv   Skip the Core Web Vitals beacon check
  -h, --help   Show this help
USAGE
}

for arg in "$@"; do
  case "$arg" in
    --url=*)     SITE_URL="${arg#*=}" ;;
    --skip-cwv)  SKIP_CWV=1 ;;
    -h|--help)   usage; exit 0 ;;
    *)           err "Unknown option: $arg"; usage; exit 2 ;;
  esac
done

SITE_URL="${SITE_URL%/}"

# ---- container helpers -------------------------------------------------------

# wp-env prints its own progress chatter on stderr; only stdout is the payload.
wp_cli() { npx wp-env run cli -- wp "$@" 2>/dev/null; }

# One STDIO MCP session: initialize, announce, then whatever is piped in.
# $1 is the server id; the remaining JSON-RPC lines come from stdin.
mcp_session() {
  local server="$1"
  {
    printf '%s\n' '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-06-18","capabilities":{},"clientInfo":{"name":"dev-tools-e2e","version":"1"}}}'
    printf '%s\n' '{"jsonrpc":"2.0","method":"notifications/initialized"}'
    cat
  } | npx wp-env run cli -- wp mcp-adapter serve --server="$server" --user=admin 2>/dev/null
}

# ---- prerequisites -----------------------------------------------------------

log "wp-dev-tools end-to-end check against ${SITE_URL}"

have npx || { err "npx not found; install Node 22 (nvm use)."; exit 2; }

if ! have docker || ! docker info >/dev/null 2>&1; then
  err "Docker is not running. Start Docker Desktop, then: npm run wp-env start"
  exit 2
fi

if ! curl -fsS -o /dev/null --max-time 10 "$SITE_URL/"; then
  err "$SITE_URL is not responding. Start the environment:"
  err "  npm run wp-env start"
  exit 2
fi

# ---- 1. the package booted, and the gate is open -----------------------------

log "1. Package booted"

BOOTED="$(wp_cli eval 'echo defined( "RT_DEV_TOOLS_BOOTED" ) ? "yes" : "no";')"
case "$BOOTED" in
  *yes*) ok "RT_DEV_TOOLS_BOOTED is defined" ;;
  *)     fail "RT_DEV_TOOLS_BOOTED is not defined. The package boots from the plugin's autoloader, so check the plugin is active first:"
         warn "  npx wp-env run cli -- wp plugin list"
         warn "  npx wp-env run cli -- wp plugin activate \$(basename \"\$PWD\")"
         warn "and that the package is installed: composer update rtcamp/wp-dev-tools -W" ;;
esac

# BOOTED only proves the autoload ran inside WordPress. This is the check that
# proves the two hard gates (RT_DEV_TOOLS_DEV_MODE + environment type) passed.
ENABLED="$(wp_cli eval 'echo \rtCamp\WPDevTools\Support\Config::is_enabled() ? "yes" : "no";')"
case "$ENABLED" in
  *yes*) ok "the dev gate is open (Config::is_enabled)" ;;
  *)     fail "the dev gate is closed -- check RT_DEV_TOOLS_DEV_MODE and WP_ENVIRONMENT_TYPE in .wp-env.override.json, then: npm run wp-env start" ;;
esac

# ---- 2. abilities registered -------------------------------------------------

log "2. Abilities registered"

# The single quotes are deliberate: this is PHP for `wp eval`, and $k/$a must
# reach the container unexpanded.
# shellcheck disable=SC2016
ABILITIES="$(wp_cli eval 'foreach ( wp_get_abilities() as $k => $a ) { $n = is_object( $a ) && method_exists( $a, "get_name" ) ? $a->get_name() : (string) $k; if ( 0 === strpos( $n, "wp-dev-tools/" ) ) { echo $n, "\n"; } }' | grep '^wp-dev-tools/' | sort)"
ABILITY_COUNT="$(printf '%s\n' "$ABILITIES" | grep -c '^wp-dev-tools/')"

if [ "$ABILITY_COUNT" -eq "$EXPECTED_COUNT" ]; then
  ok "$ABILITY_COUNT abilities registered under wp-dev-tools/"
else
  fail "expected $EXPECTED_COUNT abilities under wp-dev-tools/, found $ABILITY_COUNT"
  printf '%s\n' "$ABILITIES" | sed 's/^/       /'
fi

# ---- 3. the dedicated MCP route responds -------------------------------------

log "3. Dedicated MCP route"

ROUTE_CODE="$(curl -s -o /dev/null -w '%{http_code}' --max-time 10 -X POST \
  -H 'Content-Type: application/json' \
  --data '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{}}' \
  "$SITE_URL/wp-json/wp-dev-tools/mcp")"

# Only a refusal is a pass: the route exists and is turning away an
# unauthenticated call. Anything else -- including curl's 000 for a timeout or
# a dropped connection -- is a failure.
case "$ROUTE_CODE" in
  401|403) ok "/wp-json/wp-dev-tools/mcp responds (HTTP $ROUTE_CODE)" ;;
  404)     fail "/wp-json/wp-dev-tools/mcp returned 404 -- the MCP Adapter plugin is inactive, or the gate is closed" ;;
  000)     fail "/wp-json/wp-dev-tools/mcp did not answer within 10s (connection failed or timed out)" ;;
  2*)      fail "/wp-json/wp-dev-tools/mcp accepted an unauthenticated call (HTTP $ROUTE_CODE) -- it must refuse one" ;;
  *)       fail "/wp-json/wp-dev-tools/mcp returned HTTP $ROUTE_CODE, expected 401 or 403" ;;
esac

# ---- 4. STDIO tools/list is exactly the allow-list ----------------------------

log "4. STDIO tools/list"

TOOLS_RAW="$(printf '%s\n' '{"jsonrpc":"2.0","id":2,"method":"tools/list"}' | mcp_session wp-dev-tools)"
PROTOCOL="$(grep -o '"protocolVersion":"[^"]*"' <<<"$TOOLS_RAW" | head -1 | cut -d'"' -f4)"
TOOLS="$(grep -o '"wp-dev-tools-[a-z-]*"' <<<"$TOOLS_RAW" | tr -d '"' | sort -u)"
TOOL_COUNT="$(printf '%s\n' "$TOOLS" | grep -c '^wp-dev-tools-')"

if [ -n "$PROTOCOL" ]; then
  ok "negotiated MCP protocol version $PROTOCOL"
else
  fail "no protocolVersion in the initialize response -- the STDIO session did not start"
fi

if [ "$TOOL_COUNT" -eq "$EXPECTED_COUNT" ] && [ "$TOOLS" = "$EXPECTED_TOOLS" ]; then
  ok "tools/list is exactly the $EXPECTED_COUNT-tool allow-list"
else
  fail "tools/list does not match the allow-list (found $TOOL_COUNT)"
  diff <(printf '%s\n' "$EXPECTED_TOOLS") <(printf '%s\n' "$TOOLS") | sed 's/^/       /'
fi

# ---- 5. the capture loop -----------------------------------------------------

log "5. Capture loop"

# Capture is on shutdown, and WP-CLI requests are never captured, so the loop
# needs two real HTTP requests to compare. Each is tagged so it can be picked
# out of the list by URL; the pause keeps their second-resolution captured_at
# values apart.
curl -s -o /dev/null --max-time 10 "$SITE_URL/?dev-tools-e2e=before"
sleep 1
curl -s -o /dev/null --max-time 10 "$SITE_URL/?dev-tools-e2e=after"

LIST="$(printf '%s\n' '{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"wp-dev-tools-list-requests","arguments":{"limit":10}}}' | mcp_session wp-dev-tools)"

# Decode the rows rather than scraping UUIDs out of the raw JSON, whose text
# order says nothing about which capture came first. Prints "<tag> <id>" for
# the newest capture of each tagged request, newest first by captured_at.
PICKED="$(node -e '
  let raw = "";
  process.stdin.on("data", (c) => (raw += c)).on("end", () => {
    for (const line of raw.split("\n")) {
      let msg;
      try { msg = JSON.parse(line); } catch { continue; }
      if (msg.id !== 3 || !msg.result) continue;
      let data = msg.result.structuredContent;
      if (!data) {
        try { data = JSON.parse(msg.result.content[0].text); } catch { data = {}; }
      }
      const rows = (data.requests || [])
        .filter((r) => r && r.id && r.captured_at)
        .sort((a, b) => Date.parse(b.captured_at) - Date.parse(a.captured_at));
      for (const tag of ["after", "before"]) {
        const row = rows.find((r) => String(r.url).includes("dev-tools-e2e=" + tag));
        if (row) console.log(tag + " " + row.id);
      }
    }
  });
' <<<"$LIST")"

AFTER="$(awk '$1 == "after" { print $2; exit }' <<<"$PICKED")"
BEFORE="$(awk '$1 == "before" { print $2; exit }' <<<"$PICKED")"

if [ -n "$BEFORE" ] && [ -n "$AFTER" ]; then
  ok "list-requests returned both tagged captures"
else
  fail "list-requests is missing the tagged captures (before: ${BEFORE:-none}, after: ${AFTER:-none}) -- is Query Monitor active? (curl once and retry)"
fi

if [ -n "$AFTER" ]; then
  TELEMETRY="$(printf '%s\n' "{\"jsonrpc\":\"2.0\",\"id\":4,\"method\":\"tools/call\",\"params\":{\"name\":\"wp-dev-tools-get-telemetry\",\"arguments\":{\"request_id\":\"$AFTER\"}}}" | mcp_session wp-dev-tools)"
  if contains '"db_queries"' "$TELEMETRY" && contains '"metrics"' "$TELEMETRY"; then
    ok "get-telemetry returned the normalised sections"
  else
    fail "get-telemetry did not return db_queries + metrics for $AFTER"
  fi

  # host_file is null for core-owned frames by design; at least one frame from
  # this project's own code must translate, or the root pair is wrong.
  if contains '"host_file":"/' "$TELEMETRY"; then
    ok "host file:line translation is working"
  else
    warn "no translated host_file in this capture -- fine if nothing in your own code ran a query, wrong CONTAINER_ROOT/HOST_ROOT otherwise"
  fi
fi

if [ -n "$BEFORE" ] && [ -n "$AFTER" ]; then
  COMPARE="$(printf '%s\n' "{\"jsonrpc\":\"2.0\",\"id\":5,\"method\":\"tools/call\",\"params\":{\"name\":\"wp-dev-tools-compare-requests\",\"arguments\":{\"before_request_id\":\"$BEFORE\",\"after_request_id\":\"$AFTER\"}}}" | mcp_session wp-dev-tools)"
  if contains '"deltas"' "$COMPARE"; then
    ok "compare-requests returned deltas"
  else
    fail "compare-requests did not return deltas for $BEFORE -> $AFTER"
  fi
fi

# ---- 6. the CWV beacon is enqueued on the front end --------------------------

if [ "$SKIP_CWV" -eq 1 ]; then
  log "6. CWV beacon (skipped)"
else
  log "6. CWV beacon"
  HTML="$(curl -s "$SITE_URL/")"
  if contains 'rt-dev-tools-cwv-beacon' "$HTML" && contains 'rtDevToolsCwv' "$HTML"; then
    ok "the beacon script and its config are enqueued on the front end"
  else
    fail "the CWV beacon is not on the front end -- it is skipped in wp-admin, so check you probed a front-end URL"
  fi
fi

# ---- 7. the default adapter server exposes none of ours ----------------------

log "7. Default adapter server isolation"

DEFAULT_TOOLS="$(printf '%s\n' '{"jsonrpc":"2.0","id":6,"method":"tools/list"}' | mcp_session mcp-adapter-default-server)"
LEAKED="$(grep -o '"wp-dev-tools-[a-z-]*"' <<<"$DEFAULT_TOOLS" | sort -u | tr -d '"')"

if [ -z "$LEAKED" ]; then
  ok "the default server exposes no wp-dev-tools-* tools"
else
  fail "the default server is leaking tools -- abilities must not be flagged meta.mcp.public:"
  printf '%s\n' "$LEAKED" | sed 's/^/       /'
fi

if contains 'discover-abilities' "$DEFAULT_TOOLS"; then
  ok "the default server is up (discover-abilities present), so the check is meaningful"
else
  warn "the default server listed no tools at all -- the isolation check above proves less than it looks"
fi

# ---- summary -----------------------------------------------------------------

echo
if [ "$FAILURES" -eq 0 ]; then
  log "All checks passed."
  exit 0
fi

log "$FAILURES check(s) failed."
exit 1
