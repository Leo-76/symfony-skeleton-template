.DEFAULT_GOAL := help
.PHONY: help build up down restart logs shell db-shell \
        install install-dev update \
        db-create db-migrate db-fixtures db-reset db-validate db-diff \
        test test-unit test-controller test-coverage \
        lint cs-check cs-fix analyse security \
        cache-clear cache-warmup routes \
        user-create user-create-admin user-promote \
        messenger-consume messenger-failed messenger-purge

CYAN  := \033[36m
GREEN := \033[32m
RESET := \033[0m

##─────────────────────────────────────────────────────────────────────────────
## Docker
##─────────────────────────────────────────────────────────────────────────────

build: ## Build Docker images
	docker compose build --no-cache

up: ## Start all containers
	docker compose up -d

down: ## Stop all containers
	docker compose down

restart: down up ## Restart all containers

logs: ## Follow container logs
	docker compose logs -f

shell: ## Open shell in PHP container
	docker compose exec php sh

db-shell: ## Open PostgreSQL shell
	docker compose exec database psql -U app app

##─────────────────────────────────────────────────────────────────────────────
## Dependencies
##─────────────────────────────────────────────────────────────────────────────

install: ## Install production dependencies
	composer install --prefer-dist --no-dev --optimize-autoloader

install-dev: ## Install all dependencies (with dev)
	composer install --prefer-dist

update: ## Update dependencies
	composer update

##─────────────────────────────────────────────────────────────────────────────
## Database
##─────────────────────────────────────────────────────────────────────────────

db-create: ## Create the database
	php bin/console doctrine:database:create --if-not-exists

db-migrate: ## Run migrations
	php bin/console doctrine:migrations:migrate --no-interaction

db-diff: ## Generate a migration diff from entity changes
	php bin/console doctrine:migrations:diff

db-fixtures: ## Load data fixtures (dev only)
	php bin/console doctrine:fixtures:load --no-interaction

db-reset: ## Drop, recreate, migrate and load fixtures
	php bin/console doctrine:database:drop --force --if-exists
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate --no-interaction
	php bin/console doctrine:fixtures:load --no-interaction

db-validate: ## Validate Doctrine mapping
	php bin/console doctrine:schema:validate

##─────────────────────────────────────────────────────────────────────────────
## Tests
##─────────────────────────────────────────────────────────────────────────────

test: ## Run all tests
	php bin/phpunit

test-unit: ## Run unit tests only
	php bin/phpunit --testsuite Unit

test-controller: ## Run controller tests only
	php bin/phpunit --testsuite Controller

test-coverage: ## Run tests with HTML coverage report (requires Xdebug)
	XDEBUG_MODE=coverage php bin/phpunit --coverage-html var/coverage
	@echo "$(GREEN)Coverage report: var/coverage/index.html$(RESET)"

##─────────────────────────────────────────────────────────────────────────────
## Code Quality
##─────────────────────────────────────────────────────────────────────────────

lint: ## Lint YAML, Twig and PHP syntax
	php bin/console lint:yaml config --parse-tags
	php bin/console lint:twig templates
	find src tests -name "*.php" -print0 | xargs -0 php -l

cs-check: ## Check code style (dry-run)
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## Fix code style automatically
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix

analyse: ## Run PHPStan static analysis (level 8)
	vendor/bin/phpstan analyse --no-progress

security: ## Audit dependencies for known vulnerabilities
	composer audit

##─────────────────────────────────────────────────────────────────────────────
## Symfony
##─────────────────────────────────────────────────────────────────────────────

cache-clear: ## Clear application cache
	php bin/console cache:clear

cache-warmup: ## Warm up application cache
	php bin/console cache:warmup

routes: ## List all routes
	php bin/console debug:router

##─────────────────────────────────────────────────────────────────────────────
## Users
##─────────────────────────────────────────────────────────────────────────────

user-create: ## Create a user  (EMAIL= NAME= PASS=)
	php bin/console app:user:create $(EMAIL) "$(NAME)" $(PASS)

user-create-admin: ## Create an admin user  (EMAIL= NAME= PASS=)
	php bin/console app:user:create $(EMAIL) "$(NAME)" $(PASS) --admin --verified

user-promote: ## Promote to admin  (EMAIL=)
	php bin/console app:user:promote $(EMAIL) ROLE_ADMIN

##─────────────────────────────────────────────────────────────────────────────
## Messenger
##─────────────────────────────────────────────────────────────────────────────

messenger-consume: ## Consume async queue (1h time limit)
	php bin/console messenger:consume async --time-limit=3600

messenger-failed: ## Retry failed messages
	php bin/console messenger:consume failed --time-limit=60

messenger-purge: ## Purge failed messages older than 30 days
	php bin/console app:messenger:purge-failed --days=30

##─────────────────────────────────────────────────────────────────────────────

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "$(CYAN)%-22s$(RESET) %s\n", $$1, $$2}'
