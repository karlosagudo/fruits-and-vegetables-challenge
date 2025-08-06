<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Services\Backup;

use App\Infrastructure\Service\Backup\DbBackupStoreService;
use App\Infrastructure\Service\Backup\DbBackupStoreServiceInterface;
use App\Infrastructure\Service\Backup\MySQL\MysqlBackupServiceInterface;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @group Functional
 *
 * @internal
 *
 * @covers \App\Infrastructure\Service\Backup\DbBackupStoreService
 */
final class DbBackupStoreServiceTest extends WebTestCase
{
    private KernelBrowser $client;

    private DbBackupStoreServiceInterface $dbBackupStoreService;

    private FilesystemOperator $backupStorage;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->dbBackupStoreService = $this->client->getContainer()->get(DbBackupStoreServiceInterface::class);
        $this->backupStorage = $this->client->getContainer()->get('backup.storage');
    }

    public function testWriteFailStore(): void
    {
        $backupFilePathMock = sys_get_temp_dir().'/mockFile.sql';
        file_put_contents($backupFilePathMock, microtime());
        $mysqlBackupServiceMock = $this->createMock(MysqlBackupServiceInterface::class);
        $mysqlBackupServiceMock->expects(self::once())
            ->method('execute')
            ->willReturn($backupFilePathMock)
        ;

        $filesystemOperatorMock = $this->createMock(FilesystemOperator::class);
        $filesystemOperatorMock
            ->expects(self::once())
            ->method('write')
            ->will($this->throwException(new FileException()))
        ;

        $dbBackupStoreService = new DbBackupStoreService(
            $filesystemOperatorMock,
            $mysqlBackupServiceMock,
        );

        $this->expectException(FileException::class);
        $dbBackupStoreService->execute();
    }

    public function testFailBackup(): void
    {
        $mysqlBackupServiceMock = $this->createMock(MysqlBackupServiceInterface::class);
        $mysqlBackupServiceMock->expects(self::once())
            ->method('execute')
            ->will($this->throwException(new \ErrorException()))
        ;

        $filesystemOperatorMock = $this->createMock(FilesystemOperator::class);

        $dbBackupStoreService = new DbBackupStoreService(
            $filesystemOperatorMock,
            $mysqlBackupServiceMock,
        );

        $this->expectException(\ErrorException::class);
        $dbBackupStoreService->execute();
    }

    public function testBackup(): void
    {
        $backupFilePathMock = sys_get_temp_dir().'/mockFile.sql';
        file_put_contents($backupFilePathMock, microtime());
        $mysqlBackupServiceMock = $this->createMock(MysqlBackupServiceInterface::class);
        $mysqlBackupServiceMock->expects(self::once())
            ->method('execute')
            ->willReturn($backupFilePathMock)
        ;

        $dbBackupStoreService = new DbBackupStoreService(
            $this->backupStorage,
            $mysqlBackupServiceMock,
        );
        $storedFile = $dbBackupStoreService->execute();

        $fileBackupIsStored = $this->backupStorage->fileExists($storedFile);
        $this->assertTrue($fileBackupIsStored);
    }

    public function testRemoveOlder()
    {
        // Set modified time to older datetime
        $oldModifiedTime = strtotime('2000-03-13 12:00:00');

        // Mock and create backupfile.
        $filename = 'mockFile.sql';
        $backupFilePathMock = sys_get_temp_dir().'/'.$filename;
        file_put_contents($backupFilePathMock, microtime());
        $mysqlBackupServiceMock = $this->createMock(MysqlBackupServiceInterface::class);
        $mysqlBackupServiceMock->expects(self::once())
            ->method('execute')
            ->willReturn($backupFilePathMock)
        ;

        $dbBackupStoreService = new DbBackupStoreService(
            $this->backupStorage,
            $mysqlBackupServiceMock,
        );
        $storedFile = $dbBackupStoreService->execute();

        $fileBackupIsStored = $this->backupStorage->fileExists($storedFile);
        $this->assertTrue($fileBackupIsStored);

        // Mock and delete older backups.

        // Mock listContents method and return a list with the single file that we've created,
        //      but with old modification time, then should be deleted by the service.
        $backupStorageMock = $this->createMock($this->backupStorage::class);
        $backupStorageMock->expects(self::once())
            ->method('listContents')
            ->willReturn(
                new DirectoryListing([
                    new FileAttributes(
                        '/'.$filename,
                        1000,
                        'public',
                        $oldModifiedTime,
                    ),
                ])
            )
        ;

        $backupStorage = $this->backupStorage;
        $backupStorageMock
            ->method('delete')
            ->with($filename)
            ->willReturnCallback(function ($path) use ($backupStorage) {
                return $backupStorage->delete($path);
            })
        ;

        $dbBackupStoreService = new DbBackupStoreService(
            $backupStorageMock,
            $mysqlBackupServiceMock,
        );
        $dbBackupStoreService->removeFilesOlderThan();

        $fileBackupIsStored = $this->backupStorage->fileExists($storedFile);
        $this->assertFalse($fileBackupIsStored);
    }
}
