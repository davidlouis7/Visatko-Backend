# Installation and Implementation Plan

## Installation

```bash
composer create-project laravel/laravel visatko-backend '^12.0'
cd visatko-backend
composer require laravel/sanctum spatie/laravel-permission spatie/laravel-activitylog barryvdh/laravel-dompdf stripe/stripe-php
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan queue:work
```

PHP extensions: `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `intl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, and `xml`. Production also needs MySQL 8, Redis, a queue supervisor, object storage, and a transactional mail provider.

## Delivery phases

1. Foundation: API envelope/errors, locale, rate limits, Sanctum, roles/permissions, audit, languages, settings, media.
2. Acquisition: countries/services, consultations, contacts, CMS/blog/SEO.
3. Fulfilment: customers, visa applications, documents, staff assignment, timelines, notifications.
4. Finance: UAE VAT value objects, invoices/PDF/email, payment ledger, Stripe/Tabby/bank transfer, webhooks.
5. CRM/refunds: leads, notes/follow-ups, conversion, refund approval and credit notes.
6. Integrations/hardening: Meta CAPI/outbox, analytics events, observability, security/load testing, Postman collection and deployment runbook.

Each phase ships migrations, factories/seeders, policies, OpenAPI/Postman examples, feature/unit tests, and rollback notes. Status transitions are introduced through explicit Actions, never arbitrary model updates.

## Environment variables

```dotenv
APP_NAME="Visatko Visa Service"
APP_ENV=production
APP_URL=https://api.example.com
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
FRONTEND_URL=https://example.com
SANCTUM_STATEFUL_DOMAINS=example.com
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=visatko
DB_USERNAME=visatko
DB_PASSWORD=
QUEUE_CONNECTION=redis
CACHE_STORE=redis
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=me-central-1
AWS_BUCKET=
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_USERNAME=
MAIL_PASSWORD=
WHATSAPP_NUMBER=
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
TABBY_PUBLIC_KEY=
TABBY_SECRET_KEY=
TABBY_MERCHANT_CODE=
TABBY_WEBHOOK_SECRET=
META_PIXEL_ID=
META_CAPI_TOKEN=
META_CAPI_ENABLED=false
```

