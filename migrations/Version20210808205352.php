<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Catalog\Material;
use App\Entity\Catalog\Product;
use App\Entity\Catalog\ProductAuctionPrice;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210808205352 extends AbstractMigration implements ContainerAwareInterface
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

    const MATERIAL_NAMES = [
        'Valkite',
        'Ajatite',
        'Talkite',
        'Bastium',
        'Aegisium',
        'Oninum',
        'Charodium',
        'Merkerium',
        'Lukium',
        'Targium',
        'Tengium',
        'Ilmatrium',
        'Ukonium',
        'Vokarium',
        'Exorium',
        'Ymrium',
        'Naflite',
        'Kutonium',
        'Arkanium',
        'Corazium',
        'Xhalium',
        'Daltium',
        'Ice',
        'Surtrite',
        'Nhurgite',
        'Haderite',
        'Karnite'
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

        foreach (self::MATERIAL_NAMES as $materialName) {
            $material = new Material();
            $material->setProduct(new Product());
            $material->getProduct()->setAuctionPrice((new ProductAuctionPrice()));
            $material->getProduct()->setName($materialName);

            $entityManager->persist($material);
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

        foreach (self::MATERIAL_NAMES as $materialName) {
            /** @var Product $product */
            $product = $entityManager->getRepository(Product::class)->findOneBy(['name' => $materialName]);
            $auctionPrice = $product->getAuctionPrice();

            $entityManager->remove($product);
            $entityManager->remove($auctionPrice);
            $entityManager->flush();
        }
    }
}
