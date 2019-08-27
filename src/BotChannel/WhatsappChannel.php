<?php

namespace App\BotChannel;

use App\Entity\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;

use App\ComponentInterface\Service\UserServiceInterface;
use App\ComponentInterface\Service\ProductServiceInterface;
use App\ComponentInterface\Factory\OrderCartFactory;

use Psr\Log\LoggerInterface;

use App\ComponentInterface\CustomException\NullUserException;
use App\ComponentInterface\CustomException\CartHasProduct;


class WhatsappChannel extends BotChannel implements WhatsappInterface{


    //twilio client
    private static $twilio = null;

    public function __construct(UserServiceInterface $userService, ProductServiceInterface $productService,
     OrderCartFactory $cartFactory, LoggerInterface $logger){

        parent::__construct($userService, $productService, $cartFactory, $logger);

        //instantiate channel client
        static::$twilio = new Client($_ENV['SID'], $_ENV['TWILIO_TOKEN']);
    }


    /**
     * {@inheritDoc}
     */
    public function list(Request $request, $products){

        for($i = 0; $i < count($products); $i++){

            $message = $this->sendWhatsappMessageOrMedia(static::$twilio, $request, 
                "Product Id: " . $products[$i]->getId() . "\n".
                "Name: " . $products[$i]->getName() . "\n" . 
                "Price: " . $products[$i]->getPrice() . " EGP\n" .
                "Description: " . substr($products[$i]->getDescription(), 0, 20) . " ... \n" .
                "See " .  $request->getSchemeAndHttpHost() . "/show/product/" . $products[$i]->getId(),                                     
                $request->getUriForPath('/uploads/' . $products[$i]->getImage()));

            sleep(1);
        }

        return $message;

    }

    /**
     * {@inheritDoc}
     */
    public function cart(Request $request, $items){

        foreach($items as $item){
            $message = $this->sendWhatsappMessageOrMedia(static::$twilio, $request, 
                "Product Id: " . $item["product"]["id"] . "\n".
                "Name: " . $item["product"]["name"] . "\n" . 
                "Price: " . $item["product"]["price"] . " EGP\n" .
                "Description: " . substr($item["product"]["description"], 0, 20) . " ... \n" .
                "See " .  $request->getSchemeAndHttpHost() . "/show/product/" . $item["product"]["id"],                        
                $request->getUriForPath('/uploads/' . $item["product"]["image"]));

            sleep(1);
            
        }

        return $message;

    }

    /**
     * {@inheritDoc}
     */
    public function message(Request $request, string $message){

        $message = $this->sendWhatsappMessageOrMedia(static::$twilio, $request, $message);        
        return $message;
    }

    /**
     * {@inheritDoc}
     */
    public function findUser(Request $request){
        //auth user with phone number
        $phone = substr($request->request->all()["From"], strlen("whatsapp:+"));
        $user = $this->userService->findUserWithPhone($phone);

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function handleRequest(Request $request){

        $requestBody = $request->request->all()["Body"];
        // $body = json_decode($request->getContent())->Body;

        if($requestBody == "List" || $requestBody == "list"){

            //get all products
            $products = $this->productService->retrieveAllProducts();

            //send whatsapp channel list
            $message = $this->list($request, $products);

        }else if($requestBody == "Cart" || $requestBody == "cart"){

            //user's factory mannually assigned since it's not looged in
            $this->cartFactory->setUser($this->findUser($request), OrderCart::class);

            try{

                //get authenticated user orderCart products
                $items = $this->cartFactory->cartProducts();

                if($items && count($items) > 0)
                    //send whatsapp channel cart list
                    $message = $this->cart($request, $items);
                else $message = $this->message($request, "Your cart is empty!, send a 'Product Id' to add it to your shopping cart.");

            }catch(NullUserException $nullUser){
                //return url to register first
                $message = $this->message($request, 
                    "You need to register at shopping cart first! \n" . 
                    "Go " . $request->getSchemeAndHttpHost() . "/register");
            }
            
        }else{

            //is it a product!
            $product = $this->productService->findProductWithId($requestBody);
            if($product){
                //add the product to users cart

                //user's factory mannually assigned since it's not looged in
                $this->cartFactory->setUser($this->findUser($request), OrderCart::class);

                try{

                    $this->cartFactory->addProduct($product);
                    $message = $this->message($request, "You just added \"" .  $product->getName() . "\" to your shopping cart!");

                }catch(NullUserException $nullUser){
                    //return url to register first
                    $message = $this->message($request, 
                        "You need to register at shopping cart first! \n" . 
                        "Go " . $request->getSchemeAndHttpHost() . "/register");
                }catch(CartHasProduct $cartHasProduct){
                    $message = $this->message($request, "Product \"" . $product->getName()  . "\" is already in shopping your cart");
                }


            }else{
                $message = $this->message($request, 
                    "You said " .  $requestBody . ",  sorry i didn't understand you!"
                    . "\n\nsend: \n'List' for listing all products \n'Cart' for your cart products \n'Product Id' to add it to cart..");
            }

            
        }

        return new Response();

    }

    private function sendWhatsappMessageOrMedia($twilio, $request, $message, $mediaUrl = null){
        $message = $twilio->messages
            ->create($request->request->all()["From"] /*? $request->request->all()["From"] : "whatsapp:+14155238886"*/, // to
                $mediaUrl ? 
                array(
                    "from" => $request->request->all()["To"] /*? $request->request->all()["To"] : "whatsapp:+201152467173"*/,
                    "body" => $message,
                    "mediaurl" =>  $mediaUrl
                )
                :
                array(
                    "from" => $request->request->all()["To"],
                    "body" => $message,
                )
        );

        return $message;
    }

}