COMPOSE=docker compose
PHP=$(COMPOSE) exec app php
COMPOSER=$(COMPOSE) exec app composer
NPM=$(COMPOSE) exec app npm

.PHONY: bootstrap up down restart logs shell migrate test lint analyse architecture quality

bootstrap:
	@test -f .env || cp .env.example .env
	$(COMPOSE) build
	$(COMPOSE) up -d
	$(COMPOSER) install --no-interaction
	$(PHP) artisan key:generate --force
	$(NPM) ci
	$(NPM) run build
	$(PHP) artisan migrate --force
	@echo "Bootstrap tamamlandı. Yönetici oluşturmak için: docker compose exec app php artisan admin:create"

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

restart:
	$(COMPOSE) restart

logs:
	$(COMPOSE) logs -f

shell:
	$(COMPOSE) exec app bash

migrate:
	$(PHP) artisan migrate

test:
	$(PHP) artisan test

lint:
	$(COMPOSE) exec app vendor/bin/pint --test

analyse:
	$(COMPOSE) exec app vendor/bin/phpstan analyse

architecture:
	$(COMPOSE) exec app vendor/bin/deptrac analyse

quality:
	$(COMPOSE) exec app composer validate --strict
	$(MAKE) test
	$(MAKE) lint
	$(MAKE) analyse
	$(MAKE) architecture
	$(COMPOSE) exec app npm run build
