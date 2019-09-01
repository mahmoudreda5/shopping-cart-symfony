<?php

namespace App\BotChannel\ChannelRequest;


use Symfony\Component\HttpFoundation\Request;

abstract class ChannelRequest{

    public $request;

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    //abstract actions provided
    public static $list = "List";
    public static $cart = "Cart";
//    public static $addProduct = "Product";


    public function __construct(Request $request){
        $this->request = $request;
    }



    //implement abstract/common behavior

}