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
final class Version20210810181318 extends AbstractMigration implements ContainerAwareInterface
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

        $entityManager->persist($this->createMaterial('Valkite', 'https://wiki.starbasegame.com/images/9/94/Material-icons_Valkite.png', 'https://wiki.starbasegame.com/index.php/Valkite'));
        $entityManager->persist($this->createMaterial('Ajatite', 'https://wiki.starbasegame.com/images/1/17/Material-icons_Ajatite.png', 'https://wiki.starbasegame.com/index.php/Ajatite'));
        $entityManager->persist($this->createMaterial('Talkite', 'https://wiki.starbasegame.com/images/8/82/Material-icons_Talkite.png', 'https://wiki.starbasegame.com/index.php/Talkite'));
        $entityManager->persist($this->createMaterial('Bastium', 'https://wiki.starbasegame.com/images/e/ef/Material-icons_Bastium.png', 'https://wiki.starbasegame.com/index.php/Bastium'));
        $entityManager->persist($this->createMaterial('Aegisium', 'https://wiki.starbasegame.com/images/e/ee/Material-icons_Aegisium.png', 'https://wiki.starbasegame.com/index.php/Aegisium'));
        $entityManager->persist($this->createMaterial('Oninum', 'https://wiki.starbasegame.com/images/f/fa/Material-icons_Oninum.png', 'https://wiki.starbasegame.com/index.php/Oninum'));
        $entityManager->persist($this->createMaterial('Charodium', 'https://wiki.starbasegame.com/images/1/1f/Material-icons_Charodium.png', 'https://wiki.starbasegame.com/index.php/Charodium'));
        $entityManager->persist($this->createMaterial('Merkerium', 'https://wiki.starbasegame.com/images/f/fa/Material-icons_Merkerium.png', 'https://wiki.starbasegame.com/index.php/Merkerium'));
        $entityManager->persist($this->createMaterial('Lukium', 'https://wiki.starbasegame.com/images/9/96/Material-icon_Lukium.png', 'https://wiki.starbasegame.com/index.php/Lukium'));
        $entityManager->persist($this->createMaterial('Targium', 'https://wiki.starbasegame.com/images/6/62/Material-icons_Targium.png', 'https://wiki.starbasegame.com/index.php/Targium'));
        $entityManager->persist($this->createMaterial('Tengium', 'https://wiki.starbasegame.com/images/8/89/Material-icons_Tengium.png', 'https://wiki.starbasegame.com/index.php/Tengium'));
        $entityManager->persist($this->createMaterial('Ilmatrium', 'https://wiki.starbasegame.com/images/8/86/Material-icons_Ilmatrium.png', 'https://wiki.starbasegame.com/index.php/Ilmatrium'));
        $entityManager->persist($this->createMaterial('Ukonium', 'https://wiki.starbasegame.com/images/9/92/Material-icons_Ukonium.png', 'https://wiki.starbasegame.com/index.php/Ukonium'));
        $entityManager->persist($this->createMaterial('Vokarium', 'https://wiki.starbasegame.com/images/d/da/Material-icons_Vokarium.png', 'https://wiki.starbasegame.com/index.php/Vokarium'));
        $entityManager->persist($this->createMaterial('Exorium', 'https://wiki.starbasegame.com/images/9/93/Material-icons_Exorium.png', 'https://wiki.starbasegame.com/index.php/Exorium'));
        $entityManager->persist($this->createMaterial('Ymrium', 'https://wiki.starbasegame.com/images/2/24/Material-icons_Ymrium.png', 'https://wiki.starbasegame.com/index.php/Ymrium'));
        $entityManager->persist($this->createMaterial('Naflite', 'https://wiki.starbasegame.com/images/2/26/Material-icons_Naflite.png', 'https://wiki.starbasegame.com/index.php/Naflite'));
        $entityManager->persist($this->createMaterial('Kutonium', 'https://wiki.starbasegame.com/images/1/18/Material-icons_Kutonium.png', 'https://wiki.starbasegame.com/index.php/Kutonium'));
        $entityManager->persist($this->createMaterial('Arkanium', 'https://wiki.starbasegame.com/images/c/c6/Material-icons_Arkanium.png', 'https://wiki.starbasegame.com/index.php/Arkanium'));
        $entityManager->persist($this->createMaterial('Corazium', 'https://wiki.starbasegame.com/images/4/4f/Material-icons_Corazium.png', 'https://wiki.starbasegame.com/index.php/Corazium'));
        $entityManager->persist($this->createMaterial('Xhalium', 'https://wiki.starbasegame.com/images/e/e5/Material-icons_Xhalium.png', 'https://wiki.starbasegame.com/index.php/Xhalium'));
        $entityManager->persist($this->createMaterial('Daltium', 'https://wiki.starbasegame.com/images/0/04/Material-icons_Daltium.png', 'https://wiki.starbasegame.com/index.php/Daltium'));
        $entityManager->persist($this->createMaterial('Ice', 'https://wiki.starbasegame.com/images/3/36/Material-icons_Ice.png', 'https://wiki.starbasegame.com/index.php/Ice'));
        $entityManager->persist($this->createMaterial('Surtrite', 'https://wiki.starbasegame.com/images/6/66/Material-icons_Surtrite.png', 'https://wiki.starbasegame.com/index.php/Surtrite'));
        $entityManager->persist($this->createMaterial('Nhurgite', 'https://wiki.starbasegame.com/images/f/f9/Material-icons_Nhurgite.png', 'https://wiki.starbasegame.com/index.php/Nhurgite'));
        $entityManager->persist($this->createMaterial('Haderite', 'https://wiki.starbasegame.com/images/8/8c/Material-icons_Haderite.png', 'https://wiki.starbasegame.com/index.php/Haderite'));
        $entityManager->persist($this->createMaterial('Karnite', 'https://wiki.starbasegame.com/images/e/e6/Material-icons_Karnite.png', 'https://wiki.starbasegame.com/index.php/Karnite'));

        $entityManager->flush();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }

    /**
     * @param string $name
     * @param string|null $imageUrl
     * @param string|null $wikiPageUrl
     * @return Material
     */
    private function createMaterial(string $name, ?string $imageUrl, ?string $wikiPageUrl): Material
    {
        $material = new Material();

        $material->getProduct()->setName($name);
        $material->getProduct()->setImageUrl($imageUrl);
        $material->getProduct()->setWikiPageUrl($wikiPageUrl);
        $material->getProduct()->setModificationTime(new \DateTime());

        return $material;
    }
}
