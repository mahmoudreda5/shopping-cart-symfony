<?php

namespace App\Controller;

use App\BotChannel\ChannelFactory;
use App\BotChannel\ChannelRequest\MessengerRequest;
use App\BotChannel\MessengerChannel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class MessengerWebhookController extends AbstractController
{
    /**
     * @Route("/messenger/webhook", name="messenger_webhook")
     * @param Request $request
     * @param ChannelFactory $channelFactory
     * @return Response
     */
    public function index(Request $request, ChannelFactory $channelFactory)
    {
        ////////////////////////////////////////////////////////
            // $input = json_decode(file_get_contents('php://input'), true);
            // return new Response();
        ////////////////////////////////////////////////////////

        // return $this->verifyWebhook($request);

        //load facebook driver for botman
//         DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
//
//         //botman config
//         $config = [
//             'facebook' => [
//                 'token' => $_ENV['FACEBOOK_TOKEN'],
//                 'verification'=>$_ENV['FACEBOOK_VERIFY_TOKEN'],
//             ]
//         ];
//
//         // Create botman instance
//         $botman = BotManFactory::create($config);
//
//
//         // $messengerChannel->startHearing();
//         $botman->hears('hello', function (BotMan $bot) {
//             $bot->reply('hey');
//         });
//
//         //start hearing
//         $botman->listen();

//        $channelFactory->logger->info($request->getContent());


        $botChannel = $channelFactory->instantiateChannel(MessengerChannel::class);
        return $botChannel->handleRequest(new MessengerRequest($request));

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
