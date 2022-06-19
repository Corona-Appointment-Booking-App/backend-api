# Installation local with docker

*Tested on macOS Monterey and PhpStorm 2021.3*

In case of first installation just run, it will clone the repositories and start the docker containers

`./docker/scripts/init.sh`

## Reinitialize data

If you want to remove existing data and reinit the database just run

`./docker/scripts/init-symfony.sh`

***This should never run on production system!***

## Start the containers

`./docker/scripts/container-start.sh`

## Stop the containers

`./docker/scripts/container-stop.sh`

## Rebuild Frontend & Admin Assets

`./docker/scripts/build-js.sh`

## Running Tests

tests should run in docker container
`./docker/scripts/container-exec.sh`

### Unit Tests

`bin/phpunit --filter=Unit`

### Integration Tests

`bin/phpunit --filter=Integration`

### All tests

`bin/phpunit`

## XDebug Setup

* open Preferences -> PHP -> Servers
* click on the add button
* enter "coronaApp" in the name
* enter "corona-api.test" in the host
* enter "80" in the port
* make sure "Use path mappings" is checked
* enter "/var/www/backend-api" to "Absolute path on the server"
* click on save
* enable "Start Listening for PHP Debug Connections"
* set a breakpoint

*Xdebug should be working now if you are using postman or anything like add ?XDEBUG_SESSION=1 to the end of the request
url*

## Add /etc/hosts

`0.0.0.0 corona.test`

`0.0.0.0 corona-api.test`

## Api Health Check

`GET http://corona-api.test/api/health-check`

## Admin

admin will created automatically when running the init scripts

URL: `http://corona-api.test/admin`

| Email                 | Password              |
|-----------------------|-----------------------|
| admin@corona.test     | 123456                |

## Entrypoint Mappings

| Path in Local         | Path In Container     |
|-----------------------|-----------------------|
| services/backend-api  | /var/www/backend-api  |
| services/admin-app    | /var/www/admin-app    |
| services/frontend-app | /var/www/frontend-app |

# Commands

## Install Dependencies

`composer install`

## Execute migrations

`bin/console doctrine:migrations:migrate`

## Create opening days

`bin/console app:create-opening-day`

## Create opening times

`bin/console app:generate-opening-time`

## Create admin

`bin/console app:create-admin admin@corona.test 123456`

## Load Demo data

`bin/console doctrine:fixtures:load`

