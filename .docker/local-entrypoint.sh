#!/bin/sh
set -e

echo "Running entrypoint ..."

#exec docker-php-entrypoint "$@"

echo "End."

tail -f /dev/null