<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211220152342 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE article_article_category_field (id INT AUTO_INCREMENT NOT NULL, article_id INT NOT NULL, field_id INT NOT NULL, value LONGTEXT DEFAULT NULL, INDEX IDX_4B4F05287294869C (article_id), INDEX IDX_4B4F0528443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_article_category_field_article_category_field_option (article_article_category_field_id INT NOT NULL, article_category_field_option_id INT NOT NULL, INDEX IDX_72ED47EECB0B7A67 (article_article_category_field_id), INDEX IDX_72ED47EE9321D466 (article_category_field_option_id), PRIMARY KEY(article_article_category_field_id, article_category_field_option_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_article_category_field ADD CONSTRAINT FK_4B4F05287294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_article_category_field ADD CONSTRAINT FK_4B4F0528443707B0 FOREIGN KEY (field_id) REFERENCES article_category_field (id)');
        $this->addSql('ALTER TABLE article_article_category_field_article_category_field_option ADD CONSTRAINT FK_72ED47EECB0B7A67 FOREIGN KEY (article_article_category_field_id) REFERENCES article_article_category_field (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_article_category_field_article_category_field_option ADD CONSTRAINT FK_72ED47EE9321D466 FOREIGN KEY (article_category_field_option_id) REFERENCES article_category_field_option (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article_article_category_field_article_category_field_option DROP FOREIGN KEY FK_72ED47EECB0B7A67');
        $this->addSql('DROP TABLE article_article_category_field');
        $this->addSql('DROP TABLE article_article_category_field_article_category_field_option');
    }
}
