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
final class Version20210810181321 extends AbstractMigration implements ContainerAwareInterface
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

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

    }

    public function postUp(Schema $schema): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $entityManager->persist($this->createResearchPoint('box', 'Box', '/research-point/box.svg', 10));
        $entityManager->persist($this->createResearchPoint('lightning', 'Lightning', '/research-point/lightning.svg', 20));
        $entityManager->persist($this->createResearchPoint('shield', 'Shield', '/research-point/shield.svg', 30));
        $entityManager->persist($this->createResearchPoint('gear', 'Gear', '/research-point/gear.svg', 40));

        $entityManager->flush();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    /**
     * @param string $code
     * @param string $name
     * @param string|null $iconUrl
     * @param int $sortOrder
     * @return ResearchPoint
     */
    private function createResearchPoint(string $code, string $name, ?string $iconUrl, int $sortOrder): ResearchPoint
    {
        $researchPoint = new ResearchPoint();

        $researchPoint->setCode($code);
        $researchPoint->setName($name);
        $researchPoint->setIconUrl($iconUrl);
        $researchPoint->setSortOrder($sortOrder);

        return $researchPoint;
    }
}
