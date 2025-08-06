FIXTURES
========

How to run fixtures on prod:

1. Generate a deploy job in the pipelines of ui or api (we need to relaunch the deploy at the end)
2. ssh into the machine
3. go to folder workspace and check the last deploy date on the docker compose files:
```shell
ls -loat
```
4. enter into the docker ssh:
```shell
docker exec -ti yourapp-php /bin/sh
```
5. Touch the composer.json and delete the "extra" entry
```shell
vi composer.json
```
6. Install the dev dependency: (Since the docker runs only with composer prod dependencies). This step will ask for your username and password (apply em, later we will destroy this container)
```shell
composer require doctrine/doctrine-fixtures-bundle --dev
```
7. Touch config/bundles.php and set the `doctrineFixture` bundle for "all" environments
```shell
vi config/bundles.php
```
8. Clear cache:
```shell
bin/console c:c
```
9. Execute the fixtures: for tests and for develop.
```shell
# for tests
php bin/console doctrine:fixtures:load --group test --env=test
# for develop
php bin/console doctrine:fixtures:load --group dev --env=dev
```
10. Exit docker
11. Re-trigger deploy job
12. Execute to check the last deploy date on the docker compose files
```shell
ls -loat
```
13. check that the project page is working
