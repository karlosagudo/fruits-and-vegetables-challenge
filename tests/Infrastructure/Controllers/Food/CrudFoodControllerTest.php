<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controllers\Food;

use App\Infrastructure\DB\Fixtures\FoodTestFixture;
use App\Infrastructure\DTO\FoodDTO;
use App\Tests\HelpersTest\GetRelationsSingleton;
use App\Tests\HelpersTest\ShapeTester;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Functional
 *
 * @covers \App\Domain\Models\Food
 * @covers \App\Infrastructure\Controllers\Food\CreateFoodController
 * @covers \App\Infrastructure\Controllers\Food\DeleteFoodController
 * @covers \App\Infrastructure\Controllers\Food\GetFoodController
 * @covers \App\Infrastructure\Controllers\Food\ListFoodController
 * @covers \App\Infrastructure\Controllers\Food\UpdateFoodController
 * @covers \App\Infrastructure\DTO\FoodDTO
 *
 * @internal
 */
class CrudFoodControllerTest extends WebTestCase
{
    public const API_CRUD_FOOD = '/api/Food/food';
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
        $this->client->jsonRequest('POST', self::API_CRUD_FOOD, ['not-valid' => 0x3434]);
        $this->assertResponseIsUnprocessable();
    }

    public function testCreate()
    {
        $mainId = FoodTestFixture::NUMBER_OBJECT + 1;
        $this->client->jsonRequest(
            'POST',
            self::API_CRUD_FOOD,
            [
                'id' => $mainId,
                'name' => 'test',
                'type' => 'fruit',
                'quantity' => 5,
                'unit' => 'g',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        return $mainId;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(int|string $mainId): void
    {
        $this->client->jsonRequest(
            'PUT',
            self::API_CRUD_FOOD.'/'.$mainId,
            [
                'id' => $mainId,
                'name' => 'testUpdated',
                'type' => 'fruit',
                'quantity' => 5,
                'unit' => 'g',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    /**
     * @depends testCreate
     */
    public function testGet(int|string $mainId): void
    {
        $this->client->jsonRequest(
            'GET',
            self::API_CRUD_FOOD.'/'.$mainId
        );
        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $result = json_decode($response->getContent(), true);
        $this->assertSame($result['id'], $mainId);

        $this->shapeTester->testObject(FoodDTO::class, $response->getContent());
    }

    public function testGetList(): void
    {
        $crawler = $this->client->jsonRequest(
            'GET',
            self::API_CRUD_FOOD.'s'
        );
        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $result = json_decode($response->getContent(), true);
        $this->shapeTester->testObject(
            FoodDTO::class,
            json_encode(reset($result))
        );
    }

    public function testGetListPaginate(): void
    {
        $randNumber = random_int(1, 3);
        $crawler = $this->client->jsonRequest(
            'GET',
            self::API_CRUD_FOOD.'s?pageNumber=0&pageSize='.$randNumber
        );
        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $result = json_decode($response->getContent(), true);
        $this->assertCount($randNumber, $result);
    }

    public function testGetListPaginateNotFound(): void
    {
        $pageNumber = random_int(100000, 300000);
        $pageSize = random_int(10, 50);
        $this->client->jsonRequest(
            'GET',
            self::API_CRUD_FOOD.'s'.
            '?pageNumber='.$pageNumber.'&pageSize='.$pageSize
        );
        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @depends testCreate
     */
    public function testDelete(int|string $mainId): void
    {
        $this->client->jsonRequest(
            'DELETE',
            self::API_CRUD_FOOD.'/'.$mainId
        );
        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->relations->delete('Food');
    }
}
