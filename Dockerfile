FROM php:7.3-cli

RUN apt-get update && apt-get install -y \
    libssh-dev

RUN pecl install swoole && docker-php-ext-enable swoole

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY . /usr/src/myapp

WORKDIR /usr/src/myapp

CMD ["php", "./public/index.php"]