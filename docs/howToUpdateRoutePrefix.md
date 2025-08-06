# How to update route prefix

We need to make some changes in some files.

## [/config/packages/security.yaml](/config/packages/security.yaml)

```yml
security:

    ...

    access_control:
        ...
        - { path: ^/{PREFIX}/docs, roles: PUBLIC_ACCESS }
        - { path: ^/{PREFIX}/spec, roles: PUBLIC_ACCESS }

```

## /contracts/{contract}.yml

Path in this file must have no prefix because it will be configured in other file the next file.

## [/src/routes.yaml](/src/routes.yaml)

```yml
api:
    resource: ./routes-generated.yaml
    prefix: /{PREFIX}
```

## [/src/routes-generated.yaml](/src/routes-generated.yaml)

Path in this file must have no prefix because it will be read from the previous file.

## [/src/Infrastructure/Resources/viewer.html.twig](/src/Infrastructure/Resources/viewer.html.twig)

```twig
<!doctype html>
<html lang="en">
...
<body>
...
<rapi-doc
        ...
        spec-url="{PREFIX}/spec"
        ...
>
...

</rapi-doc>
</body>
</html>
```

## Every test where the called path is harcoded must be updated

Important mention to methods in [/tests/HelpersTest/GetRelationsSingleton.php](/tests/HelpersTest/GetRelationsSingleton.php)

```php
<?php

declare(strict_types=1);

namespace App\Tests\HelpersTest;

...

final class GetRelationsSingleton
{
    ...

    public function __get($name): string
    {
        ...

        self::$client->request('GET', '/{PREFIX}/'.$inflector->pluralize($name));

        ...
    }

    public function delete($name): void
    {
        unset(self::$values[$name]);
    }

    public function reset(): void
    {
        self::$instance = null;
    }

    public static function instance(KernelBrowser $browser): self
    {
        if (null === self::$instance) {
            ...

            foreach ($metadatas as $metadata) {
                
                ...

                self::$client->request('GET', '/{PREFIX}/'.$inflector->pluralize($name));

                ...
            }
        }

        ...
    }
}
```
