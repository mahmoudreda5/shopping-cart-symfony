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

    //response construction methods, every channel must implement

    /**
     * @return mixed
     */
    public function initializeChannelClient();

    /**
     * @param ChannelRequest $channelRequest
     * @param $response
     * @return mixed
     */
    public function channelList(ChannelRequest $channelRequest, $response);

    /**
     * @param ChannelRequest $channelRequest
     * @param $response
     * @return mixed
     */
    public function channelCart(ChannelRequest $channelRequest, $response);

    /**
     * @param ChannelRequest $channelRequest
     * @param string $message
     * @return mixed
     */
    public function channelActions(ChannelRequest $channelRequest, string $message);

    /**
     * @param ChannelRequest $channelRequest
     * @param string $message
     * @return mixed
     */
    public function channelMessage(ChannelRequest $channelRequest, string $message);


}