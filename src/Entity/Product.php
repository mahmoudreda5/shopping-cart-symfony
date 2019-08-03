<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ComponentInterface\Product\ProductInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"product" = "Product", "saleproduct" = "SaleProduct"})
 */
class Product implements ProductInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name): ProductInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): ProductInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice(float $price): ProductInterface
    {
        $this->price = $price;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity(int $quantity): ProductInterface
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * {@inheritdoc}
     */
    public function setImage(?string $image): ProductInterface
    {
        $this->image = $image;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasInstances(int $instancesNumber): ?bool
    {
        return $this->quantity >= $instancesNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function decreaseQuantity(int $instancesNumber): ?int
    {
        if($this->hasInstances($instancesNumber)){
            $this->quantity -= $instancesNumber;
            return $this->quantity;
        }

        return -1;
    }


}
