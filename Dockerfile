FROM php:8.2-apache

# PHP extensions needed by this app (mysqli + pdo_mysql)
RUN set -eux; \
    docker-php-ext-install mysqli pdo_mysql; \
    a2dismod -f mpm_event mpm_worker || true; \
    a2enmod mpm_prefork rewrite headers; \
    sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY . .

# Runtime-writable directories
RUN mkdir -p /var/www/html/public/uploads /var/www/html/logs /var/www/html/cache \
    && chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/logs /var/www/html/cache

RUN chmod +x /var/www/html/railway-start.sh

EXPOSE 8080
CMD ["/var/www/html/railway-start.sh"]
