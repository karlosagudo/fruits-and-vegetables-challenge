# How to develop a library

## Set up repository

1. **Configure git repository**

    Inicialize the repo and add the gitlab remote.

    Copy [.gitignore](../../.gitignore).

    Copy [.gitlab-ci.yml](.gitlab-ci.yml) to configure gitlab pipelines.

2. **Makefile**

    Copy [Makefile](../../Makefile).

3. **Enviroment variables**

    Copy [.env](../../.env) and fill up the configuration.

4. **Evolucare Authentication**

    Copy [auth.json.dist](auth.json.dist) into `auth.json` and fill out your gitlab credentials.

    It is necesary to authenticate in gitlab when the library has evolucare dependencies.

5. **Hooks**

    1. Copy [captainhook.json](../../captainhook.json) to configure hooks.

    2. Copy [phpmd.ruleset.xml](../../phpmd.ruleset.xml) to configure PHPmd.

    3. Copy [phpunit.xml.dist](../../phpunit.xml.dist) to configure phpunit.

    4. Copy [phpstan.neon.dist](../../phpstan.neon.dist) to configure phpstan.

    5. Copy [.php-cs-fixer.dist.php](../../.php-cs-fixer.dist.php) to configure cs-fixer.

6. **composer.json**

    Configure dependencies managed with composer.

    It's important to configure composer.json as follows:

    ``` json
    {
        "name": "evolucare/LIBRARY_NAME_KEBAB_CASE",
        "description": "LIBRARY_DESCRIPTION",
        "type": "library",
        "license": "evolucare",
        "autoload": {
            "psr-4": {
                "Evolucare\\LIBRARY_NAME_UPPER_CAMEL_CASE\\": "src/"
            }
        },
        "scripts": {
            "post-autoload-dump": "vendor/bin/captainhook install --only-enabled"
        },
        "require": {
            "php": "8.2.*",
            ...
        },
        "require-dev": {
            "captainhook/captainhook": "^5.16",
            "friendsofphp/php-cs-fixer": "^3.26",
            "phpmd/phpmd": "^2.13",
            "phpstan/phpstan": "1.10.56",
            "phpunit/phpunit": "^9.5",
            "symfony/var-dumper": "6.*|7.*",
            ...
        }
    }
    ```

7. **docker**

    Copy [.docker](../../.docker) folder.

    Copy [auth.json.dist](auth.json.dist) into `.docker/auth.json` and fill out your gitlab credentials.

    It is necesary to authenticate in gitlab inside docker when the library has evolucare dependencies.

## Test behaviour in another project

In order to test changes without commiting and pushing every change, you can add the the library in the `composer.json` of the project whitch you whant to use the library into as a symlink as follows:

``` json
{
    ...
    "minimum-stability": "dev",
    ...
    "require": {
        ...
        "evolucare/LIBRARY_NAME_KEBAB_CASE": "dev-BRANCH_NAME",
        ...
    },
    "repositories": [
        {
            "type": "path",
            "url": LIBRARY_REPOSITORY_RELATIVE_PATH,
            "options": {
                "symlink": true
            }
        },
        ...
    ],
    ...
}
```

### Example of LIBRARY_REPOSITORY_RELATIVE_PATH

`"url": "./../library/"`

for:

```tree
Dev
├── library
└── project
```

run `composer install`

Now, the library is linked so every change will seem updated without running composer again.

## Deploy and use in another project

When merged into develop, create a tag into the repo.
