<?php

namespace App\BotChannel;

use App\BotChannel\ChannelRequest\ChannelRequest;
use App\ComponentInterface\CustomException\CartHasProductException;
use App\ComponentInterface\CustomException\NullUserException;
use App\ComponentInterface\CustomException\ProductNotFoundException;
use App\ComponentInterface\Service\ProductService;
use App\ComponentInterface\Service\UserService;
use App\ComponentInterface\Factory\OrderCartFactory;

use App\Entity\OrderCart;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;


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

    public function handleRequest(ChannelRequest $channelRequest){

         try{
             //abstract factory based on channel request action
             $response = $this->process($channelRequest);


             //construct whatsapp response
             switch ($channelRequest->getRequestAction()){
                 case ChannelRequest::$list:

                     $this->channelList($channelRequest, $response);

                     break;
                 case ChannelRequest::$cart:

                     $this->channelCart($channelRequest, $response);

                     break;
                 default:
                     $this->channelMessage($channelRequest, "You just added \"" .  $response->getName() . "\" to your shopping cart!");
             }
         }catch(NullUserException $nullUser){
             $this->channelMessage($channelRequest, "You need to register at shopping cart first! \n" .
                     "Go " . $channelRequest->request->getSchemeAndHttpHost() . "/register");
         }catch (ProductNotFoundException $productNotFound){
             $this->channelActions($channelRequest, "You said " .  $channelRequest->getMessage() . ",  sorry i didn't understand you!");
         }catch (CartHasProductException $cartHasProduct){
             $this->channelMessage($channelRequest, "Product \"" . $cartHasProduct->product->getName()  . "\" is already in shopping your cart");
         }

         return new Response();
     }

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