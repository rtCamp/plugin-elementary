# Saguri

Saguri (`sgr`, internal repository `rtCamp/saguri`) is an offline code index for coding agents. It answers which handlers a WordPress hook has, which routes, blocks and settings the plugin registers, and who calls what. Agent rules are in [`AGENTS.md`](../../AGENTS.md).

The index covers this plugin, the locked `vendor/rtcamp/wp-primitives` and the installed `@rtcamp/wp-tooling` engine (set in [`.saguri.json`](../../.saguri.json)). It lives in the gitignored `.saguri/`, one per checkout.

## Install

Saguri is on rtCamp's private GitHub Packages registry and needs Node 24, which `.nvmrc` already pins. Create a classic personal access token with the `read:packages` scope, then:

```bash
nvm use
npm config set //npm.pkg.github.com/:_authToken <your-token>
npm install -g @rtcamp/saguri@0.2.1 --@rtcamp:registry=https://npm.pkg.github.com
sgr doctor
```

Keep the registry flag on the install command. Setting `@rtcamp:registry` globally would send this project's public `@rtcamp/*` packages to GitHub Packages too.

Everyone uses `0.2.1`: an older build cannot read an index written by a newer one. To upgrade, change the version here and reinstall.

## Use

```bash
sgr index .                     # build or update the index (seconds)
sgr status --verify             # is it current with the files on disk?
sgr hook rest_api_init          # a hook's handlers and the sites that fire it
sgr entity --kind rest_route    # registered routes (also block, setting, ...)
sgr refs <symbol> --callers     # who calls it
```

Nothing re-indexes automatically: run `sgr index .` after pulling, switching branches or editing code. wp-primitives is indexed only after `composer install`, and wp-tooling only after `npm install`.

`sgr path` can route through a shared hook into an unrelated handler. Confirm any path that crosses a hook with `sgr hook`.

Do not run `sgr install` or `sgr uninstall` (they add their own hooks, skill and `CLAUDE.md` block), or `sgr razu`.
