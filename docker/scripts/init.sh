#!/bin/bash

source $PWD/docker/repository-url.env

git clone $REPOSITORY_URL_FRONTEND $PWD/services/frontend-app
git clone $REPOSITORY_URL_ADMIN $PWD/services/admin-app

$PWD/docker/scripts/container-start.sh
$PWD/docker/scripts/init-symfony.sh
$PWD/docker/scripts/build-js.sh
$PWD/docker/scripts/container-exec.sh