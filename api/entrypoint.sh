#!/bin/sh

echo "Aguardando o banco de dados..."
until nc -z -v -w30 db 3306; do
          sleep 1
done
echo "Banco de dados está pronto!"

php artisan migrate:install

php artisan migrate --force

php artisan serve --host=0.0.0.0
