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

        return $this->verifyWebhook($request);

//        $botChannel = $channelFactory->instantiateChannel(MessengerChannel::class);
//        return $botChannel->handleRequest(new MessengerRequest($request));
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



}
