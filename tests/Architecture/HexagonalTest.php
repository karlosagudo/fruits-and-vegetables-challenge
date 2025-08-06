<?php

declare(strict_types=1);

namespace App\Tests\Architecture;

use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

final class HexagonalTest
{
    public function test_domain_does_not_depend_on_other_layers(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Domain'))
            ->shouldNotDependOn()
            ->classes(
                Selector::inNamespace('App\Application'),
                Selector::inNamespace('App\Infrastructure'),
            )
            ->because('Domain Must be PURE - this will break our architecture, implement it another way! see /docs/hexagonal.md')
        ;
    }

    public function test_application_does_not_depend_on_infra(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\Application'))
            ->canOnlyDependOn()
            ->classes(
                Selector::inNamespace('App\Application'),
                Selector::inNamespace('App\Domain'),
                Selector::inNamespace('App\Infrastructure\DTO'),
                // Exceptions
                Selector::inNamespace('Symfony\Component\EventDispatcher'),
                Selector::inNamespace('Symfony\Component\Uid'),
                // for getting user on security
                Selector::inNamespace('Symfony\Bundle\SecurityBundle'),
                Selector::inNamespace('Doctrine\Common\Collections'),
                Selector::inNamespace('Doctrine\ORM'),
                Selector::inNamespace('Evolucare\JwtSecurity'),
            )
            ->because('Application must depend only on domain and EventSubscriberInterface - this will break our architecture, implement it another way! see /docs/hexagonal.md')
        ;
    }
}
