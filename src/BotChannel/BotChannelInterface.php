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
     * @param string,Request,Array
     * @return mixed
     */
    public function list(string $channel, Request $request, $products);

    /**
     * list cart products to specific channel
     *
     * @param string,Request,Array
     * @return mixed
     */
    public function cart(string $channel, Request $request, $items);

    /**
     * add product from specific channel
     *
     * @param string,Request,string
     * @return mixed
     */
    public function message(string $channel, Request $request, string $message);

    /**
     * find user of specific channel
     *
     * @param string,Request
     * @return mixed
     */
    public function findUser(string $channel, Request $request);

}