<?php

namespace App\BotChannel;

use App\BotChannel\ChannelRequest\ChannelRequest;
use App\ComponentInterface\CustomException\CartHasProductException;
use App\ComponentInterface\CustomException\NoFormIsOpened;
use App\ComponentInterface\CustomException\NullUserException;
use App\ComponentInterface\CustomException\ProductNotFoundException;
use App\ComponentInterface\Service\FormLoggingService;
use App\ComponentInterface\Service\FormService;
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
    protected $formLoggingService;
    protected $formService;
    protected $logger;


    public function __construct(UserService $userService, ProductService $productService,
    OrderCartFactory $cartFactory, FormLoggingService $formLoggingService, FormService $formService, LoggerInterface $logger){

        $this->userService = $userService;
        $this->productService = $productService;
        $this->cartFactory = $cartFactory;
        $this->formLoggingService = $formLoggingService;
        $this->formService = $formService;
        $this->logger = $logger;

        $this->initializeChannelClient();
    }


    //override any common behavior

    public function handleRequest(ChannelRequest $channelRequest){

         try{
             //abstract factory based on channel request action
             $response = $this->process($channelRequest);


             //construct channel response
             switch ($channelRequest->getRequestAction()){
                 case ChannelRequest::$list:

                     $this->channelList($channelRequest, $response);

                     break;
                 case ChannelRequest::$cart:

                     $this->channelCart($channelRequest, $response);

                     break;
                 default:
                     $this->channelMessage($channelRequest, $response);

             }
         }catch(NullUserException $nullUser){
             $this->channelMessage($channelRequest, "You need to register at shopping cart first! \n\n" .
                 "Send REGISTER_ME to register here :D\n" .
                "OR Go " . $channelRequest->request->getSchemeAndHttpHost() . "/register");
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
//             case ChannelRequest::$registerMe:
//                 return $this->startForm($channelRequest);
//                 break;
             default:
                 return $this->processMessage($channelRequest, $action);
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

     public function processMessage(ChannelRequest $channelRequest, $message){

        try{

            return $this->formService->handleFormIfAvailable($channelRequest, $message);

        }catch (NoFormIsOpened $noFormIsOpened){


            $addedProduct = $this->addProduct($message);
            return "You just added \"" .  $addedProduct->getName() . "\" to your shopping cart!";

        }

     }

//     public function startForm(ChannelRequest $channelRequest){
//
//        return $startMessage = $this->formService->startUserForm($channelRequest);
//
//     }

}