#!/usr/bin/env sh
set -eu

cd /app

log() {
    printf '%s\n' "[panulat] $*"
}

bool_enabled() {
    case "$(printf '%s' "${1:-}" | tr '[:upper:]' '[:lower:]')" in
        1|true|yes|y|on) return 0 ;;
        *) return 1 ;;
    esac
}

wait_for_mysql() {
    host="${DB_HOST:-mysql}"
    port="${DB_PORT:-3306}"
    user="${DB_USERNAME:-root}"
    password="${DB_PASSWORD:-}"

    log "waiting for MySQL at ${host}:${port}"

    tries=0
    while [ "$tries" -lt 60 ]; do
        if MYSQL_PWD="$password" mysqladmin ping \
            --host="$host" \
            --port="$port" \
            --user="$user" \
            --silent >/dev/null 2>&1; then
            log "MySQL is ready"
            return 0
        fi

        tries=$((tries + 1))
        sleep 2
    done

    log "MySQL was not ready after 120 seconds"
    return 1
}

mkdir -p bootstrap/cache storage/cache storage/logs database
chmod -R ug+rwX bootstrap/cache storage database 2>/dev/null || true

if [ ! -f .env ] && [ -f .env.example ]; then
    log "creating .env from .env.example"
    cp .env.example .env
fi

if [ ! -f composer.json ]; then
    log "composer.json is missing."
    exit 1
fi

if [ ! -f vendor/autoload.php ]; then
    log "installing Composer dependencies"
    composer install --no-interaction --prefer-dist
else
    log "refreshing Composer autoload"
    composer dump-autoload --no-interaction
fi

if [ "${DB_CONNECTION:-}" = "mysql" ]; then
    wait_for_mysql
fi

if bool_enabled "${PANULAT_AUTO_MIGRATE:-true}"; then
    log "running migrations"
    php bin/console migrate
fi

if bool_enabled "${PANULAT_AUTO_SEED:-true}"; then
    log "running seeders"
    php bin/console db:seed
fi

if [ "$#" -gt 0 ]; then
    exec "$@"
fi

log "starting Panulat at http://0.0.0.0:8080"
exec php -S 0.0.0.0:8080 -t public
