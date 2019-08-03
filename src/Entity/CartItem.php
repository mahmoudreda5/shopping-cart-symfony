<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ComponentInterface\CartItem\CartItemInterface;
use App\ComponentInterface\Cart\CartInterface;
use App\ComponentInterface\Product\ProductInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CartItemRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"cartitem" = "CartItem", "ordercartitem" = "OrderCartItem"})
 */
class CartItem implements CartItemInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cart", inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

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
    public function getCart(): ?CartInterface
    {
        return $this->cart;
    }

    /**
     * {@inheritdoc}
     */
    public function setCart(?CartInterface $cart): CartItemInterface
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct(): ?ProductInterface
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(?ProductInterface $product): CartItemInterface
    {
        $this->product = $product;

        return $this;
    }
}
