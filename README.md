# Boilerpilate

Hexagonal + DDD + CQRS API boilerplate on **PHP 8.5**, **Symfony 8.1**, **Postgres (Supabase-ready)**, and **Redis**.

Built as a selection-ready portfolio base: Orders bounded context, Pest (arch/unit/feature), PHPStan, PHP-CS-Fixer, Docker, and GitHub Actions.

## Stack

| Layer | Choice |
| --- | --- |
| Runtime | PHP 8.5-FPM + Nginx |
| Framework | Symfony 8.1 (API) |
| Persistence | Doctrine ORM → Postgres / Supabase |
| Cache | Redis via Predis (`CACHE_URL`) |
| Tests | Pest (arch + unit + feature) |
| Quality | PHPStan 8 + PHP-CS-Fixer |

## Quick start

```bash
cp .env.dist .env
make up
make install
make migrate
```

API: http://localhost:8080

```bash
curl -X POST http://localhost:8080/api/orders \
  -H 'Content-Type: application/json' \
  -d '{"lines":[{"productSku":"SKU-1","quantity":2,"unitPrice":15}]}'
```

```bash
make test
make analyse
```

## Architecture

```
src/
  Shared/Domain|Application|Infrastructure   # AggregateRoot, buses
  Domain/Order                               # Aggregate, VOs, ports
  Application/Order                          # Commands / Queries
  Infrastructure/                            # Doctrine, Redis, HTTP
```

- Controllers dispatch `CommandBus` / `QueryBus` only
- Domain models stay free of Doctrine/Symfony attributes
- `TransactionalCommandBus` wraps writes in a Doctrine unit of work
- Domain events (`order.created`, `order.cancelled`) invalidate Redis cache

## Supabase

1. Create a Supabase project
2. Copy the Postgres connection string (`sslmode=require`)
3. Set `DATABASE_URL` in `.env` (pooler for app runtime)
4. Use the **direct** host for migrations when the transaction pooler rejects DDL
5. Run `make migrate`

No Supabase JS SDK — PHP talks to Postgres through Doctrine.

## API

| Method | Path | Description |
| --- | --- | --- |
| `POST` | `/api/orders` | Create order |
| `GET` | `/api/orders` | List orders (`page`, `limit`) |
| `GET` | `/api/orders/{id}` | Get order (Redis-cached) |
| `POST` | `/api/orders/{id}/cancel` | Cancel order |
| `GET` | `/health` | App + DB + Redis checks |

## Makefile

| Target | Action |
| --- | --- |
| `make up` | Build & start containers |
| `make migrate` | Run Doctrine migrations |
| `make test` | Pest suite |
| `make analyse` | PHPStan |
| `make fix` | PHP-CS-Fixer |

## License

MIT
