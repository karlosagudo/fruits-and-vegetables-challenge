<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controllers\Food;

use App\Domain\Models\FoodType;
use App\Tests\HelpersTest\ShapeTester;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Functional
 *
 * @covers \App\Domain\Models\Food
 * @covers \App\Infrastructure\Controllers\Food\ListFoodController
 * @covers \App\Infrastructure\DTO\FoodDTO
 *
 * @internal
 */
class ListFoodControllerTest extends WebTestCase
{
    public const API_CRUD_FOOD = '/api/Food/food';
    private KernelBrowser $client;
    private ShapeTester $shapeTester;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->shapeTester = ShapeTester::instance();
    }

    public function testGetListPaginateWithType(): void
    {
        $randNumber = random_int(1, 3);
        $type = FoodType::cases()[random_int(0, count(FoodType::cases()) - 1)];
        $this->client->jsonRequest(
            'GET',
            self::API_CRUD_FOOD.'s?pageNumber=0&pageSize='.$randNumber.'&type='.$type->value
        );
        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $result = json_decode($response->getContent(), true);
        $this->assertCount($randNumber, $result);
        foreach ($result as $food) {
            $this->assertSame($type->value, $food['type']);
        }
    }
}
