<?php

namespace App\BotChannel;

use App\Entity\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface BotChannelInterface{

    //basic channel contracts

    /**
     * list products to specific channel
     *
     * @param Request,Array
     * @return mixed
     */
    public function list(Request $request, $products);

    /**
     * list cart products to specific channel
     *
     * @param Request,Array
     * @return mixed
     */
    public function cart(Request $request, $items);

    /**
     * add product from specific channel
     *
     * @param Request,string
     * @return mixed
     */
    public function message(Request $request, string $message);

    /**
     * find user of specific channel
     *
     * @param Request
     * @return mixed
     */
    public function findUser(Request $request);

}