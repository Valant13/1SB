<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210810173730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device_crafting_experience ADD research_point_id INT NOT NULL');
        $this->addSql('ALTER TABLE device_crafting_experience ADD CONSTRAINT FK_BD246A243DD39968 FOREIGN KEY (research_point_id) REFERENCES research_point (id)');
        $this->addSql('CREATE INDEX IDX_BD246A243DD39968 ON device_crafting_experience (research_point_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device_crafting_experience DROP FOREIGN KEY FK_BD246A243DD39968');
        $this->addSql('DROP INDEX IDX_BD246A243DD39968 ON device_crafting_experience');
        $this->addSql('ALTER TABLE device_crafting_experience DROP research_point_id');
    }
}
