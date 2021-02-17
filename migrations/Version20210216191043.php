<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210216191043 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action (id INT NOT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_47CC8C9277153098 ON action (code)');
        $this->addSql('CREATE TABLE role (id INT NOT NULL, code VARCHAR(30) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_57698A6A77153098 ON role (code)');
        $this->addSql('CREATE TABLE role_action (role_id INT NOT NULL, action_id INT NOT NULL, PRIMARY KEY(role_id, action_id))');
        $this->addSql('CREATE INDEX IDX_ECEA6D23D60322AC ON role_action (role_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ECEA6D239D32F035 ON role_action (action_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, username VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('CREATE TABLE user_role (user_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(user_id, role_id))');
        $this->addSql('CREATE INDEX IDX_2DE8C6A3A76ED395 ON user_role (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DE8C6A3D60322AC ON user_role (role_id)');
        $this->addSql('ALTER TABLE role_action ADD CONSTRAINT FK_ECEA6D23D60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE role_action ADD CONSTRAINT FK_ECEA6D239D32F035 FOREIGN KEY (action_id) REFERENCES action (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_role ADD CONSTRAINT FK_2DE8C6A3D60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA template');
        $this->addSql('ALTER TABLE role_action DROP CONSTRAINT FK_ECEA6D239D32F035');
        $this->addSql('ALTER TABLE role_action DROP CONSTRAINT FK_ECEA6D23D60322AC');
        $this->addSql('ALTER TABLE user_role DROP CONSTRAINT FK_2DE8C6A3D60322AC');
        $this->addSql('ALTER TABLE user_role DROP CONSTRAINT FK_2DE8C6A3A76ED395');
        $this->addSql('DROP TABLE action');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE role_action');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_role');
    }
}
