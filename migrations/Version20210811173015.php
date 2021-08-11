<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811173015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_interest (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_8CB3FE67A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_inventory (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, user_inventory_id INT NOT NULL, UNIQUE INDEX UNIQ_B1CDC7D2A76ED395 (user_id), INDEX IDX_B1CDC7D28922DB5B (user_inventory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_mining (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, user_mining_id INT NOT NULL, UNIQUE INDEX UNIQ_408F716BA76ED395 (user_id), INDEX IDX_408F716B69E037B7 (user_mining_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_interest ADD CONSTRAINT FK_8CB3FE67A76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_inventory ADD CONSTRAINT FK_B1CDC7D2A76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_inventory ADD CONSTRAINT FK_B1CDC7D28922DB5B FOREIGN KEY (user_inventory_id) REFERENCES user_inventory (id)');
        $this->addSql('ALTER TABLE user_mining ADD CONSTRAINT FK_408F716BA76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_mining ADD CONSTRAINT FK_408F716B69E037B7 FOREIGN KEY (user_mining_id) REFERENCES user_mining (id)');
        $this->addSql('ALTER TABLE user_interest_device ADD user_interest_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_interest_device ADD CONSTRAINT FK_617D2048F38E361C FOREIGN KEY (user_interest_id) REFERENCES user_interest (id)');
        $this->addSql('CREATE INDEX IDX_617D2048F38E361C ON user_interest_device (user_interest_id)');
        $this->addSql('ALTER TABLE user_interest_material ADD user_interest_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_interest_material ADD CONSTRAINT FK_F8725B7EF38E361C FOREIGN KEY (user_interest_id) REFERENCES user_interest (id)');
        $this->addSql('CREATE INDEX IDX_F8725B7EF38E361C ON user_interest_material (user_interest_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_interest_device DROP FOREIGN KEY FK_617D2048F38E361C');
        $this->addSql('ALTER TABLE user_interest_material DROP FOREIGN KEY FK_F8725B7EF38E361C');
        $this->addSql('ALTER TABLE user_inventory DROP FOREIGN KEY FK_B1CDC7D28922DB5B');
        $this->addSql('ALTER TABLE user_mining DROP FOREIGN KEY FK_408F716B69E037B7');
        $this->addSql('DROP TABLE user_interest');
        $this->addSql('DROP TABLE user_inventory');
        $this->addSql('DROP TABLE user_mining');
        $this->addSql('DROP INDEX IDX_617D2048F38E361C ON user_interest_device');
        $this->addSql('ALTER TABLE user_interest_device DROP user_interest_id');
        $this->addSql('DROP INDEX IDX_F8725B7EF38E361C ON user_interest_material');
        $this->addSql('ALTER TABLE user_interest_material DROP user_interest_id');
    }
}
