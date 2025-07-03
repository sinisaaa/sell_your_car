<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220104114725 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD sms_notifications TINYINT(1) DEFAULT \'1\' NOT NULL, ADD push_notifications TINYINT(1) DEFAULT \'1\' NOT NULL, ADD discount_notification TINYINT(1) DEFAULT \'1\' NOT NULL, ADD sell_notification TINYINT(1) DEFAULT \'1\' NOT NULL, ADD buy_notifications TINYINT(1) DEFAULT \'1\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP sms_notifications, DROP push_notifications, DROP discount_notification, DROP sell_notification, DROP buy_notifications');
    }
}
