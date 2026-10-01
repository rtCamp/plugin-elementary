# Maintainer guidance

For developers maintaining Plugin Elementary itself. Developers building a client plugin should start with [Getting Started](../getting-started.md) and [Local development](../local-development.md).

The [maintenance guide](maintenance.md) covers:

- How Plugin Elementary relates to `wp-primitives`, `wp-tooling` and `wp-shared-workflows`, and which files each one owns.
- How `npm run init` reads this repository: `bin/scaffold.config.js`, the `wp:example` markers, and how to add an example set or optional feature.
- Verifying the advertised developer journeys against a specific revision.
- Updating dependencies, and developing against a local dependency checkout.
- Keeping the Claude skills and Copilot prompts in step.
- Publishing and checking this documentation.
- Temporary procedures and known gaps.

Related pages:

- [Knowledge graph](knowledge-graph.md): installing graphify, querying the graph, and who refreshes the committed baseline.
- [DevTools end-to-end check](dev-tools-e2e.md): the repeatable check for the optional DevTools feature.

Documentation and validation results must identify the commit under test and the installed dependency revisions. The guides target `main` and changes built on it; do not assume an older branch or a separately installed dependency checkout behaves the same.

## Dependency sources

[`package.json`](../../package.json) consumes `@rtcamp/wp-tooling` and the lint configs from their GitHub `npm/*` distribution branches, and [`composer.json`](../../composer.json) resolves `rtcamp/wp-primitives` and the coding standards from GitHub. Local source overrides are only for deliberate [dependency development](maintenance.md#local-dependency-development).

The remaining [temporary procedures](maintenance.md#temporary-procedures) each list the revision they apply to and the condition for retiring them.
