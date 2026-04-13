# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2024-01-01

### Added
- Initial skeleton with Symfony 7.2
- Docker setup (PHP-FPM 8.2, Nginx, PostgreSQL 16, Redis 7, Mailpit)
- User entity with UUID v7, roles, email verification, last login tracking
- Security: login/logout, remember me, CSRF protection
- Health check endpoint (`/health`)
- PHPUnit test suite with coverage
- PHPStan level 8 static analysis
- PHP-CS-Fixer code style enforcement
- GitHub Actions CI/CD pipeline
- Makefile with dev shortcuts
- Asset Mapper (no Node.js required)
- Doctrine Migrations
- Doctrine Fixtures
- Messenger with async transport
