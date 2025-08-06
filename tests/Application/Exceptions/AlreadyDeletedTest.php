<?php

declare(strict_types=1);

namespace App\Tests\Application\Exceptions;

use App\Application\Exceptions\AlreadyDeleted;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \App\Application\Exceptions\AlreadyDeleted
 */
class AlreadyDeletedTest extends TestCase
{
    public function testDummyCoverage(): void
    {
        $sut = new AlreadyDeleted();
        $this->expectException(AlreadyDeleted::class);
        $this->expectExceptionMessage('test');

        throw new $sut('test');
    }
}
