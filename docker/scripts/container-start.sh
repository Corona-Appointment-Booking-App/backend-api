#!/bin/bash
COMMAND="docker-compose"
$COMMAND down && $COMMAND up -d
