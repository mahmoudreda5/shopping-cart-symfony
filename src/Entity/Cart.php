<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\ComponentInterface\Cart\CartInterface;
use App\ComponentInterface\CartItem\CartItemInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"cart" = "Cart", "ordercart" = "OrderCart"})
 */

class Cart implements CartInterface
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
    private $items_number;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="carts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CartItem", mappedBy="cart", orphanRemoval=true)
     */
    private $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

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
    public function getItemsNumber(): ?int
    {
        return $this->items_number;
    }

    /**
     * {@inheritdoc}
     */
    public function setItemsNumber(int $items_number): CartInterface
    {
        $this->items_number = $items_number;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setUser(?User $user): CartInterface
    {
        $this->user = $user;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(CartItemInterface $item): CartInterface
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setCart($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(CartItemInterface $item): CartInterface
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getCart() === $this) {
                $item->setCart(null);
            }
        }

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function calculateItemsNumber(): ?int
    {
        return $this->items_number = count($this>items);
    }

}
