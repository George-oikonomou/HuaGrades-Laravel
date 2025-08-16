#!/bin/bash
set -e

echo "[Entrypoint] Waiting for database..."
until php -r "
try {
    new PDO('mysql:host=' . getenv('MYSQL_HOST') . ';port=' . getenv('MYSQL_PORT') . ';dbname=' . getenv('MYSQL_DATABASE'),
             getenv('MYSQL_USER'),
             getenv('MYSQL_PASSWORD'));
    exit(0);
} catch (Exception \$e) {
    exit(1);
}" ; do
    sleep 2
done

echo "[Entrypoint] Database is ready."


if [ ! -f ".env" ] || ! grep -q "APP_KEY=" .env || [ -z "$(grep APP_KEY= .env | cut -d '=' -f2)" ]; then
    /usr/local/bin/setup.sh
fi

php artisan serve --host=0.0.0.0 --port=8000 &
npm run dev -- --host 0.0.0.0 --port 5174
