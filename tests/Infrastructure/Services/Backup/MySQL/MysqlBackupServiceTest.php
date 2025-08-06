<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Services\Backup\MySQL;

use App\Infrastructure\Service\Backup\MySQL\MysqlBackupService;
use App\Infrastructure\Service\Backup\MySQL\MysqlBackupServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group Unit
 *
 * @internal
 *
 * @covers \App\Infrastructure\Service\Backup\MySQL\MysqlBackupService
 */
final class MysqlBackupServiceTest extends WebTestCase
{
    private KernelBrowser $client;

    private MysqlBackupServiceInterface $mysqlBackupService;

    private LoggerInterface $logger;

    private string $dbHost;

    private string $disableTests;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $this->mysqlBackupService = $this->client->getContainer()->get(MysqlBackupServiceInterface::class);
        $this->logger = $this->client->getContainer()->get(LoggerInterface::class);
        $this->dbHost = $this->client->getContainer()->getParameter('app.db_host');
        $this->disableTests = $this->client->getContainer()->getParameter('test.disable_some_tests');
    }

    public function testFailBackup(): void
    {
        if ('1' == $this->disableTests) {
            $this->markTestSkipped('Can\'t run this test with Sqlite. Run it in local.');
        }

        $mysqlBackupService = new MysqlBackupService(
            $this->logger,
            "mysql://test:test@'.{$this->dbHost}.':3306/test?serverVersion=mariadb-10.11.2&charset=utf8",
        );

        $this->expectException(\ErrorException::class);
        $mysqlBackupService->execute();
    }

    public function testBackup(): void
    {
        if ('1' == $this->disableTests) {
            $this->markTestSkipped('Can\'t run this test with Sqlite. Run it in local.');
        }

        $backupFile = $this->mysqlBackupService->execute();
        $backupFilePath = $backupFile;

        $fileBackupIsCreated = is_file($backupFilePath);
        $this->assertTrue($fileBackupIsCreated);

        unlink($backupFilePath);
    }
}
