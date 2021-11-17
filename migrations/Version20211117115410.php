<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211117115410 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE location ADD region_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('CREATE INDEX IDX_5E9E89CB98260155 ON location (region_id)');
        $this->addSql('ALTER TABLE user ADD location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64964D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_8D93D64964D218E ON user (location_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB98260155');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP INDEX IDX_5E9E89CB98260155 ON location');
        $this->addSql('ALTER TABLE location DROP region_id');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64964D218E');
        $this->addSql('DROP INDEX IDX_8D93D64964D218E ON `user`');
        $this->addSql('ALTER TABLE `user` DROP location_id');
    }
}
