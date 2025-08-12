# Fruits and Vegetables Challenge

Complete API with a small front with 100% coverage in unit testing, also functional testing, and 100% infection code coverage.

Most of the code has been generated with a custom handmade generator from openapi to symfony hexagonal.

## :star: How to install / use with docker

There is a makefile to start easily with the project, but the correct sequence could be.

Copy the env.local.dist and generate your own env.local.
DOCKER_HTTP_PORT=8090 Its were the front (swagger-style) api will exists:
enter in the browser http://localhost:8090 and will redirect you to http://localhost:8090/api/Foods

### With Docker already installed:

Execute to build the images
```make build```

To install project , composer dependencies etc..
```make init```

To run web server:
```make start```

### Run container

To run docker container. This will land into `/app` container folder where we can find our repository.
```make shell```

### Some problems on docker:
If there is problems with the docker setup for the permissions on var/cache or var/logs:
ONLY IN LOCAL:
```
chmod 777 -R var
```

Could happen with the mariadb container, some permissions problems with the mysql volume:
```
sudo chown ${WHOAMI}:${WHOAMI} -R ./mysql
```
And maybe
```
sudo rm -rf ./mysql
```

After this steps you should create the database:
0. ```make shell```
1. ```bin/console doctrine:database:create```
2. ```bin/console doctrine:migrations:migrate```

If you also want so fake data to interact with the API, we have generated some fixtures for dev environment and the
ones used in test environment for the functional testing.

3. ```bin/console doctrine:fixtures:load -n --group=develop```


## :rocket: What is included

- [Makefile](Makefile) with some usefull commands to work with our project. (Use ```makefile help``` to list them)
- [GitHooks](docs/githooks.md) Automatic formatting / cleaning, and testing on your local git precommit
- [Automatic changelog and release system](docs/releases.md)

This project its based in a standard symfony project including:

| Components        | Version | Dev |
|-------------------|---------|-----|
| PHP               | 8.2     |     |
| [Symfony](https://symfony.com)           | latest  |     |
| [PHPUnit](https://symfony.com/doc/current/testing.html)           | latest  | *   |
| [PHPStan](https://github.com/phpstan/phpstan-symfony)           | latest  | *   |
| [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)      | latest  | *   |
| [PHP mess detector](https://phpmd.org/documentation/index.html) | latest  | *   |


## :star: How to install / use with docker

Follow instructions on: [install](docs/install.md)

## :books: More documentation

### Hexagonal and DDD
If you are new to hexagonal architecture or DDD please read this documentation:

1. [Hexagonal Architecture](docs/hexagonal.md)
2. [Commands and Queries](docs/commands-and-queries.md)
3. [Domain Events](docs/domain-events.md)

### Migrations
There is a detailed explanation about how we use and run migrations both locally and in the dev server.
Please refer to the [migrations document](docs/migrations.md).
