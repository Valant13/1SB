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

    const RESEARCH_POINT_NAMES = [
        'Box',
        'Lightning',
        'Shield',
        'Gear'
    ];

    const RESEARCH_POINT_ICON_URLS = [
        '/research-point/box.svg',
        '/research-point/lightning.svg',
        '/research-point/shield.svg',
        '/research-point/gear.svg'
    ];

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

        $sortOrder = 0;
        foreach (self::RESEARCH_POINT_NAMES as $index => $researchPointName) {
            $sortOrder += 10;

            $researchPoint = new ResearchPoint();
            $researchPoint->setCode(strtolower($researchPointName));
            $researchPoint->setName($researchPointName);
            $researchPoint->setIconUrl(self::RESEARCH_POINT_ICON_URLS[$index]);
            $researchPoint->setSortOrder($sortOrder);

            $entityManager->persist($researchPoint);
            $entityManager->flush();
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

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
