<?php

namespace App\BotChannel;

use App\BotChannel\ChannelRequest\ChannelRequest;
use App\ComponentInterface\Service\ProductService;
use App\ComponentInterface\Service\UserService;
use App\ComponentInterface\Factory\OrderCartFactory;

use App\Entity\OrderCart;
use Psr\Log\LoggerInterface;


abstract class BotChannel implements BotChannelInterface{

    //singleton
    // public static $botChannel = null;

    // private static $botman = null;

    protected $userService;
    protected $productService;
    protected $cartFactory;
    protected $logger;


    public function __construct(UserService $userService, ProductService $productService,
    OrderCartFactory $cartFactory, LoggerInterface $logger){

        $this->userService = $userService;
        $this->productService = $productService;
        $this->cartFactory = $cartFactory;
        $this->logger = $logger;
    }


    //override any common behavior

    public function process(ChannelRequest $channelRequest){

        //assign user manually to the cart factory since no login in session
        $user = $channelRequest->getUser($this->userService);
        $this->assignCartUser($user);

        $action = $channelRequest->getRequestAction();
         switch($action){
             case ChannelRequest::$list:
                 return $this->list();
                 break;
             case ChannelRequest::$cart:
                 return $this->cart();
                 break;
             default:
                 return $this->addProduct($action);
         }
    }

    public function list(){
        //get all products
        return $this->productService->retrieveAllProducts();
    }

    public function cart(){
        //get cart products
        return $this->cartFactory->cartProducts();
    }

     public function addProduct($productIdOrName){
        $product = $this->productService->findProductWithIdOrName($productIdOrName);


        $this->cartFactory->addProduct($product);
        return $product;
     }

     public function assignCartUser($user){
        $this->cartFactory->setUser($user, OrderCart::class);
     }

}