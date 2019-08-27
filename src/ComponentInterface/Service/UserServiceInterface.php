<?php 

namespace App\ComponentInterface\Service;


interface UserServiceInterface{


    /**
     * retrieve user with phone number
     * 
     * @param
     * @return mixed
     */
    public function findUserWithPhone($phone);

}