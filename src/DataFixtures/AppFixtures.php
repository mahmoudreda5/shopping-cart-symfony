<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\SaleProduct;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 17; $i++){
            $product = new Product();
            $product->setName("product " . $i);
            $product->setDescription("hello from the products number " . $i . "side !");
            $product->setPrice(mt_rand(10, 100));
            $product->setQuantity(mt_rand(1, 10));
            $product->setImage("conan.jpg");
            $manager->persist($product);
        }

        for($i = 1; $i <= 7; $i++){
            $product = new SaleProduct();
            $product->setName("product " . $i);
            $product->setDescription("hello from the products number " . $i . "side !");
            $product->setPrice(mt_rand(10, 100));
            $product->setQuantity(mt_rand(1, 10));
            $product->setImage("conan.jpg");

            $product->setSalePrice(mt_rand(10, 100));  //sale_price can be greater than price xD
            $product->setDiscount(mt_rand(10, 100));

            $manager->persist($product);
        }

        $manager->flush();
    }
}
