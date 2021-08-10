<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Catalog\ResearchPoint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210810171441 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    const RESEARCH_POINT_NAMES = [
        'Box',
        'Lightning',
        'Shield',
        'Gear'
    ];

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E4584665A');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE research_point ADD sort_order INT NOT NULL, ADD name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_312391C077153098 ON research_point (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_312391C05E237E06 ON research_point (name)');
    }

    public function postUp(Schema $schema): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $sortOrder = 0;
        foreach (self::RESEARCH_POINT_NAMES as $researchPointName) {
            $sortOrder += 10;

            $researchPoint = new ResearchPoint();
            $researchPoint->setCode(strtolower($researchPointName));
            $researchPoint->setName($researchPointName);
            $researchPoint->setSortOrder($sortOrder);

            $entityManager->persist($researchPoint);
            $entityManager->flush();
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE device DROP FOREIGN KEY FK_92FB68E4584665A');
        $this->addSql('ALTER TABLE device ADD CONSTRAINT FK_92FB68E4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP INDEX UNIQ_312391C077153098 ON research_point');
        $this->addSql('DROP INDEX UNIQ_312391C05E237E06 ON research_point');
        $this->addSql('ALTER TABLE research_point DROP sort_order, DROP name');
    }

    public function postDown(Schema $schema): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        foreach (self::RESEARCH_POINT_NAMES as $researchPointName) {
            /** @var ResearchPoint $researchPoint */
            $researchPoint = $entityManager->getRepository(ResearchPoint::class)
                ->findOneBy(['name' => $researchPointName]);

            $entityManager->remove($researchPoint);
            $entityManager->flush();
        }
    }
}
