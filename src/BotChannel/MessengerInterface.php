<?php

namespace App\BotChannel;

use App\Entity\Product;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface MessengerInterface{

    // //messanger specific channel contracts

    // /**
    //  * list products to messenger channel
    //  *
    //  * @param Request,Array
    //  * @return mixed
    //  */
    // public function messengerList(Request $request, $products);

    // /**
    //  * list cart products to messenger channel
    //  *
    //  * @param Request,Product
    //  * @return mixed
    //  */
    // public function messengerCart(Request $request, $items);

    // /**
    //  * add product from messenger channel
    //  *
    //  * @param Request,string
    //  * @return mixed
    //  */
    // public function messengerMessage(Request $request, string $message);

    // // /**
    // //  * find user for messenger channel
    // //  *
    // //  * @param Request
    // //  * @return User
    // //  */
    // // public function findWhatsappUser(Request $request);

}