<?php

namespace App\Event;

use App\BotChannel\ChannelRequest\WhatsappRequest;
use App\BotChannel\WhatsappChannel;
use App\ComponentInterface\CustomException\CartHasProductException;
use App\ComponentInterface\Factory\OrderCartFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductAddedListener {

    private $orderCartFactory;
    private $whatsappChannel;
    private $logger;

    public function __construct(OrderCartFactory $orderCartFactory, WhatsappChannel $whatsappChannel, LoggerInterface $logger){
        $this->orderCartFactory = $orderCartFactory;
        $this->whatsappChannel = $whatsappChannel;
        $this->logger = $logger;
    }

    public function onProductAdded(ProductAddedEvent $productAddedEvent){

        $request = $productAddedEvent->getRequest();
        $product = $productAddedEvent->getProduct();

        //add product to authenticated user OrderCart
        $botNumber = WhatsappChannel::NUMBER;
        $request->request->add(['To' => $botNumber]);
        $request->request->add(['From' => "whatsapp:+" . $this->orderCartFactory->getUser()->getPhone()]);

        if(!$this->orderCartFactory->hasProduct($product)){
            $message = $this->whatsappChannel->channelMessage(new WhatsappRequest($request), "You just added \"" .  $product->getName() . "\" to your shopping cart!");
        }else {
            $message = $this->whatsappChannel->channelMessage(new WhatsappRequest($request), "Product \"" . $product->getName()  . "\" is already in shopping your cart");
        }

    }

}