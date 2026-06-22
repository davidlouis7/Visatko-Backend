# API v1 Route Map

## Public

- `POST /auth/login`, `/auth/forgot-password`, `/auth/reset-password`
- `GET /languages`, `/settings/public`
- `GET /countries`, `/visa-services`, `/visa-services/{localizedSlug}`
- `POST /consultations`, `/applications`, `/contact-messages`
- `GET /blog/*`, `/pages/{localizedSlug}`, `/reviews`, `/counters`, `/team`, `/partners`, `/branches`
- `POST /payments/stripe/session`, `/payments/tabby/session`, `/payments/bank-transfer`
- `POST /webhooks/stripe`, `/webhooks/tabby` (provider signature middleware; never Sanctum)

## Authenticated staff

- `POST /auth/logout`, `/auth/logout-all`, `/auth/change-password`; `GET/PATCH /auth/profile`
- CRUD `/admin/users`, `/admin/roles`, `/admin/permissions`, `/admin/languages`, `/admin/settings`
- CRUD `/admin/countries`, `/admin/visa-services`, `/admin/pages`, `/admin/faqs`, `/admin/blog/*`
- CRUD `/admin/consultations`, `/admin/applications`, `/admin/leads`, `/admin/contact-messages`
- `POST /admin/{resource}/{id}/assign`, `/notes`, `/status`
- CRUD `/admin/invoices`; `GET /admin/invoices/{invoice}/pdf`; `POST /admin/invoices/{invoice}/email`
- `POST /admin/bank-transfers/{payment}/approve|reject`
- CRUD `/admin/refunds`; `POST /admin/refunds/{refund}/approve|reject`
- CRUD `/admin/media`, `/admin/reviews`, `/admin/counters`, `/admin/team`, `/admin/partners`, `/admin/branches`
- `GET /admin/audit-logs`, `/admin/notifications`; `POST /admin/notifications/{id}/read`

Every write endpoint has an explicit permission, e.g. `services.create`, `applications.assign`, `invoices.issue`, `payments.review`, and `refunds.approve`.

