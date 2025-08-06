<?php

declare(strict_types=1);

namespace App\Tests\HelpersTest;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @internal
 *
 * @coversNothing
 */
class ShapeTester extends TestCase
{
    private static ?ShapeTester $instance = null;
    private static ?Serializer $serializer = null;

    public static function instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
            $normalizers = [
                new BackedEnumNormalizer(),
                new DateTimeNormalizer(),
                new UidNormalizer(),
                new ObjectNormalizer(),
            ];
            self::$serializer = new Serializer($normalizers, [new JsonEncoder()]);
        }

        return self::$instance;
    }

    /**
     * @param class-string $class
     */
    public function testObject(string $class, string $data): void
    {
        $deserialize = self::$serializer?->deserialize(
            $data,
            $class,
            'json',
            [
                'collect_denormalization_errors' => true,
            ]
        );
        static::assertInstanceOf($class, $deserialize);
    }
}
