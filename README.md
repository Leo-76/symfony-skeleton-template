#  Symfony Skeleton

[![CI](https://github.com/your-vendor/symfony-skeleton/actions/workflows/ci.yml/badge.svg)](https://github.com/your-vendor/symfony-skeleton/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-8892BF.svg)](https://php.net)
[![Symfony](https://img.shields.io/badge/Symfony-7.2-000000.svg?logo=symfony)](https://symfony.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/your-vendor/symfony-skeleton)](https://packagist.org/packages/your-vendor/symfony-skeleton)

A **production-ready** Symfony 7.2 skeleton — Docker, full auth, admin panel, async messaging, and best practices baked in. Ready to publish on Packagist.

---

##  What's Included

### Infrastructure
- PHP-FPM 8.2, multi-stage Dockerfile (base / dev / prod)
- Nginx with security headers + static asset caching
- PostgreSQL 16 with healthcheck
- Redis 7 for cache + Messenger transport
- Mailpit for local SMTP (auto-configured in dev)
- Xdebug pre-configured (off by default)

### Authentication
- Login / logout (CSRF-protected)
- User registration (terms checkbox + NotCompromisedPassword)
- Remember me (1 week)
- Forgot password → email link → reset form → new password
- `lastLoginAt` tracking via LoginSuccessListener
- `UserVoter` for per-resource USER_VIEW / USER_EDIT authorization

### Architecture Patterns
- UUID v7 primary keys (`Entity/Trait/HasUuid`)
- Timestamp lifecycle callbacks (`Entity/Trait/HasTimestamps`)
- Generic Doctrine paginator (`Pagination/DoctrinePaginator` + `PaginatedResult<T>`)
- Abstract repository with `save()`, `remove()`, `paginate()`
- Abstract API controller with `success()`, `validationError()`, `notFound()`
- Custom base controller with `requireUser()`, `addSuccess()`, `getPage()`
- Custom Twig extension: `truncate`, `human_filesize`, `initials`, `is_current_route`
- Custom form theme for clean error/label/help rendering

### Messaging
- Async transport via PostgreSQL LISTEN/NOTIFY (with pg_notify trigger in migration)
- Retry strategy (3 attempts, exponential backoff)
- Failed transport for dead-letter queue
- `SendWelcomeEmail` message + handler as example

### Emails
- `MailerService`: `sendWelcome()`, `sendPasswordReset()`, `sendEmailVerification()`
- Responsive HTML email templates with base layout

### Admin Panel (`/admin`)
- Searchable, paginated user list
- User detail page with last login
- Toggle admin role (CSRF-protected)
- Delete user (CSRF-protected, self-deletion prevented)

### API (`/api`)
- `GET /api/me` — authenticated user as JSON
- `GET /health` — structured health check

### Console Commands
| Command | Description |
|---------|-------------|
| `app:install` | First-run wizard |
| `app:user:create` | Create user from CLI |
| `app:user:promote` | Add/remove role |
| `app:messenger:purge-failed` | Purge old failed messages |

### Code Quality
- PHPStan level 8
- PHP-CS-Fixer with Symfony + PHP 8.2 rules
- PHPUnit 11 — 3 suites: Unit / Integration / Controller
- Symfony YAML, Twig, PHP syntax linters

### CI/CD
- `ci.yml`: lint → PHPStan → test matrix (8.2 + 8.3) → security audit → Docker build
- `release.yml`: auto GitHub Release + changelog on git tag
- `dependency-review.yml`: blocks high-severity new deps on PR
- Dependabot for weekly Composer + Actions updates

---

##  Quick Start

```bash
composer create-project your-vendor/symfony-skeleton my-project
cd my-project
```

Or manually:

```bash
git clone https://github.com/your-vendor/symfony-skeleton.git my-project
cd my-project
composer install
```

---

##  Docker

```bash
cp .env .env.local
make build && make up

# First-run wizard (DB + migrations + admin user):
make shell
php bin/console app:install
exit

# Or step-by-step:
make db-create && make db-migrate && make db-fixtures
```

| Service | URL |
|---------|-----|
| App | http://localhost:8080 |
| Mailpit | http://localhost:8025 |

---

## 🛠 Without Docker

Requirements: PHP 8.2+, PostgreSQL 16, Redis 7, Composer 2

```bash
composer install
cp .env .env.local   # set DATABASE_URL, MESSENGER_TRANSPORT_DSN
php bin/console app:install
symfony serve
```

---

## Tests & Quality

```bash
make test            # all suites
make test-coverage   # HTML report → var/coverage/
make lint            # YAML + Twig + PHP
make cs-fix          # auto-fix code style
make analyse         # PHPStan level 8
make security        # composer audit
```

---

##  Fixture Users

| Email | Password | Role |
|-------|----------|------|
| admin@example.com | admin123 | ROLE_ADMIN |
| user@example.com | user123 | ROLE_USER |

>  Never load fixtures in production.

---

##  Production

```bash
docker build -f docker/php/Dockerfile --target prod -t my-app:latest .
```

Required env vars: `APP_SECRET`, `DATABASE_URL`, `MESSENGER_TRANSPORT_DSN`, `MAILER_DSN`, `APP_NAME`, `APP_TIMEZONE`.




---

## Contributing ·  Security ·  License

See [CONTRIBUTING.md](CONTRIBUTING.md), [SECURITY.md](SECURITY.md), [LICENSE](LICENSE).
