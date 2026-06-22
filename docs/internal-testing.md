# Internal testing (v2)

A short guide to test `npm run init` (project rename, feature toggles, example-set removal) before the private `@rtcamp/*` packages are published. It resolves the engine from a local `wp-tooling` clone via a `file:` path, so no registry or token is needed.

> Why not `npm link`? This repo's `.npmrc` maps the `@rtcamp` scope to GitHub Packages, so `npm install` always tries the registry for `@rtcamp/*` and 404s while they're unpublished — even with the package linked. Pointing the dependency at a local `file:` path bypasses the registry and is the reliable interim method. Once the packages are published, drop the `file:` edit and follow the README quick-start (token path) instead.

## Prerequisites

- Node 22 (`nvm use`).
- `rtCamp/wp-tooling` cloned **as a sibling** of this repo, on the branch that has the init engine: `release/v1.0.0` once it lands, until then `v1.0.0/task/init-engine`.
- A **fresh/throwaway clone** of this skeleton to test against — `npm run init` rewrites files in place.

## Steps

```bash
# 0. From inside the throwaway skeleton clone, Node 22:
nvm use

# 1. Clone wp-tooling as a sibling on the engine branch (skip if you already have it):
git clone git@github.com:rtCamp/wp-tooling.git ../wp-tooling
( cd ../wp-tooling && git checkout v1.0.0/task/init-engine )   # → release/v1.0.0 once merged

# 2. Point the @rtcamp dependency at the local package (LOCAL ONLY — do not commit).
#    Add the tailwind-config line too if you will test the Tailwind feature.
npm pkg set "devDependencies.@rtcamp/wp-tooling=file:../wp-tooling/node-packages/wp-tooling"
npm pkg set "devDependencies.@rtcamp/tailwind-config=file:../wp-tooling/node-packages/tailwind-config"

# 3. Install and initialize:
composer install     # needed for the framework + sync-ai; init still runs without it
npm install          # if this fails with an ERESOLVE peer-dep conflict, use: npm install --legacy-peer-deps
npm run init         # or non-interactive: npm run init -- --name="Test Plugin" --yes --remove-examples=cron,rest

# 4. When done, revert the local-only package.json edit:
git checkout package.json
```

## What to verify

- **Identity rename:** plugin name, text domain, namespace (`Project_Name\Features` becomes yours), constant/function prefixes, composer package, and the main plugin file renamed.
- **Example sets:** removing a set (e.g. Cron, REST) deletes its `inc/Modules/<Set>/Example*.php` and strips the `use` import and registration; kept sets still load; the result is valid PHP (`composer dump-autoload`, `php -l`).
- **HMR feature:** toggling (`npm run init -- --enable=hmr` / `--disable=hmr`) flips `ENABLE_HMR` in `.env.local`.
- **Tailwind feature:** `npm run init -- --enable=tailwind` adds `src/css/tailwind.css` + `postcss.config.js` and flips the `*_ENABLE_TAILWIND` constant to true. Note: enabling it rewrites the `@rtcamp/tailwind-config` spec back to `^0.1.0`; re-apply the `file:` line from step 2 before the next `npm install` if you want to run the Tailwind build.

## Notes

- Red CI is expected right now — the private packages are not published yet. This guide is for local testing only.
- Manage mode (after first init): `npm run init -- --list` shows feature status; `--enable=`/`--disable=`/`--features=` change it.
