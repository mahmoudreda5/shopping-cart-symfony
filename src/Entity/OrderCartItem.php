<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ComponentInterface\CartItem\OrderCartItemInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderCartItemRepository")
 */
class OrderCartItem extends CartItem implements OrderCartItemInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $total_price;

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
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity(int $quantity): OrderCartItemInterface
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalPrice(float $total_price): OrderCartItemInterface
    {
        $this->total_price = $total_price;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function calculatetotalPrice(): ?float
    {
        return $this->total_price = $this->product->getTotalPrice() * $this->quantity();
    }


}
