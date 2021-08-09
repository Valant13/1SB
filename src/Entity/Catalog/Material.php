<?php

namespace App\Entity\Catalog;

use App\Repository\Catalog\MaterialRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MaterialRepository::class)
 */
class Material
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Product::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", unique=true)
     * @Assert\Valid
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
