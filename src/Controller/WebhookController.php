<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\OrderCart;

use App\Repository\ProductRepository;
use App\ComponentInterface\Factory\OrderCartFactory;

use App\BotChannel\WhatsappChannel;
use App\BotChannel\WhatsappInterface;



class WebhookController extends AbstractController
{
    /**
     * @Route("/webhook", name="webhook")
     */
    public function index(Request $request, ProductRepository $productRepository, LoggerInterface $logger, 
                            OrderCartFactory $orderCartFactory, WhatsappChannel $whatsappChannel){


        ////////////////////////////////////////////////////////
            // $logger->info(json_encode($request->request->all()));
            // return new Response();
        ////////////////////////////////////////////////////////

        
        return $whatsappChannel->handleRequest($request);    
    }

    /**
     * @Route("/test", name="test")
     */
    public function test(ProductRepository $productRepository){
        $products = $productRepository->findAll();

        echo "<pre>"; 
        var_dump($products[0]->getName());
        echo "</pre>";

        return new Response();
    }
}
