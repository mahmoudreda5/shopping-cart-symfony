<?php

namespace App\BotChannel\ChannelRequest;

use App\ComponentInterface\Service\UserService;

interface ChannelRequestInterface{

    //iabstract/common behavior

    /**
     * get request action.
     *
     * @return string
     */
    public function getRequestAction();

    /**
     * get user made that request.
     *
     * @param UserService $userService
     * @return mixed
     */
    public function getUser(UserService $userService);

    /**
     * @return mixed
     */
    public function getUserIdentification();

}