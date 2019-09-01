<?php

namespace App\BotChannel;

use App\BotChannel\ChannelRequest\ChannelRequest;
use App\BotChannel\ChannelRequest\WhatsappRequest;

use App\ComponentInterface\Service\ProductService;
use App\ComponentInterface\Service\UserService;

use Twilio\Rest\Client;

use App\ComponentInterface\Service\UserServiceInterface;
use App\ComponentInterface\Service\ProductServiceInterface;
use App\ComponentInterface\Factory\OrderCartFactory;


use Psr\Log\LoggerInterface;




class WhatsappChannel extends BotChannel{


    //twilio client
    private static $twilio = null;

    public function __construct(UserService $userService, ProductService $productService,
     OrderCartFactory $cartFactory, LoggerInterface $logger){

        parent::__construct($userService, $productService, $cartFactory, $logger);

        //instantiate channel client
        static::$twilio = new Client($_ENV['SID'], $_ENV['TWILIO_TOKEN']);
    }

    /**
     * {@inheritDoc}
     */
    public function channelList(ChannelRequest $channelRequest, $response){

        foreach ($response as $product){
            $message = $this->sendWhatsappMessageOrMedia(static::$twilio, $channelRequest,
                "Product Id: " . $product->getId() . "\n".
                "Name: " . $product->getName() . "\n" .
                "Price: " . $product->getPrice() . " EGP\n" .
                "Description: " . substr($product->getDescription(), 0, 20) . " ... \n" .
                "See " .  $channelRequest->request->getSchemeAndHttpHost() . "/show/product/" . $product->getId(),
                $channelRequest->request->getUriForPath('/uploads/' . $product->getImage()));

            sleep(1);
        }

        return $message;

    }

    /**
     * {@inheritDoc}
     */
    public function channelCart(ChannelRequest $channelRequest, $response){

        if(!$response || count($response) == 0)
            $message = $this->sendWhatsappMessageOrMedia(static::$twilio, $channelRequest, "Your cart is empty!, send a 'Product Id' to add it to your shopping cart.");

        foreach($response as $item){
            $message = $this->sendWhatsappMessageOrMedia(static::$twilio, $channelRequest,
                "Product Id: " . $item["product"]["id"] . "\n".
                "Name: " . $item["product"]["name"] . "\n" . 
                "Price: " . $item["product"]["price"] . " EGP\n" .
                "Description: " . substr($item["product"]["description"], 0, 20) . " ... \n" .
                "See " .  $channelRequest->request->getSchemeAndHttpHost() . "/show/product/" . $item["product"]["id"],
                $channelRequest->request->getUriForPath('/uploads/' . $item["product"]["image"]));

            sleep(1);
            
        }

        return $message;

    }

    /**
     * {@inheritDoc}
     */
    public function channelActions(ChannelRequest $channelRequest, string $message){
        $message .= "\n\nsend: \n'List' for listing all products \n'Cart' for your cart products \n'Product Id' to add it to cart..";
        return $this->channelMessage($channelRequest, $message);
    }

    /**
     * {@inheritDoc}
     */
    public function channelMessage(ChannelRequest $channelRequest, string $message){

        $message = $this->sendWhatsappMessageOrMedia(static::$twilio, $channelRequest, $message);
        return $message;
    }

    private function sendWhatsappMessageOrMedia($twilio, WhatsappRequest $channelRequest, $message, $mediaUrl = null){
        $message = $twilio->messages
            ->create($channelRequest->getFrom() /*? $request->request->all()["From"] : "whatsapp:+14155238886"*/, // to
                $mediaUrl ? 
                array(
                    "from" => $channelRequest->getTo() /*? $request->request->all()["To"] : "whatsapp:+201152467173"*/,
                    "body" => $message,
                    "mediaurl" =>  $mediaUrl
                )
                :
                array(
                    "from" => $channelRequest->getTo(),
                    "body" => $message,
                )
        );

        return $message;
    }

}