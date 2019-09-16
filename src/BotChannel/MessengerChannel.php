<?php

namespace App\BotChannel;

use App\BotChannel\ChannelRequest\ChannelRequest;
use App\ComponentInterface\Service\ProductService;
use App\ComponentInterface\Service\UserService;

use App\ComponentInterface\Factory\OrderCartFactory;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use Psr\Log\LoggerInterface;

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;


class MessengerChannel extends BotChannel {


    //botman client
    public static $botman = null;

    public static $httpClient = null;

    public function initializeChannelClient(){
        //load facebook driver for botman
        DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);

        //botman config
        $config = [
            'facebook' => [
                'token' => $_ENV['FACEBOOK_TOKEN'],
                'verification'=>$_ENV['FACEBOOK_VERIFY_TOKEN'],
            ]
        ];

        // Create botman instance
        static::$botman = BotManFactory::create($config);

        //for manual requests
        static::$httpClient = HttpClient::create();

    }

    /**
     * {@inheritDoc}
     */
    public function channelList(ChannelRequest $channelRequest, $response){

        $elements = [];

        for($i = 0; $i < count($response); $i++){
            $elements[] = Element::create($response[$i]->getName())
                    ->subtitle(substr($response[$i]->getDescription(), 0, 27) . " ...")
                     ->image($channelRequest->request->getUriForPath('/uploads/' . $response[$i]->getImage()))
//                    ->image('https://pbs.twimg.com/profile_images/3677320779/32a3fde04e2a08045966a4cc19926328_400x400.jpeg')
                    ->addButton(ElementButton::create('Details')
                        ->url($channelRequest->request->getSchemeAndHttpHost() . "/show/product/" . $response[$i]->getId())
                    )
                    ->addButton(ElementButton::create('Order ')
                        ->payload($response[$i]->getId())
                        ->type('postback')
                    );
        }

        static::$botman->say(GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
            ->addElements($elements)
        , $channelRequest->getPSID());

    }

    /**
     * {@inheritDoc}
     */
    public function channelCart(ChannelRequest $channelRequest, $response){

//        $message = new IncomingMessage($channelRequest->getMessage(), $channelRequest->getPSID(), $channelRequest->getRecipientId());
//        $message->logger = $this->logger;
//        /* @var BotMan */
//        $userInfo = static::$botman->getUser($message)->getInfo();
//        $this->logger->info(json_encode());

        $elements = [];

        foreach($response as $item){
            $elements[] = Element::create($item["product"]["name"])
                    ->subtitle(substr($item["product"]["description"], 0, 27) . " ...")
                     ->image($channelRequest->request->getUriForPath('/uploads/' . $item["product"]["image"]))
//                    ->image('https://pbs.twimg.com/profile_images/3677320779/32a3fde04e2a08045966a4cc19926328_400x400.jpeg')
                    ->addButton(ElementButton::create('Details')
                        ->url($channelRequest->request->getSchemeAndHttpHost() . "/show/product/" . $item["product"]["id"])
                    )
            ;
        }

        count($elements) ? 

        static::$botman->say(GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
            ->addElements($elements)
        , $channelRequest->getPSID()) : static::$botman->say("Your cart is empty!, send a 'Product Id' to add it to your shopping cart.", $channelRequest->getPSID());


    }

    public function channelActions(ChannelRequest $channelRequest, string $message){
        static::$botman->say(Question::create($message)->addButtons([
            Button::create('List')->value('list_messenger'),
            Button::create('Cart')->value('cart_messenger'),
        ]), $channelRequest->getPSID());
    }

    /**
     * {@inheritDoc}
     */
    public function channelMessage(ChannelRequest $channelRequest, string $message){
        static::$botman->say($message, $channelRequest->getPSID());
    }

    public function createBroadcastMessage()
    {

        //TODO: separate building message
        $message = new \stdClass();
        $message->dynamic_text = [
            "text" => "Hi, {{first_name}}!",
            "fallback_text" => "Hello friend!"
        ];

        $responseBody = [
            "messages" => array(
                $message
            )
        ];

//        $responseBody = '{"messages":[{"dynamic_text": {"text": "Hello , {{first_name}}!","fallback_text": "Hello friend"}}]}';

        $response = static::$httpClient->request('POST', 'https://graph.facebook.com/v4.0/me/message_creatives', [
            // these values are automatically encoded before including them in the URL
            'query' => [
                'access_token' => $_ENV['FACEBOOK_TOKEN'],
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $responseBody
        ]);

        $message_creative_id = json_decode($response->getContent())->message_creative_id;

        return $message_creative_id;

    }

    public function broadcastMessage(string $message_creative_id){

        $responseBody = [
            "message_creative_id" => $message_creative_id,
            "notification_type" => "SILENT_PUSH",
            "messaging_type" => "MESSAGE_TAG",
            "tag" => "NON_PROMOTIONAL_SUBSCRIPTION"
        ];

        $response = static::$httpClient->request('POST', 'https://graph.facebook.com/v4.0/me/broadcast_messages', [
            // these values are automatically encoded before including them in the URL
            'query' => [
                'access_token' => $_ENV['FACEBOOK_TOKEN'],
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $responseBody
        ]);

        return json_decode($response)->broadcast_id;
    }

    public function broadcast(){

        //TODO: broadcasting messenger messages

        try{

            //create broadcast message
            $message_creative_id = $this->createBroadcastMessage();

            //broadcast this message
            $broadcast_id = $this->broadcastMessage($message_creative_id);

            return $broadcast_id;

        }catch(ClientException $e){
            $this->logger->info($e->getTraceAsString());
        }

        return null;

    }

}