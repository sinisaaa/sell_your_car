<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211201173216 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, exchange TINYINT(1) DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, urgent TINYINT(1) DEFAULT \'0\' NOT NULL, fixed TINYINT(1) DEFAULT \'0\' NOT NULL, negotiable TINYINT(1) DEFAULT \'0\' NOT NULL, featured TINYINT(1) DEFAULT \'0\', conditions VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, discontinued TINYINT(1) NOT NULL, status TINYINT(1) DEFAULT \'1\' NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, sold_at DATETIME DEFAULT NULL, hits INT NOT NULL, INDEX IDX_23A0E6664D218E (location_id), INDEX IDX_23A0E66A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6664D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE article');
    }
}
