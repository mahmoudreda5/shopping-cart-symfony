<?php

namespace App\Event;

use App\BotChannel\MessengerChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductAddedSubscriber implements EventSubscriberInterface{

    private $messengerChannel;
    private $logger;

    public function __construct(MessengerChannel $messengerChannel, LoggerInterface $logger){
        $this->messengerChannel = $messengerChannel;
        $this->logger = $logger;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [

            ProductAddedEvent::NAME => 'onProductAdded'

        ];
    }

    public function onProductAdded(ProductAddedEvent $productAddedEvent){

        //TODO: messenger broadcast
        $this->messengerChannel->broadcast();

    }


}