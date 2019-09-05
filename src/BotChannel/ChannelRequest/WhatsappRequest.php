<?php

namespace App\BotChannel\ChannelRequest;

use App\ComponentInterface\Service\UserService;
use Symfony\Component\HttpFoundation\Request;


class WhatsappRequest extends ChannelRequest{

    private $accountSid;

    private $body;

    private $from;

    private $to;

    private $numMedia;

    private $messageSid;

    private $smsStatus;

    public function __construct(Request $request){

        $this->constructWhatsappRequest($request);

    }

    public function constructWhatsappRequest(Request $request){

        parent::__construct($request);

        $requestParams = $request->request->all();

        $this->accountSid = isset($requestParams["AccountSid"]) ? $requestParams["AccountSid"] : null;
        $this->body = isset($requestParams["Body"]) ? $requestParams["Body"] : null;
        $this->from = $requestParams["From"];
        $this->to = $requestParams["To"];
        $this->numMedia = isset($requestParams["NumMedia"]) ? $requestParams["NumMedia"] : null;
        $this->messageSid = isset($requestParams["MessageSid"]) ? $requestParams["MessageSid"] : null;
        $this->smsStatus = isset($requestParams["SmsStatus"]) ? $requestParams["SmsStatus"] : null;

    }

    public function getUserPhone(){
        return substr($this->from, strlen("whatsapp:+"));
    }


    /**
     * {@inheritDoc}
     */
    public function getRequestAction(){
        $action =  $this->body;
        switch($action) {
            case 'List':
            case 'list':
                return static::$list;
                break;
            case 'Cart':
            case 'cart':
                return static::$cart;
                break;
            case 'REGISTER_ME':
                return static::$registerMe;
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
        $phone = $this->getUserPhone();
        return $userService->findUserWithPhone($phone);
    }
    /**
     * {@inheritDoc}
     */
    public function getUserIdentification()
    {
        return $this->getUserPhone();
    }


    /**
     * Get the value of accountSid
     */ 
    public function getAccountSid()
    {
        return $this->accountSid;
    }

    /**
     * Set the value of accountSid
     *
     * @return  self
     */ 
    public function setAccountSid($accountSid)
    {
        $this->accountSid = $accountSid;

        return $this;
    }

    /**
     * Get the value of body
     */ 
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @return  self
     */ 
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of body
     */
    public function getMessage()
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @return  self
     */
    public function setMessage($message)
    {
        $this->body = $message;

        return $this;
    }

    /**
     * Get the value of from
     */ 
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set the value of from
     *
     * @return  self
     */ 
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get the value of to
     */ 
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set the value of to
     *
     * @return  self
     */ 
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get the value of numMedia
     */ 
    public function getNumMedia()
    {
        return $this->numMedia;
    }

    /**
     * Set the value of numMedia
     *
     * @return  self
     */ 
    public function setNumMedia($numMedia)
    {
        $this->numMedia = $numMedia;

        return $this;
    }

    /**
     * Get the value of messageSid
     */ 
    public function getMessageSid()
    {
        return $this->messageSid;
    }

    /**
     * Set the value of messageSid
     *
     * @return  self
     */ 
    public function setMessageSid($messageSid)
    {
        $this->messageSid = $messageSid;

        return $this;
    }

    /**
     * Get the value of smsStatus
     */ 
    public function getSmsStatus()
    {
        return $this->smsStatus;
    }

    /**
     * Set the value of smsStatus
     *
     * @return  self
     */ 
    public function setSmsStatus($smsStatus)
    {
        $this->smsStatus = $smsStatus;

        return $this;
    }
}