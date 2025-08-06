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
 * @covers \App\Application\Food\UpdateFoodCommand
 * @covers \App\Application\Food\UpdateFoodCommandHandler
 * @covers \App\Domain\Models\Food
 * @covers \App\Infrastructure\Controllers\Food\UpdateFoodController
 * @covers \App\Infrastructure\DTO\FoodDTO
 *
 * @internal
 */
class UpdateFoodControllerTest extends WebTestCase
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
        $id = $this->relations->food;
        $this->client->jsonRequest('PUT', '/api/Food/food/'.$id, ['not-valid' => 0x3434]);
        $this->assertResponseIsUnprocessable();
    }

    public function testCorrectRefParameter(): void
    {
        $crawler = $this->client->jsonRequest(
            'PUT',
            '/api/Food/food/',
            ['id' => 6,
                'name' => 'sample text',
                'type' => 'sample text',
                'quantity' => 6,
                'unit' => 'sample text',
            ]
        );
        $this->markTestSkipped('IMPLEMENT TEST IN '.__FILE__.':'.__LINE__);
    }
}
