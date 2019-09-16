<?php

namespace App\Controller;

use App\BotChannel\ChannelFactory;
use App\BotChannel\ChannelRequest\MessengerRequest;
use App\BotChannel\MessengerChannel;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
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
    public function index(Request $request, ChannelFactory $channelFactory, LoggerInterface $logger)
    {

//        return $this->verifyWebhook($request);

//        //load facebook driver for botman
//        DriverManager::loadDriver(\BotMan\Drivers\Facebook\FacebookDriver::class);
//
//        //botman config
//        $config = [
//            'facebook' => [
//                'token' => $_ENV['FACEBOOK_TOKEN'],
//                'verification'=>$_ENV['FACEBOOK_VERIFY_TOKEN'],
//            ]
//        ];
//
//        // Create botman instance
//        $botman = BotManFactory::create($config);
//
//        $botman->hears("{sen}", function(BotMan $botMan, $sen){
//           $botMan->reply($sen);
//        });
//
//        $botman->listen();
//
//        return new Response();


        $logger->info($request->getContent());

        $client = HttpClient::create();
             // it makes an HTTP POST request to https://plaplapla/get?token=...&name=...
             try{
                 $response = $client->request('POST', 'https://graph.facebook.com/v2.6/me/messages', [
                     // these values are automatically encoded before including them in the URL
                     'query' => [
                         'access_token' => 'EAASJ9DarqEYBAF62ZCGTaT1llZCpbsNPqBOJFADuA1NtfAZApgGvOF8ak847tYfEnhV2B9dmZACDVJEqezGw41Sf3nayZBc71wzQPX6v01WsbZAAIjuoweeSQYgJToMShzYdszZBRvfZBCZAZATy9nN9UotJMs9cTDLALbEdrtdyp83gZDZD',
                     ],
                     'body' => $responseBody
                 ]);
             }catch(RequestException $e){
                 // $logger->info(json_encode($e));
                 $logger->info($request->getContent());
             }

        /* PHP SDK v5.0.0 */
        /* make the API call */
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->post(
                '/{object-id}/private_replies',
                array (),
                '{access-token}'
            );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $graphNode = $response->getGraphNode();
        /* handle the result */


        $botChannel = $channelFactory->instantiateChannel(MessengerChannel::class);
        return $botChannel->handleRequest(new MessengerRequest($request));

        //     $client = HttpClient::create();
        //     // it makes an HTTP POST request to https://plaplapla/get?token=...&name=...
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

    }

    /**
     * @Route("/messenger/broadcast", name="messenger_broadcast")
     */
    public function broadcast(Request $request, LoggerInterface $logger)
    {

//        $responseBody = [
//            "messages" => array(
//                "dynamic_text" => [
//                    "text" => "Hi, {{first_name}}!",
//                    "fallback_text" => "Hello friend!"
//                ]
//            )
//        ];

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

         $client = HttpClient::create();
        // it makes an HTTP POST request to https://plaplapla/get?token=...&name=...
        try{

            $response = $client->request('POST', 'https://graph.facebook.com/v4.0/me/message_creatives', [
                // these values are automatically encoded before including them in the URL
                'query' => [
                    'access_token' => $_ENV['FACEBOOK_TOKEN'],
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
//                'on_progress' => function () use ($logger){
//                    // $dlNow is the number of bytes downloaded so far
//                    // $dlSize is the total size to be downloaded or -1 if it is unknown
//                    // $info is what $response->getInfo() would return at this very time
//                    $logger->info("hello there");
//                },
                'body' => $responseBody
            ]);


//            $logger->info($response->getContent());
//            $logger->info(json_encode($responseBody));

            $response = json_decode($response->getContent());
            $message_creative_id = $response->message_creative_id;

            $respBody = [
                "message_creative_id" => $message_creative_id,
                "notification_type" => "SILENT_PUSH",
                "messaging_type" => "MESSAGE_TAG",
                "tag" => "NON_PROMOTIONAL_SUBSCRIPTION"
            ];

            $logger->info(json_encode($respBody));
            $res = $client->request('POST', 'https://graph.facebook.com/v4.0/me/broadcast_messages', [
                // these values are automatically encoded before including them in the URL
                'query' => [
                    'access_token' => $_ENV['FACEBOOK_TOKEN'],
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $respBody
            ]);

            $logger->info($res->getContent());

        }catch(ClientException $e){
            $logger->info($e->getTraceAsString());
//            $logger->info($e->getResponse()->getContent());

            $logger->info("hello");

        }


        return new Response();

    }


    //manually construct messenger response, before using botman
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


    //subscribe my webhook to facebook
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


    /**
     * @Route("/info", name="info")
     */
    public function info(){
        echo phpinfo();
    }



}
