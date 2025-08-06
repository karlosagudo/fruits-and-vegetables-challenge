<?php

declare(strict_types=1);

namespace <namespace>;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class <className> extends AbstractMigration
    {
    public function getDescription(): string
    {
    return '';
    }

    public function up(Schema $schema): void
    {
    $this->skipIf(!str_contains(get_class($this->connection->getDatabasePlatform()), 'MariaDb')
    && !str_contains(get_class($this->connection->getDatabasePlatform()), 'Mysql'));

    <up>
    }

    public function down(Schema $schema): void
    {
    $this->skipIf(!str_contains(get_class($this->connection->getDatabasePlatform()), 'MariaDb')
    && !str_contains(get_class($this->connection->getDatabasePlatform()), 'Mysql'));

    <down>
    }
}