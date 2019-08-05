<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ComponentInterface\Cart\WishlistCartInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WishlistCartRepository")
 */
class WishlistCart extends Cart implements WishlistCartInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    // public function getId(): ?int
    // {
    //     return $this->id;
    // }

    //no need for additional columns, this will use only parent structue (base Cart Entity) and base CartItem Entity as relation bridge
    //ofcourse this depend on type of cart you creating, if u wanna some additional storage column you can implement them
}
