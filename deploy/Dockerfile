FROM php:7.4-cli

ENV DB_PORT 3307
ENV DB_HOST 172.17.0.1
ENV APP_ROOT_URI http://localhost:81

RUN apt-get update && apt-get install -y --no-install-recommends \
  vim \
  libzip-dev \
  mc \
  ssh \
  wget \
  host \
  iproute2\
  unzip

# Install PHP Extensions
RUN docker-php-ext-install zip \
  && docker-php-ext-install pdo_mysql \
  && docker-php-ext-install opcache \
  && docker-php-ext-enable opcache

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('SHA384', 'composer-setup.php') === rtrim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && mv composer.phar /usr/local/bin/composer

# Download RoadRunner
ENV RR_VERSION 1.8.0
RUN mkdir /tmp/rr \
  && cd /tmp/rr \
  && echo "{\"require\":{\"spiral/roadrunner\":\"${RR_VERSION}\"}}" >> composer.json \
  && composer install \
  && vendor/bin/rr get-binary -l /usr/local/bin \
  && rm -rf /tmp/rr

# Copy RoadRunner config
COPY config /etc/roadrunner

COPY . /var/www

WORKDIR /var/www

ENTRYPOINT ["/usr/local/bin/rr", "serve", "-d", "-c", "/etc/roadrunner/.rr.yaml"]