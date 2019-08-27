<?php

namespace App\BotChannel;

use App\Entity\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface WhatsappInterface{

    //whatsapp specific channel contracts

    /**
     * handle whatsapp channel request
     *
     * @param Request
     * @return mixed
     */
    public function handleRequest(Request $request);

}
