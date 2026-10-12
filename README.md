# Robw WordPress Git Forge

**Separate GPL-2.0-or-later WordPress plugin. Experimental, bounded private-repository pilot — not production-cleared.** PHP 8.1+; no Git subprocess, SSH listener or separate forge service. Git objects, refs and collaboration metadata remain in WordPress's database.

## Two plugins, two repositories

- **[Robw OAuth](https://github.com/r33drichards/robw-wordpress-oauth)** owns public-client registration, WordPress login/consent, PKCE, tokens, scopes, refresh rotation and revocation. That repository is authentication/authorization only.
- **This forge** owns repositories, Git smart HTTP, object/ref validation, repository ACLs, browsing, issues and webhook delivery. It calls `robw_oauth_principal` and registers a narrow `robw_oauth_repository_access` adapter for `robw-forge/<slug>` resources. It neither embeds nor reimplements the OAuth server.

Install the separate OAuth plugin first. Register a public Git credential-helper client with `repo:read repo:write` and its exact loopback callback. Management/browse page: `/forge`. Git remote: `https://YOUR-SITE/forge/git/PROJECT.git`. WordPress cookies authenticate browser management, **never Git transport**. Git requires a valid OAuth token and current repository permission. All repositories in this pilot are private.

## Implemented pilot

- Repository creation by WordPress authors/editors/admins; owner-managed read/write memberships.
- Database-only SHA-1 objects/refs, binary compare-and-swap, fresh reads, in-memory quarantine before publication. No publicly served repository directory.
- Smart HTTP protocol v0 fallback: upload/receive-pack, base and REF/OFS delta pack v2/v3, branches, annotated/lightweight tags, multi-parent commits, atomic multi-ref updates/deletions, stale-ref rejection and no forced non-fast-forward/tag replacement.
- Pack checksum, bounded inflation/delta expansion, object identity, graph closure/type checks, safe ref/tree names and quotas. Only reachable objects are served; unreachable objects are pruned on successful updates.
- Escaped tree/blob/log UI, issue/comment/status management and nonce-protected mutations. Repository PHP, hooks, scripts and CI are never executed.
- Transactional push-event outbox after successful ref updates, stable IDs, HMAC-SHA256 signatures, AES-256-GCM encrypted signing secrets, leased delivery, five attempts/backoff and bounded logs/responses. Delivery is at-least-once; receivers must deduplicate. Automatic retries use WP-Cron and need real traffic or a host scheduler. Owners can process the next due delivery manually.
- Production webhooks require HTTPS:443, certificate verification, public IPv4 DNS validation, cURL DNS pinning, no proxies/redirects and a 16 KiB response bound. If pinning/encryption is unavailable, delivery fails closed. Test-only loopback HTTP is a distinct fixture, not production TLS evidence.

## Deliberate bounds / unsupported features

Per object 1 MiB; incoming pack 4 MiB; decoded/reachable objects 8 MiB and 1,024 objects; at most 128 refs / 64 updates per push. Per repository encoded state 12 MiB, 100 members/issues/comments, five hooks and 100 retained/pending events. The database is the Git data store, not an interoperable bare repository on disk. Back up the database and WordPress signing-key configuration together; restore is a separate acceptance gate. This row design is not a large-repository architecture.

No SSH, LFS, pull requests, CI, arbitrary server hooks, remote URL import, SHA-256 object format, partial/shallow clones or advanced protocol v2. Only implemented capabilities are advertised. The new bounded PHP codec is not a certified/audited replacement for native Git; SHA-1 collision-detection hardening, adversarial review and fuzzing remain necessary before untrusted writers.

## Verification / release gates

Tests use native Git against official WordPress Playground and real browser OAuth consent. Covered targets: clone/push/fetch, merge history, deltas, tags, native `git fsck --strict`, privacy/scope/membership/revocation denial, XSS escaping, malformed packs/refs, atomicity and actual local HTTP webhook retries/signatures. See `verification/` for observed results; skipped/blocked checks are not PASS.

Native MySQL/Wasmer acceptance, independent review/fuzzing, multi-worker and restart/backup-restore durability, production TLS/pinned-DNS webhook tests and official native WordPress Plugin Check remain gates. Local passes are not production clearance. Nothing deploys itself to the live site.

Fixtures under `tests/mu` expose login/reset controls in a loopback-only disposable environment; **never deploy them or enable either LOCAL_TEST constant in production**. The install ZIP includes only `robw-forge/`.

Reproduce locally: Node24+, `python3 tools/setup-oauth.py`, `npm ci`, `npx playwright install chromium`, `npm test`. The test runner pins versions in the Blueprint and asserts actual PHP/WordPress runtime versions, rather than trusting the CLI banner. Native Git is needed by the test client only, not the plugin. CI also defines an official native WordPress Plugin Check gate; configured is not passed.
