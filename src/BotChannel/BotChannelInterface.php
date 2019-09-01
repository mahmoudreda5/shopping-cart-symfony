<?php

namespace App\BotChannel;

use App\BotChannel\ChannelRequest\ChannelRequest;

use Symfony\Component\HttpFoundation\Request;

interface BotChannelInterface{

    //basic channel contracts

    /**
     * list products to specific channel
     *
     * @param Request,Array
     * @return mixed
     */
public function list();

    /**
     * list cart products to specific channel
     *
     * @param Request,Array
     * @return mixed
     */
public function cart();

    /**
     * add product from specific channel
     * @param $productIdOrName
     */
public function addProduct($productIdOrName);

    /**
     * handle request of specific channel
     *
     * @param Request
     * @return mixed
     */
public function process(ChannelRequest $channelRequest);

     /**
      * handle request of specific channel
      *
      * @param Request
      * @return mixed
      */
 public function handleRequest(ChannelRequest $channelRequest);

}