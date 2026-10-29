# Security policy

## Supported versions

| Version | Supported |
|---|---|
| Latest 1.x release and `main` | Yes |
| Copies of `features-plugin-skeleton`, its earlier repository | No; start new plugins from 1.x |

Plugin Elementary is a skeleton: a plugin created from it is a copy, and it
does not update itself. A fix lands in the skeleton's next release, and the
advisory names the affected files so existing plugins can apply it by hand.
Code a project adds after creating its plugin belongs to that project's
maintainers, and issues in `rtcamp/wp-primitives` or `@rtcamp/wp-tooling`
belong in those repositories.

## Reporting a vulnerability

Please do not report security issues in public issues, pull requests or
discussions.

Report them privately through GitHub instead: open the repository's
**Security** tab and choose **Report a vulnerability**, or go straight to
<https://github.com/rtCamp/plugin-elementary/security/advisories/new>. The
report stays visible only to you and the maintainers, and the fix, the release
and the advisory are coordinated in that same thread.

Include what you can of:

- the affected file or capability, and the skeleton release or commit;
- the impact, and the conditions an attacker needs;
- steps or a minimal proof of concept that reproduce it.
