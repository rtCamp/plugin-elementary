# Knowledge graph (Graphify)

This repo ships a committed, cross-repo knowledge graph so AI assistants can answer
questions by querying a graph instead of reading the whole corpus (benchmarked at
20x-250x fewer tokens per question). It is built with the official
[Graphify](https://graphify.net) utility (`graphifyy` on PyPI; the CLI is `graphify`).

## What is tracked

One merged graph covering four repos, all checked out as siblings under the same parent
directory:

- `features-plugin-skeleton` (this repo)
- `wp-tooling`
- `wp-framework`
- `wp-shared-workflows`

Committed artifacts (only these two; everything else in `graphify-out/` is gitignored):

- `graphify-out/graph.json` - the queryable graph (nodes, edges, communities, per-node `repo` tag).
- `graphify-out/GRAPH_REPORT.md` - human-readable summary (god nodes, communities).

## Install (one time)

This environment uses an externally managed Python, so install via pipx:

```bash
pipx install graphifyy      # official package; CLI stays `graphify`
graphify install            # registers the /graphify skill
```

## Query (this is what saves tokens)

```bash
graphify query "how does the init engine remove a capability"
graphify path "Main" "AbstractModule"
graphify explain "applyExamples"
graphify affected "Loader"
```

Or run the `/graphify` skill in Claude Code and ask in natural language.

## Regenerate

The structural rebuild is fully local (tree-sitter, no API key). After large changes:

```bash
# This repo only:
graphify update .

# Full cross-repo graph (run from this repo's root, siblings checked out under ..):
ROOT="$(cd .. && pwd)"
graphify update .
( cd "$ROOT/wp-tooling"          && graphify update . )
( cd "$ROOT/wp-framework"        && graphify update . )
( cd "$ROOT/wp-shared-workflows" && graphify update . )
graphify merge-graphs \
  graphify-out/graph.json \
  "$ROOT/wp-tooling/graphify-out/graph.json" \
  "$ROOT/wp-framework/graphify-out/graph.json" \
  "$ROOT/wp-shared-workflows/graphify-out/graph.json" \
  --out graphify-out/graph.json
graphify cluster-only . --no-label --no-viz   # refresh GRAPH_REPORT.md, skip HTML
rm -rf "$ROOT"/wp-tooling/graphify-out "$ROOT"/wp-framework/graphify-out "$ROOT"/wp-shared-workflows/graphify-out
```

## Optional: semantic layer

The committed graph is structural only. The optional semantic layer (community naming,
inferred "why" edges) needs an LLM, via either an API key (for example `GEMINI_API_KEY`)
with `graphify extract . --mode deep`, or by running the `/graphify` skill inside Claude
Code, which uses the active session instead of a key.
