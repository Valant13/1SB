<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210807220846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE device (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, UNIQUE INDEX UNIQ_92FB68E4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_crafting_component (id INT AUTO_INCREMENT NOT NULL, device_id INT NOT NULL, material_id INT NOT NULL, qty INT UNSIGNED NOT NULL, INDEX IDX_40F0896094A4C7D4 (device_id), INDEX IDX_40F08960E308AC6F (material_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE device_crafting_experience (id INT AUTO_INCREMENT NOT NULL, device_id INT NOT NULL, qty INT UNSIGNED NOT NULL, INDEX IDX_BD246A2494A4C7D4 (device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, UNIQUE INDEX UNIQ_7CBE75954584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, auction_price_id INT NOT NULL, modification_user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, marketplace_price INT UNSIGNED DEFAULT NULL, image_url LONGTEXT DEFAULT NULL, wiki_page_url LONGTEXT DEFAULT NULL, modification_time DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_D34A04AD5E237E06 (name), UNIQUE INDEX UNIQ_D34A04AD639C1909 (auction_price_id), INDEX IDX_D34A04AD337A48F0 (modification_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_auction_price (id INT AUTO_INCREMENT NOT NULL, modification_user_id INT DEFAULT NULL, value INT UNSIGNED DEFAULT NULL, modification_time DATETIME DEFAULT NULL, INDEX IDX_5F31A722337A48F0 (modification_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE research_point (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, icon_url LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_calculation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, maximization_param VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_5FBCA0E5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_entity (id INT AUTO_INCREMENT NOT NULL, nickname VARCHAR(255) NOT NULL, registration_ip VARCHAR(255) NOT NULL, registration_time DATETIME NOT NULL, UNIQUE INDEX UNIQ_6B7A5F55A188FE64 (nickname), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_interest_device (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, device_id INT NOT NULL, is_excluded TINYINT(1) NOT NULL, INDEX IDX_617D2048A76ED395 (user_id), INDEX IDX_617D204894A4C7D4 (device_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_interest_material (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, material_id INT NOT NULL, is_excluded TINYINT(1) NOT NULL, INDEX IDX_F8725B7EA76ED395 (user_id), INDEX IDX_F8725B7EE308AC6F (material_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_inventory_material (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, material_id INT NOT NULL, qty INT UNSIGNED NOT NULL, INDEX IDX_64B029FFA76ED395 (user_id), INDEX IDX_64B029FFE308AC6F (material_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_log (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, user_nickname VARCHAR(255) NOT NULL, request_url LONGTEXT NOT NULL, request_ip VARCHAR(255) NOT NULL, request_time DATETIME NOT NULL, INDEX IDX_6429094EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_mining_material (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, material_id INT NOT NULL, is_acceptable TINYINT(1) NOT NULL, INDEX IDX_9DB42EF5A76ED395 (user_id), INDEX IDX_9DB42EF5E308AC6F (material_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE device_crafting_component ADD CONSTRAINT FK_40F0896094A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE device_crafting_component ADD CONSTRAINT FK_40F08960E308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
        $this->addSql('ALTER TABLE device_crafting_experience ADD CONSTRAINT FK_BD246A2494A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE material ADD CONSTRAINT FK_7CBE75954584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD639C1909 FOREIGN KEY (auction_price_id) REFERENCES product_auction_price (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD337A48F0 FOREIGN KEY (modification_user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE product_auction_price ADD CONSTRAINT FK_5F31A722337A48F0 FOREIGN KEY (modification_user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_calculation ADD CONSTRAINT FK_5FBCA0E5A76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_interest_device ADD CONSTRAINT FK_617D2048A76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_interest_device ADD CONSTRAINT FK_617D204894A4C7D4 FOREIGN KEY (device_id) REFERENCES device (id)');
        $this->addSql('ALTER TABLE user_interest_material ADD CONSTRAINT FK_F8725B7EA76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_interest_material ADD CONSTRAINT FK_F8725B7EE308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
        $this->addSql('ALTER TABLE user_inventory_material ADD CONSTRAINT FK_64B029FFA76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_inventory_material ADD CONSTRAINT FK_64B029FFE308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
        $this->addSql('ALTER TABLE user_log ADD CONSTRAINT FK_6429094EA76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_mining_material ADD CONSTRAINT FK_9DB42EF5A76ED395 FOREIGN KEY (user_id) REFERENCES user_entity (id)');
        $this->addSql('ALTER TABLE user_mining_material ADD CONSTRAINT FK_9DB42EF5E308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device_crafting_component DROP FOREIGN KEY FK_40F0896094A4C7D4');
        $this->addSql('ALTER TABLE device_crafting_experience DROP FOREIGN KEY FK_BD246A2494A4C7D4');
        $this->addSql('ALTER TABLE user_interest_device DROP FOREIGN KEY FK_617D204894A4C7D4');
        $this->addSql('ALTER TABLE device_crafting_component DROP FOREIGN KEY FK_40F08960E308AC6F');
        $this->addSql('ALTER TABLE user_interest_material DROP FOREIGN KEY FK_F8725B7EE308AC6F');
        $this->addSql('ALTER TABLE user_inventory_material DROP FOREIGN KEY FK_64B029FFE308AC6F');
        $this->addSql('ALTER TABLE user_mining_material DROP FOREIGN KEY FK_9DB42EF5E308AC6F');
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E4584665A');
        $this->addSql('ALTER TABLE material DROP FOREIGN KEY FK_7CBE75954584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD639C1909');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD337A48F0');
        $this->addSql('ALTER TABLE product_auction_price DROP FOREIGN KEY FK_5F31A722337A48F0');
        $this->addSql('ALTER TABLE user_calculation DROP FOREIGN KEY FK_5FBCA0E5A76ED395');
        $this->addSql('ALTER TABLE user_interest_device DROP FOREIGN KEY FK_617D2048A76ED395');
        $this->addSql('ALTER TABLE user_interest_material DROP FOREIGN KEY FK_F8725B7EA76ED395');
        $this->addSql('ALTER TABLE user_inventory_material DROP FOREIGN KEY FK_64B029FFA76ED395');
        $this->addSql('ALTER TABLE user_log DROP FOREIGN KEY FK_6429094EA76ED395');
        $this->addSql('ALTER TABLE user_mining_material DROP FOREIGN KEY FK_9DB42EF5A76ED395');
        $this->addSql('DROP TABLE device');
        $this->addSql('DROP TABLE device_crafting_component');
        $this->addSql('DROP TABLE device_crafting_experience');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_auction_price');
        $this->addSql('DROP TABLE research_point');
        $this->addSql('DROP TABLE user_calculation');
        $this->addSql('DROP TABLE user_entity');
        $this->addSql('DROP TABLE user_interest_device');
        $this->addSql('DROP TABLE user_interest_material');
        $this->addSql('DROP TABLE user_inventory_material');
        $this->addSql('DROP TABLE user_log');
        $this->addSql('DROP TABLE user_mining_material');
    }
}
