# Changelog

All notable changes to `detain/phlex-hub` are documented here.

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] — 2026-05-17

### Added
- Initial scaffolding: Workerman 5 HTTP application, PSR-11 container (PHP-DI 7), structured logger (Monolog 3), `/health` endpoint.
- Composer dependency on `detain/phlex-shared:^0.2` consumed via Composer VCS repository (Packagist publication deferred to v1.0).
- 5-check CI workflow (composer-validate, phpcs PSR-12, phpstan 2.x level 9, psalm v5, security audit) + phpunit.
- `migrations/` directory with placeholder; real schema lands in B.6.
- `docs/reference/env-vars.md` listing every `HUB_*` env var.
- `docs/dev/architecture-hub.md` placeholder pointing at the cross-repo design docs in `detain/phlex`.

### Notes
- DB schema and migrations land in B.6. Signup/login MVP lands in B.7.
