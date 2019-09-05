<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190903114847 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE form_log CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE name name VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE roles roles VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE phone phone VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE form_log CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE name name VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(180) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE roles roles VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE password password VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE phone phone VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
