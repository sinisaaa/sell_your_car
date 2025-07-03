<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211217080659 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ADD manufacturer_id INT DEFAULT NULL, ADD manufacturer_model_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A23B42D FOREIGN KEY (manufacturer_id) REFERENCES article_manufacturer (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66FCAD2771 FOREIGN KEY (manufacturer_model_id) REFERENCES article_manufacturer_model (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66A23B42D ON article (manufacturer_id)');
        $this->addSql('CREATE INDEX IDX_23A0E66FCAD2771 ON article (manufacturer_model_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A23B42D');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66FCAD2771');
        $this->addSql('DROP INDEX IDX_23A0E66A23B42D ON article');
        $this->addSql('DROP INDEX IDX_23A0E66FCAD2771 ON article');
        $this->addSql('ALTER TABLE article DROP manufacturer_id, DROP manufacturer_model_id');
    }
}
