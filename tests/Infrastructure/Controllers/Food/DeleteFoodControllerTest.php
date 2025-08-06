<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controllers\Food;

use App\Tests\HelpersTest\GetRelationsSingleton;
use App\Tests\HelpersTest\ShapeTester;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Functional
 *
 * @covers \App\Application\Food\DeleteFoodCommand
 * @covers \App\Application\Food\DeleteFoodCommandHandler
 * @covers \App\Domain\Models\Food
 * @covers \App\Infrastructure\Controllers\Food\DeleteFoodController
 * @covers \App\Infrastructure\DTO\FoodDTO
 *
 * @internal
 */
class DeleteFoodControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private GetRelationsSingleton $relations;
    private ShapeTester $shapeTester;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->relations = GetRelationsSingleton::instance($this->client);
        $this->shapeTester = ShapeTester::instance();
    }

    public function testResponseIsSuccessful(): void
    {
        $this->markTestSkipped('IMPLEMENT TEST IN '.__FILE__.':'.__LINE__);
    }
}
