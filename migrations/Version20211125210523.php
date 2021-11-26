<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211125210523 extends AbstractMigration
{

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Exception
     */
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery("INSERT INTO role (`name`,`code`) VALUES (?, ?);", ['Car Dealer', 'ROLE_CAR_DEALER']);
    }

    public function down(Schema $schema): void
    {
    }
}
