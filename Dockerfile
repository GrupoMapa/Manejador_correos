# Use the official PHP image as the base image
FROM php:8.2-apache

# Set the working directory inside the container
WORKDIR /var/www/html

# Install dependencies
ARG WWWGROUP
RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libssl-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip pgsql pdo pdo_pgsql
RUN apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

RUN curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer 

COPY ./ssl/certs/certificate.crt /etc/ssl/certs/certificate.crt
COPY ./ssl/private/private_unencrypted.key /etc/ssl/private/private.key

RUN chmod 600 /etc/ssl/private/private.key



# Enable Apache Rewrite module and SSL module
RUN a2enmod rewrite \ 
a2enmod ssl \
a2enmod headers

# Generate a self-signed SSL certificate
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/apache-selfsigned.key -out /etc/ssl/certs/apache-selfsigned.crt -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost"

# Configure Apache to use the SSL certificate
COPY apache-default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl

# Set the Apache document root
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Copy the entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY start-container /usr/local/bin/start-container

RUN chmod +x /usr/local/bin/docker-entrypoint

RUN chown -R www-data:www-data /var/www/html
RUN chmod +x /usr/local/bin/start-container

# Expose ports 80 and 443 for Apache
EXPOSE 80
EXPOSE 443



COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Establecer entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]