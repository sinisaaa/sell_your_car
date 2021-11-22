<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211121133619 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_email_confirm_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token LONGTEXT NOT NULL, crated_at DATETIME NOT NULL, INDEX IDX_703DD68BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_email_confirm_token ADD CONSTRAINT FK_703DD68BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE active active TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_email_confirm_token');
        $this->addSql('ALTER TABLE `user` CHANGE active active TINYINT(1) DEFAULT \'1\' NOT NULL');
    }
}
