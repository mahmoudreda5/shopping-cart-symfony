<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\OrderCart;

use App\Repository\ProductRepository;
use App\ComponentInterface\Factory\OrderCartFactory;

use App\BotChannel\BotChannel;
use App\BotChannel\WhatsappInterface;



class WebhookController extends AbstractController
{
    /**
     * @Route("/webhook", name="webhook")
     */
    public function index(Request $request, ProductRepository $productRepository, LoggerInterface $logger, 
                            OrderCartFactory $orderCartFactory, BotChannel $botChannel){


        ////////////////////////////////////////////////////////
            // $logger->info(json_encode($request->request->all()));
            // return new Response();
        ////////////////////////////////////////////////////////

        $body = $request->request->all()["Body"];
        // $body = json_decode($request->getContent())->Body;

        if($body == "List" || $body == "list"){

            //get all products
            $products = $productRepository->findAll();

            //send whatsapp channel list
            $message = $botChannel->list(WhatsappInterface::class, $request, $products);

        }else if($body == "Cart" || $body == "cart"){

            $user = $botChannel->findUser(WhatsappInterface::class, $request);

            //if he is a user on my shopping cart great system
            if($user){
                //user's factory mannually assigned since it's not looged in
                $orderCartFactory->setUser($user, OrderCart::class);

                //get authenticated user orderCart products
                $items = $orderCartFactory->cartProducts();

                if($items && count($items) > 0){
                    //send whatsapp channel cart list
                    $message = $botChannel->cart(WhatsappInterface::class, $request, $items);
                }else{
                    $message = $botChannel->message(WhatsappInterface::class, $request, "Your cart is empty!, send a 'Product Id' to add it to your shopping cart.");
                }
            }else{
                //return url to register first
                $message = $botChannel->message(WhatsappInterface::class, $request, 
                    "You need to register at shopping cart first! \n" . 
                    "Go " . $request->getSchemeAndHttpHost() . "/register");
            }

            
        }else{
            //is it a product!
            $product = $productRepository->findOneBy(["id" => $body]);
            if($product){
                //add the product to users cart

                //get whatsapp user
                $user = $botChannel->findUser(WhatsappInterface::class, $request);

                //if he is a user on my shopping cart great system
                if($user){
                    //user's factory mannually assigned since it's not looged in
                    $orderCartFactory->setUser($user, OrderCart::class);

                    if(!$orderCartFactory->hasProduct($product)){
                        $orderCartFactory->addProduct($product);

                        $message = $botChannel->message(WhatsappInterface::class, $request, "You just added \"" .  $product->getName() . "\" to your shopping cart!");
                    }else{
                        $message = $botChannel->message(WhatsappInterface::class, $request, "Product \"" . $product->getName()  . "\" is already in shopping your cart");
                    }
                }else {
                    $message = $botChannel->message(WhatsappInterface::class, $request, 
                        "You need to register at shopping cart first! \n" . 
                        "Go " . $request->getSchemeAndHttpHost() . "/register");
                }


            }else{
                $message = $botChannel->message(WhatsappInterface::class, $request, 
                    "You said " .  $request->request->all()["Body"] . ",  sorry i didn't understand you!"
                    . "\n\nsend: \n'List' for listing all products \n'Cart' for your cart products \n'Product Id' to add it to cart..");
            }

            
        }
        

        return new Response();
    
    }

    // protected function attemptUser($user){

    //     $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
    //     $this->get('security.token_storage')->setToken($token);
    
    //     // If the firewall name is not main, then the set value would be instead:
    //     // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
    //     $this->get('session')->set('_security_main', serialize($token));
        
    //     // Fire the login event manually  //no event's for now
    //     // $event = new InteractiveLoginEvent($request, $token);
    //     // $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

    // }

    /**
     * @Route("/test", name="test")
     */
    public function test(ProductRepository $productRepository){
        $products = $productRepository->findAll();

        echo "<pre>"; 
        var_dump($products[0]->getName());
        echo "</pre>";

        return new Response();
    }
}
