# Git Hooks

We use [CaptainHooks](https://github.com/captainhookphp/captainhook) to create githooks - automatic actions that will be triggered on git events.
We can create actions before commiting a code, or before merging, etc..

All the configuration is done in captaihook.json file:

## How to activate
After a composer install, install the precommit , and preparemessage hooks.

## Precommit

- Lint (checks the php files are correct, similar to..they will "compile") `php -l file`
- Check Lock file (doesn't allow to do composer.json updates without corresponding composer.lock updates)
- [Mess detector](https://phpmd.org/documentation/index.html) The rules are in phpmd.ruleset.xml, also generated a baseline to avoid new commits in old mistakes and not stopping developing
- Phpunit
- [PhpStan](https://phpstan.org/user-guide/getting-started) the configuration file is on phpstan.neon.dist with level 6
- [PHP Cs Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/tree/master) With rules for symfony on php-cs-fixer.dist.php
- [Conventional commits](https://github.com/ramsey/conventional-commits) as a help to write the commit message.
- [Infection](https://github.com/infection/infection) for mutant testing.

## Prepare message

We use conventional commits:
- **build**: Changes that affect the build system or external dependencies (composer.json/composer.lock docker, etc..)
- **ci**: Changes to our CI configuration files and scripts (changes in the .ci)
- **docs**: Documentation only changes
- **feat**: A new feature
- **fix**: A bug fix
- **perf**: A code change that improves performance
- **refactor**: A code change that neither fixes a bug nor adds a feature
- **style**: Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc)
- **test**: Adding missing tests or correcting existing tests