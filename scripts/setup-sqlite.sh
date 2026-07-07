#!/usr/bin/env sh
set -eu

[ -f .env ] || cp .env.example .env
mkdir -p database
[ -f database/database.sqlite ] || touch database/database.sqlite
php bin/console migrate
php bin/console db:seed

echo "SQLite database is ready. Run: composer serve"
