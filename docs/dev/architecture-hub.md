# Hub architecture — placeholder

This document is a placeholder for the `phlex-hub` architecture write-up.

For the cross-repo design context (what lives in `phlex-server`, what
moves to `phlex-shared`, what the hub itself owns), see the binding
plan in the `detain/phlex` repo:

- `plans/expansion/b.1-shared-design.md` — the **WHAT MOVES WHERE**
  table and the rationale for the namespace split between
  `phlex-server`, `phlex-shared`, and `phlex-hub`.
- `PHLEX_EXPANSION_PLAN.md` §2 (repo layout) and §4.2 (what
  `phlex-hub` owns).

When B.7 lands the signup/login MVP, this file will grow into the
full architecture doc: container topology, request lifecycle, the
JWT issuance flow, and the eventual reverse-tunnel relay.

## What `phlex-hub` owns (preview)

- Signup / login (B.7).
- Server claim (a media server registering itself with the hub).
- Server heartbeat + presence.
- Reverse-tunnel relay endpoints (Phase C).
- The hub web portal and the plugin host that runs alongside it.

## What `phlex-hub` does NOT own

- Library scanning, metadata refresh, FFmpeg transcoding, HLS streaming,
  DLNA, Live TV. Those stay in `phlex-server`.
- Shared DTOs (claim request/response, server info, JWT claims). Those
  live in `detain/phlex-shared`.
