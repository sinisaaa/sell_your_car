<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211213171328 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_rating (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, rated_user_id INT DEFAULT NULL, rating INT NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_BDDB3D1FA76ED395 (user_id), INDEX IDX_BDDB3D1FA8957C46 (rated_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_rating ADD CONSTRAINT FK_BDDB3D1FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user_rating ADD CONSTRAINT FK_BDDB3D1FA8957C46 FOREIGN KEY (rated_user_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_rating');
    }
}
