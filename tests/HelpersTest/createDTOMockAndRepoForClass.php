<?php

declare(strict_types=1);

namespace App\Tests\HelpersTest;

use Symfony\Component\Uid\Uuid;

trait createDTOMockAndRepoForClass
{
    /**
     * Please pass the STRING!! ex:
     * [$fakeObject, $fakeDTO, $fakeRepo] = $this->createMockDTOAndRepo('Bandage');.
     */
    public function createMockDTOAndRepo(string $class, bool $repoCalled = true, ?string $idType = 'uuid'): array
    {
        $class = ucfirst($class);
        $classDTOMock = $this->createMock('\App\Infrastructure\DTO\\'.$class.'DTO');
        $id = match ($idType) {
            default => Uuid::v7()->toRfc4122(),
            'int' => 1,
            'string' => '1',
        };
        $classDTOMock->id = $id;
        $classMock = $this->createMock('\App\Domain\Models\\'.$class);
        $classMock->id = $id;
        $classRepo = $this->createMock('\App\Domain\Repositories\\'.$class.'RepositoryInterface');
        if ($repoCalled) {
            $classRepo->expects(self::once())
                ->method('find')
                ->with($classMock->id, true)
                ->willReturn($classMock)
            ;
        }

        return [$classMock, $classDTOMock, $classRepo];
    }
}
