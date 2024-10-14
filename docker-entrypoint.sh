#!/bin/bash

# Set environment-specific configurations here

# Instalar controlador pgsql para PHP
apt-get update
apt-get install -y libpq-dev
docker-php-ext-install pdo_pgsql

# Increase PHP memory limit
sed -i 's/memory_limit = .*/memory_limit = 1G/' /usr/local/etc/php/php.ini

# Ejecutar el comando pasado como argumento o iniciar el servidor PHP
exec "$@"
# Start Apache in the foreground
apache2-foreground