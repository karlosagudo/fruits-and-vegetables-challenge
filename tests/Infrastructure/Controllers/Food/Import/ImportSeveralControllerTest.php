<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controllers\Food\Import;

use App\Infrastructure\DB\Fixtures\FoodTestFixture;
use App\Tests\HelpersTest\GetRelationsSingleton;
use App\Tests\HelpersTest\ShapeTester;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Functional
 *
 * @covers \App\Application\Food\ImportSeveralCommand
 * @covers \App\Application\Food\ImportSeveralCommandHandler
 * @covers \App\Domain\Models\Food
 * @covers \App\Infrastructure\Controllers\Food\Import\ImportSeveralController
 * @covers \App\Infrastructure\DTO\FoodDTO
 *
 * @internal
 */
class ImportSeveralControllerTest extends WebTestCase
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
        $this->client->jsonRequest('POST', '/api/Food/food/import', ['not-valid' => 0x3434]);
        $this->assertResponseIsUnprocessable();
    }

    public function testCorrectRefParameter(): void
    {
        $id = random_int(1000, 2000);
        $this->client->jsonRequest(
            'POST',
            '/api/Food/food/import',
            [['id' => $id,
                'name' => 'sample text',
                'type' => 'fruit',
                'quantity' => 6,
                'unit' => 'sample text',
            ]]
        );
        $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->client->jsonRequest(
            'GET',
            '/api/Food/foods',
        );
        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $result = json_decode($response->getContent(), true);
        $this->assertCount(FoodTestFixture::NUMBER_OBJECT + 1, $result);
    }
}
