<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;
use Psr\Log\LoggerInterface;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\OrderCart;

use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\ComponentInterface\Factory\OrderCartFactory;


use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Symfony\Component\Asset\Packages;





class WebhookController extends AbstractController
{
    /**
     * @Route("/webhook", name="webhook")
     */
    public function index(Request $request, ProductRepository $productRepository, 
                    UserRepository $userRepository, LoggerInterface $logger, OrderCartFactory $orderCartFactory, Packages $assetsManager){


    ////////////////////////////////////////////////////////
        // $logger->info(json_encode($request->request->all()));
        // // $logger->info("hello");
        // return new Response();
    ////////////////////////////////////////////////////////

    //twilio object with credintials
    $sid    = "AC96ca1aa7af4dee699842eef49be9c62a";
    $token  = "4d6651529141b63bcac57d087c2a4eef";
    $twilio = new Client($sid, $token);

    $body = $request->request->all()["Body"];
    // $body = 2;

    if($body == "List" || $body == "list"){

        //get all products
        $products = $productRepository->findAll();

        //reply with all products, i will take 7 for now
        // $limit = 7;
        for($i = 0; $i < count($products); $i++){

            $message = $this->sendWhatsappMessageOrMedia($twilio, $request, 
                                    "product_id: " . $products[$i]->getId() . "\n".
                                    "name: " . $products[$i]->getName() . "\n" . 
                                    "price: " . $products[$i]->getPrice() . "EGP\n" .
                                    "description: " . substr($products[$i]->getDescription(), 0, 20) . " ... \n" .
                                    "see " .  $request->getScheme() . 's://' . $request->getHttpHost() . $request->getBasePath() . "/show/product/" . $products[$i]->getId(), 
                                    $request->getUriForPath('/uploads/' . $products[$i]->getImage()));

            sleep(1);

            // $message = $twilio->messages
            //     ->create($request->request->all()["From"] /*"whatsapp:+201152467173"*/, // to
            //         array(
            //             "from" => $request->request->all()["To"] /*"whatsapp:+14155238886"*/,
            //             "body" => $products[$i]->getName(),
            //             "mediaurl" =>  $request->getUriForPath('/uploads/' . $products[$i]->getImage())
            //         )
            // );
        }

    }else if($body == "Cart" || $body == "cart"){

        //auth user with phone number
        $phone = substr($request->request->all()["From"], strlen("whatsapp:+"));
        // $phone = substr("whatsapp:+201152467173", strlen("whatsapp:+"));
        $user = $userRepository->findOneBy(["phone" => $phone]);

        //if he is a user on my shopping cart great system
        if($user){
            //user's factory since it's not looged in
            $orderCartFactory->setUser($user);
            $orderCartFactory->instantiateCart(OrderCart::class);  //important to instatiate Cart with your type

            //get authenticated user orderCart products
            $items = $orderCartFactory->cartProducts();

            // $image = $assetsManager->getUrl("public/uploads" . $items[0]["product"]["image"]);
            // $image = $request->getScheme() . 's://' . $request->getHttpHost() . $request->getBasePath() . 
            //     $assetsManager->getUrl("public/uploads/" . $items[0]["product"]["image"]);
            // $image = $request->getUriForPath('/uploads/' . $items[0]["product"]["image"]);
            // var_dump($image);
            // return new Response();

            foreach($items as $item){
                $message = $this->sendWhatsappMessageOrMedia($twilio, $request, 
                        "product_id: " . $item["product"]["id"] . "\n".
                        "name: " . $item["product"]["name"] . "\n" . 
                        "price: " . $item["product"]["price"] . " EGP\n" .
                        "description: " . substr($item["product"]["description"], 0, 20) . " ... \n" .
                        "see " .  $request->getScheme() . 's://' . $request->getHttpHost() . $request->getBasePath() . "/show/product/" . $item["product"]["id"],
                        $request->getUriForPath('/uploads/' . $item["product"]["image"]));

                sleep(1);
                // $message = $twilio->messages
                //         ->create($request->request->all()["From"] /*"whatsapp:+201152467173"*/, // to
                //             array(
                //                 "from" => $request->request->all()["To"] /*"whatsapp:+14155238886"*/,
                //                 "body" => $item["product"]["name"],
                //                 "mediaurl" =>  $request->getUriForPath('/uploads/' . $item["product"]["image"])
                //             )
                //     );

                // $logger->info($message);
            }

            if(!$items || count($items) == 0){
                $message = $this->sendWhatsappMessageOrMedia($twilio, $request, "you Cart is empty!, send a product_id to add it to your shopping cart.");
            }
        }else{
            //return url to register first
            $message = $this->sendWhatsappMessageOrMedia($twilio, $request, 
                "you need to register at shopping cart first! \n" . 
                "go " . $request->getScheme() . 's://' . $request->getHttpHost() . $request->getBasePath() . "/register");
        }

        
    }else{

        //get all products
        $product = $productRepository->findOneBy(["id" => $body]);
        if($product){
            //add the product to users cart

            //auth user with phone number
            $phone = substr($request->request->all()["From"], strlen("whatsapp:+")  /* egypt only */);
            // $phone = substr("whatsapp:+201152467173", strlen("whatsapp:+"));
            $user = $userRepository->findOneBy(["phone" => $phone]);

            //if he is a user on my shopping cart great system
            if($user){
                //user's factory since it's not looged in
                $orderCartFactory->setUser($user);
                $orderCartFactory->instantiateCart(OrderCart::class);  //important to instatiate Cart with your type

                if(!$orderCartFactory->hasProduct($product)){
                    $orderCartFactory->addProduct($product);

                    $message = $this->sendWhatsappMessageOrMedia($twilio, $request, "you just added " .  $product->getName() . "to your shopping cart!");
                }else{
                    $message = $this->sendWhatsappMessageOrMedia($twilio, $request, "product " . $product->getName()  . " is already in shopping your cart");
                }
            }else {
                $message = $this->sendWhatsappMessageOrMedia($twilio, $request, 
                "you need to register at shopping cart first! \n" . 
                "go " . $request->getScheme() . 's://' . $request->getHttpHost() . $request->getBasePath() . "/register");
            }


        }else{
            $message = $this->sendWhatsappMessageOrMedia($twilio, $request, 
            "you said " .  $request->request->all()["Body"] . ",  sorry i didn't understand you!"
            . "\n\nsend: \n'List' for listing all products \n'Cart' for your cart products \n'product_id' to add it to cart..");
        }

        
    }
    

    

    // var_dump($items);
    return new Response();
    
        

        ///////////////////////////////////////////////////////


        // $logger->info($request->request->all()["From"]);
    // return new Response(json_encode(["text" => ["body" => "hey mahmoud"]]), 200/*, ["encoding" => "UTF-8", "accept" => "application/json"]*/);
    // return new Response();

    // return new Response(json_encode([
    //     "To" => $request->request->all()["From"],
    //     "From" => $request->request->all()["To"],
    //     "Body" => "hello mahmoud!, you just said " . $request->request->all()["Body"]
    // ]));

    // $products = $productRepository->findAll();
    
        
        // Find your Account Sid and Auth Token at twilio.com/console
        // DANGER! This is insecure. See http://twil.io/secure
        // $sid    = "AC96ca1aa7af4dee699842eef49be9c62a";
        // $token  = "4d6651529141b63bcac57d087c2a4eef";
        // $twilio = new Client($sid, $token);

        // $limit = 7;
        // for($i = 0; $i < $limit; $i++){
        //     $message = $twilio->messages
        //                 ->create($request->request->all()["From"], // to
        //                         array(
        //                             "from" => $request->request->all()["To"],
        //                             "body" => "Hello there! you just said " . $request->request->all()["Body"],
        //                             "mediaurl" => "https://statici.behindthevoiceactors.com/behindthevoiceactors/_img/chars/conan-edogawa-case-closed-countdown-to-heaven-40.8.jpg"
        //                         )
        //                 );
        // }
        
        // $message = $twilio->messages
        //                 ->create("whatsapp:+201152467173", // to
        //                         array(
        //                             "from" => "whatsapp:+14155238886",
        //                             "body" => "Hello there!",
        //                             "mediaurl" => "https://statici.behindthevoiceactors.com/behindthevoiceactors/_img/chars/conan-edogawa-case-closed-countdown-to-heaven-40.8.jpg"
        //                         )
        //                 );

        // echo "<pre>";
        // var_dump($products);
        // echo "</pre>";
        // return new Response();


        // $response = new MessagingResponse();
        // $response->message('This is message 1 of 2.');
        // $response->message('This is message 2 of 2.');

        // return $response;

        // $fulfillment = array(
        //     "fulfillmentText" => "hello mahmoud"
        // );

        // $fulfillment =  json_encode($fulfillment);

        // echo $fulfillment; exit;
        // return new Response($fulfillment);

        


        // return $this->render('webhook/index.html.twig', [
        //     'controller_name' => 'WebhookController',
        // ]);
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

    protected function sendWhatsappMessageOrMedia($twilio, $request, $message, $mediaUrl = null){
        $message = $twilio->messages
            ->create($request->request->all()["From"] /*"whatsapp:+201152467173"*/, // to
                $mediaUrl ? 
                array(
                    "from" => $request->request->all()["To"] /*"whatsapp:+14155238886"*/,
                    "body" => $message,
                    "mediaurl" =>  $mediaUrl
                )
                :
                array(
                    "from" => $request->request->all()["To"] /*"whatsapp:+14155238886"*/,
                    "body" => $message,
                )
        );

        return $message;
    }

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
