# Security and development status

Do not expose this pilot to untrusted writers yet. This is a new bounded PHP Git implementation, not independently reviewed or certified. Report concerns privately to the owner; never include tokens, signing secrets or cookies in issues, traces or reports.

OAuth identity is delegated to the separate Robw OAuth plugin. Every operation checks current forge ACLs. Repository contents are untrusted and never executed. Objects are stored only in database rows and served after authorization and reachability checks.

Gates: native MySQL/Wasmer concurrency/durability/restore; pinned HTTPS webhook transport, DNS-rebinding/redirect/size/time failures; WordPress Plugin Check; codec fuzzing/differential tests and SHA-1 collision handling; quotas/rate limits/global creation policy; independent review. Single-worker fixtures do not prove multi-worker CAS. Loopback webhooks do not prove TLS/SSRF safety.

Verify webhook signatures and deduplicate delivery IDs. Rotating WordPress auth salts makes stored signing keys unavailable; reconfigure hooks securely afterwards. Network errors may follow delivery success, so retries are at-least-once.

No uninstall/delete path automatically removes repository data. Back up before removal/migration. Never install test mu-plugins on production.
