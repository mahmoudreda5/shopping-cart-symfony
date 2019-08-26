<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Symfony\Component\HttpClient\HttpClient;

use App\Entity\Product;
use App\Repository\ProductRepository;


use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\Drivers\Facebook\Extensions\ListTemplate;



class MessengerWebhookController extends AbstractController
{
    /**
     * @Route("/messenger/webhook", name="messenger_webhook")
     */
    public function index(Request $request, LoggerInterface $logger, ProductRepository $productRepository)
    {
        ////////////////////////////////////////////////////////
            // $input = json_decode(file_get_contents('php://input'), true);
            // $logger->info(json_encode($input));
            // $logger->info($request->getContent());
            // $logger->info("hello");
            return new Response("hello");
        ////////////////////////////////////////////////////////

        // return $this->verifyWebhook($request);

        DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);

        $config = [
            'facebook' => [
                'token' => 'EAASJ9DarqEYBAF62ZCGTaT1llZCpbsNPqBOJFADuA1NtfAZApgGvOF8ak847tYfEnhV2B9dmZACDVJEqezGw41Sf3nayZBc71wzQPX6v01WsbZAAIjuoweeSQYgJToMShzYdszZBRvfZBCZAZATy9nN9UotJMs9cTDLALbEdrtdyp83gZDZD',
                // 'app_secret' => 'YOUR-FACEBOOK-APP-SECRET-HERE',
                'verification'=>'shopping-cart-symfony',
            ]
        ];

        // Create an instance
        $botman = BotManFactory::create($config);

        // Give the bot something to listen for.
        $botman->hears('hello there!', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });

        $botman->hears('List', function (BotMan $bot) use ($productRepository, $request, $logger) {
            //get all products
            $products = $productRepository->findAll();
            $elements = [];

            // $logger->info($request->getSchemeAndHttpHost());
            // $logger->info($request->getBaseUrl());


            for($i = 0; $i < count($products); $i++){
                $elements[] = Element::create($products[$i]->getName())
                        ->subtitle(substr($products[$i]->getDescription(), 0, 27) . " ...")
                        ->image($request->getUriForPath('/uploads/' . $products[$i]->getImage()))
                        // ->image($request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . "/uploads/" . $products[$i]->getImage())
                        // ->image('https://pbs.twimg.com/profile_images/3677320779/32a3fde04e2a08045966a4cc19926328_400x400.jpeg')
                        ->addButton(ElementButton::create('Details')
                            ->url($request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . "/show/product/" . $products[$i]->getId())
                        )
                        ->addButton(ElementButton::create('Order')
                            ->payload('order')
                            ->type('postback')
                        );
            }

            $bot->reply(GenericTemplate::create()
                ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
                ->addElements($elements)
            );

            // for($i = 0; $i < /*count($products)*/ 4; $i++){
            //     $elements[] = Element::create($products[$i]->getName())
            //         ->subtitle(substr($products[$i]->getDescription(), 0, 27) . " ...")
            //         ->image($request->getUriForPath('/uploads/' . $products[$i]->getImage()))
            //         // ->addButton(ElementButton::create('tell me more')
            //         //     ->payload('tellmemore')
            //         //     ->type('postback')
            //     // );
            //     ->addButton(ElementButton::create('Details')
            //         ->url($request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . "/show/product/" . $products[$i]->getId())
            //     );
            // }
            
            // $bot->reply(ListTemplate::create()
            //     ->useCompactView()
            //     // ->addGlobalButton(ElementButton::create('view more')
            //     //     ->url('http://test.at')
            //     // )
            //     ->addElements($elements)
            //     // ->addElement($elements[1])
            //     // ->addElement($elements[2])
            // );

        });


        // Start listening
        $botman->listen();

        return new Response();


        // $entries = json_decode($request->getContent())->entry;
        // $PSID = null;
        // $message = null;
        // $attachments = null;
        // $postback = null;
        // foreach($entries as $entry){
        //     $PSID = $entry->messaging[0]->sender->id;
        //     $message = isset($entry->messaging[0]->message->text) ? $entry->messaging[0]->message->text : null;
        //     $attachments = isset($entry->messaging[0]->message->attachments) ? $entry->messaging[0]->message->attachments : null;
        //     $postback = isset($entry->messaging[0]->postback) ? $entry->messaging[0]->postback : null;

        // }

        // // $logger->info($PSID);
        // // $logger->info(json_encode(json_encode($message)));
        // // $logger->info(json_encode(json_encode($attachments)));            

        // if($PSID && ($message || $attachments || $postback)){
        //     $responseBody = null;

            
            
        //     if($attachments){
                
        //         $responseBody = $this->prepareAttachment($PSID, $attachments);
        //         // $logger->info(json_encode($responseBody));
        //         // return new Response();
        //     }else if($message){
        //         // $responseBody = $this->prepareMessage($PSID, "you said " . $message . ", so i don't understand you anyway but welcome!");
        //         $responseBody = $message == "Red" || $message == "Green" ? 
        //         $this->prepareMessage($PSID, "you said " . $message) : $this->prepareQuickReplies($PSID);
                
            
        //     }else if($postback){
        //         $responseBody = $this->prepareMessage($PSID, "you said " . $postback->title);
        //     }

        //     $logger->info(json_encode($responseBody));
            
            
    
        //     // $logger->info($request->getContent());

        //     $client = HttpClient::create();
        //     // it makes an HTTP POST request to https://httpbin.org/get?token=...&name=...
        //     try{

        //         $response = $client->request('POST', 'https://graph.facebook.com/v2.6/me/messages', [
        //             // these values are automatically encoded before including them in the URL
        //             'query' => [
        //                 'access_token' => 'EAASJ9DarqEYBAF62ZCGTaT1llZCpbsNPqBOJFADuA1NtfAZApgGvOF8ak847tYfEnhV2B9dmZACDVJEqezGw41Sf3nayZBc71wzQPX6v01WsbZAAIjuoweeSQYgJToMShzYdszZBRvfZBCZAZATy9nN9UotJMs9cTDLALbEdrtdyp83gZDZD',
        //             ],
        //             'body' => $responseBody
        //         ]);


        //     }catch(RequestException $e){
        //         // $logger->info(json_encode($e));

        //         $logger->info($request->getContent());

        //     }
        // }else {
        //     $logger->info($request->getContent());
        // }


        // // $logger->info(json_encode($response));


        // return $this->render('messenger_webhook/index.html.twig', [
        //     'controller_name' => 'MessengerWebhookController',
        // ]);
    }

    private function prepareMessage($PSID, $text){
        $responseBody = [
            "recipient" => ["id" => $PSID],
            "message" => ["text" => $text],        
        ];

        return $responseBody;
    }

    private function prepareQuickReplies($PSID){
        $responseBody = [
            "recipient" => ["id" => $PSID],
            "messaging_type" => "RESPONSE",
            "message" => [
                "text" => "Pick a color:",
                    "quick_replies" => [
                        [
                            "content_type" => "text",
                            "title" => "Red",
                            "payload" => "red",
                            "image_url" => "http://example.com/img/red.png"
                        ],
                        [
                            "content_type" => "text",
                            "title" => "Green",
                            "payload" => "green",
                            "image_url" => "http://example.com/img/green.png"
                        ]
                    ]
            ],        
        ];

        return $responseBody;
    }

    private function prepareAttachment($PSID, $attachments){
        $responseBody = [
            "recipient" => ["id" => $PSID],
            "message" => [
                "attachment" => [
                    "type" => "template",
                    "payload" => [
                        "template_type" => "generic",
                        "elements" => [
                            [
                                "title" => "Is this the right picture?",
                                "subtitle" => "Tap a button to answer.",
                                "image_url" => $attachments[0]->payload->url,
                                "buttons" => [
                                    [
                                        "type" => "postback",
                                        "title" => "Yes!",
                                        "payload" => "yes",
                                    ],
                                    [
                                        "type" => "postback",
                                        "title" => "No!",
                                        "payload" => "no",
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],        
        ];

        return $responseBody;
    }


    public function verifyWebhook($request){

        $verifyToken = "shopping-cart-symfony";

        $mode = $request->query->get("hub_mode");
        $hubVerifyToken = $request->query->get("hub_verify_token");
        $challenge = $request->query->get("hub_challenge");

        // Checks if a token and mode is in the query string of the request
        if ($mode && $hubVerifyToken) {
        
            // Checks the mode and token sent is correct
            if ($mode === 'subscribe' && $hubVerifyToken === $verifyToken) {
            
                // Responds with the challenge token from the request
                return new Response($challenge);
                
            } else {
                // Responds with '403 Forbidden' if verify tokens do not match
                return new Response("403 Forbidden", 404);
            }
        }

        

    }



}
