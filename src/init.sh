echo Installing the following packages...
echo    '--> doctrine/doctrine-fixtures-bundle (dev)'
echo    '--> fakerphp/faker (dev)'
echo    '--> nelmio/cors-bundle "^2.3"'
echo    '--> symfony/uid '
echo    '--> league/flysystem'
echo    '--> league/flysystem-bundle'

composer require --dev doctrine/doctrine-fixtures-bundle "^3.4" --no-update && \
  composer require nelmio/cors-bundle "^2.3" --no-update && \
  composer require league/flysystem --no-update && \
  composer require symfony/uid --no-update && \
  composer require --dev fakerphp/faker --no-update && \
  composer require league/flysystem-bundle --no-update && \
  composer update;

echo Updating database url...
mkdir var
mkdir var/dev
touch var/dev/data_dev.db
sed -i~ '/^DATABASE_URL=/s/=.*/="sqlite:\/\/\/%kernel\.project_dir%\/var\/dev\/data_dev\.db"/' .env
echo DOCKER_HTTP_PORT=8000 >> .env


echo Creating database and running migrations...
symfony console doctrine:database:create --env=dev && \
  symfony console make:migration -n && \
  symfony console doctrine:migrations:migrate -n && \
  symfony console doctrine:fixtures:load -n --group=develop
