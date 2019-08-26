<?php

namespace App\BotChannel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;

use App\Repository\UserRepository;

use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;

use Symfony\Component\Dotenv\Dotenv;
use Psr\Log\LoggerInterface;

use Symfony\Component\Security\Core\Security;


class BotChannel implements BotChannelInterface, WhatsappInterface{

    //singleton
    // public static $botChannel = null;

    private static $twilio = null;

    public $userRepository;
    public $logger;


    public function __construct(UserRepository $userRepository, LoggerInterface $logger){

        // //load channel credintials from .env
        // $dotenv = new Dotenv();
        // $dotenv->load(__DIR__.'/.env');

        //instantiate channel client
        static::$twilio = new Client($_ENV['SID'], $_ENV['TWILIO_TOKEN']);

        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    // public function getBotChannel(UserRepository $userRepository, LoggerInterface $logger){
    //     //lazy instantiation
    //     if(!static::$botChannel) static::$botChannel = new BotChannel($userRepository, $logger);
    //     return static::$botChannel;
    // }

    /**
     * {@inheritDoc}
     */
    public function list(string $channel, Request $request, $products){

        if($channel === WhatsappInterface::class){
            return $this->whatsappList($request, $products);
        }else return null;

    }

    /**
     * {@inheritDoc}
     */
    public function cart(string $channel, Request $request, $items){

        if($channel === WhatsappInterface::class){
            return $this->whatsappCart($request, $items);            
        }else return null;

    }

    /**
     * {@inheritDoc}
     */
    public function message(string $channel, Request $request, string $message){

        if($channel === WhatsappInterface::class){
            return $this->whatsappMessage($request, $message);            
        }else return null;

    }

    /**
     * {@inheritDoc}
     */
    public function findUser(string $channel, Request $request){

        if($channel === WhatsappInterface::class){
            return $this->findWhatsappUser($request);            
        }else return null;

    }

    /**
     * {@inheritDoc}
     */
    public function whatsappList(Request $request, $products){

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
    public function whatsappCart(Request $request, $items){

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
    public function whatsappMessage(Request $request, string $message){

        $message = $this->sendWhatsappMessageOrMedia(static::$twilio, $request, $message);        
        return $message;
    }

    /**
     * {@inheritDoc}
     */
    public function findWhatsappUser(Request $request){
        //auth user with phone number
        $phone = substr($request->request->all()["From"], strlen("whatsapp:+"));
        $user = $this->userRepository->findOneBy(["phone" => $phone]);

        return $user;
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