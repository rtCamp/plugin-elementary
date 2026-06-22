# Internal testing (v2)

A short guide to test `npm run init` (project rename, feature toggles, example-set removal) before the private `@rtcamp/*` packages are published. It links the engine from a local `wp-tooling` clone, so no registry or token is needed.

## Prerequisites

- Node 22.
- `rtCamp/wp-tooling` cloned locally, on `release/v1.0.0`.
- A **fresh** clone of this skeleton to test against. `npm run init` rewrites files in place, so use a throwaway clone.

## 1. Link the two packages from your wp-tooling clone

```bash
cd <wp-tooling>/node-packages/wp-tooling && npm link
cd <wp-tooling>/node-packages/tailwind-config && npm link
```

## 2. In the fresh skeleton clone, link them, then install

```bash
cd <skeleton-clone>
npm link @rtcamp/wp-tooling @rtcamp/tailwind-config
npm install
```

> Link **before** installing. The `@rtcamp/*` deps are not published, so a plain `npm install` would fail to resolve them. With the links in place (the linked `0.1.0` satisfies `^0.1.0`), npm keeps them and installs the rest.

## 3. Run init

```bash
npm run init
```

Walk the wizard and pick a test plugin name plus a mix of features and example sets.

## What to verify

- **Identity rename:** plugin name, text domain, namespace (`Project_Name\Features` becomes yours), constant/function prefixes, composer package, and the main plugin file renamed.
- **Tailwind feature:** enabling adds `src/css/tailwind.css` and `postcss.config.js` and flips the `*_ENABLE_TAILWIND` constant to true; disabling backs it out.
- **HMR feature:** toggling flips `ENABLE_HMR` in `.env.local`.
- **Example sets:** removing a set (e.g. Cron, REST) deletes its `inc/Modules/<Set>/Example*.php` and strips the `use` import and registration; kept sets still load; the result is valid PHP (`composer dump-autoload`, `php -l`).

## Notes

- Red CI is expected right now, the private packages are not published yet. This guide is for local testing only.
- To undo the links afterwards: `npm unlink @rtcamp/wp-tooling @rtcamp/tailwind-config` then `npm install`, or just delete the throwaway clone.
