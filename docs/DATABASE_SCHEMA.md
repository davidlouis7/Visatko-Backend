# Database Schema

Conventions: `bigint` primary keys, UTC timestamps, InnoDB foreign keys, soft deletes for recoverable business records, integer minor-unit money, string status columns backed by PHP enums, and composite indexes matching API filters.

## Identity and platform

- `users`: name, email unique, phone, password, active, last_login_at, email_verified_at, timestamps, soft delete.
- Sanctum `personal_access_tokens`; Spatie `roles`, `permissions`, and model/pivot tables.
- `languages`: code unique, name, native_name, direction, fallback_code, active, default, sort_order.
- `settings`: group/key unique, typed value JSON, public, encrypted, timestamps.
- `media`: uuid unique, disk, path unique, filename, mime, size, visibility, collection, mediable morph, uploaded_by, metadata JSON.
- `activity_log`, `notifications`, `jobs`, `failed_jobs`.

## Catalogue and CMS

- `countries` + `country_translations(country_id, language_id, name, slug, seo...)`.
- `visa_services`: country_id, processing/validity/stay values, price_minor, discount_price_minor, currency, featured, active, sort_order, application_schema JSON, timestamps, soft delete.
- `visa_service_translations`: service_id + language_id unique, title, slug unique per language, descriptions, requirements, terms, SEO/OG/canonical/robots fields.
- `service_relations`, `service_required_documents`, `service_faqs`; media supplies thumbnail/banner/gallery.
- `pages` + `page_translations`; `faqs` + translations.
- `blog_categories`, `blog_tags`, `blog_posts`, their translation tables, tag/post and related-post pivots.
- `reviews`, `counters`, `team_members`, `partners`, `branches`, with translation tables where text is localized.

## CRM and fulfilment

- `customers`: name, email, phone, whatsapp, nationality_country_id, residence_country_id, encrypted identity fields.
- `consultations`: public_id unique, customer fields/snapshot, qualification answers, destination, travel_date, notes, status, assigned_to, follow_up_at, converted_application_id, source, timestamps, soft delete; indexes `(status, created_at)`, `(assigned_to, status)`.
- `applications`: number unique, service_id, customer_id, travel/passport snapshot, payment_method, payment_status, status, assigned_to, notes, timestamps, soft delete.
- `application_documents`, `application_status_history(application_id, from_status, to_status, actor_id, note)`.
- `leads`, `crm_notes` polymorphic, `crm_activities` polymorphic, and `internal_comments` polymorphic.
- `contact_messages`: contact data, subject/body, read_at, replied_at, assigned_to, status.

## Billing and payments

- `invoices`: number unique, type, application_id, customer snapshot, currency, subtotal_minor, discount_minor, vat_minor, total_minor, status, payment_status, issue/due dates, notes, terms, timestamps.
- `invoice_items`: invoice_id, description, quantity decimal, unit_price_minor, discount_minor, vat_rate decimal, vat_minor, line_total_minor; totals are immutable after issue.
- `payments`: public_id unique, payable morph, provider, provider_reference, idempotency_key unique, amount_minor, currency, status, paid_at, metadata JSON.
- `payment_status_history`, `bank_transfer_proofs(payment_id, media_id, review fields)`.
- `webhook_events(provider, provider_event_id unique, signature_valid, payload_hash, status, attempts)`.
- `refunds`: payment_id, application_id, amount_minor, reason, status, reviewed_by, provider_reference; related credit-note invoice.

## Tracking

- `conversion_events`: event_id unique, type, customer/application morph references, provider, status, occurred_at, hashed_user_data JSON, payload JSON, attempts.

All translation parents cascade on delete. Financial/history records restrict deletion. Optional relationships use `nullOnDelete`; staff deletion never removes business records.

