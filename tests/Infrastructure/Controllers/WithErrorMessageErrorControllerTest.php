<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controllers;

use App\Infrastructure\Controllers\WithErrorMessageErrorController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * @internal
 *
 * @covers \App\Infrastructure\Controllers\WithErrorMessageErrorController
 */
class WithErrorMessageErrorControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testGetListPaginateNotFound(): void
    {
        // When
        $this->client->jsonRequest('GET', '/api/vitalSigns/invalidroute');
        $response = $this->client->getResponse();

        // Then
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        self::assertEquals(
            ['message' => 'No route found for "GET http://localhost/api/vitalSigns/invalidroute"'],
            json_decode($response->getContent(), true)
        );
    }

    public function testInvalidArgumentException(): void
    {
        // Given
        $exception = new InvalidArgumentException('test');
        $headers = FlattenException::createFromThrowable($exception)->getHeaders();
        $expectedResponse = new JsonResponse(
            ['message' => 'test'],
            Response::HTTP_BAD_REQUEST,
            $headers,
        );
        $sut = new WithErrorMessageErrorController();

        // When
        $response = ($sut)($exception);

        // Then
        self::assertEquals($expectedResponse, $response);
    }
}
