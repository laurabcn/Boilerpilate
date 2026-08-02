.PHONY: up down install migrate test analyse fix shell cs-check

up:
	docker compose up -d --build

down:
	docker compose down

install:
	docker compose run --rm app composer install

migrate:
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

test:
	docker compose exec -e APP_ENV=test -e DATABASE_URL="postgresql://boilerpilate:boilerpilate@postgres:5432/boilerpilate_test?serverVersion=16&charset=utf8" app php vendor/bin/pest

analyse:
	docker compose exec app php vendor/bin/phpstan analyse

fix:
	docker compose exec app php vendor/bin/php-cs-fixer fix

cs-check:
	docker compose exec app php vendor/bin/php-cs-fixer fix --dry-run --diff

shell:
	docker compose exec app sh
