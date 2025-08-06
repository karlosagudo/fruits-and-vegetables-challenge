<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controllers\Food;

use App\Tests\HelpersTest\GetRelationsSingleton;
use App\Tests\HelpersTest\ShapeTester;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Functional
 *
 * @covers \App\Application\Food\CreateFoodCommand
 * @covers \App\Application\Food\CreateFoodCommandHandler
 * @covers \App\Domain\Models\Food
 * @covers \App\Infrastructure\Controllers\Food\CreateFoodController
 * @covers \App\Infrastructure\DTO\FoodDTO
 *
 * @internal
 */
class CreateFoodControllerTest extends WebTestCase
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

    public function testBadShapeOfObjectSent(): void
    {
        $this->client->jsonRequest('POST', '/api/Food/food', ['not-valid' => 0x3434]);
        $this->assertResponseIsUnprocessable();
    }

    public function testCreateSuccess(): void
    {
        $faker = Factory::create('fr_FR');
        $mainId = $faker->numberBetween(1000, 2000);
        $this->client->jsonRequest(
            'POST',
            '/api/Food/food',
            [
                'id' => $mainId, 'name' => $faker->text(255), 'type' => $faker->text(255), 'quantity' => $faker->numberBetween(1, 1000), 'unit' => $faker->text(255),
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}
