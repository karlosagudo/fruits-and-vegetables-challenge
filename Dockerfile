### Production base
FROM php:8.2-fpm-alpine AS app_php_local

WORKDIR /srv/app

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# persistent / runtime deps
RUN apk add --no-cache \
    acl \
    fcgi \
    file \
    gettext \
    git \
  ;


RUN set -eux; \
    install-php-extensions \
      intl \
      zip \
      apcu \
      opcache \
      gd \
      pdo_pgsql \
      pdo_mysql \
    ; \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
    mkdir -p /var/run/php;

ENV APP_ENV=dev

# Remove root password
RUN passwd -d root

RUN apk add --no-cache mysql-client

RUN apk add --no-cache wget curl net-tools

COPY .docker/php/php.ini $PHP_INI_DIR/
COPY .docker/php/conf.d $PHP_INI_DIR/conf.d
COPY .docker/php/xdebug-toggle.sh /usr/local/bin
RUN chmod +x /usr/local/bin/xdebug-toggle.sh
ENV XDEBUG_ENABLED true

RUN apk add --no-cache bash
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash
RUN apk add symfony-cli
RUN apk add git
RUN apk add --update linux-headers
RUN apk add --no-cache $PHPIZE_DEPS
RUN pecl install xdebug && docker-php-ext-enable xdebug

ARG USER_ID
ARG GROUP_ID
ARG USER_NAME
ARG GIT_FULLNAME
ARG GIT_EMAIL

RUN echo ${USER_NAME} ${USER_ID} ${GROUP_ID}
RUN userdel -f ${USER_NAME} || true
RUN if getent group ${USER_NAME} ; then groupdel ${USER_NAME}; fi || true
RUN adduser -u ${USER_ID} -g ${USER_ID} -h /home/${USER_NAME} -D ${USER_NAME}

COPY .docker/auth.json /home/${USER_NAME}/.composer/

RUN git config --global user.name "${GIT_FULLNAME}"
RUN git config --global user.email "${GIT_EMAIL}"

RUN chmod +x bin/console; sync

COPY .docker/local-entrypoint.sh /usr/local/bin/local-entrypoint
RUN chmod +x /usr/local/bin/local-entrypoint

ENTRYPOINT ["local-entrypoint"]
