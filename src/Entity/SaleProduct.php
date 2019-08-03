<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SaleProductRepository")
 */
class SaleProduct extends Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $sale_price;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $discount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalePrice()
    {
        return $this->sale_price;
    }

    public function setSalePrice($sale_price): self
    {
        $this->sale_price = $sale_price;

        return $this;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function setDiscount($discount): self
    {
        $this->discount = $discount;

        return $this;
    }
}
