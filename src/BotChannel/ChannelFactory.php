<?php

namespace App\BotChannel;

use App\ComponentInterface\Service\ProductService;
use App\ComponentInterface\Service\UserService;
use App\ComponentInterface\Factory\OrderCartFactory;

use Psr\Log\LoggerInterface;


class ChannelFactory {

    protected $userService;
    protected $productService;
    protected $cartFactory;
    public $logger;


    public function __construct(UserService $userService, ProductService $productService,
                                OrderCartFactory $cartFactory, LoggerInterface $logger){

        $this->userService = $userService;
        $this->productService = $productService;
        $this->cartFactory = $cartFactory;
        $this->logger = $logger;
    }


    public function instantiateChannel(string $channelType){
        switch($channelType){
            case WhatsappChannel::class:
                return new WhatsappChannel($this->userService, $this->productService, $this->cartFactory, $this->logger);
                break;
            case MessengerChannel::class:
                return new MessengerChannel($this->userService, $this->productService, $this->cartFactory, $this->logger);
                break;
            default:
                return null;
        }
    }

}