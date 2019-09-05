<?php

namespace App\BotChannel\ChannelRequest;

use App\ComponentInterface\Service\UserService;
use Symfony\Component\HttpFoundation\Request;


class MessengerRequest extends ChannelRequest{

    private $object;

    private $PSID;

    private $recipientId;

    private $text;

    private $quickReply;

    private $attachments;

    private $postback;

    private $time;


    public function __construct(Request $request){

        $this->constructMessengerRequest($request);

    }

    public function constructMessengerRequest(Request $request){

        parent::__construct($request);

        $requestParams = json_decode($request->getContent());

        $this->object = $requestParams->object;

        $entries = $requestParams->entry;
        foreach($entries as $entry){
            $this->PSID = $entry->messaging[0]->sender->id;
            $this->recipientId = $entry->messaging[0]->recipient->id;
            $this->text = isset($entry->messaging[0]->message->text) ? $entry->messaging[0]->message->text : null;
            $this->quickReply = isset($entry->messaging[0]->message->quick_reply) ? $entry->messaging[0]->message->quick_reply : null;
            $this->attachments = isset($entry->messaging[0]->message->attachments) ? $entry->messaging[0]->message->attachments : null;
            $this->postback = isset($entry->messaging[0]->postback) ? $entry->messaging[0]->postback : null;
            $this->time = $entry->time;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestAction(){
        $action =  $this->quickReply ? $this->quickReply->payload : null;
        if(!$action) $action = $this->postback ? $this->postback->payload : null;
        switch($action) {
            case 'list_messenger':
                return static::$list;
                break;
            case 'cart_messenger':
                return static::$cart;
                break;
            default:
                return $action;
        }

    }

    /**
     * {@inheritDoc}
     */
    public function getUser(UserService $userService){
        //auth user with phone number
        $user = $userService->findUserWithPSID($this->PSID);
        if(!$user) $user = $userService->createUserWithPSID($this->PSID);

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserIdentification()
    {
        return $this->PSID;
    }


    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object): void
    {
        $this->object = $object;
    }

    /**
     * @return mixed
     */
    public function getPSID()
    {
        return $this->PSID;
    }

    /**
     * @param mixed $PSID
     */
    public function setPSID($PSID): void
    {
        $this->PSID = $PSID;
    }

    /**
     * @return mixed
     */
    public function getRecipientId()
    {
        return $this->recipientId;
    }

    /**
     * @param mixed $recipientId
     */
    public function setRecipientId($recipientId): void
    {
        $this->recipientId = $recipientId;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $message
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        if($this->postback) return $this->postback->payload;
        return $this->text;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->text = $message;
    }

    /**
     * @return mixed
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @param mixed $attachments
     */
    public function setAttachments($attachments): void
    {
        $this->attachments = $attachments;
    }

    /**
     * @return mixed
     */
    public function getPostback()
    {
        return $this->postback;
    }

    /**
     * @param mixed $postback
     */
    public function setPostback($postback): void
    {
        $this->postback = $postback;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time): void
    {
        $this->time = $time;
    }


}