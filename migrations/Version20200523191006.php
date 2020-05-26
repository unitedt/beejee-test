<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20200523191006 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create task table';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            CREATE TABLE `task` (
              id               INT          NOT NULL AUTO_INCREMENT,
              userName         VARCHAR(255) NOT NULL,
              email            VARCHAR(255) NOT NULL,
              content          VARCHAR(255) NOT NULL,
              isCompleted      TINYINT(1)   NOT NULL DEFAULT 0,
              isChangedByAdmin TINYINT(1)   NOT NULL DEFAULT 0,
              
              PRIMARY KEY (id)
      )');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE task');
    }
}
