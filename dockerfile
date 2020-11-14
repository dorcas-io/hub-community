FROM php:7.2-fpm
RUN apt-get update -y && apt-get install -y openssl zip unzip git nano
#RUN apt-get update -y && apt-get install -y openssl zip unzip git libxml2-dev curl nano

WORKDIR /var/www/dorcas-business-hub

#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer && composer global require hirak/prestissimo --no-plugins --no-scripts

#RUN phpdismod xdebug

RUN docker-php-ext-install pdo pdo_mysql
#RUN docker-php-ext-install pdo pdo_mysql mbstring bcmath xml ctype fileinfo json tokenizer curl


# Install dependencies
COPY composer.json /var/www/dorcas-business-hub/composer.json
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer

# Copy codebase
COPY . /var/www/dorcas-business-hub

# Finish composer
RUN composer dump-autoload --no-scripts --no-dev --optimize




#COPY . /var/www/dorcas-hub
#RUN chown -R admin:admin /app
#RUN chmod 755 /app

RUN chown -R www-data:www-data /var/www/dorcas-hub/storage

RUN chmod -R u=rwx,g=rwx,o=rwx /var/www/dorcas-hub/bootstrap/cache
RUN chmod -R u=rwx,g=rwx,o=rwx /var/www/dorcas-hub/storage
RUN chmod -R u=rwx,g=rwx,o=rw /var/www/dorcas-hub/storage/logs
RUN touch /var/www/dorcas-hub/storage/logs/laravel.log
RUN chmod u=rwx,g=rw,o=rw /var/www/dorcas-hub/storage/logs/laravel.log
RUN chmod u=rwx,g=rx,o=x /var/www/dorcas-hub/artisan


#RUN composer install
#CMD php artisan serve --host=0.0.0.0 --port=18002
#EXPOSE 18002

EXPOSE 18222
CMD ["php-fpm"]