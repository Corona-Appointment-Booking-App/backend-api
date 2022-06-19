#!/bin/bash

docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/frontend-app && rm -rf node_modules && yarn install && yarn run build"
docker exec -ti corona-symfony-app_php-fpm bash -c "cd /var/www/admin-app && rm -rf node_modules && yarn install && yarn run build"
docker exec -ti corona-symfony-app_php-fpm bash -c "rm -rf /var/www/backend-api/public/admin && mv /var/www/admin-app/dist /var/www/backend-api/public/admin"