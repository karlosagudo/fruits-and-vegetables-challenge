<?php

declare(strict_types=1);

namespace App\Tests\Application\Shared;

use App\Application\Shared\GetUserInfoService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 *
 * @covers \App\Application\Shared\GetUserInfoService
 */
class GetUserInfoServiceTest extends TestCase
{
    public function testGetUserIdentifier(): void
    {
        // Given
        $expected = Uuid::v7()->toRfc4122();
        $security = $this->createMock(Security::class);
        $user = $this->createMock(UserInterface::class);
        $user->expects(self::once())->method('getUserIdentifier')
            ->willReturn($expected)
        ;
        $security->expects(self::once())->method('getUser')
            ->willReturn($user)
        ;
        $sut = new GetUserInfoService($security);

        // When
        $result = $sut->getUserIdentifier();

        // Then
        self::assertEquals($expected, $result);
    }

    public function testGetUserIdentifierNullUser(): void
    {
        // Given
        $security = $this->createMock(Security::class);
        $security->expects(self::once())->method('getUser')
            ->willReturn(null)
        ;
        $sut = new GetUserInfoService($security);

        // When
        $result = $sut->getUserIdentifier();

        // Then
        self::assertNull($result);
    }

    public function testGetUserNameHumanized(): void
    {
        // Given
        $expected = Uuid::v7()->toRfc4122();
        $security = $this->createMock(Security::class);
        $user = $this->createMock(UserInterface::class);
        $user->expects(self::once())->method('getUserIdentifier')
            ->willReturn($expected)
        ;
        $security->expects(self::once())->method('getUser')
            ->willReturn($user)
        ;
        $sut = new GetUserInfoService($security);

        // When
        $result = $sut->getUserNameHumanized();

        // Then
        self::assertEquals($expected, $result);
    }

    public function testGetUserNameHumanizedNullUser(): void
    {
        // Given
        $security = $this->createMock(Security::class);
        $security->expects(self::once())->method('getUser')
            ->willReturn(null)
        ;
        $sut = new GetUserInfoService($security);

        // When
        $result = $sut->getUserNameHumanized();

        // Then
        self::assertNull($result);
    }
}
