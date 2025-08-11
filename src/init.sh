echo Creating database and running migrations...
bin/console doctrine:database:create --env=dev && \
  bin/console make:migration -n && \
  bin/console doctrine:migrations:migrate -n && \
  bin/console doctrine:fixtures:load -n --group=develop
