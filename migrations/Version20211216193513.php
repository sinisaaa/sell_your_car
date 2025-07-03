<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211216193513 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article_manufacturer_model (id INT NOT NULL, manufacturer_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_1F0E731CA23B42D (manufacturer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_manufacturer_model ADD CONSTRAINT FK_1F0E731CA23B42D FOREIGN KEY (manufacturer_id) REFERENCES article_manufacturer (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article_category_field CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
