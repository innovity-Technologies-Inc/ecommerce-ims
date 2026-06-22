#!/bin/bash
set -e

# Copy env template if not exists
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
fi

# Wait for DB connection
echo "Waiting for database connection..."
until php -r "
\$host = getenv('DB_HOST') ?: '127.0.0.1';
\$port = getenv('DB_PORT') ?: '3306';
\$user = getenv('DB_USERNAME') ?: 'root';
\$pass = getenv('DB_PASSWORD') ?: '';
try {
    \$dbh = new PDO(\"mysql:host=\$host;port=\$port\", \$user, \$pass);
    exit(0);
} catch (PDOException \$e) {
    exit(1);
}
" &> /dev/null; do
    echo "Database is unavailable - sleeping"
    sleep 2
done
echo "Database is up!"

# In local environment, run developer-specific commands
if [ "${APP_ENV}" = "local" ]; then
    echo "Running in local development mode..."

    # Install dependencies if vendor folder doesn't exist
    if [ ! -d "vendor" ]; then
        echo "vendor folder not found. Running composer install..."
        composer install --no-interaction
    fi

    # Generate app key if not set
    if ! grep -q "APP_KEY=base64:" .env || [ -z "$(grep APP_KEY= .env | cut -d= -f2)" ]; then
        echo "Generating application key..."
        php artisan key:generate --no-interaction
    fi

    echo "Running database migrations..."
    php artisan migrate --no-interaction

    echo "Clearing cached configurations to prevent Docker path issues..."
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear


    # Run seeders only if they haven't been run before
    if [ ! -f "storage/logs/seeded.lock" ]; then
        echo "First boot detected. Running database seeders..."
        php artisan db:seed --no-interaction || true
        echo "Pre-compiling views to speed up initial load time..."
        php artisan view:cache
        touch storage/logs/seeded.lock
    else
        echo "Database seeders already ran previously. Skipping to protect existing data..."
    fi

else
    echo "Running in production mode..."

    # Generate app key if not set (fallback)
    if ! grep -q "APP_KEY=base64:" .env || [ -z "$(grep APP_KEY= .env | cut -d= -f2)" ]; then
        echo "Generating application key..."
        php artisan key:generate --no-interaction
    fi


    echo "Running database migrations..."
    php artisan migrate --force --no-interaction

    echo "Caching configurations, routes, and views..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

fi

# Adjust folder permissions
echo "Adjusting file permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Execute CMD passed to docker container
exec "$@"
