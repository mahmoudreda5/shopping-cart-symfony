<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ComponentInterface\Cart\OrderCartInterface;


/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderCartRepository")
 */
class OrderCart extends Cart implements OrderCartInterface
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
    private $total_price;

    // /**
    //  * {@inheritdoc}
    //  */
    // public function getId(): ?int
    // {
    //     return $this->id;
    // }

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
    public function setTotalPrice(float $total_price): OrderCartInterface
    {
        $this->total_price = $total_price;

        return $this;
    }

    /**
     * {@inheritdoc}
     */    
    public function calculateTotalPrice(): ?float
    {
        //calculate and update total order price
        $totalPrice = 0;

        //for each order item price = quantity * product price
        //total order price is sum of items prices
        foreach($this->getItems() as $orderItem){
            /** @var OrderItemInterface $orderItem */
            $totalPrice += $orderItem->getTotalPrice();
        }

        //you need to persist ordercart object to update total_price
        return $this->total_price = $totalPrice;
    }

}
