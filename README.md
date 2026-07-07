# Panulat

Panulat is a modular, lightweight PHP framework for building clean REST APIs and API-first applications. It was created to keep API development as simple and focused as possible.

This repository is the Panulat starter project. It gives you a ready-to-use API application structure built on top of:


* `blcklab/panulat-core`
* `blcklab/panulat-jwt`
* `blcklab/panulat-cli`

## Quick Start


Documentation
-------------

Start here: [`docs/getting-started.md`](docs/getting-started.md)

Create a new project:

```bash
composer create-project blcklab/panulat my-api
cd my-api
```

Set up the application:

```bash
composer setup
```

Start the local development server:

```bash
composer serve
```

Open:

```txt
http://127.0.0.1:8000
```

## Docker

Start the app and MySQL with Docker:

```bash
docker compose up --build
```

Open:

```txt
http://127.0.0.1:8080
```

The included `docker-compose.yml` is intended for local development.

## Documentation

Start here: [`docs/getting-started.md`](docs/getting-started.md)

## Useful Commands

Create common application files:

```bash
php black make:controller UserController
php black make:model User
php black make:migration create_users_table
```

Run framework commands:

```bash
php black migrate
php black db:seed
```

Run tests and checks:

```bash
composer test
composer stan
composer check
```

## Package Architecture

* `blcklab/panulat-core` — framework foundation
* `blcklab/panulat-jwt` — optional JWT authentication
* `blcklab/panulat-cli` — optional developer CLI
* `blcklab/panulat` — starter API project

## Production

For production installs:

```bash
composer install --no-dev --optimize-autoloader
php bin/console migrate
php bin/console optimize
```

Use safe production environment values:

```env
APP_ENV=production
APP_DEBUG=false
```

Point your web server to:

```txt
public/
```

Do not expose the project root publicly.

## License

MIT
