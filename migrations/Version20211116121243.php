<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211116121243 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        $this->connection->beginTransaction();

        try {
            $rolesArray = ['Admin' => 'ROLE_ADMIN', 'User' => 'ROLE_USER'];

            foreach ($rolesArray as $roleName => $roleCode) {
                $this->connection->executeQuery("INSERT INTO role (`name`,`code`) VALUES (?, ?);", [$roleName, $roleCode]);
            }

            $this->connection->commit();
        } catch(\Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function down(Schema $schema): void
    {
    }
}
