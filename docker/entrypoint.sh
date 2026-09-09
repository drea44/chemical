#!/bin/bash
set -e

echo "==> Starting Chemical Stock OS Container..."

# Adjust Apache port for cloud environments (Railway, Render, etc.)
PORT="${PORT:-80}"
echo "==> Configuring Apache to listen on port ${PORT}..."
echo "Listen ${PORT}" > /etc/apache2/ports.conf
sed -ri -e "s!<VirtualHost \*:[0-9]+>!<VirtualHost \*:${PORT}>!g" /etc/apache2/sites-available/000-default.conf

# Fix AH00534: ensure only mpm_prefork is loaded
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Setup storage and cache directories
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/www/html/database

# Handle SQLite if driver is sqlite
if [ "${DB_CONNECTION}" = "sqlite" ] || [ -z "${DB_CONNECTION}" ]; then
    DB_FILE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    if [ ! -f "${DB_FILE}" ]; then
        echo "==> Initializing SQLite database at ${DB_FILE}..."
        touch "${DB_FILE}"
    fi
    chmod 664 "${DB_FILE}" || true
fi

# Run database migrations
echo "==> Running database migrations..."
php artisan migrate --force

# Run initial seeders if needed
echo "==> Seeding initial data (if not already seeded)..."
php artisan db:seed --force || true

# Cache Laravel configuration & routes for production speed
echo "==> Optimizing application caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Set full permissions for Apache (www-data) AFTER all artisan commands
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
if [ -f "${DB_FILE}" ]; then
    chmod 666 "${DB_FILE}" || true
fi

echo "==> Application ready! Starting Apache web server..."
if [ "$#" -gt 0 ]; then
    exec "$@"
else
    exec apache2-foreground
fi
