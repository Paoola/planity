#!/bin/bash

# Abort on error
set -e

ENV=$1

# Apache gets grumpy about PID files pre-existing
rm -f /run/apache2/apache2.pid

source /etc/apache2/envvars
tail -F /var/log/apache2/*  &
exec apache2 -D FOREGROUND
