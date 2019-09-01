<?php

namespace App\BotChannel;

use App\BotChannel\ChannelRequest\ChannelRequest;
use App\ComponentInterface\Service\ProductService;
use App\ComponentInterface\Service\UserService;

use App\ComponentInterface\Factory\OrderCartFactory;

use Psr\Log\LoggerInterface;

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;


class MessengerChannel extends BotChannel {


    //botman client
    public static $botman = null;

    public function __construct(UserService $userService, ProductService $productService,
     OrderCartFactory $cartFactory, LoggerInterface $logger){

        parent::__construct($userService, $productService, $cartFactory, $logger);

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
    }

    /**
     * {@inheritDoc}
     */
    public function channelList(ChannelRequest $channelRequest, $response){

        $elements = [];

        for($i = 0; $i < count($response); $i++){
            $elements[] = Element::create($response[$i]->getName())
                    ->subtitle(substr($response[$i]->getDescription(), 0, 27) . " ...")
                    // ->image($channelRequest->request->getUriForPath('/uploads/' . $response[$i]->getImage()))
                    ->image('https://pbs.twimg.com/profile_images/3677320779/32a3fde04e2a08045966a4cc19926328_400x400.jpeg')
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

        $elements = [];

        foreach($response as $item){
            $elements[] = Element::create($item["product"]["name"])
                    ->subtitle(substr($item["product"]["description"], 0, 27) . " ...")
                    // ->image($channelRequest->request->getUriForPath('/uploads/' . $item["product"]["image"]))
                    ->image('https://pbs.twimg.com/profile_images/3677320779/32a3fde04e2a08045966a4cc19926328_400x400.jpeg')
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

}