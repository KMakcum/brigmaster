# MVP Checklist

Use this checklist before considering the MVP ready.

## Core architecture checks

- [ ] Domain is isolated from WordPress APIs (`register_rest_route`, `WP_REST_*`, globals, `sanitize_*`).
- [ ] Application contains no business formulas and no WordPress calls.
- [ ] Http layer contains no business formulas (only request/response orchestration).

## Functional checks

- [ ] Endpoint `POST /wp-json/constructly/v1/estimate` returns `200` for valid input.
- [ ] Endpoint returns `400` with `validation_error` for invalid input.
- [ ] `examples/smoke_check.php` prints `PASS` for all three cases.

## What is NOT included in MVP

- No queue
- No middleware chain
- No rate limit
- No database persistence
- No repository layer
- No DI container
- No multi-tenancy
- No advanced command bus
