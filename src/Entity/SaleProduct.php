<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ComponentInterface\Product\SaleProductInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SaleProductRepository")
 */
class SaleProduct extends Product implements SaleProductInterface
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

    // /**
    //  * {@inheritdoc}
    //  */
    // public function getId(): ?int
    // {
    //     return parent::getId();
    // }

    /**
     * {@inheritdoc}
     */
    public function getSalePrice(): ?float
    {
        return $this->sale_price;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalePrice(float $sale_price): SaleProductInterface
    {
        $this->sale_price = $sale_price;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscount(float $discount): SaleProductInterface
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateDiscount(): ?float
    {
        return $this->discount = ($this->price - $this->sale_price) / $this->price * 100;
    }

    /**
     * return actual price wil be paid
     */
    public function getPaidPrice(): ?float
    {
        return $this->sale_price;
    }


}
