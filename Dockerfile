FROM php:7.4-fpm
#RUN apt-get update -y && apt-get install -y openssl zip unzip git nano
RUN apt-get update -y && apt-get install -y openssl zip unzip git libxml2-dev curl nano libonig-dev
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/dorcas-hub

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#RUN phpdismod xdebug

#RUN docker-php-ext-install pdo pdo_mysql ctype fileinfo json tokenizer curl
RUN docker-php-ext-install pdo pdo_mysql mbstring bcmath xml

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    sed -i -e "s/^ memory_limit./memory_limit = 4G/g" -e "s/^ max_execution_time./max_execution_time = 0/g" /usr/local/etc/php/php.ini


# Install dependencies
COPY composer.json /var/www/dorcas-hub/composer.json
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer

# Copy codebase
COPY . /var/www/dorcas-hub

# Finish composer
RUN composer dump-autoload --no-scripts --no-dev --optimize

RUN chown -R www-data:www-data /var/www/dorcas-hub/storage
RUN chmod -R u=rwx,g=rwx,o=rwx /var/www/dorcas-hub/storage
RUN chmod -R u=rwx,g=rwx,o=rwx /var/www/dorcas-hub/bootstrap/cache
RUN chmod -R u=rwx,g=rwx,o=rw /var/www/dorcas-hub/storage/logs
RUN touch /var/www/dorcas-hub/storage/logs/laravel.log
RUN chmod u=rwx,g=rw,o=rw /var/www/dorcas-hub/storage/logs/laravel.log
RUN chmod u=rwx,g=rx,o=x /var/www/dorcas-hub/artisan