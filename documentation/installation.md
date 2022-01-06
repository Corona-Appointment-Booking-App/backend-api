# Installation local with docker

https://github.com/Corona-Appointment-Booking-App/docker-infrastructure

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

