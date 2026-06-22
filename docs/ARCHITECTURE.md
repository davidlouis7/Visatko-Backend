# Visatko Backend Architecture

## Decisions

- Laravel 12 modular monolith on PHP 8.4 and MySQL 8.
- Versioned JSON REST API under `/api/v1`; no browser views except invoice/email templates.
- Sanctum opaque bearer tokens for staff APIs. Tokens are revoked on logout and rotated by login; no long-lived JWT refresh token is required.
- Spatie Permission provides roles/permissions. Policies remain the final object-level authorization boundary.
- Translation tables (not JSON columns) provide searchable, indexed slugs and safe addition of languages.
- Monetary values are stored as integer minor units. VAT is calculated per line with explicit half-up rounding.
- Files are modeled in `media`; public marketing assets and private identity/payment documents use separate disks.
- Payment providers implement a common gateway contract. Webhooks are signature-verified, idempotent, and persisted before processing.
- Domain events drive notifications, audit entries, tracking events, and queued integrations.

## Modules

Each `app/Modules/<Name>` module owns its Controllers, Requests, Resources, Models, Actions, Services, Policies, Events, Listeners, and routes. Cross-module access goes through public Actions/contracts rather than querying another module's internals.

`Auth`, `Users`, `Roles`, `Languages`, `Settings`, `Media`, `Services`, `Applications`, `Consultations`, `CRM`, `Invoices`, `Payments`, `Refunds`, `Blog`, `Content`, `Reviews`, `Team`, `Partners`, `Branches`, `SEO`, `Tracking`, `Notifications`, and `Audit`.

## Request lifecycle

1. API middleware assigns request/correlation ID, negotiates locale, and rate limits.
2. Form Request validates and authorizes.
3. Thin controller invokes an Action/Service.
4. A database transaction changes aggregate state and records history/outbox work.
5. API Resource serializes the response into the common envelope.
6. Queued listeners perform email, PDF, tracking, and provider calls with retries.

## API contract

Success: `{ "success": true, "message": "...", "data": {}, "meta": {} }`.

Error: `{ "success": false, "message": "...", "errors": {}, "trace_id": "..." }`.

Lists accept `page`, `per_page` (max 100), `search`, `sort`, `include`, and module-specific `filter[...]`. Locale comes from `Accept-Language` or `?locale=` and falls back to the configured language.

## Security and operations

- Separate `api.public`, `api.auth`, `webhooks`, and upload limiters.
- Private files are returned only through authorized, short-lived signed download routes.
- Provider secrets remain in environment/secret storage; public tracking IDs are selectively exposed.
- Webhook payload IDs have unique constraints; raw sensitive payloads are redacted/encrypted.
- Queue driver is Redis in production; failed jobs are monitored. Scheduler handles follow-ups, stale payments, and cleanup.
- Audit logs are append-only. Passwords, tokens, passport numbers, and payment secrets are never logged.

