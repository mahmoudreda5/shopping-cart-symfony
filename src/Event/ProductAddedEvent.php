<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class ProductAddedEvent extends Event{

    const NAME = "product.added";
    private $product;
//    private $request;

//    /**
//     * @return Request
//     */
//    public function getRequest(): Request
//    {
//        return $this->request;
//    }
//
//    /**
//     * @param Request $request
//     */
//    public function setRequest(Request $request): void
//    {
//        $this->request = $request;
//    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function __construct(Product $product, Request $request){

        $this->product = $product;
        $this->request = $request;

    }

    /**
     * @return string|null
     */
    public function getProductName(){
        return $this->product->getName();
    }

}