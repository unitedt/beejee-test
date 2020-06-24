<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200611191942 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Session table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE `sessions` (
                `sess_id` VARBINARY(128) NOT NULL PRIMARY KEY,
                `sess_data` BLOB NOT NULL,
                `sess_lifetime` INTEGER UNSIGNED NOT NULL,
                `sess_time` INTEGER UNSIGNED NOT NULL
            )
      ');
}

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE `sessions`');
    }
}
