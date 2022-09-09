FROM php:7.3-fpm-alpine
COPY crt.crt /usr/local/share/ca-certificates/cert.crt
RUN cat /usr/local/share/ca-certificates/cert.crt >> /etc/ssl/certs/ca-certificates.crt && \
    apk --no-cache add \
    curl
RUN docker-php-ext-install pdo_mysql
WORKDIR /var/www/html/
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
COPY . .
RUN composer install