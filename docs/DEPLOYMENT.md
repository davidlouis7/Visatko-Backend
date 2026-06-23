# Visatko Backend Deployment Checklist

## Server requirements

- PHP 8.3+ with `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`, and Redis extension.
- Composer 2.
- MySQL 8+ or compatible managed database.
- Redis 7+ for cache, queues, rate limiting, and optional Horizon.
- Nginx or Apache serving `public/`.
- Supervisor/systemd for queue workers.
- Cron enabled for Laravel scheduler.

## Initial deploy

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Required production `.env`

Set:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://api.example.com`
- `FRONTEND_URL=https://www.example.com`
- database credentials
- `CACHE_STORE=redis`
- `QUEUE_CONNECTION=redis`
- `SESSION_DRIVER=redis` or `database`
- `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT`
- `MAIL_*`
- `STRIPE_*`
- `TABBY_*`
- `SENTRY_LARAVEL_DSN` if Sentry is enabled
- `BACKUP_*` values for your backup target

## Queue worker

Supervisor example:

```ini
[program:visatko-worker]
command=php /var/www/visatko/artisan queue:work redis --sleep=3 --tries=3 --timeout=120
directory=/var/www/visatko
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/visatko-worker.log
```

## Horizon

Horizon config is present, but Horizon is optional. If installed:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

Protect Horizon behind VPN, basic auth, or role-gated middleware before public production exposure.

## Cron

Add:

```cron
* * * * * cd /var/www/visatko && php artisan schedule:run >> /dev/null 2>&1
```

Use scheduler for backups, queue pruning, and future recurring operations.

## Cache commands after deploy

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Permissions

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rw storage bootstrap/cache
```

## Health and monitoring

- Health endpoint: `GET /api/v1/system/health`
- API docs: `/api/documentation`
- Logs:
  - `storage/logs/api.log`
  - `storage/logs/payments.log`
  - `storage/logs/webhooks.log`
  - `storage/logs/emails.log`
- Optional Sentry: set `SENTRY_LARAVEL_DSN`.

## Backup recommendations

- Enable automated database snapshots on the database provider.
- Back up `storage/app` for private uploaded documents.
- Configure `BACKUP_*` environment variables.
- Test restore procedure monthly.

## Docker

```bash
docker compose build
docker compose up -d
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan test
```

## Pre-launch checks

- `APP_DEBUG=false`
- `php artisan migrate --force`
- `php artisan test`
- `vendor/bin/pint --test`
- `php artisan route:list --path=api/v1`
- Uploads tested on production storage.
- Email queue tested with real SMTP.
- Stripe/Tabby webhook URLs configured.
- Redis queue/cache/rate limiting tested.
- Backups enabled and restore tested.
