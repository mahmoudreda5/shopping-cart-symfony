<?php

namespace App\BotChannel;

use App\Entity\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface WhatsappInterface{

    //whatsapp specific channel contracts

    /**
     * list products to whatsapp channel
     *
     * @param Request,Array
     * @return mixed
     */
    public function whatsappList(Request $request, $products);

    /**
     * list cart products to whatsapp channel
     *
     * @param Request,Product
     * @return mixed
     */
    public function whatsappCart(Request $request, $items);

    /**
     * add product from whatsapp channel
     *
     * @param Request,string
     * @return mixed
     */
    public function whatsappMessage(Request $request, string $message);

    /**
     * find user for whatsapp channel
     *
     * @param Request
     * @return User
     */
    public function findWhatsappUser(Request $request);

}