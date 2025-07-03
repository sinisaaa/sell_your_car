<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Exception;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211216140027 extends AbstractMigration
{


    /**
     * @param Schema $schema
     * @throws Exception
     */
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery("INSERT INTO `article_category` (`id`, `name`) VALUES ('1', 'cars')");
        $this->connection->executeQuery("INSERT INTO `article_category` (`id`, `name`) VALUES ('2', 'motorcycles')");
        $this->connection->executeQuery("INSERT INTO `article_category` (`id`, `name`) VALUES ('3', 'trucks')");
        $this->connection->executeQuery("INSERT INTO `article_category` (`id`, `name`) VALUES ('4', 'wheels')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
    }
}
