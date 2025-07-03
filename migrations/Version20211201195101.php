<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211201195101 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article ADD featured_from DATETIME DEFAULT NULL, ADD featured_to DATETIME DEFAULT NULL, CHANGE featured featured_pending TINYINT(1) DEFAULT \'0\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE article DROP featured_from, DROP featured_to, CHANGE featured_pending featured TINYINT(1) DEFAULT \'0\'');
    }
}
