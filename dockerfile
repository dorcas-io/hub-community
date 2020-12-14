FROM php:7.2-fpm
RUN apt-get update -y && apt-get install -y openssl zip unzip git nano
#RUN apt-get update -y && apt-get install -y openssl zip unzip git libxml2-dev curl nano
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer && \
#  composer \
# global require hirak/prestissimo --no-plugins --no-scripts

#RUN phpdismod xdebug

RUN docker-php-ext-install pdo pdo_mysql
#RUN docker-php-ext-install pdo pdo_mysql mbstring bcmath xml ctype fileinfo json tokenizer curl

##https://github.com/emcniece/docker-wordpress/blob/master/Dockerfile


RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    sed -i -e "s/^ memory_limit./memory_limit = 4G/g" -e "s/^ max_execution_time./max_execution_time = 0/g" /usr/local/etc/php/php.ini


# Install dependencies
COPY composer.json /var/www/composer.json
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer

# Copy codebase
COPY . /var/www

# Finish composer
RUN composer dump-autoload --no-scripts --no-dev --optimize




#COPY . /var/www
#RUN chown -R admin:admin /app
#RUN chmod 755 /app

RUN chown -R www-data:www-data /var/www/storage

RUN chmod -R u=rwx,g=rwx,o=rwx /var/www/storage
RUN chmod -R u=rwx,g=rwx,o=rw /var/www/storage/logs
RUN chmod u=rwx,g=rx,o=x /var/www/artisan

