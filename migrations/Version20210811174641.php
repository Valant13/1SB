<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811174641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_interest_device DROP FOREIGN KEY FK_617D2048A76ED395');
        $this->addSql('DROP INDEX IDX_617D2048A76ED395 ON user_interest_device');
        $this->addSql('ALTER TABLE user_interest_device DROP user_id');
        $this->addSql('ALTER TABLE user_interest_material DROP FOREIGN KEY FK_F8725B7EA76ED395');
        $this->addSql('DROP INDEX IDX_F8725B7EA76ED395 ON user_interest_material');
        $this->addSql('ALTER TABLE user_interest_material DROP user_id');
        $this->addSql('ALTER TABLE user_inventory DROP FOREIGN KEY FK_B1CDC7D28922DB5B');
        $this->addSql('DROP INDEX IDX_B1CDC7D28922DB5B ON user_inventory');
        $this->addSql('ALTER TABLE user_inventory DROP user_inventory_id');
        $this->addSql('ALTER TABLE user_inventory_material DROP FOREIGN KEY FK_64B029FFA76ED395');
        $this->addSql('DROP INDEX IDX_64B029FFA76ED395 ON user_inventory_material');
        $this->addSql('ALTER TABLE user_inventory_material CHANGE user_id user_inventory_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_inventory_material ADD CONSTRAINT FK_64B029FF8922DB5B FOREIGN KEY (user_inventory_id) REFERENCES user_inventory (id)');
        $this->addSql('CREATE INDEX IDX_64B029FF8922DB5B ON user_inventory_material (user_inventory_id)');
        $this->addSql('ALTER TABLE user_mining DROP FOREIGN KEY FK_408F716B69E037B7');
        $this->addSql('DROP INDEX IDX_408F716B69E037B7 ON user_mining');
        $this->addSql('ALTER TABLE user_mining DROP user_mining_id');
        $this->addSql('ALTER TABLE user_mining_material DROP FOREIGN KEY FK_9DB42EF5A76ED395');
        $this->addSql('DROP INDEX IDX_9DB42EF5A76ED395 ON user_mining_material');
        $this->addSql('ALTER TABLE user_mining_material CHANGE user_id user_mining_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_mining_material ADD CONSTRAINT FK_9DB42EF569E037B7 FOREIGN KEY (user_mining_id) REFERENCES user_mining (id)');
        $this->addSql('CREATE INDEX IDX_9DB42EF569E037B7 ON user_mining_material (user_mining_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_interest_device ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_interest_device ADD CONSTRAINT FK_617D2048A76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_617D2048A76ED395 ON user_interest_device (user_id)');
        $this->addSql('ALTER TABLE user_interest_material ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_interest_material ADD CONSTRAINT FK_F8725B7EA76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F8725B7EA76ED395 ON user_interest_material (user_id)');
        $this->addSql('ALTER TABLE user_inventory ADD user_inventory_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_inventory ADD CONSTRAINT FK_B1CDC7D28922DB5B FOREIGN KEY (user_inventory_id) REFERENCES user_inventory (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B1CDC7D28922DB5B ON user_inventory (user_inventory_id)');
        $this->addSql('ALTER TABLE user_inventory_material DROP FOREIGN KEY FK_64B029FF8922DB5B');
        $this->addSql('DROP INDEX IDX_64B029FF8922DB5B ON user_inventory_material');
        $this->addSql('ALTER TABLE user_inventory_material CHANGE user_inventory_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_inventory_material ADD CONSTRAINT FK_64B029FFA76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_64B029FFA76ED395 ON user_inventory_material (user_id)');
        $this->addSql('ALTER TABLE user_mining ADD user_mining_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_mining ADD CONSTRAINT FK_408F716B69E037B7 FOREIGN KEY (user_mining_id) REFERENCES user_mining (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_408F716B69E037B7 ON user_mining (user_mining_id)');
        $this->addSql('ALTER TABLE user_mining_material DROP FOREIGN KEY FK_9DB42EF569E037B7');
        $this->addSql('DROP INDEX IDX_9DB42EF569E037B7 ON user_mining_material');
        $this->addSql('ALTER TABLE user_mining_material CHANGE user_mining_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_mining_material ADD CONSTRAINT FK_9DB42EF5A76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9DB42EF5A76ED395 ON user_mining_material (user_id)');
    }
}
