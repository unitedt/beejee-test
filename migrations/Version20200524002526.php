<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200524002526 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Insert test tasks';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user1', 'user1@mail.test', 'Task One', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user1', 'user1@mail.test', 'Task Two', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user1', 'user1@mail.test', 'Task Three', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user1', 'user1@mail.test', 'Task Four', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user1', 'user1@mail.test', 'Task Five', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user2', 'user2@mail.test', 'Task One', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user2', 'user2@mail.test', 'Task Two', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user2', 'user2@mail.test', 'Task Three', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user2', 'user2@mail.test', 'Task Four', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user2', 'user2@mail.test', 'Task Five', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user3', 'user3@mail.test', 'Task One', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user3', 'user3@mail.test', 'Task Two', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user3', 'user3@mail.test', 'Task Three', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user3', 'user3@mail.test', 'Task Four', 0, 0)");
        $this->addSql("INSERT INTO `task` (userName, email, content, isCompleted, isChangedByAdmin) VALUES ('user3', 'user3@mail.test', 'Task Five', 0, 0)");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("TRUNCATE TABLE `task`");

    }
}
