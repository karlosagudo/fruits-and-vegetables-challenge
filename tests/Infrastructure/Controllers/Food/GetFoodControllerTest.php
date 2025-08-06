<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controllers\Food;

use App\Infrastructure\DTO\FoodDTO;
use App\Tests\HelpersTest\GetRelationsSingleton;
use App\Tests\HelpersTest\ShapeTester;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

/**
 * @group Functional
 *
 * @covers \App\Application\Food\GetFoodQuery
 * @covers \App\Application\Food\GetFoodQueryHandler
 * @covers \App\Domain\Models\Food
 * @covers \App\Infrastructure\Controllers\Food\GetFoodController
 * @covers \App\Infrastructure\DTO\FoodDTO
 *
 * @internal
 */
class GetFoodControllerTest extends WebTestCase
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

    public function testGetEntityOk(): void
    {
        $entityId = $this->relations->food;
        $this->client->jsonRequest(
            'GET',
            '/api/Food/food/'.$entityId
        );
        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->shapeTester->testObject(FoodDTO::class, $response->getContent());
    }

    public function testGetEntityNotFound(): void
    {
        $entityId = Uuid::v7()->toRfc4122();
        $this->client->jsonRequest(
            'GET',
            '/api/Food/food/'.$entityId
        );
        $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
