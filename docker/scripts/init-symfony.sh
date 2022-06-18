#!/bin/bash

docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && rm -rf vendor && composer install --no-interaction"

docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console doctrine:database:drop --force --no-interaction"
docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console doctrine:database:create --no-interaction"
docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console doctrine:migrations:migrate --no-interaction"

docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console doctrine:database:drop --env=test --force --no-interaction"
docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console doctrine:database:create --env=test --no-interaction"
docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console doctrine:migrations:migrate --env=test --no-interaction"

docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console lexik:jwt:generate-keypair --overwrite --no-interaction"
docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console lexik:jwt:check-config --no-interaction"

docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console app:create-opening-day --no-interaction"
docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console app:generate-opening-time --no-interaction"

docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console app:create-admin admin@corona.test 123456 --no-interaction"
docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/backend-api && bin/console doctrine:fixtures:load --no-interaction"
