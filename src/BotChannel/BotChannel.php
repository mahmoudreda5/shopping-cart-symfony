<?php

namespace App\BotChannel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;

use App\ComponentInterface\Service\UserServiceInterface;
use App\ComponentInterface\Service\ProductServiceInterface;
use App\ComponentInterface\Factory\OrderCartFactory;


// use BotMan\BotMan\BotMan;
// use BotMan\BotMan\BotManFactory;
// use BotMan\BotMan\Drivers\DriverManager;

// use BotMan\Drivers\Facebook\Extensions\Element;
// use BotMan\Drivers\Facebook\Extensions\ElementButton;
// use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
// use BotMan\Drivers\Facebook\Extensions\ListTemplate;

use Symfony\Component\Dotenv\Dotenv;
use Psr\Log\LoggerInterface;

use Symfony\Component\Security\Core\Security;


abstract class BotChannel implements BotChannelInterface, WhatsappInterface, MessengerInterface{

    //singleton
    // public static $botChannel = null;

    // private static $botman = null;

    protected $userService;
    protected $productService;
    protected $cartFactory;
    protected $logger;


    public function __construct(UserServiceInterface $userService, ProductServiceInterface $productService, 
    OrderCartFactory $cartFactory, LoggerInterface $logger){

        // //load channel credintials from .env
        // $dotenv = new Dotenv();
        // $dotenv->load(__DIR__.'/.env');

        // //load facebook driver for botman
        // DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);

        // //botman config
        // $config = [
        //     'facebook' => [
        //         'token' => $_ENV['FACEBOOK_TOKEN'],
        //         'verification'=>$_ENV['FACEBOOK_VERIFY_TOKEN'],
        //     ]
        // ];

        // // Create botman instance
        // $this->botman = BotManFactory::create($config);

        $this->userService = $userService;
        $this->productService = $productService;
        $this->cartFactory = $cartFactory;
        $this->logger = $logger;
    }


    //override any common behavior

}