<?php

namespace App\BotChannel;

use App\ComponentInterface\Service\FormLoggingService;
use App\ComponentInterface\Service\FormService;
use App\ComponentInterface\Service\ProductService;
use App\ComponentInterface\Service\UserService;
use App\ComponentInterface\Factory\OrderCartFactory;

use Psr\Log\LoggerInterface;


class ChannelFactory {

    protected $userService;
    protected $productService;
    protected $cartFactory;
    protected $formLoggingService;
    protected $formService;
    public $logger;


    public function __construct(UserService $userService, ProductService $productService,
                                OrderCartFactory $cartFactory, FormLoggingService $formLoggingService, FormService $formService, LoggerInterface $logger){

        $this->userService = $userService;
        $this->productService = $productService;
        $this->cartFactory = $cartFactory;
        $this->formLoggingService = $formLoggingService;
        $this->formService = $formService;
        $this->logger = $logger;
    }


    public function instantiateChannel(string $channelType){
        switch($channelType){
            case WhatsappChannel::class:
                return new WhatsappChannel($this->userService, $this->productService, $this->cartFactory, $this->formLoggingService, $this->formService, $this->logger);
                break;
            case MessengerChannel::class:
                return new MessengerChannel($this->userService, $this->productService, $this->cartFactory, $this->formLoggingService, $this->formService, $this->logger);
                break;
            default:
                return null;
        }
    }

}