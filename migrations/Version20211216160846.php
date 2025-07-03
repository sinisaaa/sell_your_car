<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211216160846 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article_category_field_option (id INT AUTO_INCREMENT NOT NULL, field_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C66F1058443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_category_field_option ADD CONSTRAINT FK_C66F1058443707B0 FOREIGN KEY (field_id) REFERENCES article_category_field (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article_category_field CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('DROP TABLE article_category_field_option');
    }
}
