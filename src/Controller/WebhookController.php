<?php

namespace App\Controller;

use App\BotChannel\ChannelFactory;
use App\BotChannel\ChannelRequest\WhatsappRequest;
use App\BotChannel\WhatsappChannel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Repository\ProductRepository;


class WebhookController extends AbstractController
{
    /**
     * @Route("/webhook", name="webhook")
     * @param Request $request
     * @param ChannelFactory $channelFactory
     * @return Request
     */
    public function index(Request $request, ChannelFactory $channelFactory){

        $botChannel = $channelFactory->instantiateChannel(WhatsappChannel::class);
        return $botChannel->handleRequest(new WhatsappRequest($request));
    }

    /**
     * @Route("/test", name="test")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function test(ProductRepository $productRepository){
        //any code for testing
    }
}
