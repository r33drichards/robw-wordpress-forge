# Standards / evidence boundary

Git smart HTTP, pack-format and pack-protocol specifications: https://git-scm.com/docs/http-protocol , https://git-scm.com/docs/pack-format , https://git-scm.com/docs/pack-protocol . Actual native Git is the interoperability reference for clone/push/fetch, merge parents, deltas, tags and git fsck --strict. This is protocol v0 with a small capability set, not a claim of all Git features or certification.

OAuth is delegated to the separate provider, not duplicated here. Token scopes are necessary but not sufficient: current repository ACLs are checked for all Git requests and at ref publication.

SHA-1 pack footer and typed object hashes; tree/commit/tag graph closure; limits on packets, pack input, object output, delta expansion, refs, database row and retained outbox. These checks and test negatives are candidate evidence, not an independent security audit or collision-detection guarantee.

Repository mutation + event creation are one database compare-and-swap publication. Delivery occurs later with stable IDs, encrypted signing keys, HMAC, leases and at-least-once retry. Native production TLS/public-IP pinning must be independently tested; local plain HTTP fixtures are not that gate.

Official WordPress Playground supplies the WordPress/PHP test runtime. The Blueprint explicitly pins runtime tracks and tests assert actual versions: CLI banner alone is not evidence. First development run revealed that CLI-only version selection with a supplied Blueprint was ineffective; corrected explicit preferredVersions produces PHP8.3.33/WordPress6.9.10. Earlier partial run/default versions are not the final versioned evidence. Two-worker first run was interrupted by a session restart; single-worker functional proof cannot be counted as a multi-worker concurrency gate.

Official native WordPress Plugin Check is configured in CI, not certified/passed merely by configuration. Native Wasmer/MySQL, multi-worker contention, restart/restore, production webhook adversarial network tests, codec fuzzing/SHA1 collision handling and independent security review remain NOT_RUN/BLOCKED unless independently observed.
