=== Robw Git Forge ===
Contributors: r33drichards
Tags: git, repositories, forge
Requires at least: 6.5
Tested up to: 6.9
Requires PHP: 8.1
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Experimental private WordPress Git forge. Requires the separate Robw OAuth provider; no external forge service or Git subprocess.

== Description ==
Git smart HTTP, private database object/ref storage, repository ACLs, browsing, issues and signed durable webhook outbox. This is a bounded development pilot, not production-cleared. Read README and SECURITY in the source repository before use.

OAuth is a separate plugin and repository: it owns clients, login/consent and tokens. This plugin owns Git/forge functionality.

== Installation ==
Install Robw OAuth independently first. Install only this robw-forge directory. Never install tests/mu or enable local-test constants on production. Use HTTPS and review all release gates.

== Changelog ==
= 0.1.0 =
Initial separate development pilot.
