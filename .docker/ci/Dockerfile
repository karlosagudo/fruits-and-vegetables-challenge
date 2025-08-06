FROM docker.evolucare.io/evolucare/lib/dependency/docker/php/8.2:latest as composer_build

WORKDIR /srv/app/

COPY ./../../ .

RUN set -eux; \
	composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
	composer clear-cache

FROM docker.evolucare.io/evolucare/lib/dependency/docker/php/8.2:latest as app_php

COPY --from=composer_build /srv/app/.env /srv/app/composer.json /srv/app/composer.lock /srv/app/symfony.lock ./
COPY --from=composer_build /srv/app/bin bin/
COPY --from=composer_build /srv/app/config config/
COPY --from=composer_build /srv/app/migrations migrations/
COPY --from=composer_build /srv/app/public public/
COPY --from=composer_build /srv/app/src src/
COPY --from=composer_build /srv/app/vendor vendor/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
