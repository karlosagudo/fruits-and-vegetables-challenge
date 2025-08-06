# Openapi Symfony

This project is intended to be used as a template, so if this project it's not a template please clone this project, delete the .git folder, and the push to another repository.

## :rocket: What is included

- [OpenApiExtractor](https://gitlab.evolucare.io/dpi/hopitalweb/api/openapi-ddd-extractor) A tool to extract code from an openapi contract.
- [Makefile](Makefile) with some usefull commands to work with our project. (Use ```makefile help``` to list them)
- Pipelines with standards [.gitlab-ci.yml](.gitlab-ci.yml)
- [GitHooks](docs/githooks.md) Automatic formatting / cleaning, and testing on your local git precommit
- An automated [backup system](docs/backups.md).
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

If there is some problems on docker with the mariadb container:
```
sudo chown ${WHOAMI}:${WHOAMI} -R ./mysql
```

## :writing_hand: How to write the api contract

Using these conventions the generator will extract more information about your domain:

[Best Practices for Rest API DESIGN](docs/how-to-define-the-api.md)

You have a working example in the contract folder called: blogpost.yml
With examples of one-to-many and many-to-one

## :wrench: How to use

Since this project it's just a template, you should procure to it an open-api / swagger contract
and then just execute this command, replacing <WHATEVERCONTRACT.yml> for the real file

```bash
vendor/bin/openapi-extractor contracts/<WHATEVERCONTRACT.yml>
```

If you dont want to overwrite your previous code, you can tell the extractor to extract to a different folder:

```bash
vendor/bin/openapi-extractor contracts/<WHATEVERCONTRACT.yml> -o anotherFolder
```

Or you can tell the extractor to read a ignoredFiles:

```bash
vendor/bin/openapi-extractor contracts/<WHATEVERCONTRACT.yml> -i contracts/ignoredFiles.txt
```

The ignoredFiles can be any file, for example ignoreGenerator.txt and in there, you can put several relatives paths for files in each line:
```
src/Application/Measure/ListMeasureVersionsQueryHandler.php
src/Application/Measure/ListMeasureVersionsQuery.php
```

In this case, a warning will appear, but the files will never be overwritten:

[warning] Ignored file:/home/cagudo/workspace/example-tutorial-blog/src/Application/Measure/ListMeasureQueryHandler.php

There is a lot more information about this extractor in the [project folder](https://gitlab.evolucare.io/dpi/hopitalweb/api/openapi-ddd-extractor).

After executing it, we advise to execute the init :

```bash
src/init.sh
```

This init will generate a first migration, and will run all fixtures also for test.

After this you can enter into /api and you will have a swagger doc file to test .

## :books: More documentation

### Hexagonal and DDD
If you are new to hexagonal architecture or DDD please read this documentation:

1. [Hexagonal Architecture](docs/hexagonal.md)
2. [Commands and Queries](docs/commands-and-queries.md)
3. [Domain Events](docs/domain-events.md)

### Migrations
There is a detailed explanation about how we use and run migrations both locally and in the dev server.
Please refer to the [migrations document](docs/migrations.md).


## :woman_playing_water_polo: Develop / play around
For develop/ play with different contracts :

```bash
git clean -f -d && rm -rf tests && vendor/bin/openapi-extractor contracts/<YOURCONTRACT>.yml -o ./
```
